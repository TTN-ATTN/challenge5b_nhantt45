<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Hàm dùng chung: Kiểm tra mật khẩu hiện tại của người đang thao tác
     */
    private function checkCurrentPassword(Request $request)
    {
        if (!$request->filled('current_password')) {
            return false;
        }
        return Hash::check($request->current_password, Auth::user()->password);
    }

    // Hiển thị hồ sơ
    public function show(Request $request)
    {
        $targetId = $request->query('id', Auth::id());
        $profileUser = User::findOrFail($targetId);

        $isOwnProfile = (Auth::id() == $profileUser->id);

        // Lấy tin nhắn
        $messages = Message::with('sender')->where('receiver_id', $targetId)->orderBy('created_at', 'desc')->get();

        return view('profile', compact('profileUser', 'isOwnProfile', 'messages'));
    }

    // Sinh viên tự cập nhật thông tin
    public function updateStudent(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'student') abort(403, 'Chỉ sinh viên mới được tự cập nhật thông tin.');

        if (!$this->checkCurrentPassword($request)) {
            return back()->with('toast_error', 'Mật khẩu hiện tại không chính xác!');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'regex:/^[0-9+\-\s]+$/', Rule::unique('users', 'phone_number')->ignore($user->id)],
            'avatar_file' => 'nullable|mimes:jpeg,png,gif|max:2048',
            'new_password' => 'nullable'
        ], [
            'email.unique' => 'Email đã tồn tại, vui lòng sử dụng email khác!',
            'email.email' => 'Định dạng Email không hợp lệ!',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và các dấu + -',
            'phone.unique' => 'Số điện thoại đã tồn tại, vui lòng sử dụng số khác!',
            'avatar_file.mimes' => 'Chỉ hỗ trợ upload file ảnh (JPG, PNG, GIF).',
            'avatar_file.max' => 'Dung lượng ảnh vượt quá 2MB.',
        ]);

        $user->email = $validated['email'];
        $user->phone_number = $validated['phone'];

        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }

        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        } elseif ($request->filled('avatar_url')) {
            $url = $request->avatar_url;
            if (preg_match('/^https?:\/\//i', $url)) {
                $user->avatar = $url;
            }
        }

        $user->save();
        return back()->with('toast_success', 'Cập nhật thông tin thành công!');
    }

    // Hiển thị form tạo sinh viên
    public function createStudentForm()
    {
        if (Auth::user()->role !== 'teacher') abort(403, 'Chỉ Giáo viên mới có quyền truy cập khu vực này.');
        return view('create-student');
    }

    // Giáo viên tạo sinh viên mới
    public function storeStudent(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        if (!$this->checkCurrentPassword($request)) {
            return back()->withInput()->with('toast_error', 'Mật khẩu hiện tại không chính xác để xác nhận!');
        }

        $validated = $request->validate([
            'username' => ['required', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users'],
            'full_name' => ['required', 'regex:/^[a-zA-ZÀ-ỹ\s0-9]+$/u'],
            'email' => 'required|email|unique:users',
            'phone' => ['required', 'regex:/^[0-9+\-\s]+$/', 'unique:users,phone_number'],
            'password' => 'required'
        ], [
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới!',
            'username.unique' => 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!',
            'full_name.regex' => 'Họ tên chỉ được chứa chữ cái, khoảng trắng và số!',
            'email.unique' => 'Email đã tồn tại, vui lòng sử dụng email khác!',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và các dấu + -',
            'phone.unique' => 'Số điện thoại đã tồn tại, vui lòng sử dụng số khác!',
        ]);

        User::create([
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'student'
        ]);

        return redirect('/')->with('toast_success', 'Tạo sinh viên mới thành công!');
    }

    // Giáo viên cập nhật hồ sơ sinh viên
    public function teacherUpdateStudent(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        if (!$this->checkCurrentPassword($request)) {
            return back()->with('toast_error', 'Mật khẩu hiện tại không chính xác!');
        }

        $targetUser = User::findOrFail($request->student_id);

        $validated = $request->validate([
            'username' => ['required', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users')->ignore($targetUser->id)],
            'full_name' => ['required', 'regex:/^[a-zA-ZÀ-ỹ\s0-9]+$/u'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($targetUser->id)],
            'phone' => ['required', 'regex:/^[0-9+\-\s]+$/', Rule::unique('users', 'phone_number')->ignore($targetUser->id)],
            'new_password' => 'nullable'
        ], [
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới!',
            'username.unique' => 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!',
            'full_name.regex' => 'Họ tên chỉ được chứa chữ cái, khoảng trắng và số!',
            'email.unique' => 'Email đã tồn tại, vui lòng sử dụng email khác!',
            'phone.regex' => 'Số điện thoại chỉ được chứa số và các dấu + -',
            'phone.unique' => 'Số điện thoại đã tồn tại, vui lòng sử dụng số khác!',
        ]);

        $targetUser->username = $validated['username'];
        $targetUser->full_name = $validated['full_name'];
        $targetUser->email = $validated['email'];
        $targetUser->phone_number = $validated['phone'];

        if ($request->filled('new_password')) {
            $targetUser->password = Hash::make($validated['new_password']);
        }

        $targetUser->save();

        return back()->with('toast_success', 'Cập nhật thông tin sinh viên thành công!');
    }

    // Giáo viên xóa sinh viên
    public function deleteStudent(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        if (!$this->checkCurrentPassword($request)) {
            return back()->with('toast_error', 'Mật khẩu hiện tại không chính xác!');
        }

        $targetUser = User::findOrFail($request->student_id);
        if ($targetUser->role !== 'student') {
            return back()->with('toast_error', 'Chỉ có thể xóa tài khoản sinh viên!');
        } else {
            $targetUser->delete();
        }
        return redirect('/')->with('toast_success', 'Xóa sinh viên thành công!');
    }
}
