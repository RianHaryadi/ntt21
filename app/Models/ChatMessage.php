<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_chat_session_id',
        'role',
        'content',
    ];

    public function travelChatSession()
    {
        return $this->belongsTo(TravelChatSession::class, 'travel_chat_session_id');
    }
}
