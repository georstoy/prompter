<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class PromptableModel extends MongoModel
{
    protected $fillable = [
        'name',
        'html',
        'source_url'
    ];

    protected $guarded = [
        '_id',
        'html_path',
        'content'
    ];

    protected $primaryKey = '_id';

    public $incrementing = true;

    // where the html file is saved
    const HTML_STORAGE_PATH = 'public/html_records/';

    // where the html file is accessed
    const HTML_PUBLIC_PATH = 'storage/html_records/';

    // the result of reading the html
    public $content = [];

    // what element to look for in the html
    public $targetTag;

}
