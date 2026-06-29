<?php

namespace App\Controllers;

use App\Models\PeriodModel;

class PeriodController extends BaseController
{
    private PeriodModel $periodModel;

    public function __construct()
    {
        $this->periodModel = new PeriodModel();
        helper(['form', 'url']);
    }

    private function checkAuth(): bool
    {
        $role = session()->get('userRole');
        return $role === 'admin';
    }

    private function denyAccess()
    {
        return redirect()->to('/login')->with('error', 'Akses ditolak.');
    }

    public function index()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $periods = $this->periodModel->orderBy('tahun_akademik', 'DESC')->findAll();

        return view('periods/index', [
            'title'   => 'Manajemen Periode Pelaporan',
            'periods' => $periods,
        ]);
    }

    public function store()
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $rules = [
            'nama_periode'   => 'required|min_length[3]|max_length[100]',
            'tahun_akademik' => 'required|regex_match[/^\d{4}\/\d{4}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tahunAkademik = $this->request->getPost('tahun_akademik');

        $existing = $this->periodModel->where('tahun_akademik', $tahunAkademik)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Tahun akademik tersebut sudah terdaftar.');
        }

        $data = [
            'nama_periode'   => $this->request->getPost('nama_periode'),
            'tahun_akademik' => $tahunAkademik,
        ];

        if ($this->periodModel->insert($data)) {
            return redirect()->to('/periods')->with('success', 'Periode pelaporan berhasil ditambahkan.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data.');
    }

    public function update(int $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $rules = [
            'nama_periode'   => 'required|min_length[3]|max_length[100]',
            'tahun_akademik' => 'required|regex_match[/^\d{4}\/\d{4}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tahunAkademik = $this->request->getPost('tahun_akademik');

        // Check if academic year already exists in another record
        $existing = $this->periodModel->where('tahun_akademik', $tahunAkademik)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Tahun akademik tersebut sudah terdaftar pada periode lain.');
        }

        $data = [
            'nama_periode'   => $this->request->getPost('nama_periode'),
            'tahun_akademik' => $tahunAkademik,
        ];

        if ($this->periodModel->update($id, $data)) {
            return redirect()->to('/periods')->with('success', 'Periode pelaporan berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
    }

    public function delete(int $id)
    {
        if (!$this->checkAuth()) return $this->denyAccess();

        $period = $this->periodModel->find($id);
        if (!$period) {
            return redirect()->to('/periods')->with('error', 'Data tidak ditemukan.');
        }

        if ($this->periodModel->delete($id)) {
            return redirect()->to('/periods')->with('success', 'Periode pelaporan berhasil dihapus.');
        }

        return redirect()->to('/periods')->with('error', 'Gagal menghapus data.');
    }
}
