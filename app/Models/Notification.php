<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'importance',
        'notification_type',
        'content',
        'is_read',
        'related_url',
    ];

    protected $casts = [
        'importance' => 'string',
        'notification_type' => 'string',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
