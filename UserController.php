<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use Cache;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Transaction;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user, Request $request)
    {
        $users = User::latest();

        if($request->get('status') == 'archived') {
            $users = $users->onlyTrashed();
        }

        $users = $users->paginate(10);

        return view('admin.users.index', compact('users'));
    }


    public function edit_support(User $user, Request $request)
    {
        $users = User::latest();

        if($request->get('status') == 'archived') {
            $users = $users->onlyTrashed();
        }

        $users = $users->paginate(10);

        return view('admin.users.edit', compact('users'));
    }


    public function delete_support(User $user, Request $request)
    {
        $users = User::latest();

        if($request->get('status') == 'archived') {
            $users = $users->onlyTrashed();
        }

        $users = $users->paginate(10);

        return view('admin.users.delete', compact('users'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(User $user, StoreUserRequest $request)
    {
        
        //For demo purposes only. When creating user or inviting a user
        // you should create a generated random password and email it to the user
        $user->create(array_merge($request->validated(), [
            'password' => Hash::make($request->password), 
        ]));


        

        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        $user = User::find($id);

        $transactionsCount = Cache::remember(
            'count.transactions. ' . $user->id,
             now()->addSeconds(2), 
             function() use ($user) {
                return $user->transactions->count();
            });

        return view('admin.users.show', compact('user', 'transactionsCount')); 
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(User $user) 
    {
        
        return view('admin.users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Responsec
     */
    public function update(User $user, UpdateUserRequest $request) 
    {
        $user->update($request->validated());

        return redirect()->route('users.index')
            ->withSuccess(__('user updated successfully.'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) 
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
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
        User::where('id', $id)->withTrashed()->restore();

        return redirect()->route('users.index', ['status' => 'archived'])
            ->withSuccess(__('User restored successfully.'));
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
        User::where('id', $id)->withTrashed()->forceDelete();

        return redirect()->route('users.index', ['status' => 'archived'])
            ->withSuccess(__('User force deleted successfully.'));
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
        User::onlyTrashed()->restore();

        return redirect()->route('users.index')->withSuccess(__('All users restored successfully.'));
    }
}

