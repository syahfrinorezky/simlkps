<?php

namespace App\Controllers;

use App\Models\PeriodModel;
use App\Models\StudyProgramModel;
use App\Models\StudentAdmissionModel;
use App\Models\ForeignStudentModel;
use CodeIgniter\API\ResponseTrait;

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

        $periods = $this->periodModel->findAll();
        $studyPrograms = $this->studyProgramModel->where('status_aktif', 1)->orderBy('nama_prodi')->findAll();

        if (empty($periodId) && !empty($periods)) {
            foreach ($periods as $p) {
                if ($p['status'] === 'active') {
                    $periodId = $p['id'];
                    break;
                }
            }
            if (empty($periodId)) {
                $periodId = $periods[0]['id'];
            }
        }

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
                $totalAktif += $adm['mahasiswa_aktif'];
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
            $totalAsingAktif = 0;
            $totalAsingBaru = 0;
            $countries = [];
            foreach ($foreigns as $for) {
                $totalAsingAktif += $for['mahasiswa_asing_penuh_waktu'];
                $totalAsingBaru += $for['mahasiswa_asing_paruh_waktu'];
                if (!empty($for['negara_asal'])) {
                    $countries[strtolower(trim($for['negara_asal']))] = true;
                }
            }
            $stats = [
                'total_asing_aktif' => $totalAsingAktif,
                'total_asing_baru' => $totalAsingBaru,
                'total_negara' => count($countries)
            ];
        }

        return view('students/index', [
            'title' => $tab === 'admission' ? 'Seleksi Mahasiswa' : 'Mahasiswa Asing',
            'tab' => $tab,
            'periods' => $periods,
            'studyPrograms' => $studyPrograms,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'admissions' => $admissions,
            'foreigns' => $foreigns,
            'stats' => $stats
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
            'mahasiswa_aktif' => 'required|numeric|greater_than_equal_to[0]'
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
            'mahasiswa_aktif' => $this->request->getPost('mahasiswa_aktif')
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
            'mahasiswa_aktif' => 'required|numeric|greater_than_equal_to[0]'
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
            'mahasiswa_aktif' => $this->request->getPost('mahasiswa_aktif')
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

    // --- Submodul 2b Mahasiswa Asing ---

    public function storeForeign()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'study_program_id' => 'required',
            'tahun_akademik' => 'required|regex_match[/^[0-9]{4}\/[0-9]{4}$/]',
            'negara_asal' => 'required|min_length[2]|max_length[100]',
            'jenjang' => 'required',
            'mahasiswa_asing_penuh_waktu' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_paruh_waktu' => 'required|numeric|greater_than_equal_to[0]'
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
            'negara_asal' => $this->request->getPost('negara_asal'),
            'jenjang' => $this->request->getPost('jenjang'),
            'mahasiswa_asing_penuh_waktu' => $this->request->getPost('mahasiswa_asing_penuh_waktu'),
            'mahasiswa_asing_paruh_waktu' => $this->request->getPost('mahasiswa_asing_paruh_waktu')
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
            'negara_asal' => 'required|min_length[2]|max_length[100]',
            'jenjang' => 'required',
            'mahasiswa_asing_penuh_waktu' => 'required|numeric|greater_than_equal_to[0]',
            'mahasiswa_asing_paruh_waktu' => 'required|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->foreignModel->update($id, [
            'period_id' => $this->request->getPost('period_id'),
            'study_program_id' => $this->request->getPost('study_program_id'),
            'tahun_akademik' => $this->request->getPost('tahun_akademik'),
            'negara_asal' => $this->request->getPost('negara_asal'),
            'jenjang' => $this->request->getPost('jenjang'),
            'mahasiswa_asing_penuh_waktu' => $this->request->getPost('mahasiswa_asing_penuh_waktu'),
            'mahasiswa_asing_paruh_waktu' => $this->request->getPost('mahasiswa_asing_paruh_waktu')
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
}
