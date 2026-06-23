<?php

namespace App\Models;

use CodeIgniter\Model;

class CooperationModel extends Model
{
    protected $table = 'cooperations';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'period_id',
        'partner_id',
        'tingkat',
        'judul_kerjasama',
        'manfaat',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_kerjasama',
        'bukti_kerjasama',
        'waktu_durasi',
        'tahun_berakhir'
    ];
    protected $useTimestamps = true;

    public function getCooperations($jenis = null, $periodId = null, $search = null)
    {
        $builder = $this->select('cooperations.*, partners.nama_mitra, partners.jenis_mitra, partners.negara, reporting_periods.nama_periode, reporting_periods.tahun_akademik')
            ->join('partners', 'partners.id = cooperations.partner_id')
            ->join('reporting_periods', 'reporting_periods.id = cooperations.period_id');

        if ($jenis) {
            $builder->where('cooperations.jenis_kerjasama', $jenis);
        }

        if ($periodId) {
            $builder->where('cooperations.period_id', $periodId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('partners.nama_mitra', $search)
                ->orLike('cooperations.judul_kerjasama', $search)
                ->orLike('cooperations.manfaat', $search)
                ->orLike('cooperations.bukti_kerjasama', $search)
                ->groupEnd();
        }

        return $builder->orderBy('cooperations.created_at', 'DESC');
    }
}
