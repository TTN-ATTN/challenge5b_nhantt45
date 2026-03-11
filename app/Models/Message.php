<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'content'];

    // Một message thuộc về một người gửi
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Một message thuộc về một người nhận 
    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}