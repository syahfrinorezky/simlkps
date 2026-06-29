<?php

namespace App\Controllers;

use App\Models\LecturerModel;
use App\Models\ExternalLecturerModel;
use App\Models\IndustrialLecturerModel;
use App\Models\ThesisSupervisionModel;
use App\Models\LecturerWorkloadModel;
use App\Models\LecturerRecognitionModel;
use App\Models\ResearchModel;
use App\Models\CommunityServiceModel;
use App\Models\PublicationModel;
use App\Models\CitationModel;
use App\Models\ProductModel;
use App\Models\IntellectualPropertyModel;
use App\Services\LecturerService;
use CodeIgniter\API\ResponseTrait;

class LecturerController extends BaseController
{
    use ResponseTrait;

    private LecturerService $service;
    private LecturerModel $lecturerModel;
    private ExternalLecturerModel $externalModel;
    private IndustrialLecturerModel $industrialModel;
    private ThesisSupervisionModel $supervisionModel;
    private LecturerWorkloadModel $workloadModel;
    private LecturerRecognitionModel $recognitionModel;
    private ResearchModel $researchModel;
    private CommunityServiceModel $pkmModel;
    private PublicationModel $publicationModel;
    private CitationModel $citationModel;
    private ProductModel $productModel;
    private IntellectualPropertyModel $ipModel;

    public function __construct()
    {
        $this->service          = new LecturerService();
        $this->lecturerModel    = new LecturerModel();
        $this->externalModel    = new ExternalLecturerModel();
        $this->industrialModel  = new IndustrialLecturerModel();
        $this->supervisionModel = new ThesisSupervisionModel();
        $this->workloadModel    = new LecturerWorkloadModel();
        $this->recognitionModel = new LecturerRecognitionModel();
        $this->researchModel    = new ResearchModel();
        $this->pkmModel         = new CommunityServiceModel();
        $this->publicationModel = new PublicationModel();
        $this->citationModel    = new CitationModel();
        $this->productModel     = new ProductModel();
        $this->ipModel          = new IntellectualPropertyModel();
        helper(['form', 'url']);
    }

    private function checkAuth(array $allowedRoles = ['admin', 'prodi', 'dosen']): bool
    {
        return in_array(session()->get('userRole'), $allowedRoles);
    }

    private function denyAccess()
    {
        return redirect()->to('/login')->with('error', 'Akses ditolak.');
    }

    // =========================================================
    // PROFIL — Dosen Tetap (3.a.1)
    // =========================================================

    public function permanent()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters  = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats    = $this->lecturerModel->getStats();
        $query    = $this->lecturerModel->getPermanent($filters);
        $lecturers = $query->paginate(15);
        $pager    = $this->lecturerModel->pager;

        return view('lecturers/permanent', [
            'title'     => 'Dosen Tetap',
            'periods'   => $periods,
            'period_id' => $periodId,
            'filters'   => $filters,
            'stats'     => $stats,
            'lecturers' => $lecturers,
            'pager'     => $pager,
        ]);
    }

    public function storePermanent()
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $data = array_merge(
            $this->request->getPost(),
            ['id' => $this->service->generateUuid(), 'status_dosen' => 'tetap']
        );

        if (!$this->lecturerModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data dosen.')->withInput();
        }

        return redirect()->to('lecturers/permanent')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function updatePermanent(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $data = $this->request->getPost();
        unset($data['id']);

        if (!$this->lecturerModel->update($id, $data)) {
            return redirect()->back()->with('error', 'Gagal memperbarui data dosen.')->withInput();
        }

        return redirect()->to('lecturers/permanent')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function deletePermanent(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->lecturerModel->delete($id);

        return redirect()->to('lecturers/permanent')->with('success', 'Dosen berhasil dihapus.');
    }

    public function showPermanent(string $id)
    {
        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return $this->failNotFound('Data tidak ditemukan.');
        }
        return $this->respond($lecturer);
    }

    // =========================================================
    // PROFIL — Pembimbing Tugas Akhir (3.a.2)
    // =========================================================

    public function supervisor()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters     = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats       = $this->supervisionModel->getStats((int) $periodId);
        $supervisions = $this->supervisionModel->getWithLecturer((int) $periodId, $filters)->paginate(15);
        $pager       = $this->supervisionModel->pager;
        $allLecturers = $this->lecturerModel->where('status_dosen', 'tetap')->orderBy('nama')->findAll();

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

        return view('lecturers/supervisor', [
            'title'        => 'Pembimbing Tugas Akhir',
            'periods'      => $periods,
            'period_id'    => $periodId,
            'filters'      => $filters,
            'stats'        => $stats,
            'supervisions' => $supervisions,
            'pager'        => $pager,
            'allLecturers' => $allLecturers,
            'years'        => $years,
        ]);
    }

    public function storeSupervisor()
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen Pembimbing wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
        ]);
        unset($data['lecturer_name']);

        if (!$this->supervisionModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data bimbingan.')->withInput();
        }

        $periodId = $post['period_id'] ?? '';
        return redirect()->to('lecturers/supervisor' . ($periodId ? '?period_id=' . $periodId : ''))->with('success', 'Bimbingan berhasil disimpan.');
    }

    public function updateSupervisor(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen Pembimbing wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = $post;
        $data['lecturer_id'] = $lecId;
        unset($data['id'], $data['lecturer_name']);
        
        $this->supervisionModel->update($id, $data);

        $periodId = $post['period_id'] ?? '';
        return redirect()->to('lecturers/supervisor' . ($periodId ? '?period_id=' . $periodId : ''))->with('success', 'Bimbingan berhasil diperbarui.');
    }

    public function deleteSupervisor(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $supervision = $this->supervisionModel->find($id);
        $periodId = $supervision ? $supervision['period_id'] : '';
        $this->supervisionModel->delete($id);

        return redirect()->to('lecturers/supervisor' . ($periodId ? '?period_id=' . $periodId : ''))->with('success', 'Data berhasil dihapus.');
    }

    public function showSupervisor(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->supervisionModel->select('thesis_supervisions.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = thesis_supervisions.lecturer_id')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // PROFIL — Dosen Tidak Tetap (3.a.4)
    // =========================================================

    public function nonPermanent()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters  = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats    = $this->externalModel->getStats((int) $periodId);
        $lecturers = $this->externalModel->getByPeriod((int) $periodId, $filters)->paginate(15);
        $pager    = $this->externalModel->pager;

        return view('lecturers/non_permanent', [
            'title'     => 'Dosen Tidak Tetap',
            'periods'   => $periods,
            'period_id' => $periodId,
            'filters'   => $filters,
            'stats'     => $stats,
            'lecturers' => $lecturers,
            'pager'     => $pager,
        ]);
    }

    public function storeNonPermanent()
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $data = array_merge($this->request->getPost(), ['id' => $this->service->generateUuid()]);

        if (!$this->externalModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.')->withInput();
        }

        return redirect()->to('lecturers/non-permanent')->with('success', 'Data berhasil disimpan.');
    }

    public function updateNonPermanent(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $data = $this->request->getPost();
        unset($data['id']);
        $this->externalModel->update($id, $data);

        return redirect()->to('lecturers/non-permanent')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteNonPermanent(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->externalModel->delete($id);

        return redirect()->to('lecturers/non-permanent')->with('success', 'Data berhasil dihapus.');
    }

    public function showNonPermanent(string $id)
    {
        $data = $this->externalModel->find($id);
        if (!$data) return $this->failNotFound('Data tidak ditemukan.');
        return $this->respond($data);
    }

    // =========================================================
    // PROFIL — Dosen Industri (3.a.5)
    // =========================================================

    public function industry()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters  = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats    = $this->industrialModel->getStats((int) $periodId);
        $lecturers = $this->industrialModel->getByPeriod((int) $periodId, $filters)->paginate(15);
        $pager    = $this->industrialModel->pager;

        return view('lecturers/industry', [
            'title'     => 'Dosen Industri',
            'periods'   => $periods,
            'period_id' => $periodId,
            'filters'   => $filters,
            'stats'     => $stats,
            'lecturers' => $lecturers,
            'pager'     => $pager,
        ]);
    }

    public function storeIndustry()
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $data = array_merge($this->request->getPost(), ['id' => $this->service->generateUuid()]);

        if (!$this->industrialModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.')->withInput();
        }

        return redirect()->to('lecturers/industry')->with('success', 'Data berhasil disimpan.');
    }

    public function updateIndustry(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $data = $this->request->getPost();
        unset($data['id']);
        $this->industrialModel->update($id, $data);

        return redirect()->to('lecturers/industry')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteIndustry(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->industrialModel->delete($id);

        return redirect()->to('lecturers/industry')->with('success', 'Data berhasil dihapus.');
    }

    public function showIndustry(string $id)
    {
        $data = $this->industrialModel->find($id);
        if (!$data) return $this->failNotFound('Data tidak ditemukan.');
        return $this->respond($data);
    }

    // =========================================================
    // BEBAN KERJA — EWMP (3.a.3)
    // =========================================================

    public function workload()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters      = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats        = $this->workloadModel->getStats((int) $periodId);
        $workloads    = $this->workloadModel->getWithLecturer((int) $periodId, $filters)->paginate(15);
        $pager        = $this->workloadModel->pager;
        $allLecturers = $this->lecturerModel->where('status_dosen', 'tetap')->orderBy('nama')->findAll();

        return view('lecturers/workload', [
            'title'        => 'Ekivalen Waktu Mengajar Penuh (EWMP)',
            'periods'      => $periods,
            'period_id'    => $periodId,
            'filters'      => $filters,
            'stats'        => $stats,
            'workloads'    => $workloads,
            'pager'        => $pager,
            'allLecturers' => $allLecturers,
        ]);
    }

    public function storeWorkload()
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Checkbox is_dtps
        $isDtps = isset($post['is_dtps']) ? 1 : 0;

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => $isDtps
            ]);
        } else {
            $lecId = $lec['id'];
            // Sync status DTPS
            $this->lecturerModel->update($lecId, ['is_dtps' => $isDtps]);
        }

        $total = ($post['sks_pengajaran'] ?? 0) + ($post['sks_ps_lain_dalam_pt'] ?? 0) + ($post['sks_ps_luar_pt'] ?? 0)
               + ($post['sks_penelitian'] ?? 0) + ($post['sks_pkm'] ?? 0) + ($post['sks_penunjang'] ?? 0);

        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
            'total_sks' => $total,
        ]);
        unset($data['lecturer_name'], $data['is_dtps']);

        if (!$this->workloadModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data EWMP.')->withInput();
        }

        $periodId = $post['period_id'] ?? '';
        return redirect()->to('lecturers/workload' . ($periodId ? '?period_id=' . $periodId : ''))->with('success', 'Data EWMP berhasil disimpan.');
    }

    public function updateWorkload(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $isDtps = isset($post['is_dtps']) ? 1 : 0;

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => $isDtps
            ]);
        } else {
            $lecId = $lec['id'];
            $this->lecturerModel->update($lecId, ['is_dtps' => $isDtps]);
        }

        $total = ($post['sks_pengajaran'] ?? 0) + ($post['sks_ps_lain_dalam_pt'] ?? 0) + ($post['sks_ps_luar_pt'] ?? 0)
               + ($post['sks_penelitian'] ?? 0) + ($post['sks_pkm'] ?? 0) + ($post['sks_penunjang'] ?? 0);

        $data = $post;
        $data['lecturer_id'] = $lecId;
        $data['total_sks'] = $total;
        unset($data['id'], $data['lecturer_name'], $data['is_dtps']);
        
        $this->workloadModel->update($id, $data);

        $periodId = $post['period_id'] ?? '';
        return redirect()->to('lecturers/workload' . ($periodId ? '?period_id=' . $periodId : ''))->with('success', 'Data EWMP berhasil diperbarui.');
    }

    public function deleteWorkload(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $workload = $this->workloadModel->find($id);
        $periodId = $workload ? $workload['period_id'] : '';
        $this->workloadModel->delete($id);

        return redirect()->to('lecturers/workload' . ($periodId ? '?period_id=' . $periodId : ''))->with('success', 'Data berhasil dihapus.');
    }

    public function showWorkload(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->workloadModel->select('lecturer_workloads.*, lecturers.nama, lecturers.is_dtps')
            ->join('lecturers', 'lecturers.id = lecturer_workloads.lecturer_id')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // REKOGNISI — Pengakuan Dosen (3.b.1)
    // =========================================================

    public function recognition()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters      = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats        = $this->recognitionModel->getStats((int) $periodId);
        $recognitions = $this->recognitionModel->getWithLecturer((int) $periodId, $filters)->paginate(15);
        $pager        = $this->recognitionModel->pager;
        $allLecturers = $this->lecturerModel->where('status_dosen', 'tetap')->orderBy('nama')->findAll();

        return view('lecturers/recognition', [
            'title'        => 'Pengakuan/Rekognisi Dosen',
            'periods'      => $periods,
            'period_id'    => $periodId,
            'filters'      => $filters,
            'stats'        => $stats,
            'recognitions' => $recognitions,
            'pager'        => $pager,
            'allLecturers' => $allLecturers,
        ]);
    }

    public function storeRecognition()
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        if ($post['tingkat'] === 'wilayah') {
            $post['tingkat'] = 'lokal';
        }

        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
            'penyelenggara' => '', // Removed field
        ]);
        unset($data['lecturer_name']);

        if (!$this->recognitionModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.')->withInput();
        }

        return redirect()->to('lecturers/recognition')->with('success', 'Rekognisi berhasil disimpan.');
    }

    public function updateRecognition(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        if ($post['tingkat'] === 'wilayah') {
            $post['tingkat'] = 'lokal';
        }

        $data = $post;
        $data['lecturer_id'] = $lecId;
        $data['penyelenggara'] = '';
        unset($data['id'], $data['lecturer_name']);

        $this->recognitionModel->update($id, $data);

        return redirect()->to('lecturers/recognition')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteRecognition(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->recognitionModel->delete($id);

        return redirect()->to('lecturers/recognition')->with('success', 'Data berhasil dihapus.');
    }

    public function showRecognition(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->recognitionModel->select('lecturer_recognitions.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = lecturer_recognitions.lecturer_id')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // KINERJA — Penelitian DTPS (3.b.2)
    // =========================================================

    public function researchPerformance()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        // Find active period details to get the academic year (e.g. 2024/2025)
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

        // Dynamic summary based on funding source and TS years
        $summary = [
            'pt_mandiri' => [
                'ts2' => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->where('tahun', $tsYear)->countAllResults(),
            ],
            'lembaga_dalam_negeri' => [
                'ts2' => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_dalam_negeri')->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_dalam_negeri')->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_dalam_negeri')->where('tahun', $tsYear)->countAllResults(),
            ],
            'lembaga_luar_negeri' => [
                'ts2' => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->researchModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->where('tahun', $tsYear)->countAllResults(),
            ],
        ];

        $filters    = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats      = $this->researchModel->getStats((int) $periodId);
        $researches = $this->researchModel->select('researches.*, GROUP_CONCAT(lecturers.nama SEPARATOR ", ") as nama')
            ->join('research_members', 'research_members.research_id = researches.id', 'left')
            ->join('lecturers', 'lecturers.id = research_members.lecturer_id', 'left')
            ->where('researches.period_id', $periodId)
            ->groupBy('researches.id');

        if (!empty($filters['search'])) {
            $researches->groupStart()
                ->like('researches.judul_penelitian', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }
        $researchesData = $researches->orderBy('researches.tahun', 'DESC')->paginate(15);

        return view('lecturers/research', [
            'title'     => 'Penelitian DTPS',
            'periods'   => $periods,
            'period_id' => $periodId,
            'filters'   => $filters,
            'stats'     => $stats,
            'years'     => $years,
            'summary'   => $summary,
            'researches' => $researchesData,
            'pager'     => $this->researchModel->pager,
        ]);
    }

    public function storeResearch()
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $researchId = $this->service->generateUuid();
        $data = array_merge($post, [
            'id' => $researchId,
        ]);
        unset($data['lecturer_name']);

        if (!$this->researchModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data penelitian.')->withInput();
        }

        // Add lecturer to research_members
        $db = \Config\Database::connect();
        $db->table('research_members')->insert([
            'id' => $this->service->generateUuid(),
            'research_id' => $researchId,
            'lecturer_id' => $lecId,
            'peran' => 'ketua',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('lecturers/research-performance')->with('success', 'Data penelitian berhasil disimpan.');
    }

    public function updateResearch(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = $post;
        unset($data['id'], $data['lecturer_name']);
        
        $this->researchModel->update($id, $data);

        // Update member relation
        $db = \Config\Database::connect();
        $db->table('research_members')->where('research_id', $id)->delete();
        $db->table('research_members')->insert([
            'id' => $this->service->generateUuid(),
            'research_id' => $id,
            'lecturer_id' => $lecId,
            'peran' => 'ketua',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('lecturers/research-performance')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteResearch(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $db = \Config\Database::connect();
        $db->table('research_members')->where('research_id', $id)->delete();
        $this->researchModel->delete($id);

        return redirect()->to('lecturers/research-performance')->with('success', 'Data berhasil dihapus.');
    }

    public function showResearch(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->researchModel->select('researches.*, lecturers.nama')
            ->join('research_members', 'research_members.research_id = researches.id', 'left')
            ->join('lecturers', 'lecturers.id = research_members.lecturer_id', 'left')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // KINERJA — PkM DTPS (3.b.3)
    // =========================================================

    public function communityService()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        // Find active period details to get the academic year (e.g. 2024/2025)
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

        // Dynamic summary based on funding source and TS years for PkM
        $summary = [
            'pt_mandiri' => [
                'ts2' => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->where('tahun', $tsYear)->countAllResults(),
            ],
            'lembaga_dalam_negeri' => [
                'ts2' => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_dalam_negeri')->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_dalam_negeri')->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_dalam_negeri')->where('tahun', $tsYear)->countAllResults(),
            ],
            'lembaga_luar_negeri' => [
                'ts2' => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->pkmModel->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->where('tahun', $tsYear)->countAllResults(),
            ],
        ];

        $filters  = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats    = $this->pkmModel->getStats((int) $periodId);
        
        $services = $this->pkmModel->select('community_services.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = community_services.lecturer_id', 'left')
            ->where('community_services.period_id', $periodId);

        if (!empty($filters['search'])) {
            $services->groupStart()
                ->like('community_services.judul_kegiatan', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }
        $servicesData = $services->orderBy('community_services.tahun', 'DESC')->paginate(15);

        return view('lecturers/community_service', [
            'title'     => 'Pengabdian kepada Masyarakat (PkM)',
            'periods'   => $periods,
            'period_id' => $periodId,
            'filters'   => $filters,
            'stats'     => $stats,
            'years'     => $years,
            'summary'   => $summary,
            'services'  => $servicesData,
            'pager'     => $this->pkmModel->pager,
        ]);
    }

    public function storePkm()
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $pkmId = $this->service->generateUuid();
        $data = array_merge($post, [
            'id' => $pkmId,
            'lecturer_id' => $lecId,
        ]);
        unset($data['lecturer_name']);

        if (!$this->pkmModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data PkM.')->withInput();
        }

        return redirect()->to('lecturers/community-service')->with('success', 'Data PkM berhasil disimpan.');
    }

    public function updatePkm(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = $post;
        $data['lecturer_id'] = $lecId;
        unset($data['id'], $data['lecturer_name']);
        
        $this->pkmModel->update($id, $data);

        return redirect()->to('lecturers/community-service')->with('success', 'Data berhasil diperbarui.');
    }

    public function deletePkm(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->pkmModel->delete($id);

        return redirect()->to('lecturers/community-service')->with('success', 'Data berhasil dihapus.');
    }

    public function showPkm(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->pkmModel->select('community_services.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = community_services.lecturer_id', 'left')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // KINERJA — Publikasi Ilmiah (3.b.4)
    // =========================================================

    public function publications()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

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

        $jenisLabels = PublicationModel::KATEGORI_LABELS;

        // Dynamic summary based on Kategori Publikasi and TS years
        $summary = [];
        foreach ($jenisLabels as $key => $label) {
            $summary[$key] = [
                'ts2' => $this->publicationModel->where('period_id', $periodId)->where('kategori_publikasi', $key)->where('tahun', $tsYear - 2)->countAllResults(),
                'ts1' => $this->publicationModel->where('period_id', $periodId)->where('kategori_publikasi', $key)->where('tahun', $tsYear - 1)->countAllResults(),
                'ts'  => $this->publicationModel->where('period_id', $periodId)->where('kategori_publikasi', $key)->where('tahun', $tsYear)->countAllResults(),
            ];
        }

        $filters      = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats        = $this->publicationModel->getStats((int) $periodId);
        
        $publicationsQuery = $this->publicationModel->select('publications.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = publications.lecturer_id', 'left')
            ->where('publications.period_id', $periodId);

        if (!empty($filters['search'])) {
            $publicationsQuery->groupStart()
                ->like('publications.judul', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['kategori_publikasi'])) {
            $publicationsQuery->where('publications.kategori_publikasi', $filters['kategori_publikasi']);
        }

        $publications = $publicationsQuery->orderBy('publications.tahun', 'DESC')->paginate(15);

        return view('lecturers/publications', [
            'title'        => 'Publikasi Ilmiah DTPS',
            'periods'      => $periods,
            'period_id'    => $periodId,
            'filters'      => $filters,
            'stats'        => $stats,
            'years'        => $years,
            'summary'      => $summary,
            'publications' => $publications,
            'pager'        => $this->publicationModel->pager,
            'jenisLabels'  => $jenisLabels,
        ]);
    }

    public function storePublication()
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $post['jenis_publikasi'] = $post['kategori_publikasi'] ?? '';
        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
        ]);
        unset($data['lecturer_name']);

        if (!$this->publicationModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data publikasi.')->withInput();
        }

        return redirect()->to('lecturers/publications/scientific')->with('success', 'Publikasi berhasil disimpan.');
    }

    public function updatePublication(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $post['jenis_publikasi'] = $post['kategori_publikasi'] ?? '';
        $data = $post;
        $data['lecturer_id'] = $lecId;
        unset($data['id'], $data['lecturer_name']);

        $this->publicationModel->update($id, $data);

        return redirect()->to('lecturers/publications/scientific')->with('success', 'Data berhasil diperbarui.');
    }

    public function deletePublication(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->publicationModel->delete($id);

        return redirect()->to('lecturers/publications/scientific')->with('success', 'Data berhasil dihapus.');
    }

    public function showPublication(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->publicationModel->select('publications.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = publications.lecturer_id', 'left')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // KINERJA — Sitasi (3.b.5)
    // =========================================================

    public function citations()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters      = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats        = $this->citationModel->getStats((int) $periodId);
        $citations    = $this->citationModel->getWithLecturer((int) $periodId, $filters)->paginate(15);
        $pager        = $this->citationModel->pager;
        $allLecturers = $this->lecturerModel->where('status_dosen', 'tetap')->orderBy('nama')->findAll();

        return view('lecturers/citations', [
            'title'        => 'Sitasi',
            'periods'      => $periods,
            'period_id'    => $periodId,
            'filters'      => $filters,
            'stats'        => $stats,
            'citations'    => $citations,
            'pager'        => $pager,
        ]);
    }

    public function storeCitation()
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
            'sumber_sitasi' => '',
        ]);
        unset($data['lecturer_name']);

        if (!$this->citationModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data sitasi.')->withInput();
        }

        return redirect()->to('lecturers/publications/creative-works')->with('success', 'Data sitasi berhasil disimpan.');
    }

    public function updateCitation(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = $post;
        $data['lecturer_id'] = $lecId;
        $data['sumber_sitasi'] = '';
        unset($data['id'], $data['lecturer_name']);

        $this->citationModel->update($id, $data);

        return redirect()->to('lecturers/publications/creative-works')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteCitation(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->citationModel->delete($id);

        return redirect()->to('lecturers/publications/creative-works')->with('success', 'Data berhasil dihapus.');
    }

    public function showCitation(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->citationModel->select('lecturer_citations.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = lecturer_citations.lecturer_id', 'left')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // KINERJA — Produk/Jasa (3.b.6)
    public function products()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters      = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats        = $this->productModel->getStats((int) $periodId);
        $products     = $this->productModel->getWithLecturer((int) $periodId, $filters)->paginate(15);
        $pager        = $this->productModel->pager;
        $allLecturers = $this->lecturerModel->where('status_dosen', 'tetap')->orderBy('nama')->findAll();

        return view('lecturers/products', [
            'title'        => 'Produk/Jasa DTPS',
            'periods'      => $periods,
            'period_id'    => $periodId,
            'filters'      => $filters,
            'stats'        => $stats,
            'products'     => $products,
            'pager'        => $pager,
            'allLecturers' => $allLecturers,
        ]);
    }

    public function storeProduct()
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        // Find or create lecturer
        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
            'nama_industri' => '',
            'bukti_adopsi' => '',
            'status_komersialisasi' => 0,
        ]);
        unset($data['lecturer_name']);

        if (!$this->productModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data produk.')->withInput();
        }

        return redirect()->to('lecturers/hki/industry-products')->with('success', 'Data berhasil disimpan.');
    }

    public function updateProduct(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $name = trim($post['lecturer_name'] ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama Dosen wajib diisi.')->withInput();
        }

        $lec = $this->lecturerModel->where('nama', $name)->first();
        if (!$lec) {
            $lecId = $this->service->generateUuid();
            $this->lecturerModel->insert([
                'id' => $lecId,
                'nama' => $name,
                'status_dosen' => 'tetap',
                'is_dtps' => 1
            ]);
        } else {
            $lecId = $lec['id'];
        }

        $data = $post;
        $data['lecturer_id'] = $lecId;
        $data['nama_industri'] = '';
        $data['bukti_adopsi'] = '';
        $data['status_komersialisasi'] = 0;
        unset($data['id'], $data['lecturer_name']);

        $this->productModel->update($id, $data);

        return redirect()->to('lecturers/hki/industry-products')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteProduct(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->productModel->delete($id);

        return redirect()->to('lecturers/hki/industry-products')->with('success', 'Data berhasil dihapus.');
    }

    public function showProduct(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->productModel->select('lecturer_products.*, lecturers.nama')
            ->join('lecturers', 'lecturers.id = lecturer_products.lecturer_id', 'left')
            ->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }

    // =========================================================
    // KINERJA — Luaran Penelitian/PkM (3.b.7)
    // =========================================================

    public function outputs()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        ['periods' => $periods, 'activePeriodId' => $periodId] = $this->service->resolveActivePeriod(
            $this->request->getVar('period_id')
        );

        $filters      = $this->service->buildFilters($this->request->getVar() ?? []);
        $stats        = $this->ipModel->getStats((int) $periodId);
        
        $outputsQuery = $this->ipModel->where('period_id', $periodId);
        if (!empty($filters['search'])) {
            $outputsQuery->like('judul_hki', $filters['search']);
        }
        if (!empty($filters['kategori'])) {
            $outputsQuery->where('kategori', $filters['kategori']);
        }
        $outputs = $outputsQuery->orderBy('tahun', 'DESC')->paginate(15);

        $kategoriLabels = IntellectualPropertyModel::KATEGORI_LABELS;

        return view('lecturers/outputs', [
            'title'          => 'Luaran Penelitian/PkM',
            'periods'        => $periods,
            'period_id'      => $periodId,
            'filters'        => $filters,
            'stats'          => $stats,
            'outputs'        => $outputs,
            'pager'          => $this->ipModel->pager,
            'kategoriLabels' => $kategoriLabels,
        ]);
    }

    public function storeOutput()
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        
        // Find default lecturer to satisfy foreign key constraints if needed
        $lec = $this->lecturerModel->first();
        $lecId = $lec ? $lec['id'] : $this->service->generateUuid();

        $data = array_merge($post, [
            'id' => $this->service->generateUuid(),
            'lecturer_id' => $lecId,
            'jenis_hki' => '',
            'nomor_pendaftaran' => '',
            'nomor_hki' => '',
            'penerbit' => '',
            'isbn' => '',
            'status' => 'terdaftar',
        ]);

        if (!$this->ipModel->insert($data)) {
            return redirect()->back()->with('error', 'Gagal menyimpan data luaran.')->withInput();
        }

        return redirect()->to('lecturers/outputs')->with('success', 'Data luaran berhasil disimpan.');
    }

    public function updateOutput(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi', 'dosen'])) return $this->denyAccess();

        $post = $this->request->getPost();
        $data = $post;
        unset($data['id']);
        
        $data['jenis_hki'] = '';
        $data['nomor_pendaftaran'] = '';
        $data['nomor_hki'] = '';
        $data['penerbit'] = '';
        $data['isbn'] = '';
        $data['status'] = 'terdaftar';

        $this->ipModel->update($id, $data);

        return redirect()->to('lecturers/outputs')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteOutput(string $id)
    {
        if (!$this->checkAuth(['admin', 'prodi'])) return $this->denyAccess();

        $this->ipModel->delete($id);

        return redirect()->to('lecturers/outputs')->with('success', 'Data berhasil dihapus.');
    }

    public function showOutput(string $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $data = $this->ipModel->find($id);
        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($data);
    }
}
