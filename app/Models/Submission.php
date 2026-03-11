<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['assignment_id', 'student_id', 'file_path', 'score'];

    // Một bài nộp thuộc về một bài tập
    public function assignment() {
        return $this->belongsTo(Assignment::class);
    }

    // Một bài nộp thuộc về một sinh viên
    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }
}