<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Challenge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    // Tải đề bài
    public function downloadAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        $filename = basename($assignment->file_path);
        $path = 'assignments/' . $filename;
        
        if (!Storage::disk('local')->exists($path)) abort(404, 'File không tồn tại');
        
        // Tránh trùng tên bằng ID bài tập
        $downloadName = 'DeBai_ID' . $assignment->id . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        
        return Storage::disk('local')->download($path, $downloadName);
    }

    // Tải bài nộp 
    public function downloadSubmission($id)
    {
        $submission = Submission::with(['student', 'assignment'])->findOrFail($id);
        
        // Chỉ giáo viên hoặc chính sinh viên đó mới được tải file nộp về
        if (Auth::user()->role !== 'teacher' && Auth::id() !== $submission->student_id) {
            abort(403, 'Bạn không có quyền tải file này.');
        }

        $filename = basename($submission->file_path);
        $path = 'submissions/' . $filename;
        
        if (!Storage::disk('local')->exists($path)) abort(404, 'File không tồn tại');
        
        // Format: [Username]_[Ten_Bai_Tap]_[SubmissionID].[extension]
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Loại bỏ các ký tự đặc biệt có thể gây lỗi trên Windows/Mac
        $safeUsername = preg_replace('/[^a-zA-Z0-9_-]/', '', $submission->student->username);
        $safeAssignmentTitle = Str::slug($submission->assignment->title, '_'); 
        
        $downloadName = $safeUsername . '_' . $safeAssignmentTitle . '_SubID' . $submission->id . '.' . $extension;

        return Storage::disk('local')->download($path, $downloadName);
    }
}