<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['teacher_id', 'title', 'description', 'file_path', 'deadline'];

    // Bài tập thuộc về 1 giáo viên 
    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 1 bài tập có nhiều bài nộp
    public function submissions() {
        return $this->hasMany(Submission::class);
    }
}