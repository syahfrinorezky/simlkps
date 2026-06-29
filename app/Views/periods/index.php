<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Manajemen Periode Pelaporan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{
    modalOpen: false,
    modalTitle: 'Tambah Periode Pelaporan',
    formAction: '',
    id: null,
    namaPeriode: '',
    tahunAkademik: '',

    openAdd() {
        this.id = null;
        this.formAction = '<?= base_url('periods/store') ?>';
        this.modalTitle = 'Tambah Periode Baru';
        this.namaPeriode = '';
        this.tahunAkademik = '';
        this.modalOpen = true;
    },
    openEdit(item) {
        this.id = item.id;
        this.formAction = '<?= base_url('periods/update') ?>/' + item.id;
        this.modalTitle = 'Edit Periode Pelaporan';
        this.namaPeriode = item.nama_periode;
        this.tahunAkademik = item.tahun_akademik;
        this.modalOpen = true;
    },
    confirmDelete(id) {
        const deleteUrl = '<?= base_url('periods/delete') ?>/' + id;
        this.$dispatch('open-delete-modal', {
            url: deleteUrl,
            title: 'Hapus Periode Pelaporan',
            message: 'Apakah Anda yakin ingin menghapus periode ini? Tindakan ini juga akan menghapus data transaksi lain yang terkait dengan periode ini!'
        });
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Periode Pelaporan</h2>
            <p class="text-sm text-slate-500">Kelola tahun akademik pelaporan kinerja program studi (TS, TS-1, TS-2, dll.) untuk SIM-LKPS.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openAdd()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Periode
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
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
                <span>Terdapat kesalahan input:</span>
            </div>
            <ul class="list-disc pl-8 space-y-1 text-xs">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if (empty($periods)): ?>
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-20 text-center space-y-5 px-6">
                <div class="w-20 h-20 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-400 shadow-sm">
                    <i data-lucide="calendar-x" class="w-10 h-10"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-slate-800">Belum Ada Periode Pelaporan</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
                        Tambahkan periode akademik pertama untuk memulai menggunakan SIM-LKPS.
                        Semua modul pelaporan memerlukan setidaknya satu periode yang terdaftar.
                    </p>
                </div>
                <button @click="openAdd()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-xl text-sm transition-all shadow-sm cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Periode Pertama
                </button>
            </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-16 text-center">No</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Periode</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun Akademik</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php $no = 1; foreach ($periods as $p): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 text-center font-medium text-slate-400"><?= $no++ ?></td>
                        <td class="p-4 font-semibold text-slate-800"><?= esc($p['nama_periode']) ?></td>
                        <td class="p-4 font-mono font-medium text-slate-600"><?= esc($p['tahun_akademik']) ?></td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit(<?= esc(json_encode($p)) ?>)" 
                                        class="p-1.5 hover:bg-slate-100 border border-slate-200 hover:border-slate-300 rounded-lg text-slate-500 hover:text-primary transition cursor-pointer" 
                                        title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete(<?= $p['id'] ?>)" 
                                        class="p-1.5 hover:bg-red-50 border border-slate-200 hover:border-red-200 rounded-lg text-slate-500 hover:text-red-600 transition cursor-pointer" 
                                        title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
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
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none" x-cloak>
        <!-- backdrop -->
        <div x-show="modalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
             @click="modalOpen = false"></div>
        
        <!-- modal content -->
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-md bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10"
        >
            <form :action="formAction" method="POST" class="space-y-0">
                <?= csrf_field() ?>

                <!-- Header Modal -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah Periode Baru</h3>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Periode *</label>
                        <input type="text" name="nama_periode" x-model="namaPeriode" required placeholder="Contoh: Periode 2024/2025" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tahun Akademik * (Format: YYYY/YYYY)</label>
                        <input type="text" name="tahun_akademik" x-model="tahunAkademik" required placeholder="e.g. 2024/2025" pattern="^\d{4}\/\d{4}$" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        <p class="text-[10px] text-slate-400 mt-1.5">Gunakan format 4 digit tahun awal dan tahun akhir dipisahkan garis miring (/).</p>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-xl text-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary/95 text-white font-medium rounded-xl text-sm transition shadow-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
