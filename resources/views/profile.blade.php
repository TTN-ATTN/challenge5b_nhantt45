@extends('layouts.app', ['pageTitle' => 'Hồ sơ - ' . $profileUser->full_name])

@section('content')
    <div class="max-w-4xl mx-auto mt-4">

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-md mb-6 text-sm border border-red-200 shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md mb-6 flex flex-col sm:flex-row gap-6 items-start">

            <div
                class="w-32 h-32 flex-shrink-0 bg-gray-100 rounded-full overflow-hidden border-2 border-indigo-100 shadow-sm">
                @if ($profileUser->avatar)
                    <img src="{{ $profileUser->avatar }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <img src="{{ asset('assets/default-avatar.jpg') }}" alt="Default Avatar"
                        class="w-full h-full object-cover">
                @endif
            </div>

            <div class="flex-grow w-full">
                <h2 class="text-3xl font-bold text-gray-800 mb-1">{{ $profileUser->full_name }}</h2>
                <p class="text-sm font-medium text-gray-500 mb-4">
                    <span
                        class="px-2 py-0.5 rounded {{ $profileUser->role === 'teacher' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $profileUser->role === 'teacher' ? 'Giáo viên' : 'Sinh viên' }}
                    </span>
                    | {{ $profileUser->username }}
                </p>

                <div class="bg-gray-50 p-4 rounded-md mb-4 border border-gray-100">
                    <p class="mb-2"><strong class="text-gray-700">Email:</strong> {{ $profileUser->email }}</p>
                    <p><strong class="text-gray-700">Số điện thoại:</strong> {{ $profileUser->phone_number }}</p>
                </div>

                @if (Auth::user()->role === 'teacher' && $profileUser->role === 'student')
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-lg font-bold text-indigo-700 mb-4">Quyền Giáo viên: Chỉnh sửa thông tin</h4>

                        <form action="{{ route('student.teacher_update') }}" method="POST" id="teacherEditForm"
                            class="space-y-4">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $profileUser->id }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Tên đăng nhập:</label>
                                    <input type="text" name="username"
                                        value="{{ old('username', $profileUser->username) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Họ tên:</label>
                                    <input type="text" name="full_name"
                                        value="{{ old('full_name', $profileUser->full_name) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Email:</label>
                                    <input type="email" name="email" value="{{ old('email', $profileUser->email) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Số điện thoại:</label>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', $profileUser->phone_number) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Mật khẩu mới (Bỏ trống nếu
                                        không đổi):</label>
                                    <input type="password" name="new_password"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <button type="button"
                                    class="bg-indigo-600 text-white px-5 py-2 rounded shadow hover:bg-indigo-700 transition"
                                    onclick="requirePasswordAndSubmit('teacherEditForm')">Lưu Thay Đổi</button>
                            </div>
                        </form>

                        <form action="{{ route('student.delete') }}" method="POST" id="deleteStudentForm" class="mt-3">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $profileUser->id }}">
                            <button type="button"
                                onclick="if(confirm('Cảnh báo: Hành động này sẽ xóa toàn bộ bài tập và dữ liệu của sinh viên này. Tiếp tục?')) requirePasswordAndSubmit('deleteStudentForm')"
                                class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition text-sm">
                                Xóa Sinh Viên
                            </button>
                        </form>
                    </div>
                @elseif($isOwnProfile && Auth::user()->role === 'student')
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-lg font-bold text-indigo-700 mb-4">Cập nhật hồ sơ</h4>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
                            id="studentEditForm" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Email:</label>
                                    <input type="email" name="email" value="{{ old('email', $profileUser->email) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Số điện thoại:</label>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', $profileUser->phone_number) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Thay đổi Ảnh đại
                                        diện:</label>
                                    <input type="file" name="avatar_file" accept="image/jpeg, image/png, image/gif"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <p class="text-xs text-gray-500 mt-1">Hỗ trợ JPG, PNG, GIF. Kích thước tối đa 2MB.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-gray-700 text-sm font-medium mb-1">Mật khẩu mới (Bỏ trống nếu
                                        không đổi):</label>
                                    <input type="password" name="new_password"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="button"
                                    class="bg-indigo-600 text-white px-5 py-2 rounded shadow hover:bg-indigo-700 transition"
                                    onclick="requirePasswordAndSubmit('studentEditForm')">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                @endif

            </div>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Hộp thư / Tin nhắn</h3>

            @if (!$isOwnProfile)
                <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-100 mb-8 shadow-sm">
                    <h3 class="text-lg font-bold text-indigo-700 mb-4">Để lại lời nhắn cho {{ $profileUser->full_name }}
                    </h3>
                    <form action="{{ route('message.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $profileUser->id }}">
                        <textarea name="content" rows="3"
                            class="w-full p-3 border border-indigo-200 rounded-md focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-y"
                            placeholder="Nhập nội dung tin nhắn..." required></textarea>
                        <button type="submit"
                            class="mt-3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-6 rounded shadow transition">Gửi</button>
                    </form>
                </div>
            @endif

            @php
                // Lọc: Chỉ xem được nếu mình là chủ trang (người nhận) hoặc mình là người gửi
                $visibleMessages = $messages->filter(function ($msg) use ($isOwnProfile) {
                    return $isOwnProfile || Auth::id() == $msg->sender_id;
                });
            @endphp

            <div>
                <h3 class="text-xl font-bold text-gray-700 mb-4">({{ $visibleMessages->count() }} tin nhắn)</h3>

                @if ($visibleMessages->isEmpty())
                    <p class="text-gray-500 italic bg-gray-50 p-4 rounded-md text-center border border-gray-100">Chưa có
                        tin nhắn nào.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($visibleMessages as $msg)
                            <div class="bg-white border border-gray-200 border-l-4 border-l-cyan-500 p-4 rounded-md shadow-sm"
                                id="msg-box-{{ $msg->id }}">

                                <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                                    <div class="flex items-center gap-3">
                                        @php $senderAvatar = $msg->sender->avatar ?? asset('assets/default-avatar.jpg'); @endphp
                                        <img src="{{ $senderAvatar }}"
                                            class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100">
                                        <span class="text-indigo-600 font-bold">{{ $msg->sender->full_name }}</span>
                                        <span
                                            class="text-xs text-gray-400">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                                    </div>

                                    @if (Auth::id() == $msg->sender_id)
                                        <div class="flex gap-2">
                                            <button type="button" onclick="toggleMsgEdit({{ $msg->id }})"
                                                class="text-blue-500 hover:text-blue-700 text-sm font-medium transition">Sửa</button>
                                            <form action="{{ route('message.delete') }}" method="POST" class="inline"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">
                                                @csrf
                                                <input type="hidden" name="message_id" value="{{ $msg->id }}">
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-sm font-medium transition">Xóa</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-gray-700 leading-relaxed whitespace-pre-wrap break-words"
                                    id="msg-view-{{ $msg->id }}">{{ $msg->content }}</div>

                                @if (Auth::id() == $msg->sender_id)
                                    <form id="msg-edit-{{ $msg->id }}" action="{{ route('message.update') }}"
                                        method="POST" class="hidden mt-3 bg-gray-50 p-3 rounded border border-gray-200">
                                        @csrf
                                        <input type="hidden" name="message_id" value="{{ $msg->id }}">
                                        <textarea name="content" rows="3"
                                            class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-400 focus:outline-none" required>{{ $msg->content }}</textarea>
                                        <div class="mt-2 text-right space-x-2">
                                            <button type="button"
                                                class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm transition"
                                                onclick="toggleMsgEdit({{ $msg->id }})">Hủy</button>
                                            <button type="submit"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition">Lưu</button>
                                        </div>
                                    </form>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/message-handling.js') }}"></script>
@endpush