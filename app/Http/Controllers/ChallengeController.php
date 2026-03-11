<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChallengeController extends Controller
{
    // Hiển thị danh sách thử thách
    public function index()
    {
        // Lấy tất cả thử thách, xếp mới nhất lên đầu kèm thông tin giáo viên tạo
        $challenges = Challenge::with('teacher')->orderBy('created_at', 'desc')->get();
        return view('challenges', compact('challenges'));
    }

    // Giáo viên tạo thử thách mới
public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $validated = $request->validate([
            'hint' => 'nullable|string',
            'secret_file' => 'required|file|mimes:txt|max:2048'
        ], [
            'secret_file.required' => 'Bạn phải tải lên file .txt chứa đáp án.',
            'secret_file.mimes' => 'File đáp án bí mật bắt buộc phải là định dạng .txt',
            'secret_file.max' => 'File quá lớn (tối đa 2MB).'
        ]);

        $file = $request->file('secret_file');
        $fileHash = md5_file($file->getRealPath());
        $path = $file->store('challenges', 'public');

        Challenge::create([
            'teacher_id' => Auth::id(),
            'hint' => $validated['hint'],
            'file_path' => '/storage/' . $path,
            'file_hash' => $fileHash
        ]);

        return back()->with('toast_success', 'Tạo thử thách thành công!');
    }

    // Sinh viên trả lời thử thách
    public function submitAnswer(Request $request)
    {
        if (Auth::user()->role !== 'student') abort(403);

        $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'answer' => 'required|string'
        ]);

        $challenge = Challenge::findOrFail($request->challenge_id);

        // Lấy đường dẫn thật của file trong hệ thống
        $relativePath = str_replace('/storage/', '', $challenge->file_path);

        // Kiểm tra file có tồn tại không
        if (!Storage::disk('public')->exists($relativePath)) {
            return back()->with('toast_error', 'Lỗi hệ thống: Không tìm thấy file đáp án!');
        }

        // Đọc nội dung file .txt
        $secretContent = Storage::disk('public')->get($relativePath);

        // So sánh đáp án (xóa khoảng trắng thừa ở 2 đầu để tránh lỗi gõ nhầm space)
        if (trim($request->answer) === trim($secretContent)) {
            return back()->with('toast_success', '🎉 CHÍNH XÁC! Bạn đã giải mã thành công thử thách này!');
        } else {
            return back()->with('toast_error', 'Sai rồi! Hãy thử lại nhé.');
        }
    }

    // Giáo viên xóa thử thách
    public function destroy(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $challenge = Challenge::findOrFail($request->challenge_id);
        if ($challenge->teacher_id !== Auth::id()) abort(403);

        // Xóa file vật lý
        Storage::disk('public')->delete(str_replace('/storage/', '', $challenge->file_path));

        // Xóa record trong Database
        $challenge->delete();

        return back()->with('toast_success', 'Đã xóa thử thách!');
    }
}
