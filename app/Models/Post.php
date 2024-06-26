<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PostImage;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
      'title',
      'description',
    ];

    public function images()
    {
      return $this->hasMany(PostImage::class);
    }
}
