<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Favorite.
 */
class Favorite extends Model
{
    use RecordsActivity;

    /**
     * @var array
     */
    protected $guarded = [];

    public static function boot()
    {
        // weird... need the below Activity() call, otherwise the call to
        // the trait bootRecordsActivity() will not work?
        app(Activity::class);

        parent::boot();

        // as of 12/31/2017, for some reason, I cannot call this form the
        // RecordsActivity trait; this should happen automatically?
        // this is duplicated in the RecordsActivity trait$%^&*...
        $recordActivity = 'recordActivity';
        static::created(function ($parent) use ($recordActivity) {
            $parent->$recordActivity('created');
        });
    }

    public function favorited()
    {
        return $this->morphTo();
    }
}
