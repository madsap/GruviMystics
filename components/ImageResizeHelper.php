<?php

namespace app\components;

use \Yii;
use \yii\base\Component;
use \yii\helpers\VarDumper;

/**
 * Class ImageResizeHelper
 * @package app\components
 * @author  Aleksandr Mokhonko
 * Date: 03.07.15
 *
 * Helper to resize images
 */
class ImageResizeHelper extends Component
{
    private $_pathOriginalFile;
    private $_pathNewFile;
    private $_width;
    private $_height;
    private $_rgb;
    private $_quality;

    /**
     * Set params for resize
     * @param          $pathOriginalFile
     * @param null     $width
     * @param null     $height
     * @param int|bool $rgb
     * @param int      $quality
     * @return $this
     */
    public function setParams($pathOriginalFile, $width = null, $height = null, $rgb = false, $quality = 100)
    {
        $this->_pathOriginalFile = $pathOriginalFile;
        $this->_width            = $width;
        $this->_height           = $height;
        $this->_rgb              = $rgb;
        $this->_quality          = $quality;

        return $this;
    }

    /**
     * Create image another size
     * @param $pathNewFile
     * @return bool
     */
    public function createImage($pathNewFile)
    {
        $this->_pathNewFile = $pathNewFile;

        return $this->_resize();
    }


    /**
     * Create thumbnail by original image
     * @param      $pathNewFile
     * @param bool $rotate
     * @return bool
     */
    public function createSmallImage($pathNewFile, $rotate = false)
    {
        $this->_pathNewFile = $pathNewFile;
        $this->_width       = 86;
        $this->_height      = 86;

        return $this->_resize($rotate);
    }

    /**
     * Create common image by original image
     * @param      $pathNewFile
     * @param bool $rotate
     * @return bool
     */
    public function createMiddleImage($pathNewFile, $rotate = false)
    {
        $this->_pathNewFile = $pathNewFile;
        $this->_width       = 640;
        $this->_height      = 640;

        return $this->_resize($rotate);
    }

    /**
     * Create image maximum size
     * @param $pathNewFile
     * @return bool
     */
    public function createLargeImage($pathNewFile)
    {
        $this->_pathNewFile = $pathNewFile;
        $this->_width       = 1920;
        $this->_height      = 640;

        return $this->_resize();
    }

    /**
     * @param bool|false $rotate
     * @return bool
     */
    private function _resize($rotate = false)
    {
        if (!file_exists($this->_pathOriginalFile)) {
            return false;
        }

        $size = getimagesize($this->_pathOriginalFile);

        if ($size === false) {
            return false;
        }

        $format   = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $function = 'imagecreatefrom' . $format;
        if (!function_exists($function)) {
            if (in_array($function, ['imagecreatefromgif', 'imagecreatefromjpeg', 'imagecreatefrompng'])) {
                Yii::warning(VarDumper::dumpAsString('!function_exists(' . $function . ')'), 'warning');
            }

            return false;
        }

        if ($size[0] > $size[1]) {
            $ratio = $this->_width / $size[0];
        } else {
            $ratio = $this->_height / $size[1];
        }

        $newWidth  = floor($size[0] * $ratio);
        $newHeight = floor($size[1] * $ratio);

        $isrc = $function($this->_pathOriginalFile);

        if (true === $rotate) {
            $isrc = imagerotate($isrc, 180, 0);
        }

        $idest = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($idest, false);

        if ($this->_rgb === false) {
            imagesavealpha($idest, true);
        } else {
            imagefill($idest, 0, 0, $this->_rgb);
        }

        imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $newWidth, $newHeight, $size[0], $size[1]);
        imagejpeg($idest, $this->_pathNewFile, $this->_quality);

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;
    }
}