<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{ 
    detailOpen: false, 
    detailData: {},
    deleteOpen: false,
    deleteUrl: '',
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Kerja Sama',
    formAction: '',
    periodId: '',
    jenisKerjasama: '',
    isNewPartner: false,
    partnerId: '',
    newPartnerName: '',
    newPartnerType: '',
    newPartnerCountry: 'Indonesia',
    newPartnerAddress: '',
    newPartnerContact: '',
    tingkat: 'lokal',
    tahunBerakhir: '<?= date('Y') ?>',
    judulKerjasama: '',
    manfaat: '',
    tanggalMulai: '',
    tanggalSelesai: '',
    waktuDurasi: '',
    fileName: '',
    existingFile: '',
    dragging: false,
    showFileError: false,
    currentPage: 0,
    perPage: 5,
    totalRows: 0,
    get totalPages() { return Math.max(1, Math.ceil(this.totalRows / this.perPage)); },
    get startRow() { return this.currentPage * this.perPage; },
    get endRow() { return this.startRow + this.perPage; },
    isRowVisible(idx) { return idx >= this.startRow && idx < this.endRow; },
    prevPage() { if (this.currentPage > 0) this.currentPage--; },
    nextPage() { if (this.currentPage < this.totalPages - 1) this.currentPage++; },

    fetchDetail(id) {
        this.loading = true;
        this.detailOpen = true;
        fetch('<?= base_url('cooperations/detail') ?>/' + id)
            .then(res => res.json())
            .then(data => {
                this.detailData = data;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.detailOpen = false;
            });
    },
    confirmDelete(id) {
        this.deleteUrl = '<?= base_url('cooperations/delete') ?>/' + id;
        this.deleteOpen = true;
    },
    openAdd() {
        this.modalTitle = 'Tambah Kerja Sama';
        this.formAction = '<?= base_url('cooperations/store') ?>';
        this.periodId = '<?= $selectedPeriod ?>';
        this.jenisKerjasama = '<?= $selectedJenis ?>';
        this.isNewPartner = false;
        this.partnerId = '';
        this.newPartnerName = '';
        this.newPartnerType = '';
        this.newPartnerCountry = 'Indonesia';
        this.newPartnerAddress = '';
        this.newPartnerContact = '';
        this.tingkat = 'lokal';
        this.tahunBerakhir = '<?= date('Y') ?>';
        this.judulKerjasama = '';
        this.manfaat = '';
        this.tanggalMulai = '';
        this.tanggalSelesai = '';
        this.waktuDurasi = '';
        this.fileName = '';
        this.existingFile = '';
        this.showFileError = false;
        
        const fileInput = document.getElementById('bukti-file-input');
        if (fileInput) fileInput.value = '';
        
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Kerja Sama';
        this.formAction = '<?= base_url('cooperations/update') ?>/' + id;
        this.isNewPartner = false;
        this.fileName = '';
        this.existingFile = '';
        this.showFileError = false;
        
        const fileInput = document.getElementById('bukti-file-input');
        if (fileInput) fileInput.value = '';

        this.loading = true;
        this.modalOpen = true;
        
        fetch('<?= base_url('cooperations/detail') ?>/' + id)
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data kerja sama.');
                return res.json();
            })
            .then(data => {
                this.periodId = data.period_id;
                this.jenisKerjasama = data.jenis_kerjasama;
                this.partnerId = data.partner_id || '';
                this.tingkat = data.tingkat;
                this.tahunBerakhir = data.tahun_berakhir;
                this.judulKerjasama = data.judul_kerjasama;
                this.manfaat = data.manfaat;
                this.tanggalMulai = data.tanggal_mulai;
                this.tanggalSelesai = data.tanggal_selesai || '';
                this.waktuDurasi = data.waktu_durasi;
                this.existingFile = data.bukti_kerjasama || '';
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: err.message });
            });
    },
    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            if (files[0].type === 'application/pdf' || files[0].name.toLowerCase().endsWith('.pdf')) {
                document.getElementById('bukti-file-input').files = files;
                this.fileName = files[0].name;
                this.showFileError = false;
            } else {
                this.$dispatch('show-toast', { type: 'error', message: 'Hanya diperbolehkan mengunggah file PDF.' });
            }
        }
    },
    handleSelect(e) {
        const files = e.target.files;
        if (files.length) {
            this.fileName = files[0].name;
            this.showFileError = false;
        }
    }
}" x-init="totalRows = <?= count($cooperations) ?>">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800"><?= esc($title) ?></h2>
            <p class="text-sm text-slate-500">Kelola data kerja sama Tridharma Perguruan Tinggi.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openAdd()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Data
            </button>
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

    <?php if (empty($periods)): ?>
        <?= $this->include('components/no_periods') ?>
    <?php else: ?>
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <?php
            $formAction = base_url('cooperations/education');
            if ($selectedJenis === 'penelitian') {
                $formAction = base_url('cooperations/research');
            } elseif ($selectedJenis === 'pengabdian') {
                $formAction = base_url('cooperations/community');
            }
        ?>
        <form action="<?= $formAction ?>" method="get" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
                    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari berdasarkan mitra atau judul..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <?php if (empty($cooperations)) : ?>
            <div class="p-12 text-center flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                    <i data-lucide="folder-open" class="w-8 h-8"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                    <p class="text-sm text-slate-500 max-w-sm">Data kerja sama untuk kriteria yang dicari tidak ditemukan.</p>
                </div>
            </div>
        <?php else : ?>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider min-w-[250px]">Lembaga Mitra</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24 text-center">Tingkat</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-44">Judul Kegiatan</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-44">Manfaat bagi PS</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-36">Waktu & Durasi</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-36">Bukti</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24 text-center">Berakhir</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $no = 0; foreach ($cooperations as $coop) : ?>
                            <tr class="hover:bg-slate-50/50 transition-all" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                <td class="p-4 text-sm text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-800"><?= esc($coop['nama_mitra']) ?></div>
                                    <div class="text-xs text-slate-400 capitalize"><?= esc($coop['jenis_mitra']) ?> | <?= esc($coop['negara']) ?></div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full capitalize 
                                        <?= $coop['tingkat'] === 'internasional' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : ($coop['tingkat'] === 'nasional' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-slate-100 text-slate-700 border border-slate-200') ?>">
                                        <?= esc($coop['tingkat']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-slate-700 font-medium max-w-[170px] truncate" title="<?= esc($coop['judul_kerjasama']) ?>"><?= esc($coop['judul_kerjasama']) ?></td>
                                <td class="p-4 text-sm text-slate-500 max-w-[170px] truncate" title="<?= esc($coop['manfaat']) ?>"><?= esc($coop['manfaat']) ?></td>
                                <td class="p-4 text-sm text-slate-600">
                                    <div class="font-medium"><?= esc($coop['waktu_durasi']) ?></div>
                                    <div class="text-xs text-slate-400"><?= date('d/m/Y', strtotime($coop['tanggal_mulai'])) ?></div>
                                </td>
                                <td class="p-4 text-sm">
                                    <?php if (!empty($coop['bukti_kerjasama'])): ?>
                                        <a href="<?= base_url('cooperations/download/' . $coop['bukti_kerjasama']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary/10 border border-slate-200 hover:border-primary/30 rounded-xl text-xs font-semibold text-slate-600 hover:text-primary transition-all truncate max-w-[160px]" title="Unduh Bukti">
                                            <i data-lucide="download" class="w-3.5 h-3.5 text-primary"></i>
                                            <span class="truncate">Unduh Bukti</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-medium">Tidak ada file</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-sm text-slate-700 text-center font-semibold"><?= esc($coop['tahun_berakhir']) ?></td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="fetchDetail('<?= $coop['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-slate-700 transition" title="Detail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="openEdit('<?= $coop['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="confirmDelete('<?= $coop['id'] ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php $no++; endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="block md:hidden p-4 space-y-4 bg-slate-50/30">
                <?php $no = 0; foreach ($cooperations as $coop) : ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4 hover:shadow-md transition-all duration-200" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-500">
                                    No. <?= $no + 1 ?>
                                </span>
                                <h4 class="font-bold text-slate-800 text-base leading-tight"><?= esc($coop['nama_mitra']) ?></h4>
                                <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                                    <span class="font-medium capitalize"><?= esc($coop['jenis_mitra']) ?></span>
                                    <span class="text-slate-300">•</span>
                                    <span><?= esc($coop['negara']) ?></span>
                                </div>
                            </div>
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full capitalize shrink-0
                                <?= $coop['tingkat'] === 'internasional' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : ($coop['tingkat'] === 'nasional' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-slate-100 text-slate-700 border border-slate-200') ?>">
                                <?= esc($coop['tingkat']) ?>
                            </span>
                        </div>
                        
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <div>
                                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-0.5">Judul Kegiatan</span>
                                <span class="text-sm text-slate-700 font-medium"><?= esc($coop['judul_kerjasama']) ?></span>
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-0.5">Manfaat bagi PS</span>
                                <span class="text-sm text-slate-600 line-clamp-2"><?= esc($coop['manfaat']) ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Waktu & Durasi</span>
                                    <span class="text-xs text-slate-700 font-semibold"><?= esc($coop['waktu_durasi']) ?></span>
                                    <span class="text-[10px] text-slate-400 block font-medium mt-0.5"><?= date('d/m/Y', strtotime($coop['tanggal_mulai'])) ?></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Tahun Berakhir</span>
                                    <span class="text-xs text-slate-700 font-semibold"><?= esc($coop['tahun_berakhir']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                            <div>
                                <?php if (!empty($coop['bukti_kerjasama'])): ?>
                                    <a href="<?= base_url('cooperations/download/' . $coop['bukti_kerjasama']) ?>" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                        Unduh Bukti
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 italic">Tidak ada file</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="fetchDetail('<?= $coop['id'] ?>')" class="flex items-center justify-center p-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 hover:text-slate-700 transition" title="Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                                <button @click="openEdit('<?= $coop['id'] ?>')" class="flex items-center justify-center p-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete('<?= $coop['id'] ?>')" class="flex items-center justify-center p-2 border border-red-100 hover:bg-red-50 rounded-xl text-red-500 hover:text-red-600 transition" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php $no++; endforeach; ?>
            </div>

            <!-- Pagination Controls -->
            <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 bg-white rounded-b-2xl" x-show="totalPages > 1">
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
        <?php endif; ?>
    </div>

    <div x-show="detailOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-xl border border-slate-200" 
             @click.outside="detailOpen = false">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Detail Data Kerja Sama</h3>
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
                        <span class="text-xs text-slate-400 block font-semibold uppercase">Lembaga Mitra</span>
                        <span class="text-sm text-slate-800 font-bold" x-text="detailData.nama_mitra"></span>
                        <span class="text-xs text-slate-500 block" x-text="(detailData.jenis_mitra || '') + ' - ' + (detailData.negara || '')"></span>
                        <span class="text-xs text-slate-500 block" x-text="detailData.alamat || '-'"></span>
                        <span class="text-xs text-slate-500 block" x-text="'Kontak: ' + (detailData.kontak || '-')"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Tingkat</span>
                            <span class="text-sm text-slate-800 font-semibold capitalize" x-text="detailData.tingkat"></span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Tahun Berakhir</span>
                            <span class="text-sm text-slate-800 font-semibold" x-text="detailData.tahun_berakhir"></span>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <span class="text-xs text-slate-400 block font-semibold uppercase">Judul Kegiatan Kerja Sama</span>
                        <span class="text-sm text-slate-800 font-medium" x-text="detailData.judul_kerjasama"></span>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <span class="text-xs text-slate-400 block font-semibold uppercase">Manfaat Bagi PS Yang Diakreditasi</span>
                        <span class="text-sm text-slate-800" x-text="detailData.manfaat"></span>
                    </div>
                    <div class="border-t border-slate-100 pt-3 grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Durasi Kerja Sama</span>
                            <span class="text-sm text-slate-800 font-medium" x-text="detailData.waktu_durasi"></span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Periode</span>
                            <span class="text-sm text-slate-800 font-medium" x-text="detailData.nama_periode"></span>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 pt-3 grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Tanggal Mulai</span>
                            <span class="text-sm text-slate-800" x-text="detailData.tanggal_mulai"></span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-semibold uppercase">Tanggal Selesai</span>
                            <span class="text-sm text-slate-800" x-text="detailData.tanggal_selesai || '-'"></span>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 pt-3">
                        <span class="text-xs text-slate-400 block font-semibold uppercase mb-1">Bukti Kerja Sama</span>
                        <template x-if="detailData.bukti_kerjasama">
                            <a :href="'<?= base_url('cooperations/download') ?>/' + detailData.bukti_kerjasama" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-primary/10 border border-slate-200 hover:border-primary/30 rounded-xl text-xs font-semibold text-slate-600 hover:text-primary transition-all">
                                <i data-lucide="download" class="w-3.5 h-3.5 text-primary"></i>
                                <span>Unduh Bukti Dokumen</span>
                            </a>
                        </template>
                        <template x-if="!detailData.bukti_kerjasama">
                            <span class="text-sm text-slate-400 italic">Tidak ada file bukti.</span>
                        </template>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button @click="detailOpen = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div x-show="deleteOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-xl border border-slate-200"
             @click.outside="deleteOpen = false">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-800 text-lg">Konfirmasi Hapus</h3>
                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus data kerja sama ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                <button @click="deleteOpen = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition">
                    Batal
                </button>
                <a :href="deleteUrl" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl text-sm transition">
                    Hapus Data
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Form Add/Edit -->
    <div
        x-show="modalOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen"
        role="dialog"
        aria-modal="true"
        x-cloak
    >
        <!-- backdrop -->
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

        <!-- modal content -->
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
            <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-50 flex flex-col items-center justify-center space-y-3">
                <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm text-slate-500 font-semibold">Memuat data...</span>
            </div>

            <form :action="formAction" method="POST" enctype="multipart/form-data" @submit="if (!existingFile && !fileName) { $event.preventDefault(); showFileError = true; $dispatch('show-toast', { type: 'error', message: 'Silakan unggah bukti kerja sama berupa file PDF terlebih dahulu.' }); }" class="space-y-0">
                <?= csrf_field() ?>

                <!-- Header Modal -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah Kerja Sama</h3>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4.5 max-h-[70vh] overflow-y-auto">
                    
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
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Kerja Sama *</label>
                            <select name="jenis_kerjasama" x-model="jenisKerjasama" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="pendidikan">Pendidikan</option>
                                <option value="penelitian">Penelitian</option>
                                <option value="pengabdian">Pengabdian Masyarakat</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lembaga Mitra *</label>
                            <button type="button" @click="isNewPartner = !isNewPartner" class="text-xs font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer">
                                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                <span x-text="isNewPartner ? 'Pilih Mitra Terdaftar' : 'Tambah Mitra Baru'"></span>
                            </button>
                        </div>

                        <div x-show="!isNewPartner">
                            <select name="partner_id" x-model="partnerId" :required="!isNewPartner" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="">-- Pilih Lembaga Mitra --</option>
                                <?php foreach ($partners as $partner) : ?>
                                    <option value="<?= $partner['id'] ?>">
                                        <?= esc($partner['nama_mitra']) ?> (<?= esc($partner['jenis_mitra']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div x-show="isNewPartner" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-200" x-cloak>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Data Mitra Baru</h4>
                            <div>
                                <label class="block text-slate-600 font-semibold text-xs mb-1">Nama Lembaga Mitra *</label>
                                <input type="text" name="new_partner_name" x-model="newPartnerName" :required="isNewPartner" placeholder="e.g. Google LLC" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Jenis Mitra</label>
                                    <input type="text" name="new_partner_type" x-model="newPartnerType" placeholder="e.g. Industri / Perguruan Tinggi" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Negara Mitra</label>
                                    <input type="text" name="new_partner_country" x-model="newPartnerCountry" placeholder="e.g. Indonesia" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Alamat Mitra</label>
                                    <input type="text" name="new_partner_address" x-model="newPartnerAddress" placeholder="Alamat lengkap" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-600 font-semibold text-xs mb-1">Kontak Mitra</label>
                                    <input type="text" name="new_partner_contact" x-model="newPartnerContact" placeholder="Telepon / Email" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tingkat Kerja Sama *</label>
                            <select name="tingkat" x-model="tingkat" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="lokal">Wilayah / Lokal</option>
                                <option value="nasional">Nasional</option>
                                <option value="internasional">Internasional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tahun Berakhir Kerja Sama *</label>
                            <input type="number" name="tahun_berakhir" x-model="tahunBerakhir" required min="1900" max="2100" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Judul Kegiatan Kerja Sama *</label>
                        <input type="text" name="judul_kerjasama" x-model="judulKerjasama" required placeholder="Masukkan judul detail kegiatan kerja sama" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Manfaat bagi PS yang Diakreditasi *</label>
                        <textarea name="manfaat" x-model="manfaat" required placeholder="Jelaskan manfaat hasil kerja sama bagi program studi" rows="3" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all"></textarea>
                    </div>

                    <div class="border-t border-slate-100 pt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai *</label>
                            <input type="date" name="tanggal_mulai" x-model="tanggalMulai" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="tanggal_selesai" x-model="tanggalSelesai" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Waktu & Durasi *</label>
                            <input type="text" name="waktu_durasi" x-model="waktuDurasi" placeholder="e.g. 5 Tahun / 6 Bulan" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Bukti Kerja Sama (PDF, Maks. 5MB) <span x-show="!existingFile">*</span></label>
                        
                        <!-- Existing file preview (for edit mode) -->
                        <div x-show="existingFile" class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-sm" x-cloak>
                            <div class="flex items-center gap-2 text-slate-600 font-medium truncate">
                                <i data-lucide="file" class="w-4 h-4 text-primary shrink-0"></i>
                                <span class="truncate" x-text="existingFile"></span>
                            </div>
                            <a :href="'<?= base_url('cooperations/download') ?>/' + existingFile" class="text-xs font-bold text-primary hover:underline flex items-center gap-1 shrink-0">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                Unduh File
                            </a>
                        </div>

                        <!-- Drag and Drop Area -->
                        <div class="relative flex items-center justify-center w-full"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; handleDrop($event)">
                            <label :class="dragging ? 'border-primary bg-primary/5' : 'border-slate-300 bg-slate-50 hover:bg-slate-100/50'" 
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mb-2"></i>
                                    <p class="text-sm text-slate-600 font-medium mb-1">
                                        <span class="text-primary hover:underline font-semibold" x-text="existingFile ? 'Klik untuk ganti file' : 'Klik untuk unggah'"></span> atau seret file ke sini
                                    </p>
                                    <p class="text-xs text-slate-400">Hanya file PDF (Maks. 5MB)</p>
                                    <div x-show="fileName" x-cloak class="mt-2 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 flex items-center gap-1.5">
                                        <i data-lucide="file-check" class="w-3.5 h-3.5"></i>
                                        <span x-text="fileName" class="truncate max-w-[200px]"></span>
                                    </div>
                                </div>
                                <input id="bukti-file-input" type="file" name="bukti_kerjasama" class="hidden" accept=".pdf" @change="handleSelect($event)">
                            </label>
                        </div>
                        <p x-show="showFileError" class="text-xs text-red-500 font-semibold mt-1.5 flex items-center gap-1" x-cloak>
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>Bukti kerja sama berupa file PDF wajib diunggah.</span>
                        </p>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-2xl hover:bg-slate-50 transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-3 bg-primary text-white text-sm font-semibold rounded-2xl hover:bg-primary/95 shadow-md shadow-primary/10 transition-all cursor-pointer">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

<?php endif; // end no_periods guard ?>

</div>
<?= $this->endSection() ?>
