<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'subject_id',
        'teacher_id',
        'publish_date',
        'is_active',
        'audience',
        'expiry_date'
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'audience' => 'json'
    ];

    /**
     * Get the teacher who created this material
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the subject this material belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the classrooms this material is shared with
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_material')->withTimestamps();
    }

    /**
     * Check if this material is currently valid
     */
    public function isValid()
    {
        $now = now();
        return $this->is_active && 
               $now->gte($this->publish_date) && 
               (!$this->expiry_date || $now->lte($this->expiry_date));
    }

    /**
     * Check if this material is new (less than 7 days old)
     */
    public function isNew()
    {
        return $this->publish_date->diffInDays(now()) < 7;
    }

    /**
     * Get the file URL
     */
    public function getFileUrl()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Check if material is accessible by a specific user
     */
    public function isAccessibleBy(User $user)
    {
        if ($user->isAdmin() || $user->id === $this->teacher_id) {
            return true;
        }

        if ($user->isStudent()) {
            return $this->classrooms()
                ->where('classroom_id', $user->classroom_id)
                ->exists();
        }

        return false;
    }
    
    /**
     * Get the color class for file type
     */
    public function getFileColorAttribute()
    {
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        return match (strtolower($extension)) {
            'pdf' => 'text-red-500',
            'doc', 'docx' => 'text-blue-600',
            'xls', 'xlsx' => 'text-green-600',
            'ppt', 'pptx' => 'text-orange-500',
            'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
            'zip', 'rar' => 'text-gray-600',
            'mp4', 'avi', 'mov' => 'text-blue-500',
            'mp3', 'wav' => 'text-indigo-500',
            default => 'text-gray-500',
        };
    }
    
    /**
     * Get the icon for file type
     */
    public function getFileIconAttribute()
    {
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        return match (strtolower($extension)) {
            'pdf' => 'fa-file-pdf',
            'doc', 'docx' => 'fa-file-word',
            'xls', 'xlsx' => 'fa-file-excel',
            'ppt', 'pptx' => 'fa-file-powerpoint',
            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
            'zip', 'rar' => 'fa-file-archive',
            'mp4', 'avi', 'mov' => 'fa-file-video',
            'mp3', 'wav' => 'fa-file-audio',
            default => 'fa-file-alt',
        };
    }
    
    /**
     * Get short representation of file type
     */
    public function getFileTypeShort()
    {
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        return match (strtolower($extension)) {
            'pdf' => 'PDF',
            'doc', 'docx' => 'DOC',
            'xls', 'xlsx' => 'XLS',
            'ppt', 'pptx' => 'PPT',
            'jpg', 'jpeg' => 'JPG',
            'png' => 'PNG',
            'gif' => 'GIF',
            'zip' => 'ZIP',
            'rar' => 'RAR',
            'mp4', 'avi', 'mov' => 'VIDEO',
            'mp3', 'wav' => 'AUDIO',
            default => strtoupper($extension),
        };
    }

    /**
     * Get the type of file based on its extension
     */
    public function getFileType()
    {
        if (!$this->file_path) {
            return 'Unknown';
        }

        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'PDF Document';
            case 'ppt':
            case 'pptx':
                return 'PowerPoint Presentation';
            case 'doc':
            case 'docx':
                return 'Word Document';
            default:
                return 'Other Document';
        }
    }
}
