<?php


namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(
            \Intervention\Image\Drivers\Gd\Driver::class // Or use Imagick::class if preferred
        );
    }

    public function uploadImage(UploadedFile $file, string $directory): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = "{$directory}/{$filename}";

        $image = $this->manager->read($file->getRealPath())->toJpeg();

        Storage::disk('public')->put($relativePath, (string) $image);

        return $relativePath;
    }

    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
}
}
}