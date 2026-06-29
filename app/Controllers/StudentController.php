<?php

namespace App\Controllers;

use App\Models\PeriodModel;
use App\Models\StudyProgramModel;
use App\Models\StudentAdmissionModel;
use App\Models\ForeignStudentModel;
use CodeIgniter\API\ResponseTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends BaseController
{
    use ResponseTrait;

    protected $periodModel;
    protected $studyProgramModel;
    protected $admissionModel;
    protected $foreignModel;

    public function __construct()
    {
        $this->periodModel = new PeriodModel();
        $this->studyProgramModel = new StudyProgramModel();
        $this->admissionModel = new StudentAdmissionModel();
        $this->foreignModel = new ForeignStudentModel();
        helper(['form', 'url']);
    }

    private function checkAuth()
    {
        $role = session()->get('userRole');
        return in_array($role, ['admin', 'prodi']);
    }

    public function index()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $tab = $this->request->getVar('tab') ?? 'admission';
        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $lkpsService = new \App\Services\LkpsService();
        $activeData = $lkpsService->resolveActivePeriod($periodId);
        $periodId = $activeData['activePeriodId'];
        $periods = $activeData['periods'];

        $studyPrograms = $this->studyProgramModel->where('status_aktif', 1)->orderBy('nama_prodi')->findAll();

        $admissions = [];
        $foreigns = [];
        $stats = [];

        if ($tab === 'admission') {
            $admissions = $this->admissionModel->getAdmissions($periodId, $search)->findAll();
            
            // Calculate stats for widgets
            $totalPendaftar = 0;
            $totalLulus = 0;
            $totalMaba = 0;
            $totalAktif = 0;
            foreach ($admissions as $adm) {
                $totalPendaftar += $adm['jumlah_pendaftar'];
                $totalLulus += $adm['jumlah_lulus_seleksi'];
                $totalMaba += ($adm['mahasiswa_baru_reguler'] + $adm['mahasiswa_baru_transfer']);
                $totalAktif += ($adm['mahasiswa_aktif_reguler'] + $adm['mahasiswa_aktif_transfer']);
            }
            $stats = [
                'total_pendaftar' => $totalPendaftar,
                'total_lulus' => $totalLulus,
                'total_maba' => $totalMaba,
                'total_aktif' => $totalAktif,
                'rasio_keketatan' => $totalPendaftar > 0 ? round(($totalLulus / $totalPendaftar) * 100, 1) : 0
            ];
        } else {
            $foreigns = $this->foreignModel->getForeignStudents($periodId, $search)->findAll();

            // Calculate stats for widgets
            $totalActiveTS2 = 0;
            $totalActiveTS1 = 0;
            $totalActiveTS = 0;
            $totalForeignFullTS2 = 0;
            $totalForeignFullTS1 = 0;
            $totalForeignFullTS = 0;
            $totalForeignPartTS2 = 0;
            $totalForeignPartTS1 = 0;
            $totalForeignPartTS = 0;
            foreach ($foreigns as $for) {
                $totalActiveTS2 += $for['mahasiswa_aktif_ts2'];
                $totalActiveTS1 += $for['mahasiswa_aktif_ts1'];
                $totalActiveTS += $for['mahasiswa_aktif_ts'];
                $totalForeignFullTS2 += $for['mahasiswa_asing_full_ts2'];
                $totalForeignFullTS1 += $for['mahasiswa_asing_full_ts1'];
                $totalForeignFullTS += $for['mahasiswa_asing_full_ts'];
                $totalForeignPartTS2 += $for['mahasiswa_asing_part_ts2'];
                $totalForeignPartTS1 += $for['mahasiswa_asing_part_ts1'];
                $totalForeignPartTS += $for['mahasiswa_asing_part_ts'];
            }
            $stats = [
                'total_aktif_ts2' => $totalActiveTS2,
                'total_aktif_ts1' => $totalActiveTS1,
                'total_aktif_ts' => $totalActiveTS,
                'total_asing_full_ts2' => $totalForeignFullTS2,
                'total_asing_full_ts1' => $totalForeignFullTS1,
                'total_asing_full_ts' => $totalForeignFullTS,
                'total_asing_part_ts2' => $totalForeignPartTS2,
                'total_asing_part_ts1' => $totalForeignPartTS1,
                'total_asing_part_ts' => $totalForeignPartTS
            ];
        }

        $activePeriod = array_values(array_filter($periods, fn($p) => $p['id'] == $periodId))[0] ?? null;
        $tsYear = date('Y');
        if ($activePeriod) {
            $tsYear = (int) substr($activePeriod['tahun_akademik'], 0, 4);
        }
        $years = [
            'ts'  => $tsYear,
            'ts1' => $tsYear - 1,
            'ts2' => $tsYear - 2,
        ];

        return view('students/index', [
            'title' => $tab === 'admission' ? 'Seleksi Mahasiswa' : 'Mahasiswa Asing',
            'tab' => $tab,
            'periods' => $periods,
            'studyPrograms' => $studyPrograms,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'admissions' => $admissions,
            'foreigns' => $foreigns,
            'stats' => $stats,
            'years' => $years
        ]);
    }

    // --- Submodul 2a Seleksi Mahasiswa ---

    public function storeAdmission()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'study_program_id' => 'required',
            'tahun_akademik' => 'required|regex_match[/^[0-9]{4}\/[0-9]{4}$/]',
            'daya_tampung' => 'required|numeric|greater_than_equal_to[0]',
            'jumlah_pendaftar' => 'required|numeric|greater_than_equal_to[0]',
            'jumlah_lulus_seleksi' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_baru_reguler' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_baru_transfer' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_reguler' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_transfer' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate UUID
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $this->admissionModel->insert([
            'id' => $uuid,
            'period_id' => $this->request->getPost('period_id'),
            'study_program_id' => $this->request->getPost('study_program_id'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'daya_tampung' => $this->request->getPost('daya_tampung'),
            'jumlah_pendaftar' => $this->request->getPost('jumlah_pendaftar'),
            'jumlah_lulus_seleksi' => $this->request->getPost('jumlah_lulus_seleksi'),
            'mahasiswa_baru_reguler' => $this->request->getPost('mahasiswa_baru_reguler'),
            'mahasiswa_baru_transfer' => $this->request->getPost('mahasiswa_baru_transfer'),
            'mahasiswa_aktif_reguler' => $this->request->getPost('mahasiswa_aktif_reguler'),
            'mahasiswa_aktif_transfer' => $this->request->getPost('mahasiswa_aktif_transfer')
        ]);

        return redirect()->to('/students?tab=admission')->with('success', 'Data seleksi mahasiswa berhasil disimpan.');
    }

    public function detailAdmission($id)
    {
        if (!$this->checkAuth()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $data = $this->admissionModel->select('student_admissions.*, reporting_periods.nama_periode, study_programs.nama_prodi')
            ->join('reporting_periods', 'reporting_periods.id = student_admissions.period_id')
            ->join('study_programs', 'study_programs.id = student_admissions.study_program_id')
            ->find($id);

        if (!$data) {
            return $this->failNotFound('Data tidak ditemukan.');
        }

        return $this->respond($data);
    }

    public function updateAdmission($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'study_program_id' => 'required',
            'tahun_akademik' => 'required|regex_match[/^[0-9]{4}\/[0-9]{4}$/]',
            'daya_tampung' => 'required|numeric|greater_than_equal_to[0]',
            'jumlah_pendaftar' => 'required|numeric|greater_than_equal_to[0]',
            'jumlah_lulus_seleksi' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_baru_reguler' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_baru_transfer' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_reguler' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_transfer' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->admissionModel->update($id, [
            'period_id' => $this->request->getPost('period_id'),
            'study_program_id' => $this->request->getPost('study_program_id'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'daya_tampung' => $this->request->getPost('daya_tampung'),
            'jumlah_pendaftar' => $this->request->getPost('jumlah_pendaftar'),
            'jumlah_lulus_seleksi' => $this->request->getPost('jumlah_lulus_seleksi'),
            'mahasiswa_baru_reguler' => $this->request->getPost('mahasiswa_baru_reguler'),
            'mahasiswa_baru_transfer' => $this->request->getPost('mahasiswa_baru_transfer'),
            'mahasiswa_aktif_reguler' => $this->request->getPost('mahasiswa_aktif_reguler'),
            'mahasiswa_aktif_transfer' => $this->request->getPost('mahasiswa_aktif_transfer')
        ]);

        return redirect()->to('/students?tab=admission')->with('success', 'Data seleksi mahasiswa berhasil diperbarui.');
    }

    public function deleteAdmission($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $this->admissionModel->delete($id);
        return redirect()->to('/students?tab=admission')->with('success', 'Data seleksi mahasiswa berhasil dihapus.');
    }

    public function storeForeign()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'study_program_id' => 'required',
            'tahun_akademik' => 'required|regex_match[/^[0-9]{4}\/[0-9]{4}$/]',
            'mahasiswa_aktif_ts2' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_ts1' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_ts' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_full_ts2' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_full_ts1' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_full_ts' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_part_ts2' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_part_ts1' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_part_ts' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $this->foreignModel->insert([
            'id' => $uuid,
            'period_id' => $this->request->getPost('period_id'),
            'study_program_id' => $this->request->getPost('study_program_id'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'mahasiswa_aktif_ts2' => $this->request->getPost('mahasiswa_aktif_ts2'),
            'mahasiswa_aktif_ts1' => $this->request->getPost('mahasiswa_aktif_ts1'),
            'mahasiswa_aktif_ts' => $this->request->getPost('mahasiswa_aktif_ts'),
            'mahasiswa_asing_full_ts2' => $this->request->getPost('mahasiswa_asing_full_ts2'),
            'mahasiswa_asing_full_ts1' => $this->request->getPost('mahasiswa_asing_full_ts1'),
            'mahasiswa_asing_full_ts' => $this->request->getPost('mahasiswa_asing_full_ts'),
            'mahasiswa_asing_part_ts2' => $this->request->getPost('mahasiswa_asing_part_ts2'),
            'mahasiswa_asing_part_ts1' => $this->request->getPost('mahasiswa_asing_part_ts1'),
            'mahasiswa_asing_part_ts' => $this->request->getPost('mahasiswa_asing_part_ts')
        ]);

        return redirect()->to('/students?tab=foreign')->with('success', 'Data mahasiswa asing berhasil disimpan.');
    }

    public function detailForeign($id)
    {
        if (!$this->checkAuth()) {
            return $this->failForbidden('Akses ditolak.');
        }

        $data = $this->foreignModel->select('foreign_students.*, reporting_periods.nama_periode, study_programs.nama_prodi')
            ->join('reporting_periods', 'reporting_periods.id = foreign_students.period_id')
            ->join('study_programs', 'study_programs.id = foreign_students.study_program_id')
            ->find($id);

        if (!$data) {
            return $this->failNotFound('Data tidak ditemukan.');
        }

        return $this->respond($data);
    }

    public function updateForeign($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'study_program_id' => 'required',
            'tahun_akademik' => 'required|regex_match[/^[0-9]{4}\/[0-9]{4}$/]',
            'mahasiswa_aktif_ts2' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_ts1' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_aktif_ts' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_full_ts2' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_full_ts1' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_full_ts' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_part_ts2' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_part_ts1' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_part_ts' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->foreignModel->update($id, [
            'period_id' => $this->request->getPost('period_id'),
            'study_program_id' => $this->request->getPost('study_program_id'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'mahasiswa_aktif_ts2' => $this->request->getPost('mahasiswa_aktif_ts2'),
            'mahasiswa_aktif_ts1' => $this->request->getPost('mahasiswa_aktif_ts1'),
            'mahasiswa_aktif_ts' => $this->request->getPost('mahasiswa_aktif_ts'),
            'mahasiswa_asing_full_ts2' => $this->request->getPost('mahasiswa_asing_full_ts2'),
            'mahasiswa_asing_full_ts1' => $this->request->getPost('mahasiswa_asing_full_ts1'),
            'mahasiswa_asing_full_ts' => $this->request->getPost('mahasiswa_asing_full_ts'),
            'mahasiswa_asing_part_ts2' => $this->request->getPost('mahasiswa_asing_part_ts2'),
            'mahasiswa_asing_part_ts1' => $this->request->getPost('mahasiswa_asing_part_ts1'),
            'mahasiswa_asing_part_ts' => $this->request->getPost('mahasiswa_asing_part_ts')
        ]);

        return redirect()->to('/students?tab=foreign')->with('success', 'Data mahasiswa asing berhasil diperbarui.');
    }

    public function deleteForeign($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $this->foreignModel->delete($id);
        return redirect()->to('/students?tab=foreign')->with('success', 'Data mahasiswa asing berhasil dihapus.');
    }

    public function exportAdmission()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $admissions = $this->admissionModel->getAdmissions($periodId, $search)->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'No', 'Program Studi', 'Tahun Akademik', 'Daya Tampung', 
            'Jumlah Pendaftar', 'Jumlah Lulus Seleksi', 
            'Mahasiswa Baru Reguler', 'Mahasiswa Baru Transfer', 
            'Mahasiswa Aktif Reguler', 'Mahasiswa Aktif Transfer'
        ];

        foreach ($headers as $colIdx => $header) {
            $sheet->setCellValueByColumnAndRow($colIdx + 1, 1, $header);
        }

        $rowNum = 2;
        foreach ($admissions as $idx => $adm) {
            $sheet->setCellValueByColumnAndRow(1, $rowNum, $idx + 1);
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $adm['nama_prodi']);
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $adm['tahun_akademik']);
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $adm['daya_tampung']);
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $adm['jumlah_pendaftar']);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, $adm['jumlah_lulus_seleksi']);
            $sheet->setCellValueByColumnAndRow(7, $rowNum, $adm['mahasiswa_baru_reguler']);
            $sheet->setCellValueByColumnAndRow(8, $rowNum, $adm['mahasiswa_baru_transfer']);
            $sheet->setCellValueByColumnAndRow(9, $rowNum, $adm['mahasiswa_aktif_reguler']);
            $sheet->setCellValueByColumnAndRow(10, $rowNum, $adm['mahasiswa_aktif_transfer']);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'seleksi_mahasiswa_baru_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function importAdmission()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $file = $this->request->getFile('import_file');
        $periodId = $this->request->getPost('period_id');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak diunggah.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $imported = 0;
            $errors = [];

            $programs = $this->studyProgramModel->findAll();
            $programMap = [];
            foreach ($programs as $p) {
                $programMap[strtolower(trim($p['nama_prodi']))] = $p['id'];
            }

            foreach ($sheetData as $rowIdx => $row) {
                if ($rowIdx === 1) {
                    continue;
                }

                $prodiName = trim($row['B'] ?? '');
                $tahunAkademik = trim($row['C'] ?? '');

                if (empty($prodiName) || empty($tahunAkademik)) {
                    continue;
                }

                $prodiId = $programMap[strtolower($prodiName)] ?? null;

                if (!$prodiId) {
                    $errors[] = "Baris {$rowIdx}: Program Studi '{$prodiName}' tidak ditemukan di database.";
                    continue;
                }

                if (!preg_match('/^[0-9]{4}\/[0-9]{4}$/', $tahunAkademik)) {
                    $errors[] = "Baris {$rowIdx}: Format Tahun Akademik '{$tahunAkademik}' harus YYYY/YYYY (contoh: 2025/2026).";
                    continue;
                }

                $dayaTampung = max(0, intval($row['D'] ?? 0));
                $jumlahPendaftar = max(0, intval($row['E'] ?? 0));
                $jumlahLulusSeleksi = max(0, intval($row['F'] ?? 0));
                $mabaReguler = max(0, intval($row['G'] ?? 0));
                $mabaTransfer = max(0, intval($row['H'] ?? 0));
                $aktifReguler = max(0, intval($row['I'] ?? 0));
                $aktifTransfer = max(0, intval($row['J'] ?? 0));

                $existing = $this->admissionModel->where([
                    'period_id' => $periodId,
                    'study_program_id' => $prodiId,
                    'tahun_akademik' => $tahunAkademik
                ])->first();

                if ($existing) {
                    $this->admissionModel->update($existing['id'], [
                        'daya_tampung' => $dayaTampung,
                        'jumlah_pendaftar' => $jumlahPendaftar,
                        'jumlah_lulus_seleksi' => $jumlahLulusSeleksi,
                        'mahasiswa_baru_reguler' => $mabaReguler,
                        'mahasiswa_baru_transfer' => $mabaTransfer,
                        'mahasiswa_aktif_reguler' => $aktifReguler,
                        'mahasiswa_aktif_transfer' => $aktifTransfer
                    ]);
                } else {
                    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                        mt_rand(0, 0xffff),
                        mt_rand(0, 0x0fff) | 0x4000,
                        mt_rand(0, 0x3fff) | 0x8000,
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                    );

                    $this->admissionModel->insert([
                        'id' => $uuid,
                        'period_id' => $periodId,
                        'study_program_id' => $prodiId,
                        'tahun_akademik' => $tahunAkademik,
                        'daya_tampung' => $dayaTampung,
                        'jumlah_pendaftar' => $jumlahPendaftar,
                        'jumlah_lulus_seleksi' => $jumlahLulusSeleksi,
                        'mahasiswa_baru_reguler' => $mabaReguler,
                        'mahasiswa_baru_transfer' => $mabaTransfer,
                        'mahasiswa_aktif_reguler' => $aktifReguler,
                        'mahasiswa_aktif_transfer' => $aktifTransfer
                    ]);
                }

                $imported++;
            }

            if (!empty($errors)) {
                return redirect()->to('/students?tab=admission')->with('success', "Berhasil mengimpor {$imported} data.")
                    ->with('errors', $errors);
            }

            return redirect()->to('/students?tab=admission')->with('success', "Semua data ({$imported} baris) berhasil diimpor.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage());
        }
    }
}
