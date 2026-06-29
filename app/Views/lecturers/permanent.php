<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Dosen Tetap<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6" x-data="{
    detailOpen: false,
    detailData: {},
    deleteOpen: false,
    deleteUrl: '',
    loading: false,
    modalOpen: false,
    modalTitle: 'Tambah Dosen Tetap',
    formAction: '',
    form: {
        nama: '', nidn: '',
        pendidikan_magister: '', pendidikan_doktor: '',
        bidang_keahlian: '', kesesuaian_kompetensi: '',
        jabatan_akademik: '', is_dtps: true,
        sertifikat_pendidik: '1', sertifikat_kompetensi: '',
        mata_kuliah_diampu: '', kesesuaian_bidang_mk: '',
    },
    openAdd() {
        this.modalTitle = 'Tambah Dosen Tetap';
        this.formAction = '<?= base_url('lecturers/permanent/store') ?>';
        this.form = {
            nama: '', nidn: '',
            pendidikan_magister: '', pendidikan_doktor: '',
            bidang_keahlian: '', kesesuaian_kompetensi: '',
            jabatan_akademik: '', is_dtps: true,
            sertifikat_pendidik: '1', sertifikat_kompetensi: '',
            mata_kuliah_diampu: '', kesesuaian_bidang_mk: '',
        };
        this.modalOpen = true;
    },
    openEdit(id) {
        this.modalTitle = 'Edit Dosen Tetap';
        this.formAction = '<?= base_url('lecturers/permanent/update') ?>/' + id;
        this.loading = true;
        this.modalOpen = true;
        fetch('<?= base_url('lecturers/permanent/show') ?>/' + id)
            .then(res => res.json())
            .then(data => {
                // Map database values (0/1) to boolean for checkbox
                data.is_dtps = data.is_dtps == 1;
                this.form = data;
                this.loading = false;
            })
            .catch(err => {
                this.loading = false;
                this.modalOpen = false;
                this.$dispatch('show-toast', { type: 'error', message: 'Gagal memuat data.' });
            });
    },
    confirmDelete(id, name) {
        this.deleteUrl = '<?= base_url('lecturers/permanent/delete') ?>/' + id;
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
            <h2 class="text-2xl font-bold text-slate-800">Dosen Tetap</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 hidden sm:block">Data Dosen Tetap Program Studi</p>
        </div>
        <?php if (in_array(session()->get('userRole'), ['admin', 'prodi'])): ?>
        <button @click="openAdd()"
            class="inline-flex items-center justify-center gap-2 p-2 sm:px-4 sm:py-2 bg-primary self-start sm:self-auto hover:bg-primary/95 text-white font-medium rounded-xl shadow-sm transition-all text-sm cursor-pointer">
            <i data-lucide="plus" class="w-4 h-4"></i><span class="hidden sm:inline">
            Tambah Dosen Tetap
        </span></button>
        <?php endif; ?>
    </div>

    <!-- Stats Cards -->
    <div class="flex overflow-x-auto sm:grid gap-3 sm:gap-4 pb-2 sm:pb-0 snap-x hide-scrollbar sm:sm:grid-cols-2 lg:grid-cols-4 sm: pb-2 sm:pb-0 snap-x">
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">Total DTPS</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['total'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">DTPS Aktif</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['dtps'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">Bergelar Doktor</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['doktor'] ?></p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-4 sm:p-5 shadow-sm min-w-[160px] sm:min-w-0 snap-start shrink-0">
            <p class="text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-3">Guru Besar</p>
            <p class="text-xl sm:text-3xl font-bold text-slate-800"><?= $stats['gb'] ?></p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" action="<?= base_url('lecturers/permanent') ?>" class="grid grid-cols-2 sm:flex sm:flex-row gap-3 w-full">
            <div class="relative col-span-2 sm:flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>"
                    placeholder="Cari nama, NIDN/NIDK, bidang keahlian..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all">
            </div>
            <select onchange="this.form.submit()" name="jabatan_akademik"
                class="col-span-1 sm:w-auto px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-slate-50 text-slate-700 transition-all">
                <option value="">Semua Jabatan</option>
                <option value="tenaga_pengajar" <?= ($filters['jabatan_akademik'] ?? '') === 'tenaga_pengajar' ? 'selected' : '' ?>>Tenaga Pengajar</option>
                <option value="asisten_ahli" <?= ($filters['jabatan_akademik'] ?? '') === 'asisten_ahli' ? 'selected' : '' ?>>Asisten Ahli</option>
                <option value="lektor" <?= ($filters['jabatan_akademik'] ?? '') === 'lektor' ? 'selected' : '' ?>>Lektor</option>
                <option value="lektor_kepala" <?= ($filters['jabatan_akademik'] ?? '') === 'lektor_kepala' ? 'selected' : '' ?>>Lektor Kepala</option>
                <option value="guru_besar" <?= ($filters['jabatan_akademik'] ?? '') === 'guru_besar' ? 'selected' : '' ?>>Guru Besar</option>
            </select>
            <select onchange="this.form.submit()" name="is_dtps"
                class="col-span-1 sm:w-auto px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary bg-slate-50 text-slate-700 transition-all">
                <option value="">Semua Status</option>
                <option value="1" <?= ($filters['is_dtps'] ?? '') === '1' ? 'selected' : '' ?>>DTPS</option>
                <option value="0" <?= ($filters['is_dtps'] ?? '') === '0' ? 'selected' : '' ?>>Non-DTPS</option>
            </select>
            
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if (empty($lecturers)): ?>
        <div class="p-8 sm:p-12 text-center flex flex-col items-center justify-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                <i data-lucide="folder-open" class="w-8 h-8"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-slate-700">Belum Ada Data</h3>
                <p class="text-sm text-slate-500">Tidak ada data dosen tetap.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center whitespace-nowrap">No</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Nama / NIDN/NIDK</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Pendidikan</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Bidang Keahlian</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Jabatan</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Sertifikat</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-24 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($lecturers as $i => $lec): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 text-center text-slate-400"><?= $i + 1 ?></td>
                        <td class="p-4 font-semibold text-slate-800">
                            <?= esc($lec['nama']) ?>
                            <div class="text-xs font-normal text-slate-400 mt-0.5"><?= esc($lec['nidn'] ?? $lec['nidk'] ?? '-') ?></div>
                        </td>
                        <td class="p-4">
                            <?php if (!empty($lec['pendidikan_doktor'])): ?>
                            <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full bg-violet-50 text-violet-700">S3 / Doktor</span>
                            <?php elseif (!empty($lec['pendidikan_magister'])): ?>
                            <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">S2 / Magister</span>
                            <?php else: ?>
                            <span class="text-slate-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <span class="text-slate-600 font-medium"><?= esc($lec['bidang_keahlian'] ?? '-') ?></span>
                            <?php if (!empty($lec['kesesuaian_kompetensi'])): ?>
                            <div class="text-xs mt-0.5 <?= $lec['kesesuaian_kompetensi'] === 'sesuai' ? 'text-emerald-600' : 'text-red-500' ?>">
                                <?= $lec['kesesuaian_kompetensi'] === 'sesuai' ? '✓ Sesuai' : '✗ Tidak Sesuai' ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <?php
                            $jabs = [
                                'tenaga_pengajar' => 'Tenaga Pengajar', 'asisten_ahli' => 'Asisten Ahli',
                                'lektor' => 'Lektor', 'lektor_kepala' => 'Lektor Kepala', 'guru_besar' => 'Guru Besar'
                            ];
                            echo esc($jabs[$lec['jabatan_akademik']] ?? $lec['jabatan_akademik'] ?? '-');
                            ?>
                        </td>
                        <td class="p-4">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?= $lec['sertifikat_pendidik'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                <?= $lec['sertifikat_pendidik'] ? 'Pendidik' : 'Belum' ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $lec['is_dtps'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                <?= $lec['is_dtps'] ? 'DTPS' : 'Non-DTPS' ?>
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <?php if (in_array(session()->get('userRole'), ['admin', 'prodi'])): ?>
                                <button @click="openEdit('<?= $lec['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-primary transition-all cursor-pointer">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="confirmDelete('<?= $lec['id'] ?>')" class="p-1.5 hover:bg-slate-100 rounded-lg text-slate-500 hover:text-red-600 transition-all cursor-pointer">
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
        
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4" class="relative w-full max-w-2xl bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10">
            
            <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-50 flex flex-col items-center justify-center space-y-3">
                <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm text-slate-500 font-semibold">Memuat data...</span>
            </div>

            <form :action="formAction" method="POST" class="space-y-0">
                <?= csrf_field() ?>

                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah Dosen Tetap</h3>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Nama Lengkap Dosen *</label>
                            <input type="text" name="nama" x-model="form.nama" required class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">NIDN / NIDK</label>
                            <input type="text" name="nidn" x-model="form.nidn" placeholder="Masukkan NIDN atau NIDK dosen" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Pendidikan S2 / Magister <span class="text-slate-400 font-normal">(Tidak Wajib/Opsional)</span></label>
                            <input type="text" name="pendidikan_magister" x-model="form.pendidikan_magister" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Pendidikan S3 / Doktor <span class="text-slate-400 font-normal">(Tidak Wajib/Opsional)</span></label>
                            <input type="text" name="pendidikan_doktor" x-model="form.pendidikan_doktor" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Bidang Keahlian</label>
                            <input type="text" name="bidang_keahlian" x-model="form.bidang_keahlian"  class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Kesesuaian Kompetensi</label>
                            <select onchange="this.form.submit()" name="kesesuaian_kompetensi" x-model="form.kesesuaian_kompetensi" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="">Pilih</option>
                                <option value="sesuai">Sesuai</option>
                                <option value="tidak_sesuai">Tidak Sesuai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Jabatan Akademik</label>
                            <select onchange="this.form.submit()" name="jabatan_akademik" x-model="form.jabatan_akademik"  class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="">Pilih Jabatan</option>
                                <option value="tenaga_pengajar">Tenaga Pengajar</option>
                                <option value="asisten_ahli">Asisten Ahli</option>
                                <option value="lektor">Lektor</option>
                                <option value="lektor_kepala">Lektor Kepala</option>
                                <option value="guru_besar">Guru Besar</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="is_dtps" :checked="form.is_dtps" @change="form.is_dtps = $event.target.checked" value="1" class="w-5 h-5 text-primary border-slate-300 rounded-lg focus:ring-primary/30">
                                <span class="text-sm font-semibold text-slate-700">Dosen Tetap Program Studi (DTPS)</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Sertifikat Pendidik</label>
                            <select onchange="this.form.submit()" name="sertifikat_pendidik" x-model="form.sertifikat_pendidik" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                                <option value="1">Punya</option>
                                <option value="0">Tidak Punya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Sertifikat Kompetensi</label>
                            <input type="text" name="sertifikat_kompetensi" x-model="form.sertifikat_kompetensi" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <label class="block text-[10px] sm:text-[10px] sm:text-xs font-semibold text-slate-500 uppercase truncate tracking-wider mb-2">Mata Kuliah yang Diampu pada PS yang Diakreditasi</label>
                        <textarea name="mata_kuliah_diampu" x-model="form.mata_kuliah_diampu" rows="2" class="w-full px-3 py-2 sm:px-4 sm:py-3 bg-slate-50/50 text-sm border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all"></textarea>
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
                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus data dosen ini? Tindakan ini tidak dapat dibatalkan.</p>
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
