<?php

namespace App\Traits;

use DOMDocument;
use App\Exceptions\DOMException as DOMException;
use App\Exceptions\InvalidArgument as InvalidArgument;

Trait ParseHTML
{
    public function read_html($html, $tag = '')
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $container = [];

        if ($tag!=''){
            $elements = $dom->getElementsByTagName($tag);
        } else {
            $elements = $dom->childNodes;
        }
        foreach ($elements as $el){
            $el_content = $this->get_content($el);
            foreach($el_content as $content)
                array_push($container, $content);
        }
        return $container;
    }

    public function get_content($node)
    {
        $container = [];

        if ($node->childNodes){
            foreach($node->childNodes as $el){
                $el_content = $this->get_content($el);
                foreach ($el_content as $content){
                    array_push($container, $content);
                }
            }
        } else {
            $content = trim($node->textContent);
            if ($content!=''){
                array_push($container, $content);
            }
        }
        return $container;
    }
}
