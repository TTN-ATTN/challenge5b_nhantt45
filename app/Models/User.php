<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Các trường được phép thêm/sửa
    protected $fillable = [
        'username', 'password', 'full_name', 'email', 'phone_number', 'avatar', 'role', 'session_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Định nghĩa các mối quan hệ
    public function assignments() {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    public function submissions() {
        return $this->hasMany(Submission::class, 'student_id');
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}