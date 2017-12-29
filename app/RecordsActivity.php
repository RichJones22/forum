<?php

declare(strict_types=1);

namespace app;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

/**
 * Trait RecordsActivity.
 */
trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) {
            return;
        }

        /** @var Model $model */
        $model = get_class();

        // not needed; used to suppress IDE highlighting....
        $recordActivity = 'recordActivity';
        foreach (static::getActivitiesToRecord() as $event) {
            // call to model event handler, 'created'
            $model::$event(function ($parent) use ($recordActivity) {
                $parent->$recordActivity('created');
            });
        }

        // not needed; used to suppress IDE highlighting....
        $activity = 'activity';
        $model::deleting(function ($model) use ($activity) {
            $model->$activity()->delete();
        });
    }

    /**
     * @return array
     */
    protected static function getActivitiesToRecord()
    {
        // model events
        return [
            'created',
        ];
    }

    /**
     * @param string $event
     */
    protected function recordActivity(String $event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    protected function activity()
    {
        /** @var Model $model */
        $model = $this;

        return $model->morphMany(Activity::class, 'subject');
    }

    /**
     * @param string $event
     *
     * @return string
     */
    protected function getActivityType(String $event): string
    {
        return $event.'_'.strtolower((new ReflectionClass($this))->getShortName());
    }
}
