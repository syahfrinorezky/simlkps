<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\LearningIntegrationModel;
use App\Models\StudentSatisfactionModel;
use App\Models\PeriodModel;
use App\Services\LkpsService;

class CourseController extends BaseController
{
    protected CourseModel $courseModel;
    protected LearningIntegrationModel $integrationModel;
    protected StudentSatisfactionModel $satisfactionModel;
    protected PeriodModel $periodModel;
    protected LkpsService $lkpsService;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->integrationModel = new LearningIntegrationModel();
        $this->satisfactionModel = new StudentSatisfactionModel();
        $this->periodModel = new PeriodModel();
        $this->lkpsService = new LkpsService();
        helper(['form', 'url']);
    }

    private function checkAuth(): bool
    {
        $role = session()->get('userRole');
        return in_array($role, ['admin', 'prodi', 'dosen', 'asesor']);
    }

    private function canModify(): bool
    {
        $role = session()->get('userRole');
        return in_array($role, ['admin', 'prodi', 'dosen']);
    }

    // ==========================================
    // TABEL 5.a — Kurikulum Pembelajaran
    // ==========================================
    public function curriculum()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $activeData = $this->lkpsService->resolveActivePeriod($periodId);
        $periodId = $activeData['activePeriodId'];
        $periods = $activeData['periods'];

        $query = $this->courseModel->where('period_id', $periodId);
        if (!empty($search)) {
            $query->groupStart()
                ->like('kode_mk', $search)
                ->orLike('nama_mk', $search)
                ->orLike('unit_penyelenggara', $search)
                ->groupEnd();
        }

        $courses = $query->orderBy('semester', 'ASC')->orderBy('kode_mk', 'ASC')->findAll();

        return view('courses/curriculum', [
            'title' => 'Kurikulum Pembelajaran',
            'courses' => $courses,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function storeCurriculum()
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/curriculum')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'semester' => 'required|numeric',
            'kode_mk' => 'required|max_length[50]',
            'nama_mk' => 'required|max_length[255]',
            'sks_kuliah' => 'required|numeric',
            'sks_seminar' => 'required|numeric',
            'sks_praktikum' => 'required|numeric',
            'konversi_jam' => 'required|numeric',
            'unit_penyelenggara' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $docPath = null;
        $file = $this->request->getFile('dokumen_rencana_pembelajaran');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $docPath = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/curriculum', $docPath);
            $docPath = 'uploads/curriculum/' . $docPath;
        }

        $this->courseModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'semester' => $this->request->getPost('semester'),
            'kode_mk' => $this->request->getPost('kode_mk'),
            'nama_mk' => $this->request->getPost('nama_mk'),
            'mk_kompetensi' => $this->request->getPost('mk_kompetensi') ? 1 : 0,
            'sks_kuliah' => $this->request->getPost('sks_kuliah'),
            'sks_seminar' => $this->request->getPost('sks_seminar'),
            'sks_praktikum' => $this->request->getPost('sks_praktikum'),
            'konversi_jam' => $this->request->getPost('konversi_jam'),
            'cpl_sikap' => $this->request->getPost('cpl_sikap') ? 1 : 0,
            'cpl_pengetahuan' => $this->request->getPost('cpl_pengetahuan') ? 1 : 0,
            'cpl_keterampilan_umum' => $this->request->getPost('cpl_keterampilan_umum') ? 1 : 0,
            'cpl_keterampilan_khusus' => $this->request->getPost('cpl_keterampilan_khusus') ? 1 : 0,
            'dokumen_rencana_pembelajaran' => $docPath,
            'unit_penyelenggara' => $this->request->getPost('unit_penyelenggara'),
        ]);

        return redirect()->to('/courses/curriculum?period_id=' . $this->request->getPost('period_id'))->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function showCurriculum(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->courseModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function updateCurriculum(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/curriculum')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'semester' => 'required|numeric',
            'kode_mk' => 'required|max_length[50]',
            'nama_mk' => 'required|max_length[255]',
            'sks_kuliah' => 'required|numeric',
            'sks_seminar' => 'required|numeric',
            'sks_praktikum' => 'required|numeric',
            'konversi_jam' => 'required|numeric',
            'unit_penyelenggara' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $course = $this->courseModel->find($id);
        $docPath = $course['dokumen_rencana_pembelajaran'];

        $file = $this->request->getFile('dokumen_rencana_pembelajaran');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($docPath && file_exists(ROOTPATH . 'public/' . $docPath)) {
                unlink(ROOTPATH . 'public/' . $docPath);
            }
            $docPath = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/curriculum', $docPath);
            $docPath = 'uploads/curriculum/' . $docPath;
        }

        $this->courseModel->update($id, [
            'semester' => $this->request->getPost('semester'),
            'kode_mk' => $this->request->getPost('kode_mk'),
            'nama_mk' => $this->request->getPost('nama_mk'),
            'mk_kompetensi' => $this->request->getPost('mk_kompetensi') ? 1 : 0,
            'sks_kuliah' => $this->request->getPost('sks_kuliah'),
            'sks_seminar' => $this->request->getPost('sks_seminar'),
            'sks_praktikum' => $this->request->getPost('sks_praktikum'),
            'konversi_jam' => $this->request->getPost('konversi_jam'),
            'cpl_sikap' => $this->request->getPost('cpl_sikap') ? 1 : 0,
            'cpl_pengetahuan' => $this->request->getPost('cpl_pengetahuan') ? 1 : 0,
            'cpl_keterampilan_umum' => $this->request->getPost('cpl_keterampilan_umum') ? 1 : 0,
            'cpl_keterampilan_khusus' => $this->request->getPost('cpl_keterampilan_khusus') ? 1 : 0,
            'dokumen_rencana_pembelajaran' => $docPath,
            'unit_penyelenggara' => $this->request->getPost('unit_penyelenggara'),
        ]);

        return redirect()->to('/courses/curriculum?period_id=' . $course['period_id'])->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function deleteCurriculum(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/curriculum')->with('error', 'Akses ditolak.');
        }

        $course = $this->courseModel->find($id);
        if ($course) {
            if ($course['dokumen_rencana_pembelajaran'] && file_exists(ROOTPATH . 'public/' . $course['dokumen_rencana_pembelajaran'])) {
                unlink(ROOTPATH . 'public/' . $course['dokumen_rencana_pembelajaran']);
            }
            $this->courseModel->delete($id);
            return redirect()->to('/courses/curriculum?period_id=' . $course['period_id'])->with('success', 'Mata kuliah berhasil dihapus.');
        }

        return redirect()->to('/courses/curriculum')->with('error', 'Data tidak ditemukan.');
    }


    // ==========================================
    // TABEL 5.b — Integrasi Kegiatan Penelitian/PkM
    // ==========================================
    public function researchIntegration()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $activeData = $this->lkpsService->resolveActivePeriod($periodId);
        $periodId = $activeData['activePeriodId'];
        $periods = $activeData['periods'];

        $courses = $this->courseModel->where('period_id', $periodId)->orderBy('nama_mk', 'ASC')->findAll();

        $query = $this->integrationModel->select('learning_integrations.*, courses.nama_mk, courses.kode_mk')
            ->join('courses', 'courses.id = learning_integrations.course_id', 'left')
            ->where('learning_integrations.period_id', $periodId);

        if (!empty($search)) {
            $query->groupStart()
                ->like('learning_integrations.judul_penelitian_pkm', $search)
                ->orLike('learning_integrations.nama_dosen', $search)
                ->orLike('courses.nama_mk', $search)
                ->groupEnd();
        }

        $integrations = $query->orderBy('learning_integrations.tahun', 'DESC')->findAll();

        return view('courses/research_integration', [
            'title' => 'Integrasi Penelitian/PkM',
            'integrations' => $integrations,
            'courses' => $courses,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function storeIntegration()
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/research-integration')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'judul_penelitian_pkm' => 'required|min_length[5]',
            'nama_dosen' => 'required|min_length[3]|max_length[255]',
            'course_id' => 'required',
            'bentuk_integrasi' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->integrationModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'judul_penelitian_pkm' => $this->request->getPost('judul_penelitian_pkm'),
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'course_id' => $this->request->getPost('course_id'),
            'bentuk_integrasi' => $this->request->getPost('bentuk_integrasi'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/courses/research-integration?period_id=' . $this->request->getPost('period_id'))->with('success', 'Integrasi kegiatan berhasil ditambahkan.');
    }

    public function showIntegration(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->integrationModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function updateIntegration(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/research-integration')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul_penelitian_pkm' => 'required|min_length[5]',
            'nama_dosen' => 'required|min_length[3]|max_length[255]',
            'course_id' => 'required',
            'bentuk_integrasi' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $integration = $this->integrationModel->find($id);

        $this->integrationModel->update($id, [
            'judul_penelitian_pkm' => $this->request->getPost('judul_penelitian_pkm'),
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'course_id' => $this->request->getPost('course_id'),
            'bentuk_integrasi' => $this->request->getPost('bentuk_integrasi'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/courses/research-integration?period_id=' . $integration['period_id'])->with('success', 'Integrasi kegiatan berhasil diperbarui.');
    }

    public function deleteIntegration(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/research-integration')->with('error', 'Akses ditolak.');
        }

        $integration = $this->integrationModel->find($id);
        if ($integration) {
            $this->integrationModel->delete($id);
            return redirect()->to('/courses/research-integration?period_id=' . $integration['period_id'])->with('success', 'Integrasi kegiatan berhasil dihapus.');
        }

        return redirect()->to('/courses/research-integration')->with('error', 'Data tidak ditemukan.');
    }


    // ==========================================
    // TABEL 5.c — Kepuasan Mahasiswa
    // ==========================================
    private function initExcellence(int $periodId)
    {
        $count = $this->satisfactionModel->where('period_id', $periodId)->countAllResults();
        if ($count === 0) {
            $aspeks = [
                'Keandalan (Reliability)',
                'Daya Tanggap (Responsiveness)',
                'Kepastian (Assurance)',
                'Empati (Empathy)',
                'Tangible'
            ];
            foreach ($aspeks as $aspek) {
                $this->satisfactionModel->insert([
                    'id' => $this->lkpsService->generateUuid(),
                    'period_id' => $periodId,
                    'aspek' => $aspek,
                    'sangat_baik' => 0.00,
                    'baik' => 0.00,
                    'cukup' => 0.00,
                    'kurang' => 0.00,
                    'rencana_tindak_lanjut' => '',
                ]);
            }
        }
    }

    public function excellence()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $activeData = $this->lkpsService->resolveActivePeriod($periodId);
        $periodId = $activeData['activePeriodId'];
        $periods = $activeData['periods'];

        if ($periodId === null) {
            return view('courses/excellence', [
                'title' => 'Kepuasan Mahasiswa',
                'satisfactions' => [],
                'periods' => [],
                'selectedPeriod' => null,
                'search' => $search,
                'canModify' => $this->canModify(),
            ]);
        }

        // Auto initialize the 5 aspects
        $this->initExcellence($periodId);

        $query = $this->satisfactionModel->where('period_id', $periodId);
        if (!empty($search)) {
            $query->like('aspek', $search);
        }

        $satisfactions = $query->findAll();

        return view('courses/excellence', [
            'title' => 'Kepuasan Mahasiswa',
            'satisfactions' => $satisfactions,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function storeExcellence()
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/excellence')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'aspek' => 'required|min_length[3]|max_length[255]',
            'sangat_baik' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'baik' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'cukup' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'kurang' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'rencana_tindak_lanjut' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->satisfactionModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'aspek' => $this->request->getPost('aspek'),
            'sangat_baik' => $this->request->getPost('sangat_baik'),
            'baik' => $this->request->getPost('baik'),
            'cukup' => $this->request->getPost('cukup'),
            'kurang' => $this->request->getPost('kurang'),
            'rencana_tindak_lanjut' => $this->request->getPost('rencana_tindak_lanjut'),
        ]);

        return redirect()->to('/courses/excellence?period_id=' . $this->request->getPost('period_id'))->with('success', 'Aspek kepuasan berhasil ditambahkan.');
    }

    public function showExcellence(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->satisfactionModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function updateExcellence(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/excellence')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'aspek' => 'required|min_length[3]|max_length[255]',
            'sangat_baik' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'baik' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'cukup' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'kurang' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'rencana_tindak_lanjut' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $satisfaction = $this->satisfactionModel->find($id);

        $this->satisfactionModel->update($id, [
            'aspek' => $this->request->getPost('aspek'),
            'sangat_baik' => $this->request->getPost('sangat_baik'),
            'baik' => $this->request->getPost('baik'),
            'cukup' => $this->request->getPost('cukup'),
            'kurang' => $this->request->getPost('kurang'),
            'rencana_tindak_lanjut' => $this->request->getPost('rencana_tindak_lanjut'),
        ]);

        return redirect()->to('/courses/excellence?period_id=' . $satisfaction['period_id'])->with('success', 'Aspek kepuasan berhasil diperbarui.');
    }

    public function deleteExcellence(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/courses/excellence')->with('error', 'Akses ditolak.');
        }

        $satisfaction = $this->satisfactionModel->find($id);
        if ($satisfaction) {
            $this->satisfactionModel->delete($id);
            return redirect()->to('/courses/excellence?period_id=' . $satisfaction['period_id'])->with('success', 'Aspek kepuasan berhasil dihapus.');
        }

        return redirect()->to('/courses/excellence')->with('error', 'Data tidak ditemukan.');
    }
}
