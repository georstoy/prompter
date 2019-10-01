<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Set extends MongoModel
{
    protected $collection = 'sets';

    protected $fillable = [
        'request'
    ];

    protected $guarded = [
        '_id'
    ];

    protected $primaryKey = '_id';

    public $incrementing = true;

    /**
     * model life cycle event listeners
     */
    public static function boot(){
        parent::boot();

        static::creating(function ($instance){
            if (!$instance->exists) {
                $instance->id = uniqid();
            }
        });

        static::created(function ($instance){

        });
    }
}
