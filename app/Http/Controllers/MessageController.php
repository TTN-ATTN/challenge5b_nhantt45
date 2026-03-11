<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Gửi tin nhắn mới
    public function store(Request $request)
    {
        $senderId = Auth::id();
        $receiverId = $request->input('receiver_id');

        // Không cho phép tự gửi cho chính mình
        if ($senderId == $receiverId) {
            return back()->with('toast_error', 'Hành động từ chối: Không thể tự gửi tin nhắn cho chính mình!');
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ], [
            'content.required' => 'Nội dung tin nhắn không được để trống.',
            'receiver_id.exists' => 'Người nhận không tồn tại.'
        ]);

        Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $validated['receiver_id'],
            'content' => trim($validated['content'])
        ]);

        return back()->with('toast_success', 'Gửi tin nhắn thành công!');
    }

    // Sửa tin nhắn
    public function update(Request $request)
    {
        $validated = $request->validate([
            'message_id' => 'required|exists:messages,id',
            'content' => 'required|string'
        ]);

        $message = Message::findOrFail($validated['message_id']);

        // Xác minh quyền sở hữu tin nhắn
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền sửa tin nhắn này.');
        }

        $message->content = trim($validated['content']);
        $message->save();

        return back()->with('toast_success', 'Đã cập nhật tin nhắn!');
    }

    // Xóa tin nhắn
    public function destroy(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);

        $message = Message::findOrFail($request->input('message_id'));

        // Xác minh quyền sở hữu
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xóa tin nhắn này.');
        }

        $message->delete();

        return back()->with('toast_success', 'Đã xóa tin nhắn!');
    }
}