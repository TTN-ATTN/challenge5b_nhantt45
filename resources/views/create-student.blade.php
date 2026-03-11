@extends('layouts.app', ['pageTitle' => 'Thêm sinh viên mới'])

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-md mt-4 border-t-4 border-green-500">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Thêm Sinh Viên Mới</h2>
            <a href="/" class="text-gray-500 hover:text-gray-700 font-medium text-sm">&larr; Quay lại</a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-md mb-6 text-sm border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student.store') }}" method="POST" id="createStudentForm" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700 font-medium mb-1">Tên đăng nhập:</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Họ và tên:</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Số điện thoại:</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Mật khẩu:</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="pt-4">
                <button type="button" onclick="requirePasswordAndSubmit('createStudentForm')"
                    class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 transition shadow">
                    + Khởi tạo Tài khoản
                </button>
            </div>
        </form>
    </div>
@endsection
