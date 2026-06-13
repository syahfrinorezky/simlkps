<?= $this->extend('layouts/auth') ?>
<?= $this->section('title') ?>
Login
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
                        Sistem Informasi LKPS D4 Administrasi Jaringan Komputer
                    </h2>
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
                    Sistem Informasi LKPS D4 Administrasi Jaringan Komputer
                </h2>
            </div>
        </div>
        <!-- RIGHT SIDE -->
        <div class="flex justify-center">
            <div class="w-full max-w-[480px] bg-white rounded-[28px] overflow-hidden shadow-xl border border-secondary/10">
                <!-- TOP NAV -->
                <div class="grid grid-cols-2 bg-primary/5 border-b border-primary/10">
                    <button
                        type="button"
                        class="bg-white text-primary font-bold py-4 text-sm border-t-2 border-primary">
                        Login
                    </button>
                    <a
                        href="/forgot-password"
                        class="flex items-center justify-center text-primary/70 font-medium text-sm hover:text-primary transition bg-transparent">
                        Lupa Password
                    </a>
                </div>
                <!-- BODY -->
                <div class="px-6 py-8 lg:px-10 lg:py-10">
                    <div class="flex items-center gap-4 mb-6 lg:mb-8">
                        <div class="flex-1 h-px bg-secondary/20"></div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary font-montserrat">
                            Login
                        </h3>
                        <div class="flex-1 h-px bg-secondary/20"></div>
                    </div>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <?= form_open('/login/attempt') ?>
                    <div class="space-y-4 lg:space-y-5">
                        <!-- EMAIL -->
                        <div>
                            <label class="block text-primary font-medium text-sm mb-2">
                                NIP / Email
                            </label>
                            <div class="flex overflow-hidden rounded-xl border border-secondary/20 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/30 transition-all bg-white">
                                <div class="w-12 bg-primary/5 flex items-center justify-center border-r border-secondary/10">
                                    <i
                                        data-lucide="user-round"
                                        class="w-4 h-4 text-primary">
                                    </i>
                                </div>
                                <input
                                    type="text"
                                    name="email"
                                    value="<?= old('email') ?>"
                                    autofocus
                                    required
                                    placeholder="johndoe@gmail.com"
                                    class="flex-1 bg-transparent px-4 h-12 outline-none text-sm text-primary placeholder:text-secondary/50">
                            </div>
                        </div>
                        <!-- PASSWORD -->
                        <div x-data="{ show:false }">
                            <label class="block text-primary font-medium text-sm mb-2">
                                Password
                            </label>
                            <div class="flex overflow-hidden rounded-xl border border-secondary/20 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/30 transition-all bg-white">
                                <div class="w-12 bg-primary/5 flex items-center justify-center border-r border-secondary/10">
                                    <i
                                        data-lucide="lock"
                                        class="w-4 h-4 text-primary">
                                    </i>
                                </div>
                                <div class="relative flex-1">
                                    <input
                                        :type="show ? 'text' : 'password'"
                                        name="password"
                                        required
                                        placeholder="••••••••"
                                        class="w-full bg-transparent px-4 pr-12 h-12 outline-none text-sm text-primary placeholder:text-secondary/50">
                                    <button
                                        type="button"
                                        @click="show = !show"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-primary">
                                        <i
                                            x-show="!show"
                                            x-cloak
                                            data-lucide="eye"
                                            class="w-4 h-4">
                                        </i>
                                        <i
                                            x-show="show"
                                            x-cloak
                                            data-lucide="eye-off"
                                            class="w-4 h-4">
                                        </i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- OPTIONS -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-primary">
                                <input
                                    type="checkbox"
                                    name="remember">
                                Ingat Saya
                            </label>
                            <a
                                href="/forgot-password"
                                class="text-sm text-primary hover:underline">
                                Lupa Password?
                            </a>
                        </div>
                        <!-- BUTTON -->
                        <button
                            type="submit"
                            class="w-full h-12 rounded-xl bg-gradient-to-r from-primary to-[#51A8E4] text-white font-semibold hover:opacity-90 transition shadow-lg shadow-primary/20 mt-2">
                            Login
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
