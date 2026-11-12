<x-guest-layout :raw="true" :title="config('app.name', 'HIHI Shop') . ' - Đăng Nhập & Đăng Ký'">
    @include('auth.diprella', ['initialMode' => 'login'])
</x-guest-layout>
