<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function createPost(Request $request){

        $validated = Validator::make($request->all(),[
            'title' => 'required|string|unique:posts,title',
            'content' => 'required|string',
        ]);
    
        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        };

        try {

            $user_id = Auth::id();
            $post = Post::create([
                'title'=>$request->title,
                'content'=>$request->content,
                'user_id'=>$user_id,
            ]);

            return response()->json([
                'message'=>'Post has been created successfully',
                'Post'=>$post
            ]);

        } catch (\Exception $e) {
            return response()->json(['errors'=>$e->getMessage()],403);
        }
    }
    public function getPosts(){

        //FIRST WAY
        // $posts = Post::all()
        // ->map(function($post){
        //     return [
        //         'id'=>$post->id,
        //         'title'=>$post->title,
        //         'content'=>$post->content,
        //         'user'=>$post->user->name ?? 'Unknown',
        //         'created_at'=>$post->created_at,
        //         'updated_at'=>$post->updated_at,
        //     ];
        // });

        // return response()->json($posts);

        //SECONDE WAY
        $posts = Post::all();
        $post_data = PostResource::collection($posts);
        return response()->json($post_data);
    }

    public function editPost(Request $request, $id){

        // Validate data
        $validated = Validator::make($request->all(),[
            'title' => 'required|string|unique:posts,title,' .$id, // unique sauf lui meme
            'content' => 'required|string',
        ]);
    
        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        }

        // Verify if post exist
        $post = Post::find($id);
        if(!$post){
            return response()->json(['error'=>'Post not found'],404);
        }

        // Verify if the post is created by user connected (who try to update the post)
        if ($post->user_id !== Auth::id() ) {
            return response()->json(['error'=>'Unauthorized action'],403);
        }

        try {
            $post->update([
                'title'=>$request->title,
                'content'=>$request->content
            ]);

            return response()->json([
                'message'=>'Post has been updated successfully',
                'Post'=>$post
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors'=>$e->getMessage()],403);
        }
    }

    public function deletePost($id){
        // Verify if post exist
        $post = Post::find($id);
        if(!$post){
            return response()->json(['error'=>'Post not found'],404);
        }

        // Verify if the post is created by user connected (who try to update the post)
        if ($post->user_id !== Auth::id() ) {
            return response()->json(['error'=>'Unauthorized action'],403);
        }

        try {
            $post->delete();
            return response()->json([
                'message'=>'Post has been deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors'=>$e->getMessage()],403);
        }
    }

    public function getPostById($id){
        
        $post = Post::find($id);
        $post_data = new PostResource($post);
        if(!$post){
            return response()->json(['error'=>'Post not found'],404);
        }

        return response()->json($post_data);
    }
}
