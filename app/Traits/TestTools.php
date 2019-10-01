<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait TestTools
{
    public function write($info)
    {
        Log::channel('test')->info($info);
    }
}
