<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use Illuminate\Support\Collection;

class GuruDataExport implements FromCollection, WithHeadings
{
    protected $user;
    
    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // You can customize this method to export different types of data
        // For example, this exports the teacher's assignments
        $assignments = $this->user->assignments ?? collect();
        
        if ($assignments instanceof Collection) {
            return $assignments->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date,
                    'created_at' => $assignment->created_at,
                ];
            });
        }
        
        return collect(); // Return empty collection if no assignments
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Due Date',
            'Created At',
        ];
    }
}
