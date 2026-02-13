<x-auth-layout>
  {{-- HEADER --}}
  <div class="text-center mb-8">
    {{-- Jika Anda ingin menampilkan logo di sini juga, uncomment baris di bawah --}}
    {{-- <div class="flex justify-center mb-4">
            <x-application-logo class="w-12 h-12 fill-current text-indigo-600" />
        </div> --}}

    <h2 class="text-3xl font-extrabold text-gray-900">
      Selamat Datang Kembali
    </h2>
    <p class="text-sm text-gray-500 mt-2">
      Silakan masuk untuk mengakses dasbor absensi.
    </p>
  </div>

  {{-- SESSION STATUS --}}
  <x-auth-session-status class="mb-4 bg-green-50 text-green-700 p-3 rounded-md border border-green-200 text-sm"
    :status="session('status')" />

  <form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    {{-- EMAIL INPUT --}}
    <div>
      <x-input-label for="email" value="Email" class="sr-only" /> {{-- Label disembunyikan visual, tetap ada untuk screen reader --}}
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
          </svg>
        </div>
        <x-text-input id="email"
          class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm"
          type="email" name="email" :value="old('email')" required autofocus placeholder="nip@guru.id" />
      </div>
      <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600" />
    </div>

    {{-- PASSWORD INPUT --}}
    <div>
      <x-input-label for="password" value="Password" class="sr-only" />
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
              clip-rule="evenodd" />
          </svg>
        </div>
        <x-text-input id="password"
          class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm"
          type="password" name="password" required placeholder="••••••••" />
      </div>
      <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-600" />
    </div>

    {{-- REMEMBER ME & FORGOT PASSWORD --}}
    <div class="flex items-center justify-between">
      <label for="remember_me" class="inline-flex items-center cursor-pointer group">
        <input id="remember_me" type="checkbox"
          class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 group-hover:border-indigo-400 transition"
          name="remember">
        <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-800 transition">Ingat saya</span>
      </label>

      @if (Route::has('password.request'))
        <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium hover:underline"
          href="{{ route('password.request') }}">
          Lupa Password?
        </a>
      @endif
    </div>

    {{-- BUTTONS --}}
    <div>
      <x-primary-button
        class="w-full justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
        Masuk Sekarang
      </x-primary-button>
    </div>

    {{-- REGISTER LINK --}}
    <div class="mt-6 text-center">
      <p class="text-sm text-gray-600">
        Belum memiliki akun?
        <a href="{{ route('register') }}"
          class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline transition">
          Daftar di sini
        </a>
      </p>
    </div>
  </form>
</x-auth-layout>
