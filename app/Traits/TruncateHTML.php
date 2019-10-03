<?php

namespace App\Traits;

use App\Exceptions\InvalidArgument as InvalidArgument;

Trait TruncateHTML
{
    use TestTools;

    public function remove_head($html, $id)
    {
        $pattern = '/<[^>]*id=[\'\"]'.$id.'[\'\"][^<]*>/';
        preg_match($pattern, $html, $matches);
        if (empty($matches)){
            throw new InvalidArgument('Can\'t find start tag with id: '.$id.'\'');
        }
        $start = $matches[0];
        return $start.preg_split($pattern, $html)[1];
        # start tag ^ included!
    }

    public function remove_tail($html, $id)
    {
        $pattern = '/<[^>]*id=[\'\"]'.$id.'[\'\"][^<]*>/';
        preg_match($pattern, $html, $matches);
        if (empty($matches)){
            throw new InvalidArgument('Can\'t find end tag with id: '.$id.'\'');
        }
        return preg_split($pattern, $html)[0];
    }

}
