<?php

namespace App\Controllers;

use App\Models\FundUsageModel;
use App\Models\PeriodModel;
use App\Services\LkpsService;

class FundController extends BaseController
{
    protected FundUsageModel $fundUsageModel;
    protected PeriodModel $periodModel;
    protected LkpsService $lkpsService;

    public function __construct()
    {
        $this->fundUsageModel = new FundUsageModel();
        $this->periodModel = new PeriodModel();
        $this->lkpsService = new LkpsService();
        helper(['form', 'url']);
    }

    private function checkAuth(): bool
    {
        $role = session()->get('userRole');
        return in_array($role, ['admin', 'prodi', 'asesor']);
    }

    private function canModify(): bool
    {
        $role = session()->get('userRole');
        return in_array($role, ['admin', 'prodi']);
    }

    private function initRows(int $periodId)
    {
        $count = $this->fundUsageModel->where('period_id', $periodId)->countAllResults();
        if ($count === 0) {
            $predefined = [
                ['kategori' => 'Biaya Operasional Pendidikan', 'jenis_penggunaan' => 'Biaya Dosen (Gaji, Honor)'],
                ['kategori' => 'Biaya Operasional Pendidikan', 'jenis_penggunaan' => 'Biaya Tenaga Kependidikan (Gaji, Honor)'],
                ['kategori' => 'Biaya Operasional Pendidikan', 'jenis_penggunaan' => 'Biaya Operasional Pembelajaran (Bahan dan Peralatan Habis Pakai)'],
                ['kategori' => 'Biaya Operasional Pendidikan', 'jenis_penggunaan' => 'Biaya Operasional Tidak Langsung'],
                ['kategori' => 'Biaya operasional kemahasiswaan', 'jenis_penggunaan' => 'Biaya operasional kemahasiswaan'],
                ['kategori' => 'Biaya Penelitian', 'jenis_penggunaan' => 'Biaya Penelitian'],
                ['kategori' => 'Biaya PkM', 'jenis_penggunaan' => 'Biaya PkM'],
                ['kategori' => 'Biaya Investasi SDM', 'jenis_penggunaan' => 'Biaya Investasi SDM'],
                ['kategori' => 'Biaya Investasi Sarana', 'jenis_penggunaan' => 'Biaya Investasi Sarana'],
                ['kategori' => 'Biaya Investasi Prasarana', 'jenis_penggunaan' => 'Biaya Investasi Prasarana'],
            ];
            foreach ($predefined as $p) {
                $this->fundUsageModel->insert([
                    'id' => $this->lkpsService->generateUuid(),
                    'period_id' => $periodId,
                    'kategori' => $p['kategori'],
                    'jenis_penggunaan' => $p['jenis_penggunaan'],
                    'upps_ts_2' => 0.00,
                    'upps_ts_1' => 0.00,
                    'upps_ts' => 0.00,
                    'upps_rata_rata' => 0.00,
                    'ps_ts_2' => 0.00,
                    'ps_ts_1' => 0.00,
                    'ps_ts' => 0.00,
                    'ps_rata_rata' => 0.00,
                ]);
            }
        }
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

        // Auto init rows if empty
        $this->initRows($periodId);

        $years = $this->lkpsService->getYears($periodId, $periods);

        $query = $this->fundUsageModel->where('period_id', $periodId);
        if (!empty($search)) {
            $query->groupStart()
                ->like('jenis_penggunaan', $search)
                ->orLike('kategori', $search)
                ->groupEnd();
        }

        $funds = $query->orderBy('kategori', 'ASC')->orderBy('jenis_penggunaan', 'ASC')->findAll();

        return view('funds/index', [
            'title' => 'Penggunaan Dana',
            'funds' => $funds,
            'periods' => $periods,
            'selectedPeriod' => $periodId,
            'years' => $years,
            'search' => $search,
            'canModify' => $this->canModify(),
        ]);
    }

    public function store()
    {
        if (!$this->canModify()) {
            return redirect()->to('/funds')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'period_id' => 'required',
            'kategori' => 'required|min_length[3]|max_length[100]',
            'jenis_penggunaan' => 'required|min_length[3]|max_length[255]',
            'upps_ts_2' => 'required|numeric|greater_than_equal_to[0]',
            'upps_ts_1' => 'required|numeric|greater_than_equal_to[0]',
            'upps_ts' => 'required|numeric|greater_than_equal_to[0]',
            'ps_ts_2' => 'required|numeric|greater_than_equal_to[0]',
            'ps_ts_1' => 'required|numeric|greater_than_equal_to[0]',
            'ps_ts' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $upps_ts_2 = (float) $this->request->getPost('upps_ts_2');
        $upps_ts_1 = (float) $this->request->getPost('upps_ts_1');
        $upps_ts = (float) $this->request->getPost('upps_ts');
        $upps_rata_rata = ($upps_ts_2 + $upps_ts_1 + $upps_ts) / 3;

        $ps_ts_2 = (float) $this->request->getPost('ps_ts_2');
        $ps_ts_1 = (float) $this->request->getPost('ps_ts_1');
        $ps_ts = (float) $this->request->getPost('ps_ts');
        $ps_rata_rata = ($ps_ts_2 + $ps_ts_1 + $ps_ts) / 3;

        $this->fundUsageModel->insert([
            'id' => $this->lkpsService->generateUuid(),
            'period_id' => $this->request->getPost('period_id'),
            'kategori' => $this->request->getPost('kategori'),
            'jenis_penggunaan' => $this->request->getPost('jenis_penggunaan'),
            'upps_ts_2' => $upps_ts_2,
            'upps_ts_1' => $upps_ts_1,
            'upps_ts' => $upps_ts,
            'upps_rata_rata' => $upps_rata_rata,
            'ps_ts_2' => $ps_ts_2,
            'ps_ts_1' => $ps_ts_1,
            'ps_ts' => $ps_ts,
            'ps_rata_rata' => $ps_rata_rata,
        ]);

        return redirect()->to('/funds?period_id=' . $this->request->getPost('period_id'))->with('success', 'Data penggunaan dana berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['error' => 'Akses ditolak.'], 403);
        }
        $data = $this->fundUsageModel->find($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
        }
        return $this->response->setJSON($data);
    }

    public function update(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/funds')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'kategori' => 'required|min_length[3]|max_length[100]',
            'jenis_penggunaan' => 'required|min_length[3]|max_length[255]',
            'upps_ts_2' => 'required|numeric|greater_than_equal_to[0]',
            'upps_ts_1' => 'required|numeric|greater_than_equal_to[0]',
            'upps_ts' => 'required|numeric|greater_than_equal_to[0]',
            'ps_ts_2' => 'required|numeric|greater_than_equal_to[0]',
            'ps_ts_1' => 'required|numeric|greater_than_equal_to[0]',
            'ps_ts' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $upps_ts_2 = (float) $this->request->getPost('upps_ts_2');
        $upps_ts_1 = (float) $this->request->getPost('upps_ts_1');
        $upps_ts = (float) $this->request->getPost('upps_ts');
        $upps_rata_rata = ($upps_ts_2 + $upps_ts_1 + $upps_ts) / 3;

        $ps_ts_2 = (float) $this->request->getPost('ps_ts_2');
        $ps_ts_1 = (float) $this->request->getPost('ps_ts_1');
        $ps_ts = (float) $this->request->getPost('ps_ts');
        $ps_rata_rata = ($ps_ts_2 + $ps_ts_1 + $ps_ts) / 3;

        $this->fundUsageModel->update($id, [
            'kategori' => $this->request->getPost('kategori'),
            'jenis_penggunaan' => $this->request->getPost('jenis_penggunaan'),
            'upps_ts_2' => $upps_ts_2,
            'upps_ts_1' => $upps_ts_1,
            'upps_ts' => $upps_ts,
            'upps_rata_rata' => $upps_rata_rata,
            'ps_ts_2' => $ps_ts_2,
            'ps_ts_1' => $ps_ts_1,
            'ps_ts' => $ps_ts,
            'ps_rata_rata' => $ps_rata_rata,
        ]);

        $fund = $this->fundUsageModel->find($id);

        return redirect()->to('/funds?period_id=' . $fund['period_id'])->with('success', 'Data penggunaan dana berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        if (!$this->canModify()) {
            return redirect()->to('/funds')->with('error', 'Akses ditolak.');
        }

        $fund = $this->fundUsageModel->find($id);
        if ($fund) {
            $this->fundUsageModel->delete($id);
            return redirect()->to('/funds?period_id=' . $fund['period_id'])->with('success', 'Data penggunaan dana berhasil dihapus.');
        }

        return redirect()->to('/funds')->with('error', 'Data tidak ditemukan.');
    }
}
