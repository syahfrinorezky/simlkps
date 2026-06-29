<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Mata Kuliah',
    formAction: '',
    id: '',
    periodId: '<?= $selectedPeriod ?>',
    semester: 1,
    kodeMk: '',
    namaMk: '',
    mkKompetensi: false,
    sksKuliah: 0,
    sksSeminar: 0,
    sksPraktikum: 0,
    konversiJam: 0,
    cplSikap: false,
    cplPengetahuan: false,
    cplKeterampilanUmum: false,
    cplKeterampilanKhusus: false,
    unitPenyelenggara: '',
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
        const deleteUrl = '<?= base_url('courses/curriculum/delete') ?>/' + id;
        this.$dispatch('open-delete-modal', {
            url: deleteUrl,
            title: 'Hapus Mata Kuliah',
            message: 'Apakah Anda yakin ingin menghapus data mata kuliah ini?'
        });
    },
    openAdd() {
        this.modalTitle = 'Tambah Mata Kuliah';
        this.formAction = '<?= base_url('courses/curriculum/store') ?>';
        this.periodId = '<?= $selectedPeriod ?>';
        this.semester = 1;
        this.kodeMk = '';
        this.namaMk = '';
        this.mkKompetensi = false;
        this.sksKuliah = 0;
        this.sksSeminar = 0;
        this.sksPraktikum = 0;
        this.konversiJam = 0;
        this.cplSikap = false;
        this.cplPengetahuan = false;
        this.cplKeterampilanUmum = false;
        this.cplKeterampilanKhusus = false;
        this.unitPenyelenggara = '';
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Mata Kuliah';
        this.formAction = '<?= base_url('courses/curriculum/update') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;
        fetch('<?= base_url('courses/curriculum/show') ?>/' + id)
            .then(res => res.json())
            .then(data => {
                this.periodId = data.period_id;
                this.semester = data.semester;
                this.kodeMk = data.kode_mk;
                this.namaMk = data.nama_mk;
                this.mkKompetensi = data.mk_kompetensi == 1;
                this.sksKuliah = data.sks_kuliah;
                this.sksSeminar = data.sks_seminar;
                this.sksPraktikum = data.sks_praktikum;
                this.konversiJam = data.konversi_jam;
                this.cplSikap = data.cpl_sikap == 1;
                this.cplPengetahuan = data.cpl_pengetahuan == 1;
                this.cplKeterampilanUmum = data.cpl_keterampilan_umum == 1;
                this.cplKeterampilanKhusus = data.cpl_keterampilan_khusus == 1;
                this.unitPenyelenggara = data.unit_penyelenggara;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: 'Gagal memuat data.' });
            });
    }
}" x-init="totalRows = <?= count($courses) ?>">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800"><?= esc($title) ?></h2>
            <p class="text-sm text-slate-500">Kelola data kurikulum, capaian pembelajaran, dan rencana pembelajaran.</p>
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
        <form action="<?= base_url('courses/curriculum') ?>" method="get" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
                    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari berdasarkan kode atau nama mata kuliah..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <?php if (empty($courses)) : ?>
            <div class="p-12 text-center flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                    <i data-lucide="folder-open" class="w-8 h-8"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                    <p class="text-sm text-slate-500 max-w-sm">Data kurikulum untuk kriteria yang dicari tidak ditemukan.</p>
                </div>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-center">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12" rowspan="2">No</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12" rowspan="2">Sem</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32" rowspan="2">Kode MK</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-left min-w-[200px]" rowspan="2">Nama Mata Kuliah</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24" rowspan="2">Kompetensi</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200" colspan="3">Bobot Kredit (SKS)</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200 w-24" rowspan="2">Konversi (Jam)</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200" colspan="4">Capaian Pembelajaran (CPL)</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200 min-w-[150px]" rowspan="2">Rencana Pembelajaran</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider border-l border-slate-200 min-w-[150px]" rowspan="2">Unit Penyelenggara</th>
                            <?php if ($canModify) : ?>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-28" rowspan="2">Aksi</th>
                            <?php endif; ?>
                        </tr>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 text-center">
                            <th class="p-2 border-l border-slate-100 w-16">Kuliah</th>
                            <th class="p-2 w-16">Seminar</th>
                            <th class="p-2 w-16">Praktikum</th>
                            <th class="p-2 border-l border-slate-200 w-16">Sikap</th>
                            <th class="p-2 w-16">Penget</th>
                            <th class="p-2 w-16">K.Umum</th>
                            <th class="p-2 w-16">K.Khus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $no = 0; foreach ($courses as $c) : ?>
                            <tr class="hover:bg-slate-50/50 transition-all text-sm" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                <td class="p-4 text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                <td class="p-4 text-center font-semibold text-slate-800"><?= $c['semester'] ?></td>
                                <td class="p-4 font-mono font-bold text-slate-700 text-center"><?= esc($c['kode_mk']) ?></td>
                                <td class="p-4 font-semibold text-slate-900"><?= esc($c['nama_mk']) ?></td>
                                <td class="p-4 text-center">
                                    <?= $c['mk_kompetensi'] ? '<span class="inline-flex px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-xs font-semibold">Ya</span>' : '<span class="inline-flex px-2 py-0.5 bg-slate-100 text-slate-500 border border-slate-200 rounded-full text-xs font-semibold">Tidak</span>' ?>
                                </td>
                                <td class="p-4 text-center border-l border-slate-100"><?= $c['sks_kuliah'] ?></td>
                                <td class="p-4 text-center"><?= $c['sks_seminar'] ?></td>
                                <td class="p-4 text-center"><?= $c['sks_praktikum'] ?></td>
                                <td class="p-4 text-center border-l border-slate-100 font-mono font-medium"><?= number_format($c['konversi_jam'], 2) ?></td>
                                <td class="p-4 text-center border-l border-slate-200">
                                    <?= $c['cpl_sikap'] ? '<i data-lucide="check" class="w-4 h-4 text-emerald-500 mx-auto"></i>' : '-' ?>
                                </td>
                                <td class="p-4 text-center">
                                    <?= $c['cpl_pengetahuan'] ? '<i data-lucide="check" class="w-4 h-4 text-emerald-500 mx-auto"></i>' : '-' ?>
                                </td>
                                <td class="p-4 text-center">
                                    <?= $c['cpl_keterampilan_umum'] ? '<i data-lucide="check" class="w-4 h-4 text-emerald-500 mx-auto"></i>' : '-' ?>
                                </td>
                                <td class="p-4 text-center">
                                    <?= $c['cpl_keterampilan_khusus'] ? '<i data-lucide="check" class="w-4 h-4 text-emerald-500 mx-auto"></i>' : '-' ?>
                                </td>
                                <td class="p-4 border-l border-slate-100 text-center">
                                    <?php if ($c['dokumen_rencana_pembelajaran']): ?>
                                        <a href="<?= base_url($c['dokumen_rencana_pembelajaran']) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:text-primary transition-all">
                                            <i data-lucide="download" class="w-3.5 h-3.5 text-primary"></i>
                                            <span>Unduh</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-medium">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 border-l border-slate-100 font-medium text-slate-600"><?= esc($c['unit_penyelenggara']) ?></td>
                                <?php if ($canModify) : ?>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button @click="openEdit('<?= $c['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="confirmDelete('<?= $c['id'] ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
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
                <h3 class="font-bold text-slate-800 text-lg" x-text="modalTitle">Tambah Mata Kuliah</h3>
                <button @click="modalOpen = false" class="p-2 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-full transition-all cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form :action="formAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="period_id" :value="periodId">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Semester</label>
                        <input type="number" name="semester" x-model="semester" required min="1" max="8" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Kode Mata Kuliah</label>
                        <input type="text" name="kode_mk" x-model="kodeMk" required placeholder="Contoh: KB123" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk" x-model="namaMk" required placeholder="Nama matakuliah..." class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <div class="flex items-center gap-2 py-1">
                    <input type="checkbox" name="mk_kompetensi" id="mk_kompetensi" x-model="mkKompetensi" class="w-4 h-4 text-primary focus:ring-primary border-slate-300 rounded">
                    <label for="mk_kompetensi" class="text-sm font-semibold text-slate-700">Mata Kuliah Kompetensi</label>
                </div>

                <hr class="border-slate-100 my-2">
                <h4 class="font-bold text-slate-800 text-sm">Bobot Kredit (SKS) & Konversi</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Kuliah/Responsi</label>
                        <input type="number" name="sks_kuliah" x-model="sksKuliah" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Seminar</label>
                        <input type="number" name="sks_seminar" x-model="sksSeminar" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Praktikum</label>
                        <input type="number" name="sks_praktikum" x-model="sksPraktikum" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Konversi Kredit ke Jam</label>
                        <input type="number" step="0.01" name="konversi_jam" x-model="konversiJam" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <hr class="border-slate-100 my-2">
                <h4 class="font-bold text-slate-800 text-sm">Capaian Pembelajaran (CPL)</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="cpl_sikap" x-model="cplSikap" class="w-4 h-4 text-primary focus:ring-primary border-slate-300 rounded">
                        <span class="text-xs font-semibold text-slate-700">Sikap</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="cpl_pengetahuan" x-model="cplPengetahuan" class="w-4 h-4 text-primary focus:ring-primary border-slate-300 rounded">
                        <span class="text-xs font-semibold text-slate-700">Pengetahuan</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="cpl_keterampilan_umum" x-model="cplKeterampilanUmum" class="w-4 h-4 text-primary focus:ring-primary border-slate-300 rounded">
                        <span class="text-xs font-semibold text-slate-700">K. Umum</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="cpl_keterampilan_khusus" x-model="cplKeterampilanKhusus" class="w-4 h-4 text-primary focus:ring-primary border-slate-300 rounded">
                        <span class="text-xs font-semibold text-slate-700">K. Khusus</span>
                    </label>
                </div>

                <hr class="border-slate-100 my-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Dokumen Rencana Pembelajaran (.pdf)</label>
                        <input type="file" name="dokumen_rencana_pembelajaran" accept=".pdf" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Unit Penyelenggara</label>
                        <input type="text" name="unit_penyelenggara" x-model="unitPenyelenggara" required placeholder="Contoh: Jurusan Teknik Elektro..." class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
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
