<?php

namespace helpers;

class Image
{
    public function base64ToImage(string $base64, string $path)
    {
        //TODO проверить загрузку SVG
        $pattern = '/data:image\/(.+);base64,(.*)/';
        preg_match($pattern, $base64, $matches);
        if (empty($matches[1]) || empty($matches[2])) {
            return null;
        }
        $imageExtension = $matches[1];
        $encodedImageData = $matches[2];
        $decodedImageData = base64_decode($encodedImageData);
        $imageName = uniqid() . '.' . $imageExtension;
        return file_put_contents($path . $imageName, $decodedImageData) ? $imageName : false;
    }
}