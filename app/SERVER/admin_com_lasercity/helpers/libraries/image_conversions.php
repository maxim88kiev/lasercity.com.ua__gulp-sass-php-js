<?php defined('_JEXEC') or die;

class ImageConversion
{
    private $path_to_file;
    private $file_name;
    private $format;
    private $allowed_formats = array(
        'png', 'jpg', 'jpeg'
    );
    private $error;

    public function __construct($path_img)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path_img);
        $file = explode('.', array_pop($parts));
        $this->format = array_pop($file);
        $this->file_name = implode('.', $file);
        $this->path_to_file = count($parts) ? implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR : '';
    }

    public function convert($dimension, $formats, $retina = false, $original = true, $save_path = null)
    {
        $filename = $this->path_to_file . $this->file_name . '.' . $this->format;
        if (!file_exists($filename)) {
            $this->error = 'No such image';
            return false;
        }

        $this->error = 'No errors';
        if (!$this->checkFormat()) {
            $this->error = 'Image unknown format';
            return false;
        }

        $first = reset($dimension);
        $save_path_set = $save_path == null ? $this->path_to_file . $this->file_name : $save_path . DIRECTORY_SEPARATOR . $this->file_name;
        list($width, $height) = getimagesize($filename);

        if ($original) {
            $this->convert(array(
                'width' => $width,
                'height' => $height,
                'quality' => 100,
                'prefix' => 'original'
            ), $formats, false, false, $save_path);
            $this->convert(array(
                'width' => $width * 2,
                'height' => $height * 2,
                'quality' => 100,
                'prefix' => 'retina'
            ), $formats, false, false, $save_path);
        }

        if (isset($dimension['width']) && isset($dimension['height'])) {
            $quality = isset($dimension['quality']) ? $dimension['quality'] : 100;
            $count_errors = 0;
            $formats = (array)$formats;
            foreach ($formats as $format) {
                $true_color = imagecreatetruecolor($dimension['width'], $dimension['height']);
                switch ($this->format) {
                    case 'jpg':
                    case 'jpeg':
                        $image = imagecreatefromjpeg($filename);
                        break;
                    case 'png':
                        $image = imagecreatefrompng($filename);
                        break;
                    default:
                        $image = imagecreatefrombmp($filename);
                        break;
                }
                imagecopyresampled($true_color, $image, 0, 0, 0, 0, $dimension['width'], $dimension['height'], $width, $height);
                $save_image = $save_path_set . '_' . (isset($dimension['prefix']) ? $dimension['prefix'] : $dimension['width'] . 'x' . $dimension['height']) . '.' . $format;
                switch ($format) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($true_color, $save_image, $quality);
                        break;
                    case 'webp':
                        imagewebp($true_color, $save_image, $quality);
                        break;
                    default:
                        $count_errors++;
                        break;
                }
            }
            if ($retina) {
                if (!$this->convert(array(
                    'width' => $dimension['width'] * 2,
                    'height' => $dimension['height'] * 2,
                    'quality' => $quality
                ), $formats, false, false, $save_path)) {
                    $count_errors++;
                }
            }
            if ($count_errors) {
                $this->error = "Unknown formats: {$count_errors}";
            }
            return !($count_errors == count($formats));
        } else if (isset($first['width']) && isset($first['height'])) {
            $count_errors = 0;
            $dimension = (array)$dimension;
            foreach ($dimension as $size) {
                if (!$this->convert($size, $formats, $retina, false, $save_path)) {
                    $count_errors++;
                }
            }
            if ($count_errors) {
                $this->error = "Incorrect values dimension: {$count_errors}";
            }
            return !($count_errors == count($dimension));
        } else {
            $this->error = 'Incorrect value dimension';
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    private function checkFormat()
    {
        return in_array($this->format, $this->allowed_formats);
    }
}