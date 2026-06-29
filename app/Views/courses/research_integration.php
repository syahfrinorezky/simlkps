<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Integrasi Kegiatan',
    formAction: '',
    id: '',
    periodId: '<?= $selectedPeriod ?>',
    judulPenelitianPkm: '',
    namaDosen: '',
    courseId: '',
    bentukIntegrasi: '',
    tahun: new Date().getFullYear(),
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
        const deleteUrl = '<?= base_url('courses/research-integration/delete') ?>/' + id;
        this.$dispatch('open-delete-modal', {
            url: deleteUrl,
            title: 'Hapus Integrasi Kegiatan',
            message: 'Apakah Anda yakin ingin menghapus data integrasi kegiatan ini?'
        });
    },
    openAdd() {
        this.modalTitle = 'Tambah Integrasi Kegiatan';
        this.formAction = '<?= base_url('courses/research-integration/store') ?>';
        this.periodId = '<?= $selectedPeriod ?>';
        this.judulPenelitianPkm = '';
        this.namaDosen = '';
        this.courseId = '';
        this.bentukIntegrasi = '';
        this.tahun = new Date().getFullYear();
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Integrasi Kegiatan';
        this.formAction = '<?= base_url('courses/research-integration/update') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;
        fetch('<?= base_url('courses/research-integration/show') ?>/' + id)
            .then(res => res.json())
            .then(data => {
                this.periodId = data.period_id;
                this.judulPenelitianPkm = data.judul_penelitian_pkm;
                this.namaDosen = data.nama_dosen;
                this.courseId = data.course_id;
                this.bentukIntegrasi = data.bentuk_integrasi;
                this.tahun = data.tahun;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: 'Gagal memuat data.' });
            });
    }
}" x-init="totalRows = <?= count($integrations) ?>">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800"><?= esc($title) ?></h2>
            <p class="text-sm text-slate-500">Kelola integrasi hasil kegiatan penelitian dan pengabdian masyarakat (PkM) ke dalam proses pembelajaran.</p>
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
        <form action="<?= base_url('courses/research-integration') ?>" method="get" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
                    <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari berdasarkan judul, dosen, atau mata kuliah..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <?php if (empty($integrations)) : ?>
            <div class="p-12 text-center flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                    <i data-lucide="folder-open" class="w-8 h-8"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                    <p class="text-sm text-slate-500 max-w-sm">Data integrasi untuk kriteria yang dicari tidak ditemukan.</p>
                </div>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Penelitian/PkM</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Dosen</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Bentuk Integrasi</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24 text-center">Tahun</th>
                            <?php if ($canModify) : ?>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-28 text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $no = 0; foreach ($integrations as $i) : ?>
                            <tr class="hover:bg-slate-50/50 transition-all text-sm" data-row-idx="<?= $no ?>" x-show="isRowVisible(<?= $no ?>)">
                                <td class="p-4 text-slate-600 text-center font-medium"><?= $no + 1 ?></td>
                                <td class="p-4 font-semibold text-slate-800 leading-relaxed"><?= esc($i['judul_penelitian_pkm']) ?></td>
                                <td class="p-4 font-medium text-slate-700"><?= esc($i['nama_dosen']) ?></td>
                                <td class="p-4 font-bold text-primary">
                                    [<?= esc($i['kode_mk'] ?? '-') ?>] <?= esc($i['nama_mk'] ?? '-') ?>
                                </td>
                                <td class="p-4 text-slate-600 leading-relaxed"><?= esc($i['bentuk_integrasi']) ?></td>
                                <td class="p-4 text-center font-mono font-bold text-slate-700"><?= $i['tahun'] ?></td>
                                <?php if ($canModify) : ?>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button @click="openEdit('<?= $i['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" title="Edit">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="confirmDelete('<?= $i['id'] ?>')" class="p-1.5 hover:bg-red-50 rounded-lg text-slate-500 hover:text-red-600 transition" title="Hapus">
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
                <h3 class="font-bold text-slate-800 text-lg" x-text="modalTitle">Tambah Integrasi Kegiatan</h3>
                <button @click="modalOpen = false" class="p-2 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-full transition-all cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form :action="formAction" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="period_id" :value="periodId">
                
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-slate-600">Judul Kegiatan Penelitian/PkM</label>
                    <textarea name="judul_penelitian_pkm" x-model="judulPenelitianPkm" required placeholder="Judul Penelitian atau PkM..." rows="3" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Nama Dosen</label>
                        <input type="text" name="nama_dosen" x-model="namaDosen" required placeholder="Nama lengkap dosen..." class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Mata Kuliah</label>
                        <select name="course_id" x-model="courseId" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="">Pilih Mata Kuliah</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>">[<?= htmlspecialchars($c['kode_mk']) ?>] <?= htmlspecialchars($c['nama_mk']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Bentuk Integrasi</label>
                        <input type="text" name="bentuk_integrasi" x-model="bentukIntegrasi" required placeholder="Contoh: Tambahan materi kuliah..." class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-slate-600">Tahun (YYYY)</label>
                        <input type="number" name="tahun" x-model="tahun" required class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
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
