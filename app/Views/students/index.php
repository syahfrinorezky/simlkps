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
    mahasiswaAktif: 0,

    negaraAsal: '',
    jenjang: 'S1',
    mabaAsingFull: 0,
    mabaAsingPart: 0,

    detailOpen: false,
    detailData: {},
    loading: false,

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
        this.modalTitle = this.activeTab === 'admission' ? 'Tambah Data Seleksi Mahasiswa' : 'Tambah Data Mahasiswa Asing';
        this.formAction = this.activeTab === 'admission' ? '<?= base_url('students/store-admission') ?>' : '<?= base_url('students/store-foreign') ?>';
        
        this.studyProgramId = '';
        this.tahunAkademik = '<?= date('Y') . '/' . (date('Y') + 1) ?>';
        
        this.dayaTampung = 0;
        this.jumlahPendaftar = 0;
        this.jumlahLulusSeleksi = 0;
        this.mabaReguler = 0;
        this.mabaTransfer = 0;
        this.mahasiswaAktif = 0;

        this.negaraAsal = '';
        this.jenjang = 'S1';
        this.mabaAsingFull = 0;
        this.mabaAsingPart = 0;

        this.modalOpen = true;
    },

    openEdit(id) {
        this.modalTitle = this.activeTab === 'admission' ? 'Edit Data Seleksi Mahasiswa' : 'Edit Data Mahasiswa Asing';
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
                    this.mahasiswaAktif = data.mahasiswa_aktif;
                } else {
                    this.negaraAsal = data.negara_asal;
                    this.jenjang = data.jenjang || 'S1';
                    this.mabaAsingFull = data.mahasiswa_asing_penuh_waktu;
                    this.mabaAsingPart = data.mahasiswa_asing_paruh_waktu;
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
            <h2 class="text-2xl font-bold text-slate-800"><?= $tab === 'admission' ? 'Seleksi Mahasiswa' : 'Mahasiswa Asing' ?></h2>
            <p class="text-sm text-slate-500"><?= $tab === 'admission' ? 'Kelola data seleksi penerimaan mahasiswa baru program studi.' : 'Kelola data jumlah mahasiswa asing yang aktif atau terdaftar.' ?></p>
        </div>
        <div class="flex items-center gap-3">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Pendaftar</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_pendaftar'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Lulus Seleksi</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_lulus'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Maba Terdaftar</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_maba'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Mahasiswa Aktif</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_aktif'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <i data-lucide="percent" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Keketatan Seleksi</span>
                    <span class="text-xl font-bold text-slate-800"><?= $stats['rasio_keketatan'] ?? 0 ?>%</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i data-lucide="globe" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Total Negara Asal</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_negara'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Mhs Asing Aktif</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_asing_aktif'] ?? 0) ?></span>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Mhs Asing Baru</span>
                    <span class="text-xl font-bold text-slate-800"><?= number_format($stats['total_asing_baru'] ?? 0) ?></span>
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
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Cari Data</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari berdasarkan program studi atau negara..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
        </form>
    </div>

    <!-- Data Display Section -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        
        <!-- Tab Seleksi Mahasiswa (2a) -->
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
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center font-semibold">No</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold">Program Studi</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Tahun Akademik</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Daya Tampung</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Pendaftar</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Lulus</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Maba Reguler</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Maba Transfer</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Mhs Aktif</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center w-28">Aksi</th>
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
                                            <td class="p-4 text-sm text-slate-700 text-center font-semibold"><?= number_format($adm['mahasiswa_aktif']) ?></td>
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
                                        <div>
                                            <span class="text-slate-400 block">Mhs Aktif</span>
                                            <span class="font-bold text-slate-700"><?= number_format($adm['mahasiswa_aktif']) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                        <span class="text-xs text-slate-500 font-medium">Maba (Reguler/Transfer): <?= number_format($adm['mahasiswa_baru_reguler']) ?> / <?= number_format($adm['mahasiswa_baru_transfer']) ?></span>
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

        <!-- Tab Mahasiswa Asing (2b) -->
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
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center font-semibold">No</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold">Program Studi</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold text-center">Tahun Akademik</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider font-semibold">Negara Asal</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jenjang</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Mhs Aktif (Penuh Waktu)</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Mhs Baru (Paruh Waktu)</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php $no = 0; foreach ($foreigns as $for) : ?>
                                        <tr class="hover:bg-slate-50/50 transition-all" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                            <td class="p-4 text-sm text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                            <td class="p-4 font-semibold text-slate-800"><?= esc($for['nama_prodi']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><?= esc($for['tahun_akademik']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 font-medium"><?= esc($for['negara_asal']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center"><span class="px-2 py-1 bg-slate-100 rounded-md font-semibold text-xs"><?= esc($for['jenjang']) ?></span></td>
                                            <td class="p-4 text-sm text-slate-700 text-center font-semibold"><?= number_format($for['mahasiswa_asing_penuh_waktu']) ?></td>
                                            <td class="p-4 text-sm text-slate-700 text-center font-semibold"><?= number_format($for['mahasiswa_asing_paruh_waktu']) ?></td>
                                            <td class="p-4">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button @click="openDetail('<?= $for['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-700 transition" title="Detail">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>
                                                    <button @click="openEdit('<?= $for['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                                    </button>
                                                    <button @click="confirmDelete('<?= $for['id'] ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
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
                                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl text-xs">
                                        <div>
                                            <span class="text-slate-400 block">Negara Asal</span>
                                            <span class="font-bold text-slate-700"><?= esc($for['negara_asal']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block">Jenjang</span>
                                            <span class="font-bold text-slate-700"><?= esc($for['jenjang']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block">Penuh Waktu (Aktif)</span>
                                            <span class="font-bold text-slate-700"><?= number_format($for['mahasiswa_asing_penuh_waktu']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block">Paruh Waktu (Baru)</span>
                                            <span class="font-bold text-slate-700"><?= number_format($for['mahasiswa_asing_paruh_waktu']) ?></span>
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

                    <!-- Fields For Submodul 2a Seleksi Mahasiswa -->
                    <template x-if="activeTab === 'admission'">
                        <div class="space-y-4 pt-3 border-t border-slate-100">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Rincian Seleksi Mahasiswa</h4>
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
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Maba Reguler *</label>
                                    <input type="number" name="mahasiswa_baru_reguler" x-model="mabaReguler" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Maba Transfer *</label>
                                    <input type="number" name="mahasiswa_baru_transfer" x-model="mabaTransfer" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jumlah Registrasi (Mhs Aktif) *</label>
                                    <input type="number" name="mahasiswa_aktif" x-model="mahasiswaAktif" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Fields For Submodul 2b Mahasiswa Asing -->
                    <template x-if="activeTab === 'foreign'">
                        <div class="space-y-4 pt-3 border-t border-slate-100">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Rincian Mahasiswa Asing</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Negara Asal *</label>
                                    <input type="text" name="negara_asal" x-model="negaraAsal" required placeholder="e.g. Malaysia, Timor Leste" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jenjang *</label>
                                    <select name="jenjang" x-model="jenjang" required class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all cursor-pointer">
                                        <option value="D3">Diploma 3 (D3)</option>
                                        <option value="D4">Diploma 4 (D4)</option>
                                        <option value="S1">Sarjana (S1)</option>
                                        <option value="S2">Magister (S2)</option>
                                        <option value="S3">Doktor (S3)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jumlah Mahasiswa Aktif (Penuh Waktu) *</label>
                                    <input type="number" name="mahasiswa_asing_penuh_waktu" x-model="mabaAsingFull" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jumlah Mahasiswa Baru (Paruh Waktu) *</label>
                                    <input type="number" name="mahasiswa_asing_paruh_waktu" x-model="mabaAsingPart" required min="0" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="modalOpen = false" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-2xl hover:bg-slate-50 transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-3 bg-primary text-white text-sm font-semibold rounded-2xl hover:bg-primary/95 shadow-md shadow-primary/10 transition-all cursor-pointer">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail View -->
    <div
        x-show="detailOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-xl border border-slate-200" @click.outside="detailOpen = false">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Detail Evaluasi Mahasiswa</h3>
                <button @click="detailOpen = false" class="p-1 hover:bg-slate-100 rounded-lg text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div x-show="loading" class="flex flex-col items-center justify-center py-12 space-y-3">
                    <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-sm text-slate-500">Memuat data...</span>
                </div>
                <div x-show="!loading" class="space-y-4">
                    <div>
                        <span class="text-xs text-slate-400 block font-semibold uppercase">Program Studi</span>
                        <span class="text-sm text-slate-800 font-bold" x-text="detailData.nama_prodi"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Tahun Akademik</span>
                            <span class="text-sm text-slate-800 font-semibold" x-text="detailData.tahun_akademik"></span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Periode Pelaporan</span>
                            <span class="text-sm text-slate-800 font-semibold" x-text="detailData.nama_periode"></span>
                        </div>
                    </div>

                    <!-- Submodul 2a Detail -->
                    <template x-if="activeTab === 'admission'">
                        <div class="space-y-3 border-t border-slate-100 pt-3">
                            <h4 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-2">Rincian Data Seleksi</h4>
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
                                    <span class="text-slate-400 block">Jumlah Lulus Seleksi</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.jumlah_lulus_seleksi || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Mahasiswa Aktif</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_aktif || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Maba Reguler</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_baru_reguler || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Maba Transfer</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_baru_transfer || 0).toLocaleString()"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Submodul 2b Detail -->
                    <template x-if="activeTab === 'foreign'">
                        <div class="space-y-3 border-t border-slate-100 pt-3">
                            <h4 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-2">Rincian Data Mahasiswa Asing</h4>
                            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl">
                                <div>
                                    <span class="text-slate-400 block">Negara Asal</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="detailData.negara_asal"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Jenjang</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="detailData.jenjang"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Penuh Waktu (Aktif)</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_asing_penuh_waktu || 0).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block">Paruh Waktu (Baru)</span>
                                    <span class="font-bold text-slate-700 text-sm" x-text="Number(detailData.mahasiswa_asing_paruh_waktu || 0).toLocaleString()"></span>
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
