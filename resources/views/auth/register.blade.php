<x-guest-layout>
    <form method="POST" action="{{ route('admin.register.store') }}">
        @csrf

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full"
                          type="text" name="name"
                          :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email" name="email"
                          :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Phone (optional) --}}
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full"
                          type="text" name="phone"
                          :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

{{-- Role --}}
<div class="mt-4">
  <x-input-label for="role_id" :value="__('Role')" />
  <select id="role_id" name="role_id" required ...>
  <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>-- {{ __('Select role') }} --</option>
  @foreach($roles as $role)
      <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
          {{ ucfirst($role->name) }}
      </option>
  @endforeach
</select>
  <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
</div>


        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password" name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password" name="password_confirmation"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
               href="{{ route('admin.login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
        {{-- Hỗ trợ Breeze: --}}
<x-auth-session-status class="mb-4" :status="session('status')" />

{{-- Tuỳ chọn: hỗ trợ thêm các key khác --}}
@if (session('success'))
  <div class="mb-4 rounded-md bg-green-50 p-3 text-green-800 border border-green-200">
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="mb-4 rounded-md bg-red-50 p-3 text-red-800 border border-red-200">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="mb-4 rounded-md bg-red-50 p-3 text-red-800 border border-red-200">
    <ul class="list-disc list-inside">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

    </form>


</x-guest-layout>
