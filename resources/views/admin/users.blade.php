@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')
@section('page_title', 'Danh Sách Người Dùng & Phân Quyền')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
        <span class="text-xs sm:text-sm text-slate-500">
            Tổng cộng: <strong class="text-slate-900">{{ $users->total() }}</strong> tài khoản người dùng
        </span>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4 sm:px-6 w-16">ID</th>
                        <th class="p-4">Họ tên</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Ngày đăng ký</th>
                        <th class="p-4">Vai trò hiện tại</th>
                        <th class="p-4 text-center">Đổi vai trò</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        @php
                            $userRole = $u->roles->first()?->name ?? 'Customer';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 sm:px-6 text-slate-400 font-mono text-xs">#{{ $u->id }}</td>
                            <td class="p-4 font-bold text-slate-900">
                                {{ $u->name }}
                                @if($u->id === auth()->id())
                                    <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded ml-1">Bạn</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-600">{{ $u->email }}</td>
                            <td class="p-4 text-slate-500 text-xs">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="p-4">
                                @if($userRole === 'Admin')
                                    <span class="badge-danger text-xs font-bold px-2.5 py-0.5">Admin</span>
                                @elseif($userRole === 'Staff')
                                    <span class="badge-info text-xs font-bold px-2.5 py-0.5">Staff</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Khách hàng</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.updateRole', $u->id) }}" class="inline-flex items-center gap-2 m-0">
                                        @csrf
                                        <select name="role" class="form-select text-xs py-1.5 px-2 font-medium">
                                            @foreach($roles as $r)
                                                <option value="{{ $r->name }}" {{ $userRole === $r->name ? 'selected' : '' }}>
                                                    {{ $r->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-primary btn-sm py-1.5">
                                            Lưu
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Đang đăng nhập</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
