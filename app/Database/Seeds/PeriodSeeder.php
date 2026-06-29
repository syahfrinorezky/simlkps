<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PeriodSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        $startYear = 2020;
        $endYear = 2030;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $nextYear = $year + 1;
            $tahunAkademik = "{$year}/{$nextYear}";
            $namaPeriode = "Periode {$tahunAkademik}";

            $existing = $db->table('reporting_periods')->where('tahun_akademik', $tahunAkademik)->get()->getRow();
            if (!$existing) {
                $db->table('reporting_periods')->insert([
                    'nama_periode'   => $namaPeriode,
                    'tahun_akademik' => $tahunAkademik,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
