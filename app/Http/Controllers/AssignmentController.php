<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    // Hiển thị danh sách bài tập
    public function index()
    {
        $user = Auth::user();
        
        // Eloquent Eager Loading: Tự động JOIN bảng teacher và submissions (kèm student)
        $assignments = Assignment::with(['teacher', 'submissions.student'])->orderBy('created_at', 'desc')->get();
        
        $mySubmissions = collect();
        if ($user->role === 'student') {
            // Lấy tất cả bài nộp của sinh viên này, dùng keyBy để dễ truy xuất theo assignment_id
            $mySubmissions = Submission::where('student_id', $user->id)->get()->keyBy('assignment_id');
        }

        return view('assignments', compact('assignments', 'mySubmissions'));
    }

    // Giáo viên giao bài tập mới
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            // Laravel check tự động: Max 100MB (102400 KB) và giới hạn đuôi file
            'assignment_file' => 'required|file|mimes:pdf,doc,docx,txt,zip,rar|max:102400'
        ], [
            'assignment_file.mimes' => 'Chỉ hỗ trợ upload file: pdf, doc, docx, txt, zip, rar.',
            'assignment_file.max' => 'Dung lượng file vượt quá 100MB.',
            'deadline.date' => 'Deadline không hợp lệ.'
        ]);

        $path = $request->file('assignment_file')->store('assignments', 'public');

        Assignment::create([
            'teacher_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'deadline' => $validated['deadline'],
            'file_path' => '/storage/' . $path
        ]);

        return back()->with('toast_success', 'Đã giao bài tập mới!');
    }

    // Sinh viên nộp bài
    public function submit(Request $request)
    {
        if (Auth::user()->role !== 'student') abort(403);

        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'submission_file' => 'required|file|mimes:pdf,doc,docx,txt,zip,rar|max:102400'
        ], [
            'submission_file.required' => 'Vui lòng chọn file bài làm để nộp.',
            'submission_file.mimes' => 'Chỉ hỗ trợ upload file: pdf, doc, docx, txt, zip, rar.',
            'submission_file.max' => 'Dung lượng file vượt quá 100MB.'
        ]);

        $assignmentId = $validated['assignment_id'];
        $studentId = Auth::id();

        // Xóa file cũ nếu đã từng nộp
        $existingSubmission = Submission::where('assignment_id', $assignmentId)->where('student_id', $studentId)->first();
        if ($existingSubmission) {
            $oldFilePath = str_replace('/storage/', '', $existingSubmission->file_path);
            Storage::disk('public')->delete($oldFilePath);
        }

        // Upload file mới
        $path = $request->file('submission_file')->store('submissions', 'public');

        // Tạo mới hoặc Cập nhật
        Submission::updateOrCreate(
            ['assignment_id' => $assignmentId, 'student_id' => $studentId],
            ['file_path' => '/storage/' . $path, 'created_at' => now()]
        );

        return back()->with('toast_success', 'Nộp bài thành công!');
    }

    // Sinh viên gỡ bài nộp
    public function unsubmit(Request $request)
    {
        if (Auth::user()->role !== 'student') abort(403);

        $submission = Submission::findOrFail($request->submission_id);
        if ($submission->student_id !== Auth::id()) abort(403);

        // Xóa file vật lý
        Storage::disk('public')->delete(str_replace('/storage/', '', $submission->file_path));
        
        $submission->delete();

        return back()->with('toast_success', 'Đã gỡ bài nộp!');
    }

    // Giáo viên chấm điểm
    public function grade(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'score' => 'required|numeric|min:0|max:10'
        ], [
            'score.min' => 'Điểm số phải nằm trong khoảng từ 0 đến 10.',
            'score.max' => 'Điểm số phải nằm trong khoảng từ 0 đến 10.',
            'score.numeric' => 'Điểm số không hợp lệ.'
        ]);

        $submission = Submission::findOrFail($validated['submission_id']);
        $submission->score = $validated['score'];
        $submission->save();

        return back()->with('toast_success', 'Đã lưu điểm thành công!');
    }

    // Giáo viên xóa bài tập
    public function destroy(Request $request)
    {
        if (Auth::user()->role !== 'teacher') abort(403);

        $assignment = Assignment::findOrFail($request->assignment_id);
        if ($assignment->teacher_id !== Auth::id()) abort(403);

        // Xóa file đề bài vật lý
        Storage::disk('public')->delete(str_replace('/storage/', '', $assignment->file_path));

        // Note: Do có cascade on delete ở DB, các submission cũng sẽ bị xóa. 
        // Nếu muốn dọn sạch luôn file submission vật lý thì thêm vòng lặp xóa file.
        foreach ($assignment->submissions as $sub) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $sub->file_path));
        }

        $assignment->delete();

        return back()->with('toast_success', 'Đã xóa bài tập!');
    }
}