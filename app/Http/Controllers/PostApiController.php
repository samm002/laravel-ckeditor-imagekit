<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Post;
use App\Models\PostImage;
use App\Services\ImageService;

class PostApiController extends Controller
{
  private $imageService;

  public function __construct(ImageService $imageService)
  {
      $this->imageService = $imageService;
  }

  private function extractDescriptionImageUrls($description)
  {
    $imageUrls = [];

    // Use regular expression to find all image URLs
    preg_match_all('/<img[^>]+src="([^"]+)"/', $description, $matches);

    if (!empty($matches[1])) {
        $imageUrls = $matches[1];
    }

    return $imageUrls;
  }

  public function getFileFromFolder(string $id)
  {
    $post = Post::find($id);
    $title = Str::slug($post->title);
    $folder = "image/blog/" . $title;
    $listFiles = $this->imageService->listFilesInFolder($folder)[0]->name;
    $extension = pathinfo($listFiles, PATHINFO_EXTENSION);

    return response()->json([
      'status' => 'success',
      'message' => 'List Files from folder ' . $folder . ' success',
      'folder' => $folder,
      'data' => $listFiles,
      'extension' => $extension,
    ]);
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

  public function create(Request $request)
{
    $post = new Post;

    $post->title = $request->title;
    $post->description = $request->description;

    $post->save();

    // Extract image URLs from description field
    $imageUrls = $this->extractDescriptionImageUrls($request->description);

    // Save image URLs associated with the post
    foreach ($imageUrls as $imageUrl) {
      $postImage = new PostImage;
      $postImage->create([
          'post_id' => $post->id,
          'image_url' => $imageUrl,
      ]);
    }

    return response()->json([
        'status' => 'success', 
        'message' => 'create post success',
        'data' => $post, 
    ], 201);
}

  public function update(Request $request, string $id)
  {
    $post = Post::find($id);

    $previous_title = $post->title;
    $previous_imageUrls = $this->extractDescriptionImageUrls($post->description);

    $post->update([
      'title' => $request->title,
      'description' => $request->description,
    ]);
    
    if($request->hasFile('upload')) {
      $current_title = $post->title;
      $current_imageUrls = $this->extractDescriptionImageUrls($post->description);

      $removed_imageUrls = array_diff($previous_imageUrls, $current_imageUrls);
      $added_imageUrls = array_diff($current_imageUrls, $previous_imageUrls);

      $removed_imageUrls_value = array_values($removed_imageUrls);
      $added_imageUrls_value = array_values($added_imageUrls);

      $slug_previous_title = Str::slug($previous_title);
      $previous_folder = "image/blog/" . $slug_previous_title;

      $slug_current_title = Str::slug($current_title);
      $current_folder = "image/blog/" . $slug_current_title;

        if ($current_title != $previous_title) {
          $this->imageService->moveAndRenameFilesToFolder($previous_folder, $current_folder, $slug_current_title);
          $this->imageService->deleteFolder($previous_folder);
        }
        
        foreach ($removed_imageUrls_value as $removed_url) {
          $this->imageService->deleteFile($current_folder, $removed_url);
          $postImage = PostImage::where('image_url', $removed_url)->first();
          $postImage->delete();
        }
        
        foreach ($added_imageUrls_value as $added_image_url) {
          $postImage = new PostImage;
          $postImage->create([
            'post_id' => $post->id,
            'image_url' => $added_image_url,
          ]);
        }
      }
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
