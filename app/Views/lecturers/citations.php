<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Sitasi Karya Ilmiah<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    detailOpen: false,
    detailData: {},
    deleteOpen: false,
    deleteUrl: '',
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Sitasi',
    formAction: '',
    form: {
        lecturer_name: '', judul_artikel: '', jumlah_sitasi: 0
    },
    openAdd() {
        this.modalTitle = 'Tambah Sitasi';
        this.formAction = '<?= base_url('lecturers/citations/store') ?>';
        this.form = {
            lecturer_name: '', judul_artikel: '', jumlah_sitasi: 0
        };
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Sitasi';
        this.formAction = '<?= base_url('lecturers/citations/update') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;
        fetch('<?= base_url('lecturers/citations/show') ?>/' + id)
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
        this.deleteUrl = '<?= base_url('lecturers/citations/delete') ?>/' + id;
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
            <h2 class="text-2xl font-bold text-slate-800">Sitasi Artikel Dosen</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 hidden sm:block">Data Sitasi Artikel Dosen Tetap</p>
        </div>
        <?php if (in_array(session()->get('userRole'), ['admin', 'prodi', 'dosen'])): ?>
        <button @click="openAdd()"
            class="inline-flex items-center justify-center gap-2 p-2 sm:px-4 sm:py-2 bg-primary self-start sm:self-auto hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i><span class="hidden sm:inline">
            Tambah Sitasi
        </span></button>
        <?php endif; ?>
    </div>

    <!-- Stats -->
    <div class="flex overflow-x-auto sm:grid sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 pb-2 sm:pb-0 snap-x hide-scrollbar ">
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">Total Artikel</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['total'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">Total Sitasi</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['total_sitasi'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">Rata-rata Sitasi</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['avg_sitasi'] ?></p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" action="<?= base_url('lecturers/publications/creative-works') ?>" class="grid grid-cols-2 sm:flex sm:flex-row gap-3 w-full">
            <select name="period_id" class="col-span-1 sm:w-auto px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-slate-50 text-slate-700 transition-all">
                <?php foreach ($periods as $p): ?><option value="<?= $p['id'] ?>" <?= $period_id == $p['id'] ? 'selected' : '' ?>><?= esc($p['nama_periode']) ?></option><?php endforeach; ?>
            </select>
            <div class="relative col-span-2 sm:flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari judul artikel atau nama dosen..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
            </div>
            <button type="submit" class="col-span-2 sm:w-auto px-4 py-2 bg-primary self-start sm:self-auto hover:bg-primary/95 text-white text-sm font-semibold rounded-xl transition-all cursor-pointer">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if (empty($citations)): ?>
        <div class="p-8 sm:p-12 text-center flex flex-col items-center justify-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                <i data-lucide="folder-open" class="w-8 h-8"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                <p class="text-sm text-slate-500">Tidak ada data sitasi dosen.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center whitespace-nowrap">No</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nama Dosen</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Judul Artikel yang Disitasi</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center whitespace-nowrap">Jumlah Sitasi</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($citations as $i => $c): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 text-center text-slate-400"><?= $i + 1 ?></td>
                        <td class="p-4 font-semibold text-slate-800"><?= esc($c['nama'] ?? '-') ?></td>
                        <td class="p-4 text-slate-700 max-w-[300px] font-medium leading-tight"><?= esc($c['judul_artikel']) ?></td>
                        <td class="p-4 text-center">
                            <span class="inline-flex text-sm font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700"><?= $c['jumlah_sitasi'] ?></span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <?php if (in_array(session()->get('userRole'), ['admin', 'prodi', 'dosen'])): ?>
                                <button @click="openEdit('<?= $c['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition-all cursor-pointer">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete('<?= $c['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-red-600 transition-all cursor-pointer">
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
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah Sitasi</h3>
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
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Judul Artikel *</label>
                        <textarea name="judul_artikel" x-model="form.judul_artikel" required rows="3" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Jumlah Sitasi</label>
                        <input step="any" type="number" name="jumlah_sitasi" x-model="form.jumlah_sitasi"  min="0" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
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
                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus data sitasi ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                <button @click="deleteOpen = false" class="w-full sm:w-auto px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition text-center">Batal</button>
                <a :href="deleteUrl" class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl text-sm transition text-center">Hapus Data</a>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
