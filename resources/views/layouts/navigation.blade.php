<nav x-data="{ open: false }" class="bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}">
                        <span class="font-extrabold text-2xl text-blue-700 dark:text-sky-400 tracking-tight">Laporan Harian</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="dark:text-slate-300">
                            {{ __('Dashboard Admin') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="dark:text-slate-300">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- DESKTOP MENU (TAMPIL DI LAPTOP) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-5">

                <!-- TOMBOL DARK MODE KAPSUL (DESKTOP) -->
                <button type="button" class="theme-toggle relative inline-flex h-8 w-16 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600">
                    <span class="sr-only">Toggle Dark Mode</span>
                    <span class="theme-toggle-ball flex h-6 w-6 transform items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-9">
                        <svg class="w-4 h-4 text-amber-500 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm-9.855 0a1 1 0 010 1.414l-.849.849a1 1 0 01-1.414-1.414l.849-.849a1 1 0 011.414 0zM10 6a4 4 0 100 8 4 4 0 000-8zm-4 4a1 1 0 11-2 0 1 1 0 012 0zm11-1a1 1 0 110 2h-1a1 1 0 110-2h1zM5.636 15.636a1 1 0 011.414 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm9.855 0a1 1 0 010 1.414l-.849.849a1 1 0 01-1.414-1.414l.849-.849a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"></path></svg>
                        <svg class="w-4 h-4 text-slate-700 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </span>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-slate-300 bg-white dark:bg-slate-800 hover:text-gray-700 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="dark:bg-slate-800">
                            <x-dropdown-link :href="route('profile.edit')" class="dark:text-slate-300 dark:hover:bg-slate-700">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" class="dark:text-slate-300 dark:hover:bg-slate-700">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- MOBILE MENU (TAMPIL DI HP) -->
            <div class="-me-2 flex items-center sm:hidden gap-3">

                <!-- TOMBOL DARK MODE KAPSUL (MOBILE) -->
                <button type="button" class="theme-toggle relative inline-flex h-8 w-16 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600">
                    <span class="sr-only">Toggle Dark Mode</span>
                    <span class="theme-toggle-ball flex h-6 w-6 transform items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-9">
                        <svg class="w-4 h-4 text-amber-500 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm-9.855 0a1 1 0 010 1.414l-.849.849a1 1 0 01-1.414-1.414l.849-.849a1 1 0 011.414 0zM10 6a4 4 0 100 8 4 4 0 000-8zm-4 4a1 1 0 11-2 0 1 1 0 012 0zm11-1a1 1 0 110 2h-1a1 1 0 110-2h1zM5.636 15.636a1 1 0 011.414 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm9.855 0a1 1 0 010 1.414l-.849.849a1 1 0 01-1.414-1.414l.849-.849a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"></path></svg>
                        <svg class="w-4 h-4 text-slate-700 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </span>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-slate-400 hover:text-gray-500 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-700 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Dropdown Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden dark:bg-slate-800 border-t dark:border-slate-700">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="dark:text-slate-300">
                    {{ __('Dashboard Admin') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="dark:text-slate-300">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-slate-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="dark:text-slate-300">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="dark:text-slate-300">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
