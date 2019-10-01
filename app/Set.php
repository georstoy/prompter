<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Set extends MongoModel
{
    protected $collection = 'sets';

    protected $fillable = [
        'request'
    ];

    protected $primaryKey = '_id';

    public $incrementing = false;

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
