<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    public function create(Request $request){
        
        $post = new Post;
        $post->user_id = Auth::user()->id;
        $post->desc = $request ->desc;

        //check if post has a photo
        if($request->photo != ''){
            //chooses a unique name for the photo
            file_put_contents('storage/posts/'.photo,base64_decode($request->photo));
            $post->photo =$photo;
        }
        $post->save();
        $post->user;
        return response()->json([
            'success' => true,
            'message' =>'posted',
            'post'=>$post
        ]);
    }

    public function update(Request $request){
        $post = Post::find($request->id);
        //check if user is editing in his own post
        if(Auth::user()->id != $post->user_id){
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ]);
        }
        $post->desc = $request->desc;
        $post->update();
        return response()->json([
            'success' => true,
            'message' => 'post edited'
        ]);
    }

    public function delete(Request $request){
        $post = Post::find($request->id);
        //check if user is deleting their own post
        if(Auth::user()->id != $post->user_id){
            return response()->json([
                'success' => false,
                'message' => 'unauthorized acess'
            ]);
        }
       
        //check if post has a photo to delete
        if($post->photo != ''){
            Storage::delete('public/posts/'.$post->photo);
        }
        return response()->json([
            'success' => true,
            'message' => 'post deleted'
        ]);
    }

    public function posts(Request $request){
    $posts = Post::orderBy('id','desc')->get();
    foreach($posts as $post){
        //get user of post
       $post->user;
       //comments count
       $post['commentsCount'] = count($post->comments);
       //likes count
       $post['likesCount'] = count($post->likes);
        //check if users liked his own post
       $post['selfLike'] = false;
       foreach($post->likes as $like){
           if($like->user_id == Auth::user()->id){
            $post['selfLike'] = true;
           }
       }
    }
        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    public function myPosts(){
         $posts = Post::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
         $user = Auth::user();
         return response()->json([
            'success' => true,
            'posts' => $posts,
            'user'  => $user
        ]);

    }
}
