<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChallengeController extends Controller
{
    // Hàm lọc dấu tiếng Việt từ code cũ của bạn
    private function normalizeString($str) {
        $str = mb_strtolower(trim($str), 'UTF-8');
        
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        
        $str = preg_replace('/\s+/', ' ', $str);
        
        return $str;
    }

    public function index()
    {
        $challenges = Challenge::with('teacher')->orderBy('created_at', 'desc')->get();
        return view('challenges', compact('challenges'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $validated = $request->validate([
            'hint' => 'required|string',
            'secret_file' => 'required|file|mimes:txt|max:2048'
        ], [
            'hint.required' => 'Vui lòng nhập gợi ý.',
            'secret_file.required' => 'Bạn phải tải lên file .txt chứa đáp án.',
            'secret_file.mimes' => 'File đáp án bí mật bắt buộc phải là định dạng .txt',
            'secret_file.max' => 'File quá lớn (tối đa 2MB).'
        ]);

        $file = $request->file('secret_file');
        
        // Logic từ code cũ: Tên file là đáp án
        $fileName = $file->getClientOriginalName(); // VD: Sơn Tùng M-TP.txt
        $rawAnswer = pathinfo($fileName, PATHINFO_FILENAME); // Lấy chữ: Sơn Tùng M-TP
        $answer = $this->normalizeString($rawAnswer); // Thành: son tung m-tp
        $fileHash = md5($answer); // Băm ra mã MD5

        // Lưu file với tên là chuỗi hash
        $file->storeAs('challenges', $fileHash . '.txt', 'public');

        Challenge::create([
            'teacher_id' => Auth::id(),
            'hint' => $validated['hint'],
            'file_hash' => $fileHash
        ]);

        return back()->with('toast_success', 'Tạo thử thách thành công!');
    }

    public function submitAnswer(Request $request)
    {
        if (Auth::user()->role !== 'student') abort(403);

        $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'answer' => 'required|string'
        ]);

        $challenge = Challenge::findOrFail($request->challenge_id);
        
        // Xử lý câu trả lời của sinh viên giống hệt lúc tạo
        $studentAnswer = $this->normalizeString($request->answer);
        $inputHash = md5($studentAnswer);
        
        if ($inputHash === $challenge->file_hash) {
            $filePath = 'challenges/' . $inputHash . '.txt';
            
            if (Storage::disk('public')->exists($filePath)) {
                $content = Storage::disk('public')->get($filePath);
                
                // Trả về kèm theo nội dung file giải mã thành công (giống code cũ)
                return back()
                    ->with('toast_success', 'Chúc mừng! Đáp án chính xác.')
                    ->with('solved_id', $challenge->id)
                    ->with('solved_content', $content);
            } else {
                return back()->with('toast_error', 'Lỗi hệ thống: Không tìm thấy file gốc.');
            }
        } else {
            return back()->with('toast_error', 'Đáp án chưa chính xác, thử lại nhé!');
        }
    }

    public function destroy(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $challenge = Challenge::findOrFail($request->challenge_id);
        if ($challenge->teacher_id !== Auth::id()) abort(403);

        $filePath = 'challenges/' . $challenge->file_hash . '.txt';
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        $challenge->delete();

        return back()->with('toast_success', 'Đã xóa thử thách!');
    }
}