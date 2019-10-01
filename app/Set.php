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
}
