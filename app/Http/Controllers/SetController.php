<?php

namespace App\Http\Controllers;

use App\Set;
use App\Traits\TestTools;
use Exception;
use Illuminate\Http\Request;

class SetController extends Controller
{
    use TestTools;
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
        $data['request'] = $request->all();
        $set = Set::create($data);
        try{
            $set->fetch();
            $set->save();
        }
        catch (Exception $e){
            return response()->json([
                'action' => 'create',
            'resource_type' => 'Set',
                'error' => $e->getMessage()
            ], '400');
        }

        return response()->json([
            'action' => 'create',
            'resource_type' => 'Set',
            'resource' => $set
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
