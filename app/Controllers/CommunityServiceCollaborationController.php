<?php

namespace App\Controllers;

use App\Models\StudentCommunityServiceModel;
use App\Models\PeriodModel;
use App\Services\LkpsService;

class CommunityServiceCollaborationController extends BaseController
{
    protected StudentCommunityServiceModel $communityServiceModel;
    protected PeriodModel $periodModel;
    protected LkpsService $lkpsService;

    public function __construct()
    {
        $this->communityServiceModel = new StudentCommunityServiceModel();
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

        $query = $this->communityServiceModel->where('period_id', $periodId);
        if (!empty($search)) {
            $query->groupStart()
                ->like('nama_dosen', $search)
                ->orLike('nama_mahasiswa', $search)
                ->orLike('judul_kegiatan', $search)
                ->orLike('tema_roadmap', $search)
                ->groupEnd();
        }

        $collaborations = $query->orderBy('tahun', 'DESC')->findAll();

        return view('community_services/collaboration', [
            'title' => 'PkM Kolaborasi Mahasiswa',
            'collaborations' => $collaborations,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function storeCollaboration()
    {
        if (!$this->canModify()) {
            return redirect()->to('/community-services/collaboration')->with('error', 'Akses ditolak.');
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

        $this->communityServiceModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'tema_roadmap' => $this->request->getPost('tema_roadmap'),
            'nama_mahasiswa' => $this->request->getPost('nama_mahasiswa'),
            'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/community-services/collaboration?period_id=' . $this->request->getPost('period_id'))->with('success', 'Data kolaborasi PkM berhasil ditambahkan.');
    }

    public function showCollaboration(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->communityServiceModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function updateCollaboration(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/community-services/collaboration')->with('error', 'Akses ditolak.');
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

        $collab = $this->communityServiceModel->find($id);

        $this->communityServiceModel->update($id, [
            'nama_dosen' => $this->request->getPost('nama_dosen'),
            'tema_roadmap' => $this->request->getPost('tema_roadmap'),
            'nama_mahasiswa' => $this->request->getPost('nama_mahasiswa'),
            'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
            'tahun' => $this->request->getPost('tahun'),
        ]);

        return redirect()->to('/community-services/collaboration?period_id=' . $collab['period_id'])->with('success', 'Data kolaborasi PkM berhasil diperbarui.');
    }

    public function deleteCollaboration(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/community-services/collaboration')->with('error', 'Akses ditolak.');
        }

        $collab = $this->communityServiceModel->find($id);
        if ($collab) {
            $this->communityServiceModel->delete($id);
            return redirect()->to('/community-services/collaboration?period_id=' . $collab['period_id'])->with('success', 'Data kolaborasi PkM berhasil dihapus.');
        }

        return redirect()->to('/community-services/collaboration')->with('error', 'Data tidak ditemukan.');
    }
}
