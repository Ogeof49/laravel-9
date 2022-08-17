<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Image;
use Session;
use Cache;
use App\Models\Post;
use App\Models\Client;

class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(User $user) {

    $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        $followersCount = Cache::remember(
        'count.followers. ' . $user->id,
        now()->addSeconds(2), 
         function() use ($user) {
            return $user->profile->followers->count();
        });


        $followingCount =  Cache::remember(
        'count.following. ' . $user->id,
        now()->addSeconds(2), 
         function() use ($user) {
            return $user->following->count();
        });   


        

        $users = auth()->user()->following()->pluck('profiles.user_id');
        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(6);


        return view('admin.profiles.index', compact('user', 'posts', 'users')); 
    }

    public function edit(User $user)
    {
    
        

        return view('admin.profiles.edit', compact('user'));
    }

    public function update(User $user)
    {
     


        $data = request()->validate([
            'ocupation' => 'required',
            'mobile' => 'required',
            'location' => 'required',
            'NationalID' => 'required',
            'url' => 'url',
            'image' => '',
        
        ]);

       
        if (request('image')) {
            $imagePath = request('image')->store('profile', 'public');


            $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
            $image->save();

            $imageArray = ['image' => $imagePath];

    }

        $user->profile->update(array_merge(
            $data,
            $imageArray ?? []
        ));


        Session::flash('success', 'Your profile has been updated successfully');

        return redirect("/profile/{$user->id}");
    }


    
    
}





