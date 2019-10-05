<?php

namespace App;

use App\Traits\ParseHTML;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class PromptableModel extends MongoModel
{
    use ParseHTML;

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

    # where files are saved
    const HTML_STORAGE_PATH = 'public/html_records/';
    const JSON_STORAGE_PATH = 'public/json';

    # where files are accessed
    const HTML_PUBLIC_PATH = 'storage/html_records/';
    const JSON_PUBLIC_PATH = 'storage/json';
}
