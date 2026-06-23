<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    activeTab: '<?= $tab ?>',

    modalOpen: false,
    modalTitle: '',
    formAction: '',
    
    periodId: '<?= $selectedPeriod ?>',
    studyProgramId: '',
    tahunAkademik: '<?= date('Y') . '/' . (date('Y') + 1) ?>',

    dayaTampung: 0,
    jumlahPendaftar: 0,
    jumlahLulusSeleksi: 0,
    mabaReguler: 0,
    mabaTransfer: 0,
    aktifReguler: 0,
    aktifTransfer: 0,

    aktifTs2: 0,
    aktifTs1: 0,
    aktifTs: 0,
    asingFullTs2: 0,
    asingFullTs1: 0,
    asingFullTs: 0,
    asingPartTs2: 0,
    asingPartTs1: 0,
    asingPartTs: 0,

    detailOpen: false,
    detailData: {},
    loading: false,

    importModalOpen: false,

    currentPage: 0,
    perPage: 5,
    totalRows: 0,
    get totalPages() { return Math.max(1, Math.ceil(this.totalRows / this.perPage)); },
    get startRow() { return this.currentPage * this.perPage; },
    get endRow() { return this.startRow + this.perPage; },
    isRowVisible(idx) { return idx >= this.startRow && idx < this.endRow; },
    prevPage() { if (this.currentPage > 0) this.currentPage--; },
    nextPage() { if (this.currentPage < this.totalPages - 1) this.currentPage++; },

    openAdd() {
        this.modalTitle = this.activeTab === 'admission' ? 'Tambah Data Seleksi Mahasiswa Baru' : 'Tambah Data Mahasiswa Asing';
        this.formAction = this.activeTab === 'admission' ? '<?= base_url('students/store-admission') ?>' : '<?= base_url('students/store-foreign') ?>';
        
        this.studyProgramId = '';
        this.tahunAkademik = '<?= date('Y') . '/' . (date('Y') + 1) ?>';
    
        this.dayaTampung = 0;
        this.jumlahPendaftar = 0;
        this.jumlahLulusSeleksi = 0;
        this.mabaReguler = 0;
        this.mabaTransfer = 0;
        this.aktifReguler = 0;
        this.aktifTransfer = 0;

        this.aktifTs2 = 0;
        this.aktifTs1 = 0;
        this.aktifTs = 0;
        this.asingFullTs2 = 0;
        this.asingFullTs1 = 0;
        this.asingFullTs = 0;
        this.asingPartTs2 = 0;
        this.asingPartTs1 = 0;
        this.asingPartTs = 0;

        this.modalOpen = true;
    },

    openEdit(id) {
        this.modalTitle = this.activeTab === 'admission' ? 'Edit Data Seleksi Mahasiswa Baru' : 'Edit Data Mahasiswa Asing';
        this.formAction = this.activeTab === 'admission' ? '<?= base_url('students/update-admission') ?>/' + id : '<?= base_url('students/update-foreign') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;

        const detailUrl = this.activeTab === 'admission' ? '<?= base_url('students/detail-admission') ?>/' + id : '<?= base_url('students/detail-foreign') ?>/' + id;

        fetch(detailUrl)
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data.');
                return res.json();
            })
            .then(data => {
                this.periodId = data.period_id;
                this.studyProgramId = data.study_program_id;
                this.tahunAkademik = data.tahun_akademik;

                if (this.activeTab === 'admission') {
                    this.dayaTampung = data.daya_tampung;
                    this.jumlahPendaftar = data.jumlah_pendaftar;
                    this.jumlahLulusSeleksi = data.jumlah_lulus_seleksi;
                    this.mabaReguler = data.mahasiswa_baru_reguler;
                    this.mabaTransfer = data.mahasiswa_baru_transfer;
                    this.aktifReguler = data.mahasiswa_aktif_reguler;
                    this.aktifTransfer = data.mahasiswa_aktif_transfer;
                } else {
                    this.aktifTs2 = data.mahasiswa_aktif_ts2;
                    this.aktifTs1 = data.mahasiswa_aktif_ts1;
                    this.aktifTs = data.mahasiswa_aktif_ts;
                    this.asingFullTs2 = data.mahasiswa_asing_full_ts2;
                    this.asingFullTs1 = data.mahasiswa_asing_full_ts1;
                    this.asingFullTs = data.mahasiswa_asing_full_ts;
                    this.asingPartTs2 = data.mahasiswa_asing_part_ts2;
                    this.asingPartTs1 = data.mahasiswa_asing_part_ts1;
                    this.asingPartTs = data.mahasiswa_asing_part_ts;
                }
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: err.message });
            });
    },

    openDetail(id) {
        this.loading = true;
        this.detailOpen = true;
        const detailUrl = this.activeTab === 'admission' ? '<?= base_url('students/detail-admission') ?>/' + id : '<?= base_url('students/detail-foreign') ?>/' + id;

        fetch(detailUrl)
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil detail.');
                return res.json();
            })
            .then(data => {
                this.detailData = data;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.detailOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: err.message });
            });
    },

    confirmDelete(id) {
        const deleteUrl = this.activeTab === 'admission' ? '<?= base_url('students/delete-admission') ?>/' + id : '<?= base_url('students/delete-foreign') ?>/' + id;
        this.$dispatch('open-delete-modal', {
            url: deleteUrl,
            title: 'Hapus Data Mahasiswa',
            message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.'
        });
    }
}" x-init="totalRows = <?= $tab === 'admission' ? count($admissions) : count($foreigns) ?>">

    <!-- Title and Action Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800"><?= $tab === 'admission' ? 'Seleksi Mahasiswa Baru' : 'Mahasiswa Asing' ?></h2>
            <p class="text-sm text-slate-500"><?= $tab === 'admission' ? 'Kelola data seleksi penerimaan mahasiswa baru program studi.' : 'Kelola data jumlah mahasiswa asing yang aktif atau terdaftar.' ?></p>
        </div>
        <div class="flex items-center flex-wrap gap-2">
            <!-- 
            <?php if ($tab === 'admission'): ?>
                <a href="<?= base_url('students/export-admission?period_id=' . $selectedPeriod . '&search=' . urlencode($search)) ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-xs transition-all text-sm cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export Excel
                </a>
            <?php endif; ?>
            -->

            <button @click="openAdd()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Data
            </button>
        </div>
    </div>

    <!-- Alert Success / Errors -->
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

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-sm text-red-600 space-y-2">
            <div class="font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-red-500"></i>
                <span>Terdapat kesalahan pengisian form:</span>
            </div>
            <ul class="list-disc pl-8 space-y-1 text-xs">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Widget Statistik -->
    <?php if ($tab === 'admission'): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                </div>
                <div>
                    <span class="text-[9px] sm:text-xs text-slate-400 block font-semibold uppercase tracking-wider leading-none mb-1">Pendaftar</span>
                    <span class="text-sm sm:text-lg font-bold text-slate-800 leading-none"><?= number_format($stats['total_pendaftar'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                </div>
                <div>
                    <span class="text-[9px] sm:text-xs text-slate-400 block font-semibold uppercase tracking-wider leading-none mb-1">Lulus Seleksi</span>
                    <span class="text-sm sm:text-lg font-bold text-slate-800 leading-none"><?= number_format($stats['total_lulus'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                </div>
                <div>
                    <span class="text-[9px] sm:text-xs text-slate-400 block font-semibold uppercase tracking-wider leading-none mb-1">Maba</span>
                    <span class="text-sm sm:text-lg font-bold text-slate-800 leading-none"><?= number_format($stats['total_maba'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i data-lucide="graduation-cap" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                </div>
                <div>
                    <span class="text-[9px] sm:text-xs text-slate-400 block font-semibold uppercase tracking-wider leading-none mb-1">Mhs Aktif</span>
                    <span class="text-sm sm:text-lg font-bold text-slate-800 leading-none"><?= number_format($stats['total_aktif'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3 col-span-2 sm:col-span-1">
                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <i data-lucide="percent" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                </div>
                <div>
                    <span class="text-[9px] sm:text-xs text-slate-400 block font-semibold uppercase tracking-wider leading-none mb-1">Keketatan</span>
                    <span class="text-sm sm:text-lg font-bold text-slate-800 leading-none"><?= $stats['rasio_keketatan'] ?? 0 ?>%</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="flex items-center gap-2 text-slate-700">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Mahasiswa Aktif</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center pt-1">
                    <div class="bg-slate-50 p-2 rounded-xl">
                        <span class="text-[9px] text-slate-400 block mb-0.5">TS-2</span>
                        <span class="text-sm font-bold text-slate-800"><?= number_format($stats['total_aktif_ts2'] ?? 0) ?></span>
                    </div>
                    <div class="bg-slate-50 p-2 rounded-xl">
                        <span class="text-[9px] text-slate-400 block mb-0.5">TS-1</span>
                        <span class="text-sm font-bold text-slate-800"><?= number_format($stats['total_aktif_ts1'] ?? 0) ?></span>
                    </div>
                    <div class="bg-slate-50 p-2 rounded-xl">
                        <span class="text-[9px] text-slate-400 block mb-0.5">TS</span>
                        <span class="text-sm font-bold text-slate-800"><?= number_format($stats['total_aktif_ts'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="flex items-center gap-2 text-indigo-600">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
                        <i data-lucide="globe" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Asing Full-Time</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center pt-1">
                    <div class="bg-indigo-50/50 p-2 rounded-xl">
                        <span class="text-[9px] text-indigo-400 block mb-0.5">TS-2</span>
                        <span class="text-sm font-bold text-indigo-700"><?= number_format($stats['total_asing_full_ts2'] ?? 0) ?></span>
                    </div>
                    <div class="bg-indigo-50/50 p-2 rounded-xl">
                        <span class="text-[9px] text-indigo-400 block mb-0.5">TS-1</span>
                        <span class="text-sm font-bold text-indigo-700"><?= number_format($stats['total_asing_full_ts1'] ?? 0) ?></span>
                    </div>
                    <div class="bg-indigo-50/50 p-2 rounded-xl">
                        <span class="text-[9px] text-indigo-400 block mb-0.5">TS</span>
                        <span class="text-sm font-bold text-indigo-700"><?= number_format($stats['total_asing_full_ts'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                <div class="flex items-center gap-2 text-emerald-600">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                        <i data-lucide="globe-2" class="w-4 h-4"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Asing Part-Time</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center pt-1">
                    <div class="bg-emerald-50/50 p-2 rounded-xl">
                        <span class="text-[9px] text-emerald-400 block mb-0.5">TS-2</span>
                        <span class="text-sm font-bold text-emerald-700"><?= number_format($stats['total_asing_part_ts2'] ?? 0) ?></span>
                    </div>
                    <div class="bg-emerald-50/50 p-2 rounded-xl">
                        <span class="text-[9px] text-emerald-400 block mb-0.5">TS-1</span>
                        <span class="text-sm font-bold text-emerald-700"><?= number_format($stats['total_asing_part_ts1'] ?? 0) ?></span>
                    </div>
                    <div class="bg-emerald-50/50 p-2 rounded-xl">
                        <span class="text-[9px] text-emerald-400 block mb-0.5">TS</span>
                        <span class="text-sm font-bold text-emerald-700"><?= number_format($stats['total_asing_part_ts'] ?? 0) ?></span>
                    </div>
    </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filter and Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="<?= base_url('students') ?>" method="get" class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <input type="hidden" name="tab" :value="activeTab">
            
            <div class="w-full sm:w-60">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Periode Pelaporan</label>
                <div class="relative">
                    <select name="period_id" onchange="this.form.submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                        <option value="">-- Pilih Periode --</option>
                        <?php foreach ($periods as $p) : ?>
                            <option value="<?= $p['id'] ?>" <?= $selectedPeriod == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['nama_periode']) ?> (<?= esc($p['tahun_akademik']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <div class="w-full sm:flex-1">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Cari Program Studi</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari berdasarkan program studi atau tahun..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
        </form>
    </div>

    <!-- Data Display Section -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        
        <!-- Tab Seleksi Mahasiswa Baru (2A) -->
        <template x-if="activeTab === 'admission'">
            <div>
                <?php if (empty($admissions)) : ?>
                    <div class="p-12 text-center flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                            <i data-lucide="folder-open" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                            <p class="text-sm text-slate-500 max-w-sm">Data seleksi mahasiswa tidak ditemukan.</p>
                        </div>
                    </div>
                <?php else : ?>
                    <div>
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Program Studi</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Tahun Akademik</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Daya Tampung</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Pendaftar</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Lulus</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Maba Reguler</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Maba Transfer</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Mhs Aktif Reguler</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Mhs Aktif Transfer</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php $no = 0; foreach ($admissions as $adm) : ?>
                                        <tr class="hover:bg-slate-50/50 transition-all" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                            <td class="p-4 text-sm text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                            <td class="p-4 font-semibold text-slate-800"><?= esc($adm['nama_prodi']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= esc($adm['tahun_akademik']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center font-medium"><?= number_format($adm['daya_tampung']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= number_format($adm['jumlah_pendaftar']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= number_format($adm['jumlah_lulus_seleksi']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= number_format($adm['mahasiswa_baru_reguler']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= number_format($adm['mahasiswa_baru_transfer']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= number_format($adm['mahasiswa_aktif_reguler']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= number_format($adm['mahasiswa_aktif_transfer']) ?></td>
                                            <td class="p-4">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button @click="openDetail('<?= $adm['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-700 transition" title="Detail">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>
                                                    <button @click="openEdit('<?= $adm['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                                    </button>
                                                    <button @click="confirmDelete('<?= $adm['id'] ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="block md:hidden p-4 space-y-4 bg-slate-50/30">
                            <?php $no = 0; foreach ($admissions as $adm) : ?>
                                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-500 mb-1.5">
                                                No. <?= $no + 1 ?>
                                            </span>
                                            <h4 class="font-bold text-slate-800 text-base leading-tight"><?= esc($adm['nama_prodi']) ?></h4>
                                            <p class="text-xs text-slate-500 mt-1">TA: <?= esc($adm['tahun_akademik']) ?></p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl text-xs">
                                        <div>
                                            <span class="text-slate-400 block">Daya Tampung</span>
                                            <span class="font-bold text-slate-700"><?= number_format($adm['daya_tampung']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block">Total Pendaftar</span>
                                            <span class="font-bold text-slate-700"><?= number_format($adm['jumlah_pendaftar']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block">Lulus Seleksi</span>
                                            <span class="font-bold text-slate-700"><?= number_format($adm['jumlah_lulus_seleksi']) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                        <span class="text-xs text-slate-500 font-medium">Mhs Aktif (Reg/Trans): <?= number_format($adm['mahasiswa_aktif_reguler']) ?> / <?= number_format($adm['mahasiswa_aktif_transfer']) ?></span>
                                        <div class="flex items-center gap-1.5">
                                            <button @click="openDetail('<?= $adm['id'] ?>')" class="p-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 hover:text-slate-700 transition" title="Detail">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="openEdit('<?= $adm['id'] ?>')" class="p-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 hover:text-primary transition" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="confirmDelete('<?= $adm['id'] ?>')" class="p-2 border border-red-100 hover:bg-red-50 rounded-xl text-red-500 hover:text-red-600 transition" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php $no++; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </template>

        <!-- Tab Mahasiswa Asing (2B) -->
        <template x-if="activeTab === 'foreign'">
            <div>
                <?php if (empty($foreigns)) : ?>
                    <div class="p-12 text-center flex flex-col items-center justify-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                            <i data-lucide="folder-open" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                            <p class="text-sm text-slate-500 max-w-sm">Data mahasiswa asing tidak ditemukan.</p>
                        </div>
                    </div>
                <?php else : ?>
                    <div>
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        <th class="p-4 w-12 text-center row-span-2">No</th>
                                        <th class="p-4 text-left">Program Studi</th>
                                        <th class="p-4">TA</th>
                                        <th class="p-2 border-l border-slate-200">Aktif TS-2</th>
                                        <th class="p-2">Aktif TS-1</th>
                                        <th class="p-2">Aktif TS</th>
                                        <th class="p-2 border-l border-slate-200 text-indigo-600">Asing Full TS-2</th>
                                        <th class="p-2 text-indigo-600">Asing Full TS-1</th>
                                        <th class="p-2 text-indigo-600">Asing Full TS</th>
                                        <th class="p-2 border-l border-slate-200 text-emerald-600">Asing Part TS-2</th>
                                        <th class="p-2 text-emerald-600">Asing Part TS-1</th>
                                        <th class="p-2 text-emerald-600">Asing Part TS</th>
                                        <th class="p-4 w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php $no = 0; foreach ($foreigns as $for) : ?>
                                        <tr class="hover:bg-slate-50/50 transition-all text-center text-xs" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                            <td class="p-4 text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                            <td class="p-4 font-semibold text-slate-800 text-left"><?= esc($for['nama_prodi']) ?></td>
                                            <td class="p-4 text-slate-700 text-center"><?= esc($for['tahun_akademik']) ?></td>
                                            <td class="p-2 border-l border-slate-100 text-slate-700 font-semibold"><?= number_format($for['mahasiswa_aktif_ts2']) ?></td>
                                            <td class="p-2 text-slate-700 font-semibold"><?= number_format($for['mahasiswa_aktif_ts1']) ?></td>
                                            <td class="p-2 text-slate-700 font-semibold"><?= number_format($for['mahasiswa_aktif_ts']) ?></td>
                                            <td class="p-2 border-l border-slate-100 text-indigo-600 font-semibold"><?= number_format($for['mahasiswa_asing_full_ts2']) ?></td>
                                            <td class="p-2 text-indigo-600 font-semibold"><?= number_format($for['mahasiswa_asing_full_ts1']) ?></td>
                                            <td class="p-2 text-indigo-600 font-semibold"><?= number_format($for['mahasiswa_asing_full_ts']) ?></td>
                                            <td class="p-2 border-l border-slate-100 text-emerald-600 font-semibold"><?= number_format($for['mahasiswa_asing_part_ts2']) ?></td>
                                            <td class="p-2 text-emerald-600 font-semibold"><?= number_format($for['mahasiswa_asing_part_ts1']) ?></td>
                                            <td class="p-2 text-emerald-600 font-semibold"><?= number_format($for['mahasiswa_asing_part_ts']) ?></td>
                                            <td class="p-4">
                                                <div class="flex items-center justify-center gap-1">
                                                    <button @click="openDetail('<?= $for['id'] ?>')" class="p-1 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-700 transition" title="Detail">
                                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                    <button @click="openEdit('<?= $for['id'] ?>')" class="p-1 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                    <button @click="confirmDelete('<?= $for['id'] ?>')" class="p-1 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="block md:hidden p-4 space-y-4 bg-slate-50/30">
                            <?php $no = 0; foreach ($foreigns as $for) : ?>
                                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-500 mb-1.5">
                                                No. <?= $no + 1 ?>
                                            </span>
                                            <h4 class="font-bold text-slate-800 text-base leading-tight"><?= esc($for['nama_prodi']) ?></h4>
                                            <p class="text-xs text-slate-500 mt-1">TA: <?= esc($for['tahun_akademik']) ?></p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2 bg-slate-50 p-3 rounded-xl text-xs">
                                        <div>
                                            <span class="text-slate-400 block font-medium">Aktif TS-2 / TS-1 / TS</span>
                                            <span class="font-bold text-slate-700"><?= $for['mahasiswa_aktif_ts2'] ?> / <?= $for['mahasiswa_aktif_ts1'] ?> / <?= $for['mahasiswa_aktif_ts'] ?></span>
                                        </div>
                                        <div>
                                            <span class="text-indigo-400 block font-medium">Asing Full TS-2 / TS-1 / TS</span>
                                            <span class="font-bold text-indigo-700"><?= $for['mahasiswa_asing_full_ts2'] ?> / <?= $for['mahasiswa_asing_full_ts1'] ?> / <?= $for['mahasiswa_asing_full_ts'] ?></span>
                                        </div>
                                        <div>
                                            <span class="text-emerald-400 block font-medium">Asing Part TS-2 / TS-1 / TS</span>
                                            <span class="font-bold text-emerald-700"><?= $for['mahasiswa_asing_part_ts2'] ?> / <?= $for['mahasiswa_asing_part_ts1'] ?> / <?= $for['mahasiswa_asing_part_ts'] ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-1.5 pt-3 border-t border-slate-100">
                                        <button @click="openDetail('<?= $for['id'] ?>')" class="p-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 hover:text-slate-700 transition" title="Detail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="openEdit('<?= $for['id'] ?>')" class="p-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 hover:text-primary transition" title="Edit">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="confirmDelete('<?= $for['id'] ?>')" class="p-2 border border-red-100 hover:bg-red-50 rounded-xl text-red-500 hover:text-red-600 transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php $no++; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </template>

        <!-- Pagination Controls -->
        <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 bg-white rounded-b-2xl" x-show="totalPages > 1" x-cloak>
            <p class="text-sm text-slate-500">
                Menampilkan <span class="font-semibold text-slate-700" x-text="Math.min(startRow + 1, totalRows)"></span>–<span class="font-semibold text-slate-700" x-text="Math.min(endRow, totalRows)"></span>
                dari <span class="font-semibold text-slate-700" x-text="totalRows"></span> data
            </p>
            <div class="flex items-center gap-2">
                <button
                    @click="prevPage()"
                    :disabled="currentPage === 0"
                    :class="currentPage === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 cursor-pointer'"
                    class="p-2 rounded-xl border border-slate-200 text-slate-600 transition-all"
                    title="Halaman sebelumnya"
                >
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>

                <template x-for="page in totalPages" :key="page">
                    <button
                        @click="currentPage = page - 1"
                        :class="currentPage === page - 1 ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100 cursor-pointer'"
                        class="w-8 h-8 flex items-center justify-center rounded-xl border text-sm font-semibold transition-all"
                        x-text="page"
                    ></button>
                </template>

                <button
                    @click="nextPage()"
                    :disabled="currentPage >= totalPages - 1"
                    :class="currentPage >= totalPages - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 cursor-pointer'"
                    class="p-2 rounded-xl border border-slate-200 text-slate-600 transition-all"
                    title="Halaman berikutnya"
                >
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit -->
    <div
        x-show="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen"
        role="dialog"
        aria-modal="true"
        x-cloak
    >
        <!-- Backdrop -->
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="modalOpen = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-md"
        ></div>

        <!-- Modal Content Container -->
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-2xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10"
        >
            <!-- Loading Indicator -->
            <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-50 flex flex-col items-center justify-center space-y-3">
                <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm text-slate-500 font-semibold">Memuat data...</span>
            </div>

            <form :action="formAction" method="POST" class="space-y-0">
                <?= csrf_field() ?>

                <!-- Header Modal -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah Data</h3>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4.5 max-h-[70vh] overflow-y-auto">
                    <!-- General Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Periode Pelaporan *</label>
                            <select name="period_id" x-model="periodId" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="">-- Pilih Periode --</option>
                                <?php foreach ($periods as $p) : ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= esc($p['nama_periode']) ?> (<?= esc($p['tahun_akademik']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Program Studi *</label>
                            <select name="study_program_id" x-model="studyProgramId" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($studyPrograms as $sp) : ?>
                                    <option value="<?= $sp['id'] ?>">
                                        <?= esc($sp['nama_prodi']) ?> (<?= esc($sp['jenjang']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tahun Akademik * (Format: YYYY/YYYY)</label>
                            <input type="text" name="tahun_akademik" x-model="tahunAkademik" required placeholder="e.g. 2025/2026" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>


                    <template x-if="activeTab === 'admission'">
                        <div class="space-y-4 pt-3 border-t border-slate-100">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Rincian Seleksi Mahasiswa Baru</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Daya Tampung *</label>
                                    <input type="number" name="daya_tampung" x-model="dayaTampung" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jumlah Pendaftar *</label>
                                    <input type="number" name="jumlah_pendaftar" x-model="jumlahPendaftar" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jumlah Lulus Seleksi *</label>
                                    <input type="number" name="jumlah_lulus_seleksi" x-model="jumlahLulusSeleksi" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Mahasiswa Baru Reguler *</label>
                                    <input type="number" name="mahasiswa_baru_reguler" x-model="mabaReguler" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Mahasiswa Baru Transfer *</label>
                                    <input type="number" name="mahasiswa_baru_transfer" x-model="mabaTransfer" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Mahasiswa Aktif Reguler *</label>
                                    <input type="number" name="mahasiswa_aktif_reguler" x-model="aktifReguler" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Mahasiswa Aktif Transfer *</label>
                                    <input type="number" name="mahasiswa_aktif_transfer" x-model="aktifTransfer" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="activeTab === 'foreign'">
                        <div class="space-y-4 pt-3 border-t border-slate-100">
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Mahasiswa Aktif</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-slate-600 font-semibold text-xs mb-1">TS-2 *</label>
                                        <input type="number" name="mahasiswa_aktif_ts2" x-model="aktifTs2" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-600 font-semibold text-xs mb-1">TS-1 *</label>
                                        <input type="number" name="mahasiswa_aktif_ts1" x-model="aktifTs1" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-600 font-semibold text-xs mb-1">TS *</label>
                                        <input type="number" name="mahasiswa_aktif_ts" x-model="aktifTs" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-500 mb-1">Mahasiswa Asing Full-time (Penuh Waktu)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-indigo-600 font-semibold text-xs mb-1">TS-2 *</label>
                                        <input type="number" name="mahasiswa_asing_full_ts2" x-model="asingFullTs2" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-indigo-600 font-semibold text-xs mb-1">TS-1 *</label>
                                        <input type="number" name="mahasiswa_asing_full_ts1" x-model="asingFullTs1" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-indigo-600 font-semibold text-xs mb-1">TS *</label>
                                        <input type="number" name="mahasiswa_asing_full_ts" x-model="asingFullTs" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-500 mb-1">Mahasiswa Asing Part-time (Paruh Waktu)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-emerald-600 font-semibold text-xs mb-1">TS-2 *</label>
                                        <input type="number" name="mahasiswa_asing_part_ts2" x-model="asingPartTs2" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-emerald-600 font-semibold text-xs mb-1">TS-1 *</label>
                                        <input type="number" name="mahasiswa_asing_part_ts1" x-model="asingPartTs1" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-emerald-600 font-semibold text-xs mb-1">TS *</label>
                                        <input type="number" name="mahasiswa_asing_part_ts" x-model="asingPartTs" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/95 text-white font-medium rounded-xl text-sm transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div
        x-show="detailOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen"
        role="dialog"
        aria-modal="true"
        x-cloak
    >
        <!-- Backdrop -->
        <div
            x-show="detailOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="detailOpen = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-md"
        ></div>

        <!-- Content -->
        <div
            x-show="detailOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10"
        >
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 tracking-tight">Detail Data</h3>
                <button type="button" @click="detailOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-slate-400 block">Periode Pelaporan</span>
                            <span class="font-bold text-slate-700" x-text="detailData.nama_periode"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Program Studi</span>
                            <span class="font-bold text-slate-700" x-text="detailData.nama_prodi"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400 block">Tahun Akademik</span>
                            <span class="font-bold text-slate-700" x-text="detailData.tahun_akademik"></span>
                        </div>
                    </div>

                    <!-- Detail Seleksi Mahasiswa Baru (2A) -->
                    <template x-if="activeTab === 'admission'">
                        <div class="space-y-3 border-t border-slate-100 pt-3">
                            <h4 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Rincian Seleksi</h4>
                            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl">
                                <div>
                                    <span class="text-slate-400 block">Daya Tampung</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.daya_tampung || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Jumlah Pendaftar</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.jumlah_pendaftar || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Lulus Seleksi</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.jumlah_lulus_seleksi || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Maba Reguler</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_baru_reguler || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Maba Transfer</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_baru_transfer || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Mhs Aktif Reguler</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_aktif_reguler || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Mhs Aktif Transfer</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_aktif_transfer || 0).toLocaleString()"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Detail Mahasiswa Asing (2B) -->
                    <template x-if="activeTab === 'foreign'">
                        <div class="space-y-3 border-t border-slate-100 pt-3">
                            <h4 class="text-xs font-bold uppercase text-slate-400 tracking-wider">Rincian Data Mahasiswa Asing</h4>
                            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl">
                                <div>
                                    <span class="text-slate-400 block">Mahasiswa Aktif TS-2</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_aktif_ts2 || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Mahasiswa Aktif TS-1</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_aktif_ts1 || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Mahasiswa Aktif TS</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_aktif_ts || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-indigo-400 block">Mahasiswa Asing Full TS-2</span>
                                    <span class="font-bold text-indigo-700 text-sm" x-text="Number(detailData.mahasiswa_asing_full_ts2 || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-indigo-400 block">Mahasiswa Asing Full TS-1</span>
                                    <span class="font-bold text-indigo-700 text-sm" x-text="Number(detailData.mahasiswa_asing_full_ts1 || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-indigo-400 block">Mahasiswa Asing Full TS</span>
                                    <span class="font-bold text-indigo-700 text-sm" x-text="Number(detailData.mahasiswa_asing_full_ts || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-emerald-400 block">Mahasiswa Asing Part TS-2</span>
                                    <span class="font-bold text-emerald-700 text-sm" x-text="Number(detailData.mahasiswa_asing_part_ts2 || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-emerald-400 block">Mahasiswa Asing Part TS-1</span>
                                    <span class="font-bold text-emerald-700 text-sm" x-text="Number(detailData.mahasiswa_asing_part_ts1 || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-emerald-400 block">Mahasiswa Asing Part TS</span>
                                    <span class="font-bold text-emerald-700 text-sm" x-text="Number(detailData.mahasiswa_asing_part_ts || 0).toLocaleString()"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button @click="detailOpen = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
