<?php

namespace App\Http\Controllers;

use App\Set;
use App\Traits\TestTools;
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
        return response()->json([
            'action' => 'list',
            'resource_type' => 'Set',
            'resources' => Set::all()
        ]);
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
        return response()->json([
            'action' => 'create',
            'resource_type' => 'Set',
            'resource' => $set
        ]);
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
        $this->write($set);
        return response()->json([
            'action' => 'show',
            'resource_type' => 'Set',
            'resource' => $set
        ]);
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
        ]);
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
        ]);
    }
}
