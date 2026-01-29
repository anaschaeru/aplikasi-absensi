<x-auth-layout>
  <div class="text-center mb-8">
    <h2 class="text-2xl font-bold text-gray-800">
      Login Akun
    </h2>
    <p class="text-sm text-gray-600 mt-1">
      Selamat datang kembali!
    </p>
  </div>

  <x-auth-session-status class="mb-4" :status="session('status')" />

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
      <x-input-label for="email" value="Email" />
      <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
        autofocus />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label for="password" value="Password" />
      <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="block mt-4">
      <label for="remember_me" class="inline-flex items-center">
        <input id="remember_me" type="checkbox"
          class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
        <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
      </label>
    </div>

    <div class="flex items-center justify-between mt-6">
      <a class="underline text-sm text-gray-600 hover:text-gray-900" aria-disabled="true" onclick="return false;"
        href="{{ route('register') }} ">
        Belum punya akun?
      </a>

      <x-primary-button class="ms-3">
        Masuk
      </x-primary-button>
    </div>
  </form>
</x-auth-layout>
