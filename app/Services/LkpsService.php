<?php

namespace App\Services;

use App\Models\PeriodModel;

class LkpsService
{
    private PeriodModel $periodModel;

    public function __construct()
    {
        $this->periodModel = new PeriodModel();
    }

    public function resolveActivePeriod(?string $periodId): array
    {
        $periods = $this->periodModel->orderBy('tahun_akademik', 'DESC')->findAll();

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

        return ['periods' => $periods, 'activePeriodId' => (int) $periodId];
    }

    public function getYears(int $periodId, array $periods): array
    {
        $activePeriod = array_values(array_filter($periods, fn($p) => $p['id'] == $periodId))[0] ?? null;
        $tsYear = (int) date('Y');
        if ($activePeriod) {
            $tsYear = (int) substr($activePeriod['tahun_akademik'], 0, 4);
        }

        return [
            'ts'  => $tsYear,
            'ts1' => $tsYear - 1,
            'ts2' => $tsYear - 2,
        ];
    }

    public function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
