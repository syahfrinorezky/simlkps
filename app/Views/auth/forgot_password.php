<?= $this->extend('layouts/auth') ?>
<?= $this->section('title') ?>
Lupa Password
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="min-h-screen flex items-center justify-center px-6 sm:px-8 py-8 lg:py-12">
    <div class="w-full max-w-7xl grid lg:grid-cols-[1fr_480px] gap-10 lg:gap-16 items-center">
        <!-- LEFT SIDE -->
        <div class="hidden lg:flex justify-center">
            <div class="max-w-xl">
                <!-- LOGO -->
                <div class="flex items-center gap-3 mb-6">
                    <img
                        src="<?= base_url('assets/logo/logo-pnb.png') ?>"
                        alt="Logo PNB"
                        class="h-8 object-contain">
                    <img
                        src="<?= base_url('assets/logo/logo-jti.png') ?>"
                        alt="Logo JTI"
                        class="h-8 object-contain">
                </div>
                <!-- HEADING -->
                <div>
                    <p class="text-sm font-bold text-[#4AA7EA] font-montserrat mb-3 tracking-widest uppercase">
                        Selamat Datang
                    </p>
                    <h2 class="text-3xl leading-snug font-bold text-primary font-montserrat">
                        Sistem Smart Repository Otomatis LKPS
                    </h2>
                    <p class="text-lg font-semibold text-secondary font-montserrat mt-2">
                        Program Studi D2 Administrasi Jaringan Komputer
                    </p>
                    <p class="text-base text-secondary/80 font-montserrat">
                        Jurusan Teknologi Informasi
                    </p>
                </div>
                <!-- HERO -->
                <div class="mt-10">
                    <img
                        src="<?= base_url('assets/images/hero-assets.svg') ?>"
                        alt="Hero"
                        class="w-64 opacity-90">
                </div>
            </div>
        </div>
        <!-- MOBILE HEADER -->
        <div class="lg:hidden flex flex-col items-center text-center mb-6 sm:mb-8">
            <!-- LOGO -->
            <div class="flex items-center gap-3 mb-2">
                <img
                    src="<?= base_url('assets/logo/logo-pnb.png') ?>"
                    alt="Logo PNB"
                    class="h-7 object-contain">
                <img
                    src="<?= base_url('assets/logo/logo-jti.png') ?>"
                    alt="Logo JTI"
                    class="h-7 object-contain">
            </div>
            <!-- HEADING -->
            <div>
                <p class="text-sm font-bold text-[#4AA7EA] font-montserrat mb-1 tracking-widest uppercase">
                    Selamat Datang
                </p>
                <h2 class="text-base font-bold text-primary font-montserrat leading-snug">
                    Sistem Smart Repository Otomatis LKPS
                </h2>
                <p class="text-xs font-semibold text-secondary font-montserrat mt-0.5">
                    Program Studi D2 Administrasi Jaringan Komputer
                </p>
                <p class="text-[10px] text-secondary/80 font-montserrat">
                    Jurusan Teknologi Informasi
                </p>
            </div>
        </div>
        <!-- RIGHT SIDE -->
        <div class="flex justify-center">
            <div class="w-full max-w-[480px] bg-white rounded-[28px] overflow-hidden shadow-xl border border-secondary/10">
                <!-- TOP NAV -->
                <div class="grid grid-cols-2 bg-primary/5 border-b border-primary/10">
                    <a
                        href="/login"
                        class="flex items-center justify-center text-primary/70 font-medium text-sm hover:text-primary transition bg-transparent">
                        Login
                    </a>
                    <button
                        type="button"
                        class="bg-white text-primary font-bold py-4 text-sm border-t-2 border-primary">
                        Lupa Password
                    </button>
                </div>
                <!-- BODY -->
                <div class="px-6 py-8 lg:px-10 lg:py-10">
                    <div class="flex items-center gap-4 mb-6 lg:mb-8">
                        <div class="flex-1 h-px bg-secondary/20"></div>
                        <h3 class="text-2xl lg:text-2xl font-bold text-primary font-montserrat text-center">
                            Reset Password
                        </h3>
                        <div class="flex-1 h-px bg-secondary/20"></div>
                    </div>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 break-words">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    <p class="text-sm text-secondary mb-6 text-center">
                        Masukkan email Anda dan kami akan mengirimkan link untuk mereset password Anda.
                    </p>
                    <?= form_open('/forgot-password') ?>
                    <div class="space-y-4 lg:space-y-5">
                        <!-- EMAIL -->
                        <div>
                            <label class="block text-primary font-medium text-sm mb-2">
                                Email
                            </label>
                            <div class="flex overflow-hidden rounded-xl border border-secondary/20 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/30 transition-all bg-white">
                                <div class="w-12 bg-primary/5 flex items-center justify-center border-r border-secondary/10">
                                    <i
                                        data-lucide="mail"
                                        class="w-4 h-4 text-primary">
                                    </i>
                                </div>
                                <input
                                    type="email"
                                    name="email"
                                    value="<?= old('email') ?>"
                                    autofocus
                                    required
                                    placeholder="johndoe@gmail.com"
                                    class="flex-1 bg-transparent px-4 h-12 outline-none text-sm text-primary placeholder:text-secondary/50">
                            </div>
                        </div>
                        <!-- BUTTON -->
                        <button
                            type="submit"
                            class="w-full h-12 rounded-xl bg-gradient-to-r from-primary to-[#51A8E4] text-white font-semibold hover:opacity-90 transition shadow-lg shadow-primary/20 mt-4">
                            Kirim Link Reset
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
