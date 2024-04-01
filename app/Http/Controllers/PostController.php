<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Services\ImageService;

class PostController extends Controller
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

    if($request->hasFile('upload'))
    {
      $originName = $request->file('upload')->getClientOriginalName();
      $fileName = pathinfo($originName, PATHINFO_FILENAME);
      $extension = $request->file('upload')->getClientOriginalExtension();
      $fileName = $fileName . '_' . time() . '.' . $extension;

      $request->file('upload')->move(public_path('media'), $fileName);

      $url = asset('media/' . $fileName);
      return response()->json([
        'filename' => $fileName, 
        'uploaded' => 1, 
        'url' => $url
      ]);
    }

    $post->save();

    return response()->json([
      'status' => 'success', 
      'message' => 'create post success',
      'data' => $post, 
    ], 200);
  }

  public function update(Request $request, string $id)
  {
    $post = Post::find($id);
    $post_id = $post->id;

    $post->update([
      'title' => $request->title,
      'description' => $request->description,
    ]);

    return redirect()->route('show', compact('post_id'));

    // return response()->json([
    //   'status' => 'success', 
    //   'message' => 'update post success',
    //   'data' => $post, 
    // ], 200);
  }

  public function edit(Request $request, string $id)
  {
    $post = Post::find($id);

    return view('edit', compact('post'));

    // return response()->json([
    //   'status' => 'success', 
    //   'message' => 'get all post success',
    //   'data' => $post, 
    // ], 200);
  }

  public function showAll()
  {
    $posts = Post::all();

    return view('showAll', compact('posts'));

    // return response()->json([
    //   'status' => 'success', 
    //   'message' => 'get all post success',
    //   'data' => $post, 
    // ], 200);
  }

  public function show(string $id)
  {
    $post = Post::find($id);

    return view('show', compact('post'));

    // return response()->json([
    //   'status' => 'success', 
    //   'message' => 'get all post success',
    //   'data' => $post, 
    // ], 200);
  }

  public function delete(string $id)
  {
    $post = Post::find($id);

    $post->delete();

    return redirect()->route('showAll');
    // return response()->json([
    //   'status' => 'success', 
    //   'message' => 'get all post success',
    //   'data' => $post, 
    // ], 200);
  }


}
