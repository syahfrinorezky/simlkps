<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Publikasi Ilmiah DTPS<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (empty($periods)): ?>
    <?= $this->include('components/no_periods') ?>
<?php else: ?>

<div class="space-y-6" x-data="{
    detailOpen: false,
    detailData: {},
    deleteOpen: false,
    deleteUrl: '',
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Publikasi',
    formAction: '',
    form: {
        lecturer_name: '', judul: '', kategori_publikasi: '',
        penerbit: '', nomor_issn_isbn: '', tahun: new Date().getFullYear(),
        tingkat: 'nasional', tautan: ''
    },
    openAdd() {
        this.modalTitle = 'Tambah Publikasi';
        this.formAction = '<?= base_url('lecturers/publications/store') ?>';
        this.form = {
            lecturer_name: '', judul: '', kategori_publikasi: '',
            penerbit: '', nomor_issn_isbn: '', tahun: new Date().getFullYear(),
            tingkat: 'nasional', tautan: ''
        };
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Publikasi';
        this.formAction = '<?= base_url('lecturers/publications/update') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;
        fetch('<?= base_url('lecturers/publications/show') ?>/' + id)
            .then(res => res.json())
            .then(data => {
                data.lecturer_name = data.nama || '';
                this.form = data;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: 'Gagal memuat data.' });
            });
    },
    confirmDelete(id) {
        this.deleteUrl = '<?= base_url('lecturers/publications/delete') ?>/' + id;
        this.deleteOpen = true;
    }
}" x-init="
    <?php if (session()->getFlashdata('success')): ?>
        $dispatch('show-toast', { type: 'success', message: '<?= session()->getFlashdata('success') ?>' });
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        $dispatch('show-toast', { type: 'error', message: '<?= session()->getFlashdata('error') ?>' });
    <?php endif; ?>
">

    <!-- Page Header -->
    <div class="flex flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Publikasi Ilmiah DTPS</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 hidden sm:block">Publikasi Ilmiah Dosen Tetap Program Studi</p>
        </div>
        <?php if (in_array(session()->get('userRole'), ['admin', 'prodi', 'dosen'])): ?>
        <button @click="openAdd()"
            class="inline-flex items-center justify-center gap-2 p-2 sm:px-4 sm:py-2 bg-primary self-start sm:self-auto hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i><span class="hidden sm:inline">
            Tambah Publikasi
        </span></button>
        <?php endif; ?>
    </div>

    <!-- TABEL 1: RANGKUMAN AGREGASI PUBLIKASI -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="font-bold text-slate-800 text-sm">Rangkuman Jumlah Judul Publikasi Ilmiah DTPS</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-600 font-semibold">
                        <th class="p-4 w-12 text-center whitespace-nowrap" rowspan="2">No</th>
                        <th class="p-4 whitespace-nowrap" rowspan="2">Jenis Publikasi</th>
                        <th class="p-4 text-center border-l border-slate-200 whitespace-nowrap" colspan="3">Jumlah Judul</th>
                        <th class="p-4 text-center border-l border-slate-200 w-32 whitespace-nowrap" rowspan="2">Jumlah</th>
                    </tr>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 text-xs">
                        <th class="p-2 text-center border-l border-slate-100 w-24 whitespace-nowrap">TS-2 (<?= $years['ts2'] ?>)</th>
                        <th class="p-2 text-center border-l border-slate-100 w-24 whitespace-nowrap">TS-1 (<?= $years['ts1'] ?>)</th>
                        <th class="p-2 text-center border-l border-slate-100 w-24 whitespace-nowrap">TS (<?= $years['ts'] ?>)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php
                    $no = 1;
                    $grandTs2 = $grandTs1 = $grandTs = $grandTotal = 0;
                    foreach ($jenisLabels as $key => $label):
                        $ts2Val = $summary[$key]['ts2'] ?? 0;
                        $ts1Val = $summary[$key]['ts1'] ?? 0;
                        $tsVal  = $summary[$key]['ts'] ?? 0;
                        $rowTotal = $ts2Val + $ts1Val + $tsVal;
                        
                        $grandTs2 += $ts2Val;
                        $grandTs1 += $ts1Val;
                        $grandTs  += $tsVal;
                        $grandTotal += $rowTotal;
                    ?>
                    <tr>
                        <td class="p-4 text-center text-slate-400"><?= $no++ ?></td>
                        <td class="p-4 font-medium text-xs leading-normal max-w-[350px]"><?= $label ?></td>
                        <td class="p-4 text-center border-l border-slate-100 font-semibold"><?= $ts2Val ?></td>
                        <td class="p-4 text-center border-l border-slate-100 font-semibold"><?= $ts1Val ?></td>
                        <td class="p-4 text-center border-l border-slate-100 font-semibold"><?= $tsVal ?></td>
                        <td class="p-4 text-center border-l border-slate-200 font-bold bg-slate-50/30"><?= $rowTotal ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-slate-50 font-bold text-slate-800 border-t border-slate-200">
                        <td class="p-4 text-center" colspan="2">Jumlah</td>
                        <td class="p-4 text-center border-l border-slate-200"><?= $grandTs2 ?></td>
                        <td class="p-4 text-center border-l border-slate-200"><?= $grandTs1 ?></td>
                        <td class="p-4 text-center border-l border-slate-200"><?= $grandTs ?></td>
                        <td class="p-4 text-center border-l border-slate-200 bg-slate-100/50"><?= $grandTotal ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL 2: DETAIL DATA PUBLIKASI -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Detail Data Publikasi Ilmiah</h3>
            <form method="GET" action="<?= base_url('lecturers/publications/scientific') ?>" class="flex gap-2">
                <select name="period_id" onchange="this.form.submit()" class="col-span-1 sm:w-auto px-3 py-1.5 text-xs border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    <?php foreach ($periods as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $period_id == $p['id'] ? 'selected' : '' ?>><?= esc($p['nama_periode']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php if (empty($publications)): ?>
        <div class="p-8 sm:p-12 text-center flex flex-col items-center justify-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                <i data-lucide="folder-open" class="w-8 h-8"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                <p class="text-sm text-slate-500">Tidak ada detail data publikasi ilmiah.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center whitespace-nowrap">No</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nama Dosen</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Judul</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Jenis Publikasi</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Penerbit/Prosiding</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center whitespace-nowrap">Tahun</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($publications as $i => $pub): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 text-center text-slate-400"><?= $i + 1 ?></td>
                        <td class="p-4 font-semibold text-slate-800"><?= esc($pub['nama'] ?? '-') ?></td>
                        <td class="p-4">
                            <p class="text-slate-700 max-w-[250px] font-semibold leading-tight"><?= esc($pub['judul']) ?></p>
                            <?php if (!empty($pub['tautan'])): ?>
                            <a href="<?= esc($pub['tautan']) ?>" target="_blank" class="text-xs text-primary hover:underline flex items-center gap-0.5 mt-1">
                                <span>Lihat Dokumen</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                                <?= $jenisLabels[$pub['kategori_publikasi']] ?? $pub['kategori_publikasi'] ?>
                            </span>
                        </td>
                        <td class="p-4 text-slate-600 text-xs max-w-[150px] truncate"><?= esc($pub['penerbit'] ?? '-') ?></td>
                        <td class="p-4 text-center text-slate-600"><?= $pub['tahun'] ?></td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <?php if (in_array(session()->get('userRole'), ['admin', 'prodi', 'dosen'])): ?>
                                <button @click="openEdit('<?= $pub['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition-all cursor-pointer">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete('<?= $pub['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-red-600 transition-all cursor-pointer">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal Form Add/Edit -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen" role="dialog" aria-modal="true" x-cloak>
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="modalOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-md"></div>
        
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative w-full max-w-xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10">
            
            <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-50 flex flex-col items-center justify-center space-y-3">
                <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm text-slate-500 font-semibold">Memuat data...</span>
            </div>

            <form :action="formAction" method="POST" class="space-y-0">
                <?= csrf_field() ?>
                <input type="hidden" name="period_id" value="<?= $period_id ?>">

                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah Publikasi</h3>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Nama Lengkap Dosen *</label>
                        <input type="text" name="lecturer_name" x-model="form.lecturer_name" required placeholder="Ketik nama dosen" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Judul Publikasi *</label>
                        <textarea name="judul" x-model="form.judul" required rows="3" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Jenis / Kategori Publikasi</label>
                        <select onchange="this.form.submit()" name="kategori_publikasi" x-model="form.kategori_publikasi"  class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                            <option value="">Pilih Jenis</option>
                            <?php foreach ($jenisLabels as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Penerbit / Prosiding</label>
                            <input type="text" name="penerbit" x-model="form.penerbit" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Nomor ISSN / ISBN</label>
                            <input type="text" name="nomor_issn_isbn" x-model="form.nomor_issn_isbn" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Tahun</label>
                            <input type="number" name="tahun" x-model="form.tahun"  min="2000" max="2099" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Tingkat</label>
                            <select onchange="this.form.submit()" name="tingkat" x-model="form.tingkat" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="lokal">Lokal / Wilayah</option>
                                <option value="nasional">Nasional</option>
                                <option value="internasional">Internasional</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">URL / Tautan Publikasi</label>
                        <input type="url" name="tautan" x-model="form.tautan" placeholder="https://..." class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                    </div>
                </div>

                <div class="p-4 sm:p-6 border-t border-slate-100 bg-slate-50/50 grid grid-cols-2 sm:flex sm:flex-row justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="w-full sm:w-auto px-5 py-3 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary/95 shadow-md transition-all cursor-pointer">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete -->
    <div x-show="deleteOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" x-transition x-cloak>
        <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-xl border border-slate-200" @click.outside="deleteOpen = false">
            <div class="p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-800 text-lg">Konfirmasi Hapus</h3>
                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus data publikasi ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                <button @click="deleteOpen = false" class="w-full sm:w-auto px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition text-center">Batal</button>
                <a :href="deleteUrl" class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl text-sm transition text-center">Hapus Data</a>
            </div>
        </div>
    </div>

</div>
<?php endif; // end no_periods guard ?>
<?= $this->endSection() ?>
