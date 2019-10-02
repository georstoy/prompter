<?php

namespace App\Traits;

use DOMDocument;

Trait Fetcher
{

    public function fetch()
    {
        $filename = $this->_id;
        $page = $this->stripPage(
            $this->getPage($this->request['url']),
            $this->request['headId'],
            $this->request['footId']
        );
        $this->savePage($page, $filename);
        $this->content = $this->readPage(
            $page,
            $this->request['targetTag']
        );
    }

    public function stripPage($page, $headId, $footId)
    {
        $matches = [];
        $head = '/<{1}.*id=[\'\"]'.$headId.'[\'\"]>/';
        preg_match($head, $page, $matches);
        $headstr = $matches[0];

        $page = $headstr.preg_split($head, $page)[1];

        $foot = '/<{1}.*id=[\'\"]'.$footId.'[\'\"]>/';
        $page = preg_split($foot, $page)[0];

        return $page;
    }

    public function getPage($url)
    {
        return file_get_contents($url);
    }

    public function savePage($page, $filename)
    {
        file_put_contents($this->page_path($filename), $page);
    }

    public function readPage($filename, $targetTag)
    {

        $dom = new DOMDocument;
        if ($dom->loadHTML($this->page_path($filename))){
            $nodes = $dom->getElementsByTagName($targetTag);
            if (!empty($nodes)){
                $items = [];
                foreach($nodes as $node){
                    array_push($items, $node->nodeValue);
                }
            return $items;
            } else {
                throw new LengthException('No '.$targetTag.' tags found.');
            }
        } else {
            throw new BadFunctionCallException('Couldn\'t load the page.');
        }
        return $items;

    }

    protected function page_path($filename)
    {
        return storage_path('app/pages/'.$filename);
    }

}
