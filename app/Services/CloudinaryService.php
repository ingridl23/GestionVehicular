<?php
namespace App\Services;

use Cloudinary\Cloudinary;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud.cloud_name'),
                'api_key' => config('cloudinary.cloud.api_key'),
                'api_secret' => config('cloudinary.cloud.api_secret'),
            ],
        ]);
    }

    public function upload($filePath, $folder = 'default')
    {
        return $this->cloudinary->uploadApi()->upload($filePath, [
            'folder' => $folder
        ]);
    }

    public function destroy($publicId)
    {
        return $this->cloudinary->uploadApi()->destroy($publicId);
    }
}
