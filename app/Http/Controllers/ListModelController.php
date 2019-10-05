<?php

namespace App\Http\Controllers;

use App\ListModel;
use App\Traits\TestTools;
use App\Traits\ParseHTML;
use App\Traits\TruncateHTML;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListModelController extends Controller
{
    use TruncateHTML, ParseHTML, TestTools;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $lists = ListModel::all();
        $indexed_lists = [];
        foreach($lists as $list){
            array_push($indexed_lists, [
                'id' => $list['_id'],
                'name' => $list['name'],
                'item_count' => $list['content']['item_count'],
                'content' => $list['content']['json']
            ]);
        }

        if (count($lists)){
            return response()->json([
                'action' => 'index',
                'resource_type' => 'List',
                'results' => count($lists),
                'resources' => $indexed_lists
            ]);

        } else {
            return response()->json([
                'action' => 'list',
                'resource_type' => 'List',
                'error' => 'This list is empty.'
            ], '404');
        }
        ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $newList = ListModel::create();
        $newList['name'] = $request['name'];
        if (isset($request['source_url'])){
            $newList['source_url'] = $request['source_url'];
        }

        // Get html
        if ($request->hasFile('html')){
            $html_content = file_get_contents($request->html->path());
        } else {
            if (isset($newList['source_url'])){
                $html_content = file_get_contents($newList['source_url']);
            } else {
                return response()->json([
                    'action' => 'fetch',
                    'resource' => 'html file',
                    'error' => 'source_url missing.'
                ], '400');
            }
        }

        // Truncate
        $filters = [];
        if (isset($request['start_from_id'])){
            $filters['start_from_id'] = $request['start_from_id'];
            $html = $this->remove_head($html_content, $request['start_from_id']);
        }
        if (isset($request['stop_before_id'])){
            $filters['stop_before_id'] = $request['stop_before_id'];
            $html = $this->remove_tail($html_content, $request['stop_before_id']);
        }
        $parent = $this->find_parent($html_content, isset($request['parent']) ? $request['parent'] : '');
        if ($parent['content']!=''){
            $html_content = $parent['content'];
        }
        $html_data = [];
        $html_data['parent'] = $parent['tag'];
        $html_data['item'] = isset($request['item']) ? $request['item'] : '';
        $html_data['filters'] = $filters;

        // Save html to public file
        $html_filename = ListModel::PREFIX.$newList->_id.'.html';
        Storage::put(ListModel::HTML_STORAGE_PATH.$html_filename, $html_content);
        $html_link = asset(ListModel::HTML_PUBLIC_PATH.$html_filename);
        $html_data = array('link' => $html_link) + $html_data;

        // Get content TODO: asynchronous
        $content_data = [];
        $content = $this->read_html(
            $html_content,
            $html_data['item']
        );
        $content_data['item_count'] = count($content);
            // Save content to public Json
        $json_filename = ListModel::PREFIX.$newList->_id.'.json';
        Storage::put(ListModel::JSON_STORAGE_PATH.$json_filename, json_encode($content));
        $json_link = asset(ListModel::JSON_PUBLIC_PATH.$json_filename);
        $content_data['json'] = $json_link;


        $newList['html'] = $html_data;
        $newList['content'] = $content_data;
        $newList->save();
        return response()->json([
            'action' => 'create',
            'resource_type' => 'List',
            'resource' => $newList
        ], '201');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ListModel  $list
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $list = ListModel::find($id);
        if (!empty($list)){

            $show_schema = [
                'list_id' => $list['_id'],
                'name' => isset($list['name']) ? $list['name'] : 'Unnamed',
                'item_count' => isset($list['content']['item_count']) ? $list['content']['item_count'] : 'Unknown',
                'content' => isset($list['content']['json']) ? $list['content']['json'] : 'Unknown',
                'created_at' => $list['created_at'],
                'updated_at' => $list['updated_at'],
                'html'  => [
                    'source' => isset($list['source_url']) ? $list['source_url'] : 'Upload',
                    'filters' => !empty($list['html']['filters']) ? $list['html']['filters'] : 'None',
                    'link' => isset($list['html']['link']) ? $list['html']['link'] : 'Unavailable',
                    'parent' => isset($list['html']['parent']) ? $list['html']['parent'] : 'None',
                    'item' => isset($list['html']['item']) ? $list['html']['item'] : 'Undefined'
                ]
            ];

            return response()->json([
                'action' => 'show',
                'resource_type' => 'List',
                'resource' => $show_schema
            ], '200');
        } else {
            return response()->json([
                'action' => 'show',
                'resource_type' => 'List',
                'error' => 'This list does not exist.'
            ], '404');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ListModel  $list
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $list = ListModel::find($id);
        $data['request']= $request->all();
        $list->fill($data);
        $list->save();
        return response()->json([
            'action' => 'update',
            'resource_type' => 'List',
            'resource' => $list
        ], '200');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ListModel  $list
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $list = ListModel::find($id);
        $list->delete();
        return response()->json([
            'action' => 'delete',
            'message' => 'List with ID '.$id.' was deleted!',
            'resource_type' => 'List',
        ], '200');
    }
}
