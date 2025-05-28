<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assignment;
use App\Models\SchoolClass;

class FixAssignmentClassroomRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:assignment-classroom-relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix assignments that are missing classroom relations but have class relations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to check and fix assignment-classroom relations...');
        
        // Get all assignments
        $assignments = Assignment::with('classes', 'classrooms')->get();
        
        $fixedCount = 0;
        $alreadyCorrectCount = 0;
        
        foreach ($assignments as $assignment) {
            // Skip if there are no classes attached
            if ($assignment->classes->isEmpty()) {
                continue;
            }
            
            // Get all class IDs for this assignment
            $classIds = $assignment->classes->pluck('id')->toArray();
            
            // Get all classroom IDs that should be attached
            $classroomIds = SchoolClass::whereIn('id', $classIds)
                ->pluck('classroom_id')
                ->filter() // Remove nulls
                ->unique()
                ->values()
                ->all();
                
            // Check if we need a fallback (if no classroom_ids found)
            if (empty($classroomIds)) {
                $this->warn("Assignment #{$assignment->id} ({$assignment->title}): No classroom_ids found in related classes. Using class IDs as fallback.");
                $classroomIds = $classIds;
            }
            
            // Get current classroom IDs
            $currentClassroomIds = $assignment->classrooms->pluck('id')->toArray();
            
            // Check if we need to sync
            $missing = array_diff($classroomIds, $currentClassroomIds);
            
            if (!empty($missing)) {
                // We have missing classroom relations
                $this->info("Fixing Assignment #{$assignment->id} ({$assignment->title}): Adding " . count($missing) . " classroom relations");
                
                // Get all classroom IDs (existing + missing)
                $allClassroomIds = array_unique(array_merge($currentClassroomIds, $classroomIds));
                
                // Sync classroom relations
                $assignment->classrooms()->sync($allClassroomIds);
                $fixedCount++;
            } else {
                $this->line("Assignment #{$assignment->id} ({$assignment->title}): Already has correct classroom relations");
                $alreadyCorrectCount++;
            }
        }
        
        $this->info("Finished checking and fixing assignment-classroom relations.");
        $this->info("Summary:");
        $this->info("- Total assignments checked: " . $assignments->count());
        $this->info("- Assignments fixed: {$fixedCount}");
        $this->info("- Assignments already correct: {$alreadyCorrectCount}");
        
        return 0;
    }
}
