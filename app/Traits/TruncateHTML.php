<?php

namespace App\Traits;

use App\Exceptions\InvalidArgument as InvalidArgument;

Trait TruncateHTML
{
    use TestTools;

    //Todo: implement parentTag
    public function strip_html($headId = '', $footId = '')
    {
        $matches = [];

        if (!empty(headId)){
            $head = '/<{1}.*id=[\'\"]'.$headId.'[\'\"]>/'; # matches from the beggining of the line
            preg_match($head, $this->html, $matches);
            if (empty($matches)){
                throw new InvalidArgument('Head not found. No html element with id=\''.$headId.'\'');
            }
            $headstr = $matches[0];

            $this->html = $headstr.preg_split($head, $this->html)[1];

        }

        if (!empty(footId)){
            $foot = '/<{1}.*id=[\'\"]'.$footId.'[\'\"]>/'; # matches from the beggining of the line
            preg_match($foot, $this->html, $matches);
            if (empty($matches)){
                throw new InvalidArgument('Foot not found. No element with id=\''.$footId.'\'');
            }
            $this->html = preg_split($foot, $this->html)[0];
        }
    }

}
