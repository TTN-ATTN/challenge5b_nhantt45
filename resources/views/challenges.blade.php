@extends('layouts.app', ['pageTitle' => 'Giải Đố - Challenges'])

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6 sm:mb-8 border-b pb-2">
        <h2 class="text-2xl sm:text-3xl font-bold text-indigo-600">Thử Thách Giải Đố</h2>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-md mb-6 text-sm border border-red-200 shadow-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(Auth::user()->role === 'teacher')
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-200 mb-6 sm:mb-8">
            <h3 class="text-xl font-bold text-green-600 mb-4">+ Tạo Thử Thách Mới</h3>
            <form action="{{ route('challenges.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Gợi ý (Hint):</label>
                    <textarea name="hint" rows="2" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none" placeholder="Ví dụ: Nội dung file này là mã sinh viên của người giỏi nhất lớp..."></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">File Đáp án (.txt):</label>
                    <p class="text-sm text-gray-500 mb-2">Hệ thống sẽ đọc nội dung bên trong file .txt này để làm đáp án bí mật.</p>
                    <input type="file" name="secret_file" required accept=".txt" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition">Đăng Thử Thách</button>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($challenges as $challenge)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col h-full">
                
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-xl font-bold text-indigo-700">Thử thách #{{ $challenge->id }}</h4>
                    @if(Auth::user()->role === 'teacher' && Auth::id() === $challenge->teacher_id)
                        <form action="{{ route('challenges.delete') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thử thách này?');">
                            @csrf
                            <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition" title="Xóa thử thách">Xóa</button>
                        </form>
                    @endif
                </div>

                <p class="text-sm text-gray-500 mb-4 border-b pb-3">
                    Bởi: <strong class="text-gray-700">{{ $challenge->teacher->full_name ?? 'N/A' }}</strong> - 
                    {{ $challenge->created_at->diffForHumans() }}
                </p>

                <div class="flex-grow">
                    <strong class="text-gray-700">Gợi ý:</strong>
                    <div class="bg-yellow-50 text-yellow-800 p-3 rounded border border-yellow-200 mt-2 mb-4 text-sm font-medium">
                        {{ $challenge->hint ?: 'Không có gợi ý nào cho thử thách này!' }}
                    </div>
                </div>

                @if(Auth::user()->role === 'student')
                    <form action="{{ route('challenges.submit') }}" method="POST" class="mt-auto border-t pt-4">
                        @csrf
                        <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Câu trả lời của bạn:</label>
                        <div class="flex gap-2">
                            <input type="text" name="answer" required placeholder="Nhập đáp án..." class="flex-grow px-3 py-2 border rounded focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition shadow">Gửi</button>
                        </div>
                    </form>
                @elseif(Auth::user()->role === 'teacher')
                    <div class="mt-auto border-t pt-4">
                        <a href="{{ $challenge->file_path }}" download class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Tải file bí mật về kiểm tra
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 text-center bg-white p-8 rounded shadow-sm">
                <p class="text-gray-500 italic text-lg">Hiện chưa có thử thách nào được tạo.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection