<?php

namespace DummyNamespace;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class DummyClass extends MongoModel
{
    protected $fillable = [

    ];
    protected $guarded = [
        '_id'
    ];

    protected $primaryKey = '_id';

    public $incrementing = true;
}
