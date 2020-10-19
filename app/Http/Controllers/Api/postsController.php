<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use Auth;

class postsController extends Controller
{
    public function create(Request $request){
        $post = new Post;
        $post-> user_id = Auth::user()->id;
        $post->desc = $request->desc;

        //check if post has photo

        if($request->photo != ''){
            $filenameWithExt = $request->file('photo')->getClientOriginalImage();


            //get just file name
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            $extension = $request->file("photo")->getClientOriginalExtension();

            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file("photo")->storeAs('storage/posts', $fileNameToStore);

            
            
        }else{
            $fileNameToStore = "noimage.jpg";
            $post->photo = $fileNameToStore;
        }


        // if($request->photo != ''){
        //     //choose a unique name for photo
        //     $photo = time().'jpg';
            
        //     file_put_contents('storage/posts/'.$photo,base64_decode($request->photo));
        //     $post->photo = $photo;
        // }

        $post->photo = $fileNameToStore;
        $post -> save();
        $post->user;
        return response()->json([
            'success'=> true,
            'message' => 'posted',
            'post' => $post
        ]);
    }

    public function update(Request $request){
        $post = Post::find($request->id);
        if(Auth::user()->id != $request->id){
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ]);
        }
        $post->desc = $request->desc;
        $post->update();
        return response()->json([
            'success'=> true,
            'message' => 'post edited'
        ]);
    }

    public function delete(Request $request){
        $post = Post::find($request->id);
        if(Auth::user()->id != $request->id){
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ]);
        }
        

        if($post->photo != ''){
            Storage::delete('public/posts/'.$post->photo);

        }
        return response()->json([
            'success'=> true,
            'message' => 'post edited'
        ]);
    }
    public function posts(){
        $posts = Post::orderBy('id', 'desc')->get();
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
            'success'=> true,
            'post' => $posts
        ]);
    }
}
