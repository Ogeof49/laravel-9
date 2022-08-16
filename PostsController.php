<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Image;
use Session;
use App\Models\User;
use App\Models\Profile;


class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');
        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->paginate(5);
    
        return view('posts.index')->withPosts($posts)->withUsers($users);
    }

    //
    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        $data = request()->validate([
            'caption' => 'required',
            'image' => ['required', 'image'],
        ]);

        $imagePath = (request('image')->store('uploads', 'public'));

        $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200, 1200);
        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);
        
        Session::flash('Success', 'Post was successfully Uploaded!');


        return redirect('/profile/' . auth()->user()->id);
    }

    public function show(\App\Post $post)
    {
        
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        

        return view('posts.edit', compact('post'));

    }

    public function update(Post $post)
    {
        
        $data = request()->validate([
            'caption' => 'required',
            'image' => '',
        
        ]);

       
        if (request('image')) {
            $imagePath = request('image')->store('uploads', 'public');


            $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200, 1200);
            $image->save();

            $imageArray = ['image' => $imagePath];

    }

        $post->update(array_merge(
            $data,
            $imageArray ?? []
        ));


        Session::flash('Success', 'Your post was updated successfully');

        return redirect("/p/{$post->id}");
    }

    public function destroy(Post $post)
    {
        
        $post = Post::find($post->id);

        $post->delete();

        Session::flash('Success', 'The post was successfully deleted.');

        return redirect('/profile/' . auth()->user()->id);
    }
  
}


