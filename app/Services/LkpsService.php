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

        // If no explicit period_id from URL, always default to current academic year
        if (empty($periodId) && !empty($periods)) {
            $currentYear  = (int) date('Y');
            $currentMonth = (int) date('m');
            $expectedTahunAkademik = $currentMonth >= 7
                ? $currentYear . '/' . ($currentYear + 1)
                : ($currentYear - 1) . '/' . $currentYear;

            foreach ($periods as $p) {
                if ($p['tahun_akademik'] === $expectedTahunAkademik) {
                    $periodId = $p['id'];
                    break;
                }
            }

            // Fallback: first period in list if current year not found in DB
            if (empty($periodId)) {
                $periodId = $periods[0]['id'];
            }
        }

        // Store explicit selection in session for cross-tab consistency
        if (!empty($periodId)) {
            session()->set('active_period_id', (int) $periodId);
        }

        // Return null (not 0) when no periods exist to prevent FK constraint errors
        $activePeriodId = !empty($periodId) ? (int) $periodId : null;

        return ['periods' => $periods, 'activePeriodId' => $activePeriodId];
    }

    public function getYears(?int $periodId, array $periods): array
    {
        $activePeriod = $periodId
            ? (array_values(array_filter($periods, fn($p) => $p['id'] == $periodId))[0] ?? null)
            : null;
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
