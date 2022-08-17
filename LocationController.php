<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Models\User;


class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Location $location, Request $request)
    {

        $users = User::all();

        $locations = Location::latest();

        if($request->get('status') == 'archived') {
            $locations = $locations->onlyTrashed();
        }

        $locations = $locations->paginate(20);

        return view('admin.locations.index', compact('locations','users'));
    }


    public function edit_locations(Location $location, Request $request)
    {
        $locations = Location::latest();

        if($request->get('status') == 'archived') {
            $locations = $locations->onlyTrashed();
        }

        $locations = $locations->paginate(10);

        return view('admin.locations.edit', compact('locations'));
    }

    public function login_location(){
        return view('admin.locations.login');
    }


    public function delete_locations(Location $location, Request $request)
    {
        $locations = Location::latest();

        if($request->get('status') == 'archived') {
            $locations = $locations->onlyTrashed();
        }

        $locations = $locations->paginate(10);

        return view('admin.locations.delete', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Location $location, StoreLocationRequest $request)
    {
        //For demo purposes only. When creating user or inviting a user
        // you should create a generated random password and email it to the user
        $location->create(array_merge($request->validated(), [
            'pin' => 'test' 
        ]));

        return redirect()->route('locations.index')
            ->withSuccess(__('Location created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $location = Location::find($id);

        return view('admin.locations.show', compact('location')); 
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location) 
    {

        return view('admin.locations.edit', [
            'location' => $location,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Location $location, UpdateLocationRequest $request) 
    {
        $location->update($request->validated());

        return redirect()->route('locations.index')
            ->withSuccess(__('Location updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location) 
    {
        $location->delete();

        return redirect()->route('locations.index')
            ->withSuccess(__('location deleted successfully.'));
    }

    /**
     *  Restore user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function restore($id) 
    {
        Location::where('id', $id)->withTrashed()->restore();

        return redirect()->route('locations.index', ['status' => 'archived'])
            ->withSuccess(__('Location restored successfully.'));
    }

    /**
     * Force delete user data
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id) 
    {
        Location::where('id', $id)->withTrashed()->forceDelete();

        return redirect()->route('locations.index', ['status' => 'archived'])
            ->withSuccess(__('Location force deleted successfully.'));
    }

    /**
     * Restore all archived users
     * 
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function restoreAll() 
    {
        Location::onlyTrashed()->restore();

        return redirect()->route('locations.index')->withSuccess(__('All locations restored successfully.'));
    }
}

