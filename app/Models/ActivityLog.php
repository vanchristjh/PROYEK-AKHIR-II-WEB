<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'properties',
        'ip_address',
        'user_agent',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
