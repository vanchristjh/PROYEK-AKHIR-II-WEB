<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentNotification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assignment_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'sender_id',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user who received this notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who sent this notification.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
        
        return $this;
    }
}
