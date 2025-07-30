<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key',
        'sender_number',
        'recipient_number',
        'message_body',
        'footer_text',
        'scheduled_at',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
