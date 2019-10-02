<?php

namespace App;

use App\Traits\Fetcher;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Set extends MongoModel
{
    use Fetcher;

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
