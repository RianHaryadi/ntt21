<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'status',
        'preferences',
        'recommendation_raw',
        'recommendation_edited',
    ];

    protected $casts = [
        'preferences' => 'array',
        'recommendation_edited' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'travel_chat_session_id');
    }
}
