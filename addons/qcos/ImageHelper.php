<?php

namespace addons\qcos;

use Qcloud\Cos\Client;
use think\Addons;
use think\Config;

class ImageHelper
{
    private static $instance = null;
    
    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new ImageHelper();
        }
        return self::$instance;
    }

    public function resize_image($file, $w, $h) {
        list($width, $height) = getimagesize($file);
        if($width < $w || $height < $h) {
            return;
        }
        $r = $width / $height;
       
        if ($w/$h > $r) {
            $newheight = $w/$r;
            $newwidth = $w;
        } else {
            $newwidth = $h*$r;
            $newheight = $h;
        }
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $this->resize_imagejpg($file, $newwidth, $newheight, $width, $height);
        } else if ($extension == 'png') {
            $this->resize_imagepng($file, $newwidth, $newheight, $width, $height);
        } else if ($extension == 'gif') {
            $this->resize_imagegif($file, $newwidth, $newheight, $width, $height);
        } else if ($extension == 'bmp') {
            $this->resize_imagebmp($file, $newwidth, $newheight, $width, $height);
        }
    }

    // for jpg 
    private function resize_imagejpg($file, $newwidth, $newheight, $width, $height) {
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($dst ,$file);
        imagedestroy($dst);
    }
    
    // for png
    private function resize_imagepng($file, $newwidth, $newheight, $width, $height) {
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);

        // integer representation of the color black (rgb: 0,0,0)
        $background = imagecolorallocate($dst , 0, 0, 0);
        // removing the black from the placeholder
        imagecolortransparent($dst, $background);
        // turning off alpha blending (to ensure alpha channel information
        // is preserved, rather than removed (blending with the rest of the
        // image in the form of black))
        imagealphablending($dst, false);
        // turning on alpha channel information saving (to ensure the full range
        // of transparency is preserved)
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagepng($dst ,$file);
        imagedestroy($dst);

    }
    
    // for gif
    private function resize_imagegif($file, $newwidth, $newheight, $width, $height) {
        $src = imagecreatefromgif($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagegif($dst ,$file);
        imagedestroy($dst);
    }
    
    // for bmp
    private function resize_imagebmp($file, $newwidth, $newheight, $width, $height) {
        $src = imagecreatefrombmp($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagebmp($dst ,$file);
        imagedestroy($dst);
    }

}
