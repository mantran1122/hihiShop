<x-guest-layout :raw="true" :title="config('app.name', 'HIHI Shop') . ' - Tạo Tài Khoản'">
    @include('auth.diprella', ['initialMode' => 'register'])
</x-guest-layout>
