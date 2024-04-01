<?php

namespace App\Services;

use App\Services\ImageKitService;

class ImageService
{
    private $imageKit;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKit = $imageKitService->getImageKit();
    }

    public function deleteFile($folder, $url) 
    {
        $file_name = pathinfo($url, PATHINFO_BASENAME);
        $existing_file = $this->imageKit->listFiles(array(
          "path" => $folder,
          "name" => $file_name,
        ));

        $existing_file_id = $existing_file->result[0]->fileId;

        $delete = $this->imageKit->deleteFile($existing_file_id);
    }

    public function uploadFile($folder, $file, $fileName, $tags) 
    {
        $isUrl = gettype($file) != "object" ? true : false;
        $file_extension = $isUrl ? "jpg" : $file->extension();
        $file_name = $fileName . "-" . time(). "." . $file_extension;

        $fileToUpload = [
          'file' => $isUrl ? $file : fopen($file->getRealPath(), 'r'),
          "fileName" => $file_name,
          "folder" => $folder,
          "tags" => $tags,
          "useUniqueFileName" => false,
        ];

        $upload_file = $this->imageKit->uploadFile($fileToUpload);
        $file_url = $upload_file->result->url;

        return $file_url;
    }

    public function deleteFolder($folder) 
    {
      $this->imageKit->deleteFolder($folder);
    }
}
