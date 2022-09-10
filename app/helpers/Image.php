<?php

namespace helpers;

class Image
{
    public function base64ToImage(string $base64, string $path)
    {
        $pattern = '/data:image\/(.+);base64,(.*)/';
        preg_match($pattern, $base64, $matches);
        $imageExtension = $matches[1];
        $encodedImageData = $matches[2];
        $decodedImageData = base64_decode($encodedImageData);
        $imageName = uniqid() . '.' . $imageExtension;
        return file_put_contents($path . $imageName, $decodedImageData) ? $imageName : false;
    }
}