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
        <div class="bg-purple-50 p-6 rounded-lg shadow-sm border-l-4 border-purple-500 mb-8">
            <h3 class="text-xl font-bold text-purple-700 mb-4">+ Tạo Challenge Mới</h3>
            <form action="{{ route('challenges.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Gợi ý (Hint):</label>
                    <textarea name="hint" rows="2" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-purple-400 focus:outline-none" placeholder="Ví dụ: Tên một bài hát của Sơn Tùng M-TP" required></textarea>
                </div>
                <div>
                    <label class="block text-red-600 font-bold mb-1">Upload File (.txt):</label>
                    <p class="text-sm text-gray-600 mb-2">Tên file chính là đáp án (viết không dấu, cách nhau khoảng trắng, vd: <strong class="text-gray-800">Em cua ngay hom qua.txt</strong>)</p>
                    <input type="file" name="secret_file" required accept=".txt" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200">
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded shadow transition">Tạo Challenge</button>
            </form>
        </div>
    @endif

    <h3 class="text-2xl font-bold text-gray-700 mb-4">Danh Sách Thử Thách</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($challenges as $challenge)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col h-full">
                
                <div class="flex justify-between items-start mb-2">
                    <h4 class="text-xl font-bold text-indigo-700">Challenge #{{ $challenge->id }}</h4>
                    @if(Auth::user()->role === 'teacher' && Auth::id() === $challenge->teacher_id)
                        <form action="{{ route('challenges.delete') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thử thách này?');">
                            @csrf
                            <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition" title="Xóa thử thách">Xóa</button>
                        </form>
                    @endif
                </div>

                <p class="text-sm text-gray-500 mb-4 border-b pb-3">
                    Giáo viên tạo: <strong class="text-gray-700">{{ $challenge->teacher->full_name ?? 'N/A' }}</strong> - 
                    {{ $challenge->created_at->format('d/m/Y') }}
                </p>

                <div class="flex-grow">
                    <div class="bg-yellow-50 text-yellow-800 p-4 border-l-4 border-yellow-400 rounded-r mb-4">
                        <strong class="font-semibold text-yellow-900">Gợi ý:</strong> {!! nl2br(e($challenge->hint)) !!}
                    </div>
                </div>

                @if(session('solved_id') == $challenge->id)
                    <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded font-mono whitespace-pre-wrap break-all leading-relaxed mt-4 mb-4">
                        <strong class="text-lg text-green-900 block mb-2">Chúc mừng! Nội dung:</strong>{{ session('solved_content') }}
                    </div>
                @endif

                @if(Auth::user()->role === 'student')
                    <form action="{{ route('challenges.submit') }}" method="POST" class="mt-auto border-t pt-4">
                        @csrf
                        <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
                        <div class="flex gap-2">
                            <input type="text" name="answer" required placeholder="Nhập đáp án của bạn..." class="flex-grow px-3 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition shadow whitespace-nowrap">Giải Đố</button>
                        </div>
                    </form>
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