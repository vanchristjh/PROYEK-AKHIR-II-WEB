<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $action The action performed
     * @param string|null $description Additional description
     * @param \Illuminate\Database\Eloquent\Model|null $model The model associated with this activity
     * @param array $properties Additional properties to log
     * @param string $type Activity type for icon selection (default: 'system')
     * @return \App\Models\ActivityLog|null
     */
    public static function log($action, $description = null, $model = null, $properties = [], $type = 'system')
    {
        try {
            $user = Auth::user();
            
            $data = [
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'properties' => $properties,
                'type' => $type,
            ];
            
            if ($model instanceof Model) {
                $data['model_type'] = get_class($model);
                $data['model_id'] = $model->id;
            }
            
            $log = ActivityLog::create($data);
            
            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Log an activity related to downloading a file
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model being downloaded
     * @param string $description Description of the download activity
     * @return \App\Models\ActivityLog|null
     */
    public static function logDownload(Model $model, $description = null)
    {
        $modelName = class_basename($model);
        $description = $description ?: "Downloaded {$modelName}: {$model->title}";
          return self::log(
            'download',
            $description,
            $model,
            ['action' => 'download'],
            'download'
        );
    }
}
