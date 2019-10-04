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

    public function find_parent($html, $clue = '')
    {
        $parent = [
            'content' => '',
            'tag' => ''
        ];

        if ($clue=='None'){
            $parent['content'] = $html;
            $parent['tag'] = 'None';
            return $parent;
        }

        if ($clue!=''){
            # maybe the clue starts with <
            preg_match('/^<(\w+)/', $clue, $matches);
            if (!empty($matches)){
                # clue starts with <
                $tag = $matches[1]; # get the group
                $tag_q = preg_quote($tag, '/');
                $clue_q = preg_quote($clue, '/');
                $ptag_ptrn = '/'.$clue_q.'.*?<\/'.$tag_q.'>/s';
                preg_match_all($ptag_ptrn, $html, $matches);
                $parent = $this->get_largest($matches);
            } else {
                # clue doesn't start with < but
                # maybe the first word of the clue is a tag name
                preg_match('/[\w]+/', $clue, $matches);
                $tag = $matches[0];
                $tag_q = preg_quote($tag, '/');
                $rest = preg_replace('/'.$tag_q.'/', '', $clue, 1);
                $rest_q = preg_quote($rest, '/');
                $ptag_ptrn = '/<('.$tag_q.')'.$rest_q.'.*?<\/\1>/s';
                preg_match_all($ptag_ptrn, $html, $matches);
                if (!empty($matches[0])){
                    # first word is a tag
                    $parent = $this->get_largest($matches);
                } else {
                    # first word not a tag
                    $clue_q = preg_quote($clue, '/');
                    $ptag_ptrn = '/<(\w+)[^>]*'.$clue_q.'.*?<\/\1>/s';
                    preg_match_all($ptag_ptrn, $html, $matches);
                    $parent = $this->get_largest($matches);
                }
            }
        }
        # user didn't give a clue for the parent
        # or bad clue
        # make sure that a parent is returned
        if ($parent['content']==''){
            $ptag_ptrn = '/<(\w+).*<\/\1>/s';
            preg_match_all($ptag_ptrn, $html, $matches);
            $parent = $this->get_largest($matches);
        }

        # if there are no tags at all
        if ($parent['content']==''){
            $parent['content'] = $html;
            $parent['tag'] = 'None';
        }
            return $parent;
        }

    # Assuming the user is looking for
    # the parent element with most content
    protected function get_largest($parents)
    {
        $largest_parent['content'] = '';
        $largest_parent['tag'] = '';

        if (!empty($parents[0])){
            for ($i = 0; $i<count($parents[0]); $i++){
                if (strlen($parents[0][$i])>strlen($largest_parent['content'])){
                    $largest_parent['content']  = $parents[0][$i];
                    $largest_parent['tag']      = $parents[1][$i];
                }
            }
        }
        return $largest_parent;
    }

}
