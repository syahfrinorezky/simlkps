<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{ 
    detailOpen: false, 
    detailData: {},
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Penggunaan Dana',
    formAction: '',
    id: '',
    periodId: '<?= $selectedPeriod ?>',
    kategori: '',
    jenisPenggunaan: '',
    uppsTs2: 0,
    uppsTs1: 0,
    uppsTs: 0,
    psTs2: 0,
    psTs1: 0,
    psTs: 0,
    currentPage: 0,
    perPage: 10,
    totalRows: 0,
    get totalPages() { return Math.max(1, Math.ceil(this.totalRows / this.perPage)); },
    get startRow() { return this.currentPage * this.perPage; },
    get endRow() { return this.startRow + this.perPage; },
    isRowVisible(idx) { return idx >= this.startRow && idx < this.endRow; },
    prevPage() { if (this.currentPage > 0) this.currentPage--; },
    nextPage() { if (this.currentPage < this.totalPages - 1) this.currentPage++; },

    confirmDelete(id) {
        const deleteUrl = '<?= base_url('funds/delete') ?>/' + id;
        this.$dispatch('open-delete-modal', {
            url: deleteUrl,
            title: 'Hapus Penggunaan Dana',
            message: 'Apakah Anda yakin ingin menghapus data penggunaan dana ini?'
        });
    },
    openAdd() {
        this.modalTitle = 'Tambah Penggunaan Dana';
        this.formAction = '<?= base_url('funds/store') ?>';
        this.periodId = '<?= $selectedPeriod ?>';
        this.kategori = '';
        this.jenisPenggunaan = '';
        this.uppsTs2 = 0;
        this.uppsTs1 = 0;
        this.uppsTs = 0;
        this.psTs2 = 0;
        this.psTs1 = 0;
        this.psTs = 0;
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Penggunaan Dana';
        this.formAction = '<?= base_url('funds/update') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;
        
        fetch('<?= base_url('funds/show') ?>/' + id)
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data.');
                return res.json();
            })
            .then(data => {
                this.periodId = data.period_id;
                this.kategori = data.kategori;
                this.jenisPenggunaan = data.jenis_penggunaan;
                this.uppsTs2 = data.upps_ts_2;
                this.uppsTs1 = data.upps_ts_1;
                this.uppsTs = data.upps_ts;
                this.psTs2 = data.ps_ts_2;
                this.psTs1 = data.ps_ts_1;
                this.psTs = data.ps_ts;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: err.message });
            });
    }
}" x-init="totalRows = <?= count($funds) ?>">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800"><?= esc($title) ?></h2>
            <p class="text-sm text-slate-500">Kelola data penggunaan dana Unit Pengelola Program Studi dan Program Studi.</p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($canModify) : ?>
                <button @click="openAdd()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Data
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-sm text-emerald-600 flex items-center gap-3">
            <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0 text-emerald-500"></i>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-sm text-red-600 flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-red-500"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="<?= base_url('funds') ?>" method="get" class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <div class="w-full sm:w-60">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Periode Pelaporan</label>
                <div class="relative">
                    <select name="period_id" onchange="this.form.submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                        <option value="">-- Pilih Periode --</option>
                        <?php foreach ($periods as $p) : ?>
                            <option value="<?= $p['id'] ?>" <?= $selectedPeriod == $p['id'] ? 'selected' : '' ?>>
                                <?= format_periode($p['nama_periode'], $p['tahun_akademik']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div class="w-full sm:flex-1">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Cari Data</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari berdasarkan jenis penggunaan atau kategori..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <?php if (empty($funds)) : ?>
            <div class="p-12 text-center flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                    <i data-lucide="folder-open" class="w-8 h-8"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                    <p class="text-sm text-slate-500 max-w-sm">Data penggunaan dana untuk kriteria yang dicari tidak ditemukan.</p>
                </div>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-center">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12" rowspan="2">No</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-left min-w-[250px]" rowspan="2">Jenis Penggunaan</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200" colspan="4">UPPS (Rupiah)</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200" colspan="4">Program Studi (Rupiah)</th>
                            <?php if ($canModify) : ?>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-28" rowspan="2">Aksi</th>
                            <?php endif; ?>
                        </tr>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 text-center">
                            <th class="p-2 border-l border-slate-200">TS-2 (<?= $years['ts2'] ?>)</th>
                            <th class="p-2">TS-1 (<?= $years['ts1'] ?>)</th>
                            <th class="p-2">TS (<?= $years['ts'] ?>)</th>
                            <th class="p-2 bg-indigo-50/50 text-primary">Rata-rata</th>
                            <th class="p-2 border-l border-slate-200">TS-2 (<?= $years['ts2'] ?>)</th>
                            <th class="p-2">TS-1 (<?= $years['ts1'] ?>)</th>
                            <th class="p-2">TS (<?= $years['ts'] ?>)</th>
                            <th class="p-2 bg-indigo-50/50 text-primary">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $no = 0; foreach ($funds as $f) : ?>
                            <tr class="hover:bg-slate-50/50 transition-all text-sm" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                <td class="p-4 text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-800"><?= esc($f['jenis_penggunaan']) ?></div>
                                </td>
                                <td class="p-4 text-right border-l border-slate-100"><?= number_format($f['upps_ts_2'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right"><?= number_format($f['upps_ts_1'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right"><?= number_format($f['upps_ts'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right font-bold text-primary bg-indigo-50/20"><?= number_format($f['upps_rata_rata'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right border-l border-slate-200"><?= number_format($f['ps_ts_2'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right"><?= number_format($f['ps_ts_1'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right"><?= number_format($f['ps_ts'], 0, ',', '.') ?></td>
                                <td class="p-4 text-right font-bold text-primary bg-indigo-50/20"><?= number_format($f['ps_rata_rata'], 0, ',', '.') ?></td>
                                <?php if ($canModify) : ?>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button @click="openEdit('<?= $f['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="confirmDelete('<?= $f['id'] ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php $no++; endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-800" x-text="startRow + 1"></span> sampai 
                    <span class="font-semibold text-slate-800" x-text="Math.min(endRow, totalRows)"></span> dari 
                    <span class="font-semibold text-slate-800" x-text="totalRows"></span> data
                </div>
                <div class="flex gap-2">
                    <button @click="prevPage()" :disabled="currentPage === 0" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Prev
                    </button>
                    <button @click="nextPage()" :disabled="currentPage >= totalPages - 1" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Next
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Form (Add / Edit) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen" x-cloak>
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" @click="modalOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-10">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-lg" x-text="modalTitle">Tambah Penggunaan Dana</h3>
                <button @click="modalOpen = false" class="p-2 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-full transition-all cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form :action="formAction" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="period_id" :value="periodId">
                
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-slate-600 font-bold">Jenis Penggunaan *</label>
                    <input type="text" name="jenis_penggunaan" x-model="jenisPenggunaan" required placeholder="Jenis Penggunaan..." class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                </div>

                <hr class="border-slate-100 my-2">
                <h4 class="font-bold text-slate-800 text-sm">Unit Pengelola Program Studi (Rupiah)</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">TS-2</label>
                        <input type="number" step="0.01" name="upps_ts_2" x-model="uppsTs2" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">TS-1</label>
                        <input type="number" step="0.01" name="upps_ts_1" x-model="uppsTs1" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">TS</label>
                        <input type="number" step="0.01" name="upps_ts" x-model="uppsTs" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <hr class="border-slate-100 my-2">
                <h4 class="font-bold text-slate-800 text-sm">Program Studi (Rupiah)</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">TS-2</label>
                        <input type="number" step="0.01" name="ps_ts_2" x-model="psTs2" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">TS-1</label>
                        <input type="number" step="0.01" name="ps_ts_1" x-model="psTs1" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">TS</label>
                        <input type="number" step="0.01" name="ps_ts" x-model="psTs" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 mt-4">
                    <button type="button" @click="modalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-xl text-sm shadow-sm transition-all cursor-pointer">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
