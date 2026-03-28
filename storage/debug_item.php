<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
$item = \App\Models\CourseSessionItem::find(18);
if (! $item) { echo "no item\n"; exit; }
$cloudinary = app(\App\Services\CloudinaryPrivateMediaService::class);
$url = $cloudinary->temporaryAccessUrl(
    $item->cloudinary_public_id,
    $item->cloudinary_resource_type === 'video' ? 'mp4' : $item->cloudinary_format,
    $item->cloudinary_resource_type,
    $item->cloudinary_delivery_type ?: 'private'
);
print "url=$url\n";
try {
    $resp = \Illuminate\Support\Facades\Http::withOptions([
        'stream' => true,
        'http_errors' => false,
        'connect_timeout' => 10,
        'timeout' => 20,
        'verify' => false,
    ])->get($url);
    echo "status={$resp->status()}\n";
} catch (Throwable $e) {
    echo "EX: ".$e->getMessage()."\n";
}
