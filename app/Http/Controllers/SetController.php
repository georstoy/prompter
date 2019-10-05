<?php

namespace App\Http\Controllers;

use App\Set;
use App\Traits\TestTools;
use App\Traits\ParseHTML;
use App\Traits\TruncateHTML;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SetController extends Controller
{
    use TruncateHTML, ParseHTML, TestTools;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $sets = Set::all();
        if (count($sets)){
            return response()->json([
                'action' => 'list',
                'resource_type' => 'Set',
                'count' => count($sets),
                'resources' => $sets
            ]);

        } else {
            return response()->json([
                'action' => 'list',
                'resource_type' => 'Set',
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

        $newSet = Set::create();
        $newSet['name'] = $request['name'];
        if (isset($request['source_url'])){
            $newSet['source_url'] = $request['source_url'];
        }

        // Get html
        if ($request->hasFile('html')){
            $html_content = file_get_contents($request->html->path());
        } else {
            if (isset($newSet['source_url'])){
                $html_content = file_get_contents($newSet['source_url']);
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

        // Save html to file
        $html_filename = 'set_'.$newSet->_id.'.html';
        Storage::put(Set::HTML_STORAGE_PATH.$html_filename, $html_content);
        $html_link = asset(Set::HTML_PUBLIC_PATH.$html_filename);
        $html_data = array('link' => $html_link) + $html_data;

        // Get content TODO: asynchronous
        $newSet['content'] = $this->read_html(
            $html_content,
            $html_data['item']
        );

        $newSet['html'] = $html_data;
        $newSet->save();
        return response()->json([
            'action' => 'create',
            'resource_type' => 'Set',
            'resource' => $newSet
        ], '201');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Set  $set
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $set = Set::find($id);
        if (!empty($set)){
            return response()->json([
                'action' => 'show',
                'resource_type' => 'Set',
                'resource' => $set
            ], '200');
        } else {
            return response()->json([
                'action' => 'show',
                'resource_type' => 'Set',
                'error' => 'This set does not exist.'
            ], '404');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Set  $set
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $set = Set::find($id);
        $data['request']= $request->all();
        $set->fill($data);
        $set->save();
        return response()->json([
            'action' => 'update',
            'resource_type' => 'Set',
            'resource' => $set
        ], '200');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Set  $set
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $set = Set::find($id);
        $set->delete();
        return response()->json([
            'action' => 'delete',
            'message' => 'Set with ID '.$id.' was deleted!',
            'resource_type' => 'Set',
        ], '200');
    }
}
