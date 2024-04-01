<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Services\ImageService;

class PostApiController extends Controller
{
  private $imageService;

  public function __construct(ImageService $imageService)
  {
      $this->imageService = $imageService;
  }

  public function postForm()
  {
    return view('create');
  }

  public function uploadCreate(Request $request)
  {
    if($request->hasFile('upload'))
    {
      $file = $request->file('upload');
      $title = $request->title ?? 'anonymous_blog';
      $filename = Str::slug($title);
      $image_folder = "image/blog/" . $filename;
      $image_file_name = $filename;
      $image_tags = ["blog"];

      $image_url = $this->imageService->uploadFile($image_folder, $file, $image_file_name, $image_tags);

      return response()->json([
        'filename' => $filename, 
        'uploaded' => 1, 
        'url' => $image_url
      ]);
    }
  }
  public function uploadUpdate(Request $request)
  {
    $id = $request->postId;
    $post = Post::find($id);
    $previousTitle = $post->title;
    
    if($request->hasFile('upload'))
    {
      $file = $request->file('upload');
      $updatedTitle = $request->title ?? 'anonymous_blog';
      $filename = Str::slug($updatedTitle);
      $image_folder = "image/blog/" . $filename;
      $image_file_name = $filename;
      $image_tags = ["blog"];

      if ($request->title != $previousTitle)
      {
        $slugPreviousTitle = Str::slug($previousTitle);
        $previousImageFolder = "image/blog/" . $slugPreviousTitle;
        $this->imageService->deleteFolder($previousImageFolder);
      }

      $image_url = $this->imageService->uploadFile($image_folder, $file, $image_file_name, $image_tags);
  
      return response()->json([
        'filename' => $filename, 
        'uploaded' => 1, 
        'url' => $image_url
      ]);
    }
  }

  public function create(Request $request)
  {
    $post = new Post;

    $post->title = $request->title;
    $post->description = $request->description;

    $post->save();

    return response()->json([
      'status' => 'success', 
      'message' => 'create post success',
      'data' => $post, 
    ], 201);
  }

  public function update(Request $request, string $id)
  {
    $post = Post::find($id);

    $post->update([
      'title' => $request->title,
      'description' => $request->description,
    ]);

    return response()->json([
      'status' => 'success', 
      'message' => 'update post with id ' . $id . ' success',
      'data' => $post, 
    ], 200);
  }

  public function showAll()
  {
    $posts = Post::all();

    return response()->json([
      'status' => 'success', 
      'message' => 'get all post success',
      'data' => $posts, 
    ], 200);
  }

  public function show(string $id)
  {
    $post = Post::find($id);

    return response()->json([
      'status' => 'success', 
      'message' => 'get post with id ' .$id . ' success',
      'data' => $post, 
    ], 200);
  }

  public function delete(string $id)
  {
    $post = Post::find($id);

    $post->delete();

    return response()->json([
      'status' => 'success', 
      'message' => 'Delete post with id ' . $id . ' success',
      'data' => $post, 
    ], 200);
  }
}
