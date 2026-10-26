@extends('layouts.app')

@section('content')
<main class="container mx-auto max-w-2xl px-4 py-8">
    <section class="box">
        <h1 class="mt-0">Buat kata sandi baru</h1>
        <p>Halo, {{ $employee->nama }}. Untuk keamanan akun, ubah kata sandi sebelum melanjutkan.</p>
        <p class="mu">Gunakan minimal 8 karakter. Setelah perubahan pertama, pengaturan ulang kata sandi hanya dapat dilakukan oleh Pengelola Rapat.</p>
        @if($errors->any())
            <div class="box mt-3" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('employee.password.update') }}">
            @csrf
            <label for="password">Kata sandi baru</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
            <label for="password-confirmation">Ulangi kata sandi baru</label>
            <input id="password-confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
            <button class="btn pri mt-3">Simpan kata sandi</button>
        </form>
        <form method="post" action="{{ route('employee.logout') }}" class="mt-3">
            @csrf
            <button class="btn">Keluar</button>
        </form>
    </section>
</main>
@endsection
