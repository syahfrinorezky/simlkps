<?php

namespace App\Controllers;

use App\Models\StudentResearchModel;
use App\Models\ThesisReferenceModel;
use App\Models\PeriodModel;
use App\Services\LkpsService;

class ResearchCollaborationController extends BaseController
{
    protected StudentResearchModel $studentResearchModel;
    protected ThesisReferenceModel $thesisReferenceModel;
    protected PeriodModel $periodModel;
    protected LkpsService $lkpsService;

    public function __construct()
    {
        $this->studentResearchModel = new StudentResearchModel();
        $this->thesisReferenceModel = new ThesisReferenceModel();
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
    // TABEL 6.a — Penelitian Melibatkan Mahasiswa
    // ==========================================
    public function index()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $activeData = $this->lkpsService->resolveActivePeriod($periodId);
        $periodId = $activeData['activePeriodId'];
        $periods = $activeData['periods'];

        $query = $this->studentResearchModel->where('period_id', $periodId);
        if (!empty($search)) {
            $query->groupStart()
                ->like('nama_dosen', $search)
                ->orLike('nama_mahasiswa', $search)
                ->orLike('judul_kegiatan', $search)
                ->orLike('tema_roadmap', $search)
                ->groupEnd();
        }

        $researches = $query->orderBy('tahun', 'DESC')->findAll();

        return view('researches/collaboration', [
            'title' => 'Penelitian Kolaborasi Mahasiswa',
            'researches' => $researches,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function storeCollaboration()
    {
        if (!$this->canModify()) {
            return redirect()->to('/researches/collaboration')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'nama_dosen' => 'required|min_length[3]|max_length[255]',
            'tema_roadmap' => 'required',
            'nama_mahasiswa' => 'required|min_length[3]|max_length[255]',
            'judul_kegiatan' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->studentResearchModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'tema_roadmap' => $this->request->getPost('tema_roadmap'),
            'nama_mahasiswa' => $this->request->getPost('nama_mahasiswa'),
            'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/researches/collaboration?period_id=' . $this->request->getPost('period_id'))->with('success', 'Data kolaborasi penelitian berhasil ditambahkan.');
    }

    public function showCollaboration(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->studentResearchModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function updateCollaboration(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/researches/collaboration')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama_dosen' => 'required|min_length[3]|max_length[255]',
            'tema_roadmap' => 'required',
            'nama_mahasiswa' => 'required|min_length[3]|max_length[255]',
            'judul_kegiatan' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $collab = $this->studentResearchModel->find($id);

        $this->studentResearchModel->update($id, [
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'tema_roadmap' => $this->request->getPost('tema_roadmap'),
            'nama_mahasiswa' => $this->request->getPost('nama_mahasiswa'),
            'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/researches/collaboration?period_id=' . $collab['period_id'])->with('success', 'Data kolaborasi penelitian berhasil diperbarui.');
    }

    public function deleteCollaboration(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/researches/collaboration')->with('error', 'Akses ditolak.');
        }

        $collab = $this->studentResearchModel->find($id);
        if ($collab) {
            $this->studentResearchModel->delete($id);
            return redirect()->to('/researches/collaboration?period_id=' . $collab['period_id'])->with('success', 'Data kolaborasi penelitian berhasil dihapus.');
        }

        return redirect()->to('/researches/collaboration')->with('error', 'Data tidak ditemukan.');
    }


    // ==========================================
    // TABEL 6.b — Rujukan Tesis/Disertasi
    // ==========================================
    public function references()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $activeData = $this->lkpsService->resolveActivePeriod($periodId);
        $periodId = $activeData['activePeriodId'];
        $periods = $activeData['periods'];

        $query = $this->thesisReferenceModel->where('period_id', $periodId);
        if (!empty($search)) {
            $query->groupStart()
                ->like('nama_dosen', $search)
                ->orLike('nama_mahasiswa', $search)
                ->orLike('judul_tesis_disertasi', $search)
                ->orLike('tema_roadmap', $search)
                ->groupEnd();
        }

        $references = $query->orderBy('tahun', 'DESC')->findAll();

        return view('researches/references', [
            'title' => 'Rujukan Penelitian Tesis/Disertasi',
            'references' => $references,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function storeReference()
    {
        if (!$this->canModify()) {
            return redirect()->to('/researches/references')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'nama_dosen' => 'required|min_length[3]|max_length[255]',
            'tema_roadmap' => 'required',
            'nama_mahasiswa' => 'required|min_length[3]|max_length[255]',
            'judul_tesis_disertasi' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->thesisReferenceModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'tema_roadmap' => $this->request->getPost('tema_roadmap'),
            'nama_mahasiswa' => $this->request->getPost('nama_mahasiswa'),
            'judul_tesis_disertasi' => $this->request->getPost('judul_tesis_disertasi'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/researches/references?period_id=' . $this->request->getPost('period_id'))->with('success', 'Data rujukan tesis berhasil ditambahkan.');
    }

    public function showReference(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->thesisReferenceModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function updateReference(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/researches/references')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama_dosen' => 'required|min_length[3]|max_length[255]',
            'tema_roadmap' => 'required',
            'nama_mahasiswa' => 'required|min_length[3]|max_length[255]',
            'judul_tesis_disertasi' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ref = $this->thesisReferenceModel->find($id);

        $this->thesisReferenceModel->update($id, [
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'tema_roadmap' => $this->request->getPost('tema_roadmap'),
            'nama_mahasiswa' => $this->request->getPost('nama_mahasiswa'),
            'judul_tesis_disertasi' => $this->request->getPost('judul_tesis_disertasi'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/researches/references?period_id=' . $ref['period_id'])->with('success', 'Data rujukan tesis berhasil diperbarui.');
    }

    public function deleteReference(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/researches/references')->with('error', 'Akses ditolak.');
        }

        $ref = $this->thesisReferenceModel->find($id);
        if ($ref) {
            $this->thesisReferenceModel->delete($id);
            return redirect()->to('/researches/references?period_id=' . $ref['period_id'])->with('success', 'Data rujukan tesis berhasil dihapus.');
        }

        return redirect()->to('/researches/references')->with('error', 'Data tidak ditemukan.');
    }
}
