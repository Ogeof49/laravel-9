<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use App\Http\Requests\StoreStageRequest;
use App\Http\Requests\UpdateStageRequest;
use App\Models\Client;
use App\Models\Stage;
use App\Models\Location;


class StageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Stage $stage, Request $request)
    {
        
        $locations = Location::all();
        $stages = Stage::all();
        $stages = Stage::latest();

        if($request->get('status') == 'archived') {
            $stages = $stages->onlyTrashed();
        }

        $stages = $stages->paginate(10);

        return view('admin.stages.index', compact('stages','stages','locations'));
    }


    public function edit_stages(Stage $stage, Request $request)
    {
        $stages = Stage::latest();

        if($request->get('status') == 'archived') {
            $stages = $stages->onlyTrashed();
        }

        $stages = $stages->paginate(10);

        return view('admin.stages.edit', compact('stages'));
    }

    public function delete_stages(Stage $stage, Request $request)
    {
        $stages = Stage::latest();

        if($request->get('status') == 'archived') {
            $stages = $stages->onlyTrashed();
        }

        $stages = $stages->paginate(10);

        return view('admin.stages.delete', compact('stages'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stages = Stage::all();
        return view('admin.stages.create')->withStages($stages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Stage $stage, StoreStageRequest $request)
    {
 
        //For demo purposes only. When creating stage or inviting a stage
        // you should create a generated random password and email it to the stage
        $stage->create(array_merge($request->validated()));

        return redirect()->route('stages.index')
            ->withSuccess(__('stage created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        $stage = Stage::find($id);


        return view('admin.stages.show', compact('stage')); 
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Stage $stage) 
    {
        $stages = Stage::all();

        return view('admin.stages.edit', [
            'stage' => $stage,
            'stages' => $stages
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Responsec
     */
    public function update(Stage $stage, UpdateStageRequest $request) 
    {
        $stage->update($request->validated());

        return redirect()->route('stages.index')
            ->withSuccess(__('stage updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stage $stage) 
    {
        $stage->delete();

        return redirect()->route('stages.index')
            ->withSuccess(__('stage deleted successfully.'));
    }

    /**
     *  Restore stage data
     * 
     * @param stage $stage
     * 
     * @return \Illuminate\Http\Response
     */
    public function restore($id) 
    {
        Stage::where('id', $id)->withTrashed()->restore();

        return redirect()->route('stages.index', ['status' => 'archived'])
            ->withSuccess(__('stage restored successfully.'));
    }

    /**
     * Force delete stage data
     * 
     * @param stage $stage
     * 
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id) 
    {
        Stage::where('id', $id)->withTrashed()->forceDelete();

        return redirect()->route('stages.index', ['status' => 'archived'])
            ->withSuccess(__('stage force deleted successfully.'));
    }

    /**sho
     * Restore all archived stages
     * 
     * @param stage $stage
     * 
     * @return \Illuminate\Http\Response
     */
    public function restoreAll() 
    {
        Stage::onlyTrashed()->restore();

        return redirect()->route('stages.index')->withSuccess(__('All stages restored successfully.'));
    }
}

