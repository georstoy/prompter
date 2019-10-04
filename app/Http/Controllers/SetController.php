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

        $newSet = Set::create(['name' => $request['name']]);
        if (isset($request['source_url'])){
            $newSet['source_url'] = $request['source_url'];
        }

        // Get html
        if ($request->hasFile('html')){
            $html = file_get_contents($request->html->path());
        } else {
            if (isset($newSet['source_url'])){
                $html = file_get_contents($newSet['source_url']);
            } else {
                return response()->json([
                    'action' => 'fetch',
                    'resource' => 'html file',
                    'error' => 'source_url missing.'
                ], '400');
            }
        }

        // Truncate
        if (isset($request['start_from_id'])){
            $html = $this->remove_head($html, $request['start_from_id']);
        }
        if (isset($request['stop_before_id'])){
            $html = $this->remove_tail($html, $request['stop_before_id']);
        }
        $parent = $this->find_parent($html, isset($request['parent']) ? $request['parent'] : '');
        if ($parent['content']!=''){
            $html = $parent['content'];
        }
        $newSet['parent'] = $parent['tag'];

        // Save html to file
        $html_filename = 'set_'.$newSet->_id.'.html';
        Storage::put(Set::HTML_STORAGE_PATH.$html_filename, $html);
        $newSet['html'] = asset(Set::HTML_PUBLIC_PATH.$html_filename);

        // Get content - TODO
        //$newSet['child'] = $this->find_child($html, isset($request['child']) ? $request['child'] : '');
        //$newSet['value'] = $this->find_value($html, isset($request['value']) ? $request['value'] : '');
        //
        //$content = $this->read_html(
        //    $html,
        //    $newSet['parent'],
        //    $newSet['child'],
        //    $newSet['value']
        //);
        //$newSet['content'] = $content;

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
