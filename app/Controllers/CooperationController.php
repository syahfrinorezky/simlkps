<?php

namespace App\Controllers;

use App\Models\CooperationModel;
use App\Models\PartnerModel;
use App\Models\PeriodModel;

class CooperationController extends BaseController
{
    protected $cooperationModel;
    protected $partnerModel;
    protected $periodModel;

    public function __construct()
    {
        $this->cooperationModel = new CooperationModel();
        $this->partnerModel = new PartnerModel();
        $this->periodModel = new PeriodModel();
        helper(['form', 'url']);
    }

    private function checkAuth()
    {
        $role = session()->get('userRole');
        if (!$role || $role !== 'admin') {
            return false;
        }
        return true;
    }

    private function getRedirectPath($jenis)
    {
        switch ($jenis) {
            case 'penelitian':
                return 'cooperations/research';
            case 'pengabdian':
                return 'cooperations/community';
            case 'pendidikan':
            default:
                return 'cooperations/education';
        }
    }

    public function index()
    {
        return $this->education();
    }

    public function education()
    {
        return $this->showCooperations('pendidikan', 'Kerja Sama Pendidikan');
    }

    public function research()
    {
        return $this->showCooperations('penelitian', 'Kerja Sama Penelitian');
    }

    public function community()
    {
        return $this->showCooperations('pengabdian', 'Kerja Sama Pengabdian Masyarakat');
    }

    private function showCooperations($jenis, $title)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $periodId = $this->request->getVar('period_id');
        $search = $this->request->getVar('search');

        $periods = $this->periodModel->findAll();
        
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

        $cooperations = $this->cooperationModel->getCooperations($jenis, $periodId, $search)->findAll();

        return view('cooperations/index', [
            'title' => $title,
            'cooperations' => $cooperations,
            'periods' => $periods,
            'partners' => $this->partnerModel->findAll(),
            'selectedPeriod' => $periodId,
            'selectedJenis' => $jenis,
            'search' => $search
        ]);
    }

    public function create()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $jenis = $this->request->getVar('jenis') ?? 'pendidikan';
        return redirect()->to(base_url($this->getRedirectPath($jenis)));
    }

    public function store()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'jenis_kerjasama' => 'required|in_list[pendidikan,penelitian,pengabdian]',
            'tingkat' => 'required|in_list[lokal,nasional,internasional]',
            'judul_kerjasama' => 'required|min_length[3]|max_length[255]',
            'manfaat' => 'required',
            'tanggal_mulai' => 'required|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'waktu_durasi' => 'required|max_length[100]',
            'bukti_kerjasama' => 'uploaded[bukti_kerjasama]|max_size[bukti_kerjasama,5120]|ext_in[bukti_kerjasama,pdf]',
            'tahun_berakhir' => 'required|numeric'
        ];

        $partnerId = $this->request->getPost('partner_id');
        $newPartnerName = $this->request->getPost('new_partner_name');

        if (empty($partnerId) && empty($newPartnerName)) {
            $rules['partner_id'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (empty($partnerId) && !empty($newPartnerName)) {
            $partnerId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $this->partnerModel->insert([
                'id' => $partnerId,
                'nama_mitra' => $newPartnerName,
                'jenis_mitra' => $this->request->getPost('new_partner_type') ?: 'Industri',
                'negara' => $this->request->getPost('new_partner_country') ?: 'Indonesia',
                'alamat' => $this->request->getPost('new_partner_address') ?: '',
                'kontak' => $this->request->getPost('new_partner_contact') ?: '',
            ]);
        }

        $buktiKerjasama = '';
        $file = $this->request->getFile('bukti_kerjasama');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(FCPATH . 'uploads/cooperations')) {
                mkdir(FCPATH . 'uploads/cooperations', 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/cooperations', $newName);
            $buktiKerjasama = $newName;
        }

        $id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

        $this->cooperationModel->insert([
            'id' => $id,
            'period_id' => $this->request->getPost('period_id'),
            'partner_id' => $partnerId,
            'jenis_kerjasama' => $this->request->getPost('jenis_kerjasama'),
            'tingkat' => $this->request->getPost('tingkat'),
            'judul_kerjasama' => $this->request->getPost('judul_kerjasama'),
            'manfaat' => $this->request->getPost('manfaat'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'bukti_kerjasama' => $buktiKerjasama,
            'waktu_durasi' => $this->request->getPost('waktu_durasi'),
            'tahun_berakhir' => $this->request->getPost('tahun_berakhir')
        ]);

        $jenis = $this->request->getPost('jenis_kerjasama');
        return redirect()->to(base_url($this->getRedirectPath($jenis) . '?period_id=' . $this->request->getPost('period_id')))->with('success', 'Data kerja sama berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $cooperation = $this->cooperationModel->find($id);
        if (!$cooperation) {
            return redirect()->to('/cooperations')->with('error', 'Data tidak ditemukan.');
        }

        return redirect()->to(base_url($this->getRedirectPath($cooperation['jenis_kerjasama']) . '?period_id=' . $cooperation['period_id']));
    }

    public function update($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $cooperation = $this->cooperationModel->find($id);
        if (!$cooperation) {
            return redirect()->to('/cooperations')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'period_id' => 'required',
            'jenis_kerjasama' => 'required|in_list[pendidikan,penelitian,pengabdian]',
            'tingkat' => 'required|in_list[lokal,nasional,internasional]',
            'judul_kerjasama' => 'required|min_length[3]|max_length[255]',
            'manfaat' => 'required',
            'tanggal_mulai' => 'required|valid_date[Y-m-d]',
            'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
            'waktu_durasi' => 'required|max_length[100]',
            'bukti_kerjasama' => 'permit_empty|max_size[bukti_kerjasama,5120]|ext_in[bukti_kerjasama,pdf]',
            'tahun_berakhir' => 'required|numeric'
        ];

        $partnerId = $this->request->getPost('partner_id');
        $newPartnerName = $this->request->getPost('new_partner_name');

        if (empty($partnerId) && empty($newPartnerName)) {
            $rules['partner_id'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (empty($partnerId) && !empty($newPartnerName)) {
            $partnerId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $this->partnerModel->insert([
                'id' => $partnerId,
                'nama_mitra' => $newPartnerName,
                'jenis_mitra' => $this->request->getPost('new_partner_type') ?: 'Industri',
                'negara' => $this->request->getPost('new_partner_country') ?: 'Indonesia',
                'alamat' => $this->request->getPost('new_partner_address') ?: '',
                'kontak' => $this->request->getPost('new_partner_contact') ?: '',
            ]);
        }

        $buktiKerjasama = $cooperation['bukti_kerjasama'];
        $file = $this->request->getFile('bukti_kerjasama');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(FCPATH . 'uploads/cooperations')) {
                mkdir(FCPATH . 'uploads/cooperations', 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/cooperations', $newName);
            
            if (!empty($cooperation['bukti_kerjasama']) && file_exists(FCPATH . 'uploads/cooperations/' . $cooperation['bukti_kerjasama'])) {
                @unlink(FCPATH . 'uploads/cooperations/' . $cooperation['bukti_kerjasama']);
            }
            $buktiKerjasama = $newName;
        }

        $this->cooperationModel->update($id, [
            'period_id' => $this->request->getPost('period_id'),
            'partner_id' => $partnerId,
            'jenis_kerjasama' => $this->request->getPost('jenis_kerjasama'),
            'tingkat' => $this->request->getPost('tingkat'),
            'judul_kerjasama' => $this->request->getPost('judul_kerjasama'),
            'manfaat' => $this->request->getPost('manfaat'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'bukti_kerjasama' => $buktiKerjasama,
            'waktu_durasi' => $this->request->getPost('waktu_durasi'),
            'tahun_berakhir' => $this->request->getPost('tahun_berakhir')
        ]);

        $jenis = $this->request->getPost('jenis_kerjasama');
        return redirect()->to(base_url($this->getRedirectPath($jenis) . '?period_id=' . $this->request->getPost('period_id')))->with('success', 'Data kerja sama berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $cooperation = $this->cooperationModel->find($id);
        if (!$cooperation) {
            return redirect()->to('/cooperations')->with('error', 'Data tidak ditemukan.');
        }

        if (!empty($cooperation['bukti_kerjasama']) && file_exists(FCPATH . 'uploads/cooperations/' . $cooperation['bukti_kerjasama'])) {
            @unlink(FCPATH . 'uploads/cooperations/' . $cooperation['bukti_kerjasama']);
        }

        $this->cooperationModel->delete($id);

        return redirect()->to(base_url($this->getRedirectPath($cooperation['jenis_kerjasama']) . '?period_id=' . $cooperation['period_id']))->with('success', 'Data kerja sama berhasil dihapus.');
    }

    public function download($filename)
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.');
        }

        $filepath = FCPATH . 'uploads/cooperations/' . $filename;
        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return $this->response->download($filepath, null);
    }

    public function detail($id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $cooperation = $this->cooperationModel
            ->select('cooperations.*, partners.nama_mitra, partners.jenis_mitra, partners.negara, partners.alamat, partners.kontak, reporting_periods.nama_periode, reporting_periods.tahun_akademik')
            ->join('partners', 'partners.id = cooperations.partner_id')
            ->join('reporting_periods', 'reporting_periods.id = cooperations.period_id')
            ->find($id);

        if (!$cooperation) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'])->setStatusCode(404);
        }

        return $this->response->setJSON($cooperation);
    }
}
