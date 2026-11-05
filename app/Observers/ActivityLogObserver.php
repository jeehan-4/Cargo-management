<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogObserver
{
    /**
     * Handle the "created" event for any model we watch.
     */
    public function created(Model $model)
    {
        $this->log($model, 'created');
    }

    /**
     * Handle the "updated" event.
     */
    public function updated(Model $model)
    {
        $this->log($model, 'updated');
    }

    /**
     * Handle the "deleted" event (in case we ever need it).
     */
    public function deleted(Model $model)
    {
        $this->log($model, 'deleted');
    }

    /**
     * Central logging method.
     */
    protected function log(Model $model, string $event)
    {
        // Determine action type based on model class and event
        $actionMap = [
            'App\\Models\\Shipment' => [
                'created' => 'create_shipment',
                'updated' => 'update_shipment',
                'deleted' => 'delete_shipment',
            ],
            'App\\Models\\Driver' => [
                'created' => 'create_driver',
                'updated' => 'update_driver',
                'deleted' => 'delete_driver',
            ],
            'App\\Models\\Truck' => [
                'created' => 'create_truck',
                'updated' => 'update_truck',
                'deleted' => 'delete_truck',
            ],
            'App\\Models\\Destination' => [
                'created' => 'create_destination',
                'updated' => 'update_destination',
                'deleted' => 'delete_destination',
            ],
        ];

        $class = get_class($model);
        if (!isset($actionMap[$class][$event])) {
            // Not a model we care about
            return;
        }

        $actionType = $actionMap[$class][$event];

        // Capture old and new values for updates only
        $oldValues = null;
        $newValues = null;
        if ($event === 'updated') {
            $dirty = $model->getChanges();
            $original = $model->getOriginal();
            $oldValues = [];
            $newValues = [];
            foreach ($dirty as $key => $value) {
                $oldValues[$key] = $original[$key] ?? null;
                $newValues[$key] = $value;
            }
        }

        // Build relationship IDs if applicable
        $shipmentId = null;
        $driverId = null;
        if (method_exists($model, 'shipment')) {
            $shipmentId = $model->shipment->id ?? null;
        }
        if (method_exists($model, 'driver')) {
            $driverId = $model->driver->id ?? null;
        }
        // For shipment-specific events we can directly use its own ID (except on delete)
        if ($class === 'App\\Models\\Shipment' && $event !== 'deleted') {
            $shipmentId = $model->id;
        }
        if ($class === 'App\\Models\\Driver' && $event !== 'deleted') {
            $driverId = $model->id;
        }

        ActivityLog::create([
            'action_type' => $actionType,
            'shipment_id' => $shipmentId,
            'driver_id'   => $driverId,
            'user_id'     => Auth::id(),
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'notes'       => null,
        ]);
    }
}
?>