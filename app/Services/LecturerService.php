<?php

namespace App\Services;

use App\Models\PeriodModel;

class LecturerService
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

    public function buildFilters(array $request): array
    {
        return array_filter([
            'search'           => $request['search'] ?? '',
            'jabatan_akademik' => $request['jabatan_akademik'] ?? '',
            'tingkat'          => $request['tingkat'] ?? '',
            'sumber_dana'      => $request['sumber_dana'] ?? '',
            'jenis_publikasi'  => $request['jenis_publikasi'] ?? '',
            'kategori'         => $request['kategori'] ?? '',
            'semester'         => $request['semester'] ?? '',
            'tahun'            => $request['tahun'] ?? '',
            'is_dtps'          => $request['is_dtps'] ?? '',
            'status_komersialisasi' => $request['status_komersialisasi'] ?? '',
        ], fn($v) => $v !== '');
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

    public function uploadFile(array $file, string $subDir = 'documents'): ?string
    {
        if (!$file || $file['error'] !== 0) {
            return null;
        }

        $uploadPath = ROOTPATH . 'public/uploads/' . $subDir;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $this->generateUuid() . '.' . $ext;

        move_uploaded_file($file['tmp_name'], $uploadPath . '/' . $fileName);

        return 'uploads/' . $subDir . '/' . $fileName;
    }

    public function deleteFile(?string $path): void
    {
        if ($path && file_exists(ROOTPATH . 'public/' . $path)) {
            unlink(ROOTPATH . 'public/' . $path);
        }
    }
}
