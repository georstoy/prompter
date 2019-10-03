<?php

namespace App\Traits;

use DOMDocument;
use App\Exceptions\DOMException as DOMException;
use App\Exceptions\InvalidArgument as InvalidArgument;

Trait ParseHTML
{
    public function read_html()
    {
        if (empty($this->targetTag)){
            throw new InvalidArgument('targetTag missing.');
        }

        $dom = new DOMDocument;
        if ($dom->loadHTML($this->html)){
            $nodes = $dom->getElementsByTagName($this->targetTag);
            if (!empty($nodes)){
                foreach($nodes as $node){
                    array_push($this->content, $node->nodeValue);
                }
            } else {
                throw new InvalidArgument('No '.$this->targetTag.' tags found.');
            }
        } else {
            throw new DOMException('Couldn\'t load the html.');
        }
    }
}
