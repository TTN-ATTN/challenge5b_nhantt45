<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    // Khai báo TẤT CẢ các cột được phép lưu vào Database
    protected $fillable = [
        'teacher_id', 
        'hint', 
        'file_path', 
        'file_hash'
    ];

    // Mỗi challenge thuộc về một giáo viên
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}