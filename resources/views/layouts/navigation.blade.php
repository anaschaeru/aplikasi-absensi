<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
  {{-- Primary Navigation Menu --}}
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        {{-- Logo --}}
        <div class="shrink-0 flex items-center">
          <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
          </a>
        </div>

        {{-- Navigation Links (Desktop) --}}
        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
          @auth
            {{-- 1. LINK UMUM (DASHBOARD) --}}
            <x-nav-link :href="auth()->user()->role == 'walikelas' ? route('walikelas.dashboard') : route('dashboard')" :active="request()->routeIs('walikelas.dashboard') || request()->routeIs('dashboard')">
              {{ __('Dashboard') }}
            </x-nav-link>


            {{-- 2. MENU KHUSUS ADMIN --}}
            @if (auth()->user()->role == 'admin')
              {{-- Dropdown Manajemen Data --}}
              <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="left" width="48">
                  <x-slot name="trigger">
                    <button
                      class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.kelas.*') || request()->routeIs('admin.mapel.*') || request()->routeIs('admin.guru.*') || request()->routeIs('admin.siswa.*') || request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.users.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                      <div>Manajemen Data</div>
                      <div class="ms-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                        </svg>
                      </div>
                    </button>
                  </x-slot>
                  <x-slot name="content">
                    <x-dropdown-link :href="route('admin.kelas.index')">{{ __('Daftar Kelas') }}</x-dropdown-link>
                    <x-dropdown-link :href="route('admin.mapel.index')">{{ __('Mata Pelajaran') }}</x-dropdown-link>
                    <x-dropdown-link :href="route('admin.guru.index')">{{ __('Daftar Guru') }}</x-dropdown-link>
                    <x-dropdown-link :href="route('admin.siswa.index')">{{ __('Daftar Siswa') }}</x-dropdown-link>
                    <x-dropdown-link :href="route('admin.jadwal.index')">{{ __('Jadwal Pelajaran') }}</x-dropdown-link>
                    <div class="border-t border-gray-200"></div>
                    <x-dropdown-link :href="route('admin.users.index')">{{ __('Manajemen Pengguna') }}</x-dropdown-link>
                  </x-slot>
                </x-dropdown>
              </div>

              {{-- Dropdown Lainnya --}}
              <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="left" width="48">
                  <x-slot name="trigger">
                    <button
                      class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.laporan.*') || request()->routeIs('admin.import.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500' }} text-sm font-medium leading-5 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                      <div>Lainnya</div>
                      <div class="ms-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                        </svg>
                      </div>
                    </button>
                  </x-slot>
                  <x-slot name="content">
                    <x-dropdown-link :href="route('admin.laporan.absensi.index')">{{ __('Laporan Absensi') }}</x-dropdown-link>
                    <x-dropdown-link :href="route('admin.import.index')">{{ __('Import Data') }}</x-dropdown-link>
                  </x-slot>
                </x-dropdown>
              </div>

              {{-- Link Akses Cepat ke Piket --}}
              <x-nav-link :href="route('guru.piket.dashboard')" :active="request()->routeIs('guru.piket.dashboard')">
                {{ __('Dasbor Piket') }}
              </x-nav-link>
            @endif

            {{-- 3. MENU GURU, GURU PIKET & WALIKELAS --}}
            @if (in_array(auth()->user()->role, ['guru', 'guru_piket', 'walikelas']))
              <x-nav-link :href="route('guru.absensi.history')" :active="request()->routeIs('guru.absensi.history')">
                {{ __('Riwayat Absensi') }}
              </x-nav-link>
              <x-nav-link :href="route('guru.piket.dashboard')" :active="request()->routeIs('guru.piket.dashboard')">
                {{ __('Dasbor Piket') }}
              </x-nav-link>
              <x-nav-link :href="route('guru.piket.izin.index')" :active="request()->routeIs('guru.piket.izin.*')">
                {{ __('Manajemen Izin') }}
              </x-nav-link>
            @endif

            {{-- 4. MENU KHUSUS SISWA --}}
            @if (auth()->user()->role == 'siswa')
              <x-nav-link :href="route('siswa.my_qrcode')" :active="request()->routeIs('siswa.my_qrcode')">
                {{ __('QR Code Saya') }}
              </x-nav-link>
              <x-nav-link :href="route('siswa.izin.index')" :active="request()->routeIs('siswa.izin.*')">
                {{ __('Ajukan Izin') }}
              </x-nav-link>
            @endif
          @endauth
        </div>
      </div>

      {{-- Settings Dropdown (Desktop) --}}
      <div class="hidden sm:flex sm:items-center sm:ms-6">
        @auth
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                <div>{{ Auth::user()->name }}</div>
                <div class="ms-1">
                  <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>
            <x-slot name="content">
              {{-- <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link> --}}
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                  onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        @endauth
      </div>

      {{-- Hamburger (Mobile Trigger) --}}
      <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open"
          class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
              stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- Mobile Navigation Menu --}}
  <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
      @auth
        {{-- 1. LINK UMUM --}}
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
          {{ __('Dashboard') }}
        </x-responsive-nav-link>

        {{-- 2. MENU MOBILE KHUSUS ADMIN --}}
        @if (auth()->user()->role == 'admin')
          {{-- Dropdown Manajemen Data (Mobile) --}}
          <div x-data="{ dropdownOpen: {{ request()->routeIs('admin.kelas.*') || request()->routeIs('admin.mapel.*') || request()->routeIs('admin.guru.*') || request()->routeIs('admin.siswa.*') || request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.users.*') ? 'true' : 'false' }} }" class="pt-2 pb-1 border-t border-gray-200">
            <button @click="dropdownOpen = !dropdownOpen"
              class="w-full flex items-center justify-between ps-3 pe-4 py-2 text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
              <span>Manajemen Data</span>
              <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': dropdownOpen }" fill="currentColor"
                viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd" />
              </svg>
            </button>
            <div x-show="dropdownOpen" x-transition class="mt-2 space-y-1">
              <x-responsive-nav-link :href="route('admin.kelas.index')" :active="request()->routeIs('admin.kelas.*')"
                class="ps-8">{{ __('Daftar Kelas') }}</x-responsive-nav-link>
              <x-responsive-nav-link :href="route('admin.mapel.index')" :active="request()->routeIs('admin.mapel.*')"
                class="ps-8">{{ __('Mata Pelajaran') }}</x-responsive-nav-link>
              <x-responsive-nav-link :href="route('admin.guru.index')" :active="request()->routeIs('admin.guru.*')"
                class="ps-8">{{ __('Daftar Guru') }}</x-responsive-nav-link>
              <x-responsive-nav-link :href="route('admin.siswa.index')" :active="request()->routeIs('admin.siswa.*')"
                class="ps-8">{{ __('Daftar Siswa') }}</x-responsive-nav-link>
              <x-responsive-nav-link :href="route('admin.jadwal.index')" :active="request()->routeIs('admin.jadwal.*')"
                class="ps-8">{{ __('Jadwal Pelajaran') }}</x-responsive-nav-link>
              <div class="border-t border-gray-200 my-1 mx-4"></div>
              <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"
                class="ps-8">{{ __('Manajemen Pengguna') }}</x-responsive-nav-link>
            </div>
          </div>

          {{-- Dropdown Lainnya (Mobile) --}}
          <div x-data="{ dropdownOpen: {{ request()->routeIs('admin.laporan.*') || request()->routeIs('admin.import.*') ? 'true' : 'false' }} }" class="pt-2 pb-1 border-t border-gray-200">
            <button @click="dropdownOpen = !dropdownOpen"
              class="w-full flex items-center justify-between ps-3 pe-4 py-2 text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
              <span>Lainnya</span>
              <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': dropdownOpen }" fill="currentColor"
                viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd" />
              </svg>
            </button>
            <div x-show="dropdownOpen" x-transition class="mt-2 space-y-1">
              <x-responsive-nav-link :href="route('admin.laporan.absensi.index')" :active="request()->routeIs('admin.laporan.*')"
                class="ps-8">{{ __('Laporan Absensi') }}</x-responsive-nav-link>
              <x-responsive-nav-link :href="route('admin.import.index')" :active="request()->routeIs('admin.import.*')"
                class="ps-8">{{ __('Import Data') }}</x-responsive-nav-link>
            </div>
          </div>
        @endif

        {{-- 3. MENU MOBILE GURU, GURU PIKET & WALIKELAS --}}
        @if (in_array(auth()->user()->role, ['guru', 'guru_piket', 'walikelas']))
          <x-responsive-nav-link :href="route('guru.absensi.history')" :active="request()->routeIs('guru.absensi.history')">
            {{ __('Riwayat Absensi') }}
          </x-responsive-nav-link>
          <x-responsive-nav-link :href="route('guru.piket.dashboard')" :active="request()->routeIs('guru.piket.dashboard')">
            {{ __('Dasbor Piket') }}
          </x-responsive-nav-link>
          <x-responsive-nav-link :href="route('guru.piket.izin.index')" :active="request()->routeIs('guru.piket.izin.*')">
            {{ __('Manajemen Izin') }}
          </x-responsive-nav-link>
        @endif

        {{-- 4. MENU MOBILE KHUSUS SISWA --}}
        @if (auth()->user()->role == 'siswa')
          <x-responsive-nav-link :href="route('siswa.my_qrcode')" :active="request()->routeIs('siswa.my_qrcode')">
            {{ __('QR Code Saya') }}
          </x-responsive-nav-link>
          <x-responsive-nav-link :href="route('siswa.izin.index')" :active="request()->routeIs('siswa.izin.*')">
            {{ __('Ajukan Izin') }}
          </x-responsive-nav-link>
        @endif
      @endauth
    </div>

    {{-- Mobile User Options --}}
    <div class="pt-4 pb-1 border-t border-gray-200">
      @auth
        <div class="px-4">
          <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
          <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
        </div>
        <div class="mt-3 space-y-1">
          {{-- <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link> --}}
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')"
              onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
          </form>
        </div>
      @endauth
    </div>
  </div>
</nav>
