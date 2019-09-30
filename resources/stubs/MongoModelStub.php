<?php

namespace DummyNamespace;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class DummyClass extends MongoModel
{
    protected $collection = 'DummyCollection';

    protected $fillable = [

    ];

    protected $primaryKey = 'id';

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
