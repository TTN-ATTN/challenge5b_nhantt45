<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\FileController;
use App\Http\Middleware\CheckConcurrentLogin;

// Các route không cần đăng nhập
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Tất cả các route dưới đây ĐỀU PHẢI NẰM TRONG middleware auth
Route::middleware(['auth', CheckConcurrentLogin::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile (sinh viên)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'updateStudent'])->name('profile.update');

    // Profile (giáo viên)
    Route::get('/create-student', [ProfileController::class, 'createStudentForm'])->name('student.create.form');
    Route::post('/create-student', [ProfileController::class, 'storeStudent'])->name('student.store');
    Route::post('/profile/teacher-update', [ProfileController::class, 'teacherUpdateStudent'])->name('student.teacher_update');
    Route::post('/profile/delete', [ProfileController::class, 'deleteStudent'])->name('student.delete');

    // Tin nhắn
    Route::post('/send-message', [MessageController::class, 'store'])->name('message.store');
    Route::post('/edit-message', [MessageController::class, 'update'])->name('message.update');
    Route::post('/delete-message', [MessageController::class, 'destroy'])->name('message.delete');

    // Bài tập
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments/create', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/submit', [AssignmentController::class, 'submit'])->name('assignments.submit');
    Route::post('/assignments/unsubmit', [AssignmentController::class, 'unsubmit'])->name('assignments.unsubmit');
    Route::post('/assignments/grade', [AssignmentController::class, 'grade'])->name('assignments.grade');
    Route::post('/assignments/delete', [AssignmentController::class, 'destroy'])->name('assignments.delete');

    // Module Giải đố (Challenges)
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/challenges/create', [ChallengeController::class, 'store'])->name('challenges.store');
    Route::post('/challenges/submit', [ChallengeController::class, 'submitAnswer'])->name('challenges.submit');
    Route::post('/challenges/delete', [ChallengeController::class, 'destroy'])->name('challenges.delete');

    Route::get('/download/assignment/{id}', [FileController::class, 'downloadAssignment'])->name('download.assignment');
    Route::get('/download/submission/{id}', [FileController::class, 'downloadSubmission'])->name('download.submission');
});
