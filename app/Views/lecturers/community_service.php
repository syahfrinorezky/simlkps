<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Pengabdian Kepada Masyarakat (PkM) DTPS<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (empty($periods)): ?>
    <?= $this->include('components/no_periods') ?>
<?php else: ?>

<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">PkM DTPS</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 hidden sm:block">Jumlah Judul PkM Dosen Tetap Program Studi</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="<?= base_url('lecturers/community-service') ?>" class="flex gap-2">
                <select name="period_id" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary shadow-xs">
                    <?php foreach ($periods as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $period_id == $p['id'] ? 'selected' : '' ?>><?= format_periode($p['nama_periode'], $p['tahun_akademik']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Alert Success/Error -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-medium">
        <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm font-medium">
        <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- EDITABLE SUMMARY TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Input Jumlah Judul PkM DTPS</h3>
        </div>
        
        <form action="<?= base_url('lecturers/community-service/store') ?>" method="POST"
            x-data="{
                summary: {
                    pt_mandiri: { ts2: <?= $summary['pt_mandiri']['ts2'] ?? 0 ?>, ts1: <?= $summary['pt_mandiri']['ts1'] ?? 0 ?>, ts: <?= $summary['pt_mandiri']['ts'] ?? 0 ?> },
                    lembaga_dalam_negeri: { ts2: <?= $summary['lembaga_dalam_negeri']['ts2'] ?? 0 ?>, ts1: <?= $summary['lembaga_dalam_negeri']['ts1'] ?? 0 ?>, ts: <?= $summary['lembaga_dalam_negeri']['ts'] ?? 0 ?> },
                    lembaga_luar_negeri: { ts2: <?= $summary['lembaga_luar_negeri']['ts2'] ?? 0 ?>, ts1: <?= $summary['lembaga_luar_negeri']['ts1'] ?? 0 ?>, ts: <?= $summary['lembaga_luar_negeri']['ts'] ?? 0 ?> }
                }
            }">
            <?= csrf_field() ?>
            <input type="hidden" name="period_id" value="<?= $period_id ?>">

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-600 font-semibold">
                            <th class="p-4 w-12 text-center whitespace-nowrap">No</th>
                            <th class="p-4 whitespace-nowrap">Sumber Pembiayaan</th>
                            <th class="p-4 text-center border-l border-slate-200 w-32 whitespace-nowrap">TS-2</th>
                            <th class="p-4 text-center border-l border-slate-200 w-32 whitespace-nowrap">TS-1</th>
                            <th class="p-4 text-center border-l border-slate-200 w-32 whitespace-nowrap">TS</th>
                            <th class="p-4 text-center border-l border-slate-200 w-32 whitespace-nowrap">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <!-- PT Mandiri -->
                        <tr>
                            <td class="p-4 text-center text-slate-400">1</td>
                            <td class="p-4 font-medium">a) Perguruan Tinggi yang bersangkutan (Mandiri)</td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[pt_mandiri][ts2]" x-model.number="summary.pt_mandiri.ts2" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[pt_mandiri][ts1]" x-model.number="summary.pt_mandiri.ts1" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[pt_mandiri][ts]" x-model.number="summary.pt_mandiri.ts" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-200 bg-slate-50/70 text-center font-bold text-slate-800"
                                x-text="(summary.pt_mandiri.ts2 || 0) + (summary.pt_mandiri.ts1 || 0) + (summary.pt_mandiri.ts || 0)">
                            </td>
                        </tr>
                        <!-- Lembaga Dalam Negeri -->
                        <tr>
                            <td class="p-4 text-center text-slate-400">2</td>
                            <td class="p-4 font-medium">b) Lembaga dalam negeri (diluar perguruan tinggi)</td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[lembaga_dalam_negeri][ts2]" x-model.number="summary.lembaga_dalam_negeri.ts2" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[lembaga_dalam_negeri][ts1]" x-model.number="summary.lembaga_dalam_negeri.ts1" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[lembaga_dalam_negeri][ts]" x-model.number="summary.lembaga_dalam_negeri.ts" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-200 bg-slate-50/70 text-center font-bold text-slate-800"
                                x-text="(summary.lembaga_dalam_negeri.ts2 || 0) + (summary.lembaga_dalam_negeri.ts1 || 0) + (summary.lembaga_dalam_negeri.ts || 0)">
                            </td>
                        </tr>
                        <!-- Lembaga Luar Negeri -->
                        <tr>
                            <td class="p-4 text-center text-slate-400">3</td>
                            <td class="p-4 font-medium">c) Lembaga luar negeri</td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[lembaga_luar_negeri][ts2]" x-model.number="summary.lembaga_luar_negeri.ts2" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[lembaga_luar_negeri][ts1]" x-model.number="summary.lembaga_luar_negeri.ts1" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-100 bg-slate-50/30">
                                <input type="number" name="summary[lembaga_luar_negeri][ts]" x-model.number="summary.lembaga_luar_negeri.ts" min="0" required
                                    class="w-full px-3 py-1.5 bg-white border border-slate-250 rounded-xl text-center text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            </td>
                            <td class="p-3 border-l border-slate-200 bg-slate-50/70 text-center font-bold text-slate-800"
                                x-text="(summary.lembaga_luar_negeri.ts2 || 0) + (summary.lembaga_luar_negeri.ts1 || 0) + (summary.lembaga_luar_negeri.ts || 0)">
                            </td>
                        </tr>
                        
                        <!-- Jumlah Summary Row -->
                        <tr class="bg-slate-100/60 font-bold border-t-2 border-slate-200">
                            <td class="p-4 text-center" colspan="2">Jumlah</td>
                            <td class="p-4 text-center border-l border-slate-200 text-slate-800"
                                x-text="(summary.pt_mandiri.ts2 || 0) + (summary.lembaga_dalam_negeri.ts2 || 0) + (summary.lembaga_luar_negeri.ts2 || 0)">
                            </td>
                            <td class="p-4 text-center border-l border-slate-200 text-slate-800"
                                x-text="(summary.pt_mandiri.ts1 || 0) + (summary.lembaga_dalam_negeri.ts1 || 0) + (summary.lembaga_luar_negeri.ts1 || 0)">
                            </td>
                            <td class="p-4 text-center border-l border-slate-200 text-slate-800"
                                x-text="(summary.pt_mandiri.ts || 0) + (summary.lembaga_dalam_negeri.ts || 0) + (summary.lembaga_luar_negeri.ts || 0)">
                            </td>
                            <td class="p-4 text-center border-l border-slate-200 bg-primary/5 text-primary text-base font-extrabold"
                                x-text="(summary.pt_mandiri.ts2 || 0) + (summary.pt_mandiri.ts1 || 0) + (summary.pt_mandiri.ts || 0) + (summary.lembaga_dalam_negeri.ts2 || 0) + (summary.lembaga_dalam_negeri.ts1 || 0) + (summary.lembaga_dalam_negeri.ts || 0) + (summary.lembaga_luar_negeri.ts2 || 0) + (summary.lembaga_luar_negeri.ts1 || 0) + (summary.lembaga_luar_negeri.ts || 0)">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if (in_array(session()->get('userRole'), ['admin', 'prodi'])): ?>
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/95 text-white font-semibold rounded-xl shadow-md transition-all text-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>

</div>
<?php endif; // end no_periods guard ?>
<?= $this->endSection() ?>
