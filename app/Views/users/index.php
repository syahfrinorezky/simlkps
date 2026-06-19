<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Manajemen User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div x-data="{
    searchQuery: '',
    roleFilter: '',
    showTrash: false,
    modalOpen: false,
    modalTitle: 'Tambah User Baru',
    formAction: '',
    userId: null,
    namaLengkap: '',
    email: '',
    telepon: '',
    password: '',
    passwordRequired: true,
    roleId: '',
    studyProgramId: '',
    lecturerId: '',
    isActive: '1',
    showProdi: false,
    showDosen: false,
    showStatus: false,
    tomSelectProdi: null,
    tomSelectDosen: null,
    rolesMap: {
        <?php foreach ($roles as $r): ?>
        '<?= esc($r['id']) ?>': '<?= esc($r['nama']) ?>',
        <?php endforeach; ?>
    },
    openAdd() {
        this.userId = null;
        this.formAction = '<?= base_url('users/store') ?>';
        this.modalTitle = 'Tambah User Baru';
        this.namaLengkap = '';
        this.email = '';
        this.telepon = '';
        this.password = '';
        this.passwordRequired = true;
        this.roleId = '';
        this.studyProgramId = '';
        this.lecturerId = '';
        this.isActive = '1';
        this.showProdi = false;
        this.showDosen = false;
        this.showStatus = false;
        if (this.tomSelectProdi) this.tomSelectProdi.setValue('');
        if (this.tomSelectDosen) this.tomSelectDosen.setValue('');
        this.modalOpen = true;
    },
    openEdit(id) {
        this.userId = id;
        this.formAction = '<?= base_url('users/update') ?>/' + id;
        this.modalTitle = 'Edit Data User';
        this.password = '';
        this.passwordRequired = false;
        this.showStatus = true;

        fetch('<?= base_url('users/edit') ?>/' + id)
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data user.');
                return res.json();
            })
            .then(data => {
                this.namaLengkap = data.nama_lengkap;
                this.email = data.email;
                this.telepon = data.telepon || '';
                this.roleId = data.role_id;
                this.isActive = data.is_active;
                this.handleRoleChange(data.role_id);
                this.studyProgramId = data.study_program_id || '';
                this.lecturerId = data.lecturer_id || '';
                this.$nextTick(() => {
                    if (this.tomSelectProdi) this.tomSelectProdi.setValue(data.study_program_id || '');
                    if (this.tomSelectDosen) this.tomSelectDosen.setValue(data.lecturer_id || '');
                });
                this.modalOpen = true;
            })
            .catch(err => this.$dispatch('show-toast', { type: 'error', message: err.message }));
    },
    handleRoleChange(roleId) {
        const roleName = this.rolesMap[roleId] || '';
        this.showProdi = (roleName === 'prodi');
        this.showDosen = (roleName === 'dosen');
        if (!this.showProdi) {
            this.studyProgramId = '';
            if (this.tomSelectProdi) this.tomSelectProdi.setValue('');
        }
        if (!this.showDosen) {
            this.lecturerId = '';
            if (this.tomSelectDosen) this.tomSelectDosen.setValue('');
        }
    }
}">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen User</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data pengguna, hak akses role, serta hubungkan akun dengan data Dosen atau Program Studi.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showTrash = !showTrash" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all cursor-pointer">
                <template x-if="!showTrash">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </template>
                <template x-if="showTrash">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </template>
                <span x-text="showTrash ? 'Lihat User Aktif' : 'Lihat Sampah (<?= count($trash) ?>)'">Lihat Sampah</span>
            </button>
            <button @click="openAdd()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary/95 shadow-sm transition-all cursor-pointer">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Tambah User
            </button>
        </div>
    </div>

    <!-- flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 p-4 bg-emerald-50 border border-green-200 text-emerald-700 rounded-xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0"></i>
        <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl space-y-2">
        <div class="flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0"></i>
            <span class="text-sm font-semibold">Terdapat kesalahan input:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1 pl-8">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- card n grid data -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- filter n search -->
        <div class="p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative w-full sm:max-w-xs">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input x-model="searchQuery" type="text" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>
            <div class="w-full sm:w-auto">
                <select x-model="roleFilter" class="w-full sm:w-48 px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    <option value="">Semua Role</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= esc($r['nama']) ?>"><?= esc(ucfirst($r['nama'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- tabel user aktif -->
        <div class="overflow-x-auto" x-show="!showTrash">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 text-xs font-semibold uppercase bg-slate-50/30">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Relasi Data</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium">Belum ada data user.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <!-- row dengan state alpine per baris untuk toggle status reaktif -->
                        <tr class="hover:bg-slate-50/50 transition-colors"
                            x-data="{ isActive: <?= $user['is_active'] ? 'true' : 'false' ?>, toggling: false }"
                            x-show="(searchQuery === '' || '<?= esc(strtolower($user['nama_lengkap'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= esc(strtolower($user['email'])) ?>'.includes(searchQuery.toLowerCase())) && (roleFilter === '' || '<?= esc($user['role_name']) ?>' === roleFilter)">
                            <td class="px-6 py-4.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary/5 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                                        <?= esc(strtoupper(substr($user['nama_lengkap'], 0, 2))) ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800 truncate"><?= esc($user['nama_lengkap']) ?></div>
                                        <div class="text-xs text-slate-400 truncate"><?= esc($user['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold tracking-wide capitalize
                                    <?php
                                        switch ($user['role_name']) {
                                            case 'admin': echo 'bg-blue-50 text-blue-700 border border-blue-200'; break;
                                            case 'prodi': echo 'bg-amber-50 text-amber-700 border border-amber-200'; break;
                                            case 'dosen': echo 'bg-emerald-50 text-emerald-700 border border-green-200'; break;
                                            default: echo 'bg-slate-50 text-slate-600 border border-slate-200'; break;
                                        }
                                    ?>">
                                    <?= esc($user['role_name']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4.5">
                                <div class="text-xs space-y-1">
                                    <?php if ($user['role_name'] === 'dosen' && $user['lecturer_name']): ?>
                                        <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                                            <i data-lucide="contact" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span><?= esc($user['lecturer_name']) ?></span>
                                        </div>
                                        <div class="text-slate-400 pl-5">NIDN: <?= esc($user['nidn'] ?: '-') ?></div>
                                    <?php elseif ($user['role_name'] === 'prodi' && $user['nama_prodi']): ?>
                                        <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                                            <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span><?= esc($user['nama_prodi']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4.5 text-center">
                                <!-- toggle status reaktif alpine per baris, tanpa manipulasi DOM langsung -->
                                <button
                                    :disabled="toggling"
                                    @click="
                                        toggling = true;
                                        fetch('<?= base_url('users/toggle/' . $user['id']) ?>', {
                                            method: 'POST',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                                            }
                                        })
                                        .then(res => { if (!res.ok) throw new Error('Gagal mengubah status.'); return res.json(); })
                                        .then(data => {
                                            if (data.success) {
                                                isActive = data.is_active;
                                                $dispatch('show-toast', { type: 'success', message: isActive ? 'User diaktifkan.' : 'User dinonaktifkan.' });
                                            }
                                        })
                                        .catch(err => $dispatch('show-toast', { type: 'error', message: err.message }))
                                        .finally(() => toggling = false);
                                    "
                                    class="inline-flex cursor-pointer transition-transform active:scale-95 disabled:opacity-60"
                                >
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold leading-none"
                                        :class="isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'"
                                        x-text="isActive ? 'Aktif' : 'Nonaktif'"
                                    ></span>
                                </button>
                            </td>
                            <td class="px-6 py-4.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit(<?= $user['id'] ?>)" class="p-2 text-slate-400 hover:text-primary hover:bg-slate-50 rounded-lg transition-colors cursor-pointer" title="Edit User">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <?php if ($user['id'] != session()->get('userId')): ?>
                                        <button @click="$dispatch('open-delete-modal', { url: '<?= base_url('users/delete/' . $user['id']) ?>', title: 'Hapus User', message: 'Apakah Anda yakin ingin menghapus user <?= esc($user['nama_lengkap']) ?>? Data akan dipindahkan ke tempat sampah.' })" class="p-2 text-slate-400 hover:text-red-600 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer" title="Hapus User">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- tabel sampah -->
        <div class="overflow-x-auto" x-show="showTrash" x-cloak>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 text-xs font-semibold uppercase bg-slate-50/30">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Relasi Data</th>
                        <th class="px-6 py-4 text-center">Sisa Waktu</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                    <?php if (empty($trash)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium">Tempat sampah kosong.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trash as $user): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors"
                            x-show="(searchQuery === '' || '<?= esc(strtolower($user['nama_lengkap'])) ?>'.includes(searchQuery.toLowerCase()) || '<?= esc(strtolower($user['email'])) ?>'.includes(searchQuery.toLowerCase())) && (roleFilter === '' || '<?= esc($user['role_name']) ?>' === roleFilter)">
                            <td class="px-6 py-4.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-sm shrink-0">
                                        <?= esc(strtoupper(substr($user['nama_lengkap'], 0, 2))) ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800 truncate"><?= esc($user['nama_lengkap']) ?></div>
                                        <div class="text-xs text-slate-400 truncate"><?= esc($user['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold tracking-wide capitalize bg-slate-50 text-slate-600 border border-slate-200">
                                    <?= esc($user['role_name']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4.5">
                                <div class="text-xs space-y-1">
                                    <?php if ($user['role_name'] === 'dosen' && $user['lecturer_name']): ?>
                                        <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                            <i data-lucide="contact" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span><?= esc($user['lecturer_name']) ?></span>
                                        </div>
                                    <?php elseif ($user['role_name'] === 'prodi' && $user['nama_prodi']): ?>
                                        <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                                            <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span><?= esc($user['nama_prodi']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4.5 text-center">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700">
                                    <i data-lucide="clock" class="w-3 h-3 text-amber-600 shrink-0"></i>
                                    <?= esc($user['countdown']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= base_url('users/restore/' . $user['id']) ?>" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer" title="Kembalikan User">
                                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    </a>
                                    <button @click="$dispatch('open-delete-modal', { url: '<?= base_url('users/purge/' . $user['id']) ?>', title: 'Hapus Permanen', message: 'Apakah Anda yakin ingin menghapus user <?= esc($user['nama_lengkap']) ?> secara permanen? Data ini akan terhapus selamanya dari database.' })" class="p-2 text-slate-400 hover:text-red-600 hover:bg-slate-50 rounded-lg transition-colors cursor-pointer" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- modal form add/edit -->
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

        <!-- konten modal -->
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10"
        >
            <form :action="formAction" method="POST" class="space-y-0">
                <?= csrf_field() ?>

                <!-- header modal -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight" x-text="modalTitle">Tambah User Baru</h3>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- body modal -->
                <div class="p-6 space-y-4.5 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" x-model="namaLengkap" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Alamat Email</label>
                            <input type="email" name="email" x-model="email" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">No. Telepon</label>
                            <input type="text" name="telepon" x-model="telepon" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" x-model="password" :required="passwordRequired" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all">
                        <p class="text-xs text-slate-400 mt-1.5" x-show="!passwordRequired">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Hak Akses Role</label>
                        <select name="role_id" id="role_id" x-model="roleId" @change="handleRoleChange($event.target.value)" required class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                            <option value="">Pilih Role</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= esc($r['id']) ?>" data-name="<?= esc($r['nama']) ?>"><?= esc(ucfirst($r['nama'])) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- relasi program studi (role prodi) -->
                    <div x-show="showProdi" x-transition>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Program Studi</label>
                        <select name="study_program_id" x-model="studyProgramId" x-init="tomSelectProdi = new TomSelect($el, { create: false, placeholder: 'Pilih Program Studi' })" @change="studyProgramId = $event.target.value" :required="showProdi" class="w-full">
                            <option value="">Pilih Program Studi</option>
                            <?php foreach ($studyPrograms as $sp): ?>
                                <option value="<?= esc($sp['id']) ?>"><?= esc($sp['nama_prodi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- relasi dosen (role dosen) -->
                    <div x-show="showDosen" x-transition>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Data Dosen</label>
                        <select name="lecturer_id" x-model="lecturerId" x-init="tomSelectDosen = new TomSelect($el, { create: false, placeholder: 'Pilih Dosen' })" @change="lecturerId = $event.target.value" :required="showDosen" class="w-full">
                            <option value="">Pilih Dosen</option>
                            <?php foreach ($lecturers as $l): ?>
                                <option value="<?= esc($l['id']) ?>"><?= esc($l['nama']) ?> (NIDN: <?= esc($l['nidn'] ?: '-') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- status akun (hanya saat edit) -->
                    <div x-show="showStatus" x-transition>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status Akun</label>
                        <select name="is_active" x-model="isActive" class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white transition-all cursor-pointer">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <!-- footer modal -->
                <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-2xl hover:bg-slate-50 transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-3 bg-primary text-white text-sm font-semibold rounded-2xl hover:bg-primary/95 shadow-md shadow-primary/10 transition-all cursor-pointer">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
