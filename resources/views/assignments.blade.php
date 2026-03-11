@extends('layouts.app', ['pageTitle' => 'Giao Bài & Trả Bài'])

@section('content')
<div class="max-w-5xl mx-auto">

    <h2 class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-6 sm:mb-8 border-b pb-2">Hệ Thống Bài Tập</h2>

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
            <h3 class="text-xl font-bold text-green-600 mb-4">+ Tạo Bài Tập Mới</h3>
            <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 shadow-sm p-4 bg-gray-50 rounded border border-gray-100">
                @csrf
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Tiêu đề:</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Mô tả / Hướng dẫn:</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Deadline:</label>
                    <input type="datetime-local" name="deadline" required class="w-full md:w-1/2 px-4 py-2 border rounded focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">File đề bài (pdf, docx, txt, zip, rar - Max 100MB):</label>
                    <input type="file" name="assignment_file" required accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition">Giao Bài</button>
            </form>
        </div>
    @endif

    <h3 class="text-2xl font-bold text-gray-700 mb-4">Danh Sách Bài Tập Của Lớp</h3>
    
    @if($assignments->isEmpty())
        <p class="text-gray-500 bg-white p-6 rounded shadow-sm text-center italic">Hiện chưa có bài tập nào.</p>
    @else
        <div class="space-y-6">
            @foreach($assignments as $hw)
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 relative">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-start mb-4 gap-4">
                        <div>
                            <h4 class="text-xl font-bold text-indigo-700 mb-1">{{ $hw->title }}</h4>
                            <p class="text-sm text-gray-500">
                                Giáo viên: <strong class="text-gray-700">{{ $hw->teacher->full_name ?? 'N/A' }}</strong> |
                                Hạn nộp: <strong class="text-red-500">{{ \Carbon\Carbon::parse($hw->deadline)->format('d/m/Y H:i') }}</strong>
                            </p>
                        </div>
                        
                        @if(Auth::user()->role === 'teacher' && Auth::id() === $hw->teacher_id)
                            <form action="{{ route('assignments.delete') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài tập này và toàn bộ bài nộp của sinh viên?');">
                                @csrf
                                <input type="hidden" name="assignment_id" value="{{ $hw->id }}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-1.5 px-3 rounded transition shadow-sm">Xóa Bài</button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="text-gray-700 mb-4 bg-gray-50 p-4 rounded text-sm leading-relaxed border border-gray-100">
                        {!! nl2br(e($hw->description)) !!}
                    </div>

                    <a href="{{ $hw->file_path }}" download class="inline-block bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-bold py-2 px-4 rounded shadow transition mb-4">Tải Đề Bài Về</a>

                    @if(Auth::user()->role === 'student')
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            @if(isset($mySubmissions[$hw->id]))
                                @php
                                    $mySub = $mySubmissions[$hw->id];
                                    $isLate = \Carbon\Carbon::parse($mySub->created_at)->gt(\Carbon\Carbon::parse($hw->deadline));
                                @endphp
                                <div class="mb-4 p-4 bg-blue-50 rounded-md border-l-4 border-blue-500 shadow-sm">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-blue-50 gap-2">
                                        <div class="text-sm text-gray-700">
                                            <strong>Trạng thái:</strong> Đã nộp lúc {{ \Carbon\Carbon::parse($mySub->created_at)->format('d/m/Y H:i') }}
                                            @if($isLate)
                                                <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs ml-2 font-medium">Nộp trễ</span>
                                            @else
                                                <span class="bg-green-500 text-white px-2 py-0.5 rounded text-xs ml-2 font-medium">Đúng hạn</span>
                                            @endif
                                        </div>

                                        <form action="{{ route('assignments.unsubmit') }}" method="POST" onsubmit="return confirm('Bạn muốn gỡ bài nộp này?');">
                                            @csrf
                                            <input type="hidden" name="submission_id" value="{{ $mySub->id }}">
                                            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white text-xs py-1 px-3 rounded transition">Gỡ bài</button>
                                        </form>
                                    </div>

                                    <div class="mt-2 text-sm">
                                        <strong class="text-gray-700">Điểm số:</strong>
                                        @if($mySub->score !== null)
                                            <span class="text-red-600 font-bold text-lg ml-1">{{ floatval($mySub->score) }} / 10</span>
                                        @else
                                            <span class="text-gray-500 italic ml-1">Giáo viên chưa chấm điểm</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="bg-white p-4 border border-gray-200 rounded">
                                <strong class="block mb-2 text-gray-700">{{ isset($mySubmissions[$hw->id]) ? 'Nộp lại bài:' : 'Nộp bài làm:' }}</strong>
                                <form action="{{ route('assignments.submit') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                    @csrf
                                    <input type="hidden" name="assignment_id" value="{{ $hw->id }}">
                                    <input type="file" name="submission_file" required accept=".pdf,.doc,.docx,.txt,.zip,.rar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition whitespace-nowrap">{{ isset($mySubmissions[$hw->id]) ? 'Nộp Lại' : 'Nộp Bài' }}</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->role === 'teacher')
                        <div class="mt-6 bg-gray-50 border-l-4 border-green-500 p-4 rounded">
                            <strong class="text-gray-700 block mb-2">Danh sách nộp bài ({{ $hw->submissions->count() }}):</strong>
                            
                            @if($hw->submissions->isEmpty())
                                <p class="text-sm text-gray-500 italic">Chưa có sinh viên nào nộp bài.</p>
                            @else
                                <ul class="space-y-3">
                                    @foreach($hw->submissions as $sub)
                                        @php
                                            $isLate = \Carbon\Carbon::parse($sub->created_at)->gt(\Carbon\Carbon::parse($hw->deadline));
                                        @endphp
                                        <li class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-3 bg-white border border-gray-200 rounded shadow-sm">
                                            <div class="flex-1 flex flex-wrap items-center gap-2">
                                                <strong class="text-indigo-600">{{ $sub->student->full_name ?? 'Sinh viên ẩn danh' }}</strong>
                                                <span class="text-xs text-gray-500">({{ \Carbon\Carbon::parse($sub->created_at)->format('d/m/Y H:i') }})</span>
                                                @if($isLate)
                                                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-xs font-bold">Nộp trễ</span>
                                                @endif
                                            </div>

                                            <a href="{{ $sub->file_path }}" download class="text-cyan-600 hover:text-cyan-800 text-sm font-medium hover:underline whitespace-nowrap">Tải bài làm</a>

                                            <form action="{{ route('assignments.grade') }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                                <input type="number" step="0.1" min="0" max="10" name="score" value="{{ $sub->score }}" placeholder="Điểm" class="w-16 px-2 py-1 text-sm border rounded focus:ring-2 focus:ring-green-400 focus:outline-none" required>
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded transition shadow-sm">Lưu</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection