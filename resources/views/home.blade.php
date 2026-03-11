@extends('layouts.app', ['pageTitle' => 'Danh sách người dùng'])

@section('content')
<div class="max-w-6xl mx-auto bg-white p-4 sm:p-8 rounded shadow mt-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b">
        <h3 class="text-2xl font-bold text-gray-800">Danh sách Người dùng</h3>
        @if(Auth::user()->role === 'teacher')
            <a href="/create-student" class="mt-3 sm:mt-0 inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded shadow transition">+ Thêm Sinh viên</a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse bg-white shadow-sm rounded-lg overflow-hidden">
            <thead class="bg-gray-200 text-gray-700 uppercase text-sm font-semibold">
                <tr>
                    <th class="py-3 px-4 border-b">ID</th>
                    <th class="py-3 px-4 border-b">Tên đăng nhập</th>
                    <th class="py-3 px-4 border-b">Họ tên</th>
                    <th class="py-3 px-4 border-b">Email</th>
                    <th class="py-3 px-4 border-b">Vai trò</th>
                    <th class="py-3 px-4 border-b text-center">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach($allUsers as $u)
                    <tr class="hover:bg-gray-50 border-b last:border-0 transition">
                        <td class="py-3 px-4">{{ $u->id }}</td>
                        <td class="py-3 px-4">{{ $u->username }}</td>
                        <td class="py-3 px-4 font-medium text-gray-800">{{ $u->full_name }}</td>
                        <td class="py-3 px-4">{{ $u->email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $u->role === 'teacher' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $u->role === 'teacher' ? 'Giáo viên' : 'Sinh viên' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="/profile?id={{ $u->id }}" class="text-indigo-500 hover:text-indigo-700 font-medium hover:underline">Xem chi tiết</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection