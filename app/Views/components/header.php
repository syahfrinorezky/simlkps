<header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-slate-200 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 -mb-px">

            <!-- Header: Left side -->
            <div class="flex items-center">
                
                <!-- Hamburger button (Mobile) -->
                <button
                    class="text-slate-500 hover:text-slate-600 lg:hidden"
                    @click.stop="sidebarOpen = !sidebarOpen"
                    aria-controls="sidebar"
                    :aria-expanded="sidebarOpen"
                >
                    <span class="sr-only">Open sidebar</span>
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>

                <!-- Page Title -->
                <h1 class="hidden sm:block ml-4 text-xl font-bold text-slate-800">
                    <?= $title ?? 'Dashboard' ?>
                </h1>

            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">
                
                <!-- User dropdown -->
                <div class="relative inline-flex" x-data="{ open: false }">
                    <button
                        class="inline-flex justify-center items-center group"
                        aria-haspopup="true"
                        @click.prevent="open = !open"
                        :aria-expanded="open"
                    >
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 text-slate-600 group-hover:bg-slate-200 transition-colors">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <div class="flex items-center truncate">
                            <span class="truncate ml-2 text-sm font-medium group-hover:text-slate-800">
                                <?= session()->get('userName') ?? 'User' ?>
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 ml-1 text-slate-400"></i>
                        </div>
                    </button>

                    <!-- Dropdown -->
                    <div
                        class="origin-top-right z-10 absolute top-full right-0 min-w-44 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200 transform"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-out duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-cloak
                    >
                        <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-slate-200">
                            <div class="font-medium text-slate-800"><?= session()->get('userName') ?? 'User' ?></div>
                            <div class="text-xs text-slate-500 capitalize"><?= session()->get('userRole') ?? 'Guest' ?></div>
                        </div>
                        <ul>
                            <li>
                                <a class="font-medium text-sm text-primary flex items-center py-1 px-3 hover:text-primary/80" href="<?= base_url('logout') ?>" @click="open = false">
                                    Sign Out
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>
    </div>
</header>
