<?php

namespace App;

use App\Traits\Fetcher;
use App\Traits\Reader;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class PromptableModel extends MongoModel
{
    use Fetcher, Reader;

    protected $fillable = [
        'name',
        'html',
        'url',
        'targetTag',
        'headId',
        'footId',
    ];

    protected $guarded = [
        '_id',
        'html_path',
        'content'
    ];

    protected $primaryKey = '_id';

    public $incrementing = true;

    public $html_filename;

    // where the html file is saved
    const HTML_STORAGE_PATH = 'public/html_records/';

    // where the html file is accessed
    const HTML_PUBLIC_PATH = 'storage/html_records/';

    // the result of reading the html
    public $content = [];

    // what element to look for in the html
    public $targetTag;

}
