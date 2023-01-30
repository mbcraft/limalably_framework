<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LImageUtils
{
    private static function clamp($index,$wm_size,$img_size) {
        if (($index+1)*$wm_size > $img_size) {
            return $img_size-$index*$wm_size;
        } else {
            return $wm_size;
        }
    }
    
    public static function watermark_fully($source,$watermark,$dest) {
        $src_img = self::load_image($source);
        $wm_img = self::load_image($watermark);
        

        $src_info = self::get_image_data($source);
        $wm_info = self::get_image_data($watermark);

        $dest_img = @imagecreatetruecolor($src_info['width'],$src_info['height']);
        imagealphablending($dest_img,true);

        imagecopy($dest_img,$src_img,0,0,0,0,$src_info['width'],$src_info['height']);
        
        for ($ix=0;$ix<ceil($src_info['width']/$wm_info['width']);$ix++) {
            for ($iy=0;$iy<ceil($src_info['height']/$wm_info['height']);$iy++) {
                imagecopy($dest_img,$wm_img,$ix*$wm_info['width'],$iy*$wm_info['height'],0,0,self::clamp($ix,$wm_info['width'],$src_info['width']),self::clamp($iy,$wm_info['height'],$src_info['height']));      
            }
        }

        self::save_image($dest_img,$dest);

        self::close_image($src_img);
        self::close_image($wm_img);
        self::close_image($dest_img);
           
    }

    private static function parameterAsFile($param) {
        if ($param instanceof LFile) return $param;
        if (is_string($param)) return new LFile($param);

        throw new \Exception("Parameter $param is not path or string.");
    }

    /*
     * Ridimensiona un'immagina per larghezza
     * */
    public static function resize_by_width($source,$dest,$width)
    {
        $source_file = self::parameterAsFile($source);

        $dest_file = self::parameterAsFile($dest);

        $source_img = self::load_image($source_file);

        $info = self::get_image_data($source_file);

        $factor = $width / $info["width"];

        $final_width = $info["width"] * $factor;
        $final_heigth = $info["height"] * $factor;


        $dest_img = imagecreatetruecolor($final_width,$final_heigth);

        imagecopyresampled($dest_img,$source_img,0,0,0,0,$final_width,$final_heigth,$info["width"],$info["height"]);

        self::save_image($dest_img,$dest_file);
        imagedestroy($source_img);
        imagedestroy($dest_img);
    }

    /*
     * Ridimensiona un'immagine per altezza
     * */
    public static function resize_by_height($source,$dest,$height)
    {
        $source_file = self::parameterAsFile($source);

        $dest_file = self::parameterAsFile($dest);

        $source_img = self::load_image($source_file);

        $info = self::get_image_data($source_file);

        $factor = $height / $info["height"];

        $final_width = $info["width"] * $factor;
        $final_heigth = $info["height"] * $factor;


        $dest_img = imagecreatetruecolor($final_width,$final_heigth);
        imagecopyresampled($dest_img,$source_img,0,0,0,0,$final_width,$final_heigth,$info["width"],$info["height"]);

        self::save_image($dest_img,$dest_file);
        imagedestroy($source_img);
        imagedestroy($dest_img);
    }
/*
 * Carica un'immagine.
 * */
    private static function load_image($source)
    {
    	$source_file = self::parameterAsFile($source);

        $extension = $source_file->getExtension();

        $lower_case_extension = strtolower($extension);

        switch ($lower_case_extension)
        {
            case "gif" : return imagecreatefromgif($source_file->getFullPath());
            case "jpg" :
            case "jpeg" :
            case "jfif" : return imagecreatefromjpeg($source_file->getFullPath());
            case "png" : return imagecreatefrompng($source_file->getFullPath());
            default : throw new ImageException("Estensione ".$extension." non supportata!!");
        }
    }
/*
 * Salva un'immagina nel formato specificato.
 * */
    private static function save_image($gdimage,$dest)
    {
	    $dest_file = self::parameterAsFile($dest);

        $extension = $dest_file->getExtension();

        $lower_case_extension = strtolower($extension);

        switch ($lower_case_extension)
        {
            case "gif" : return imagegif($gdimage,$dest_file->getFullPath());
            case "jpg":
            case "jpeg": 
            case "jfif" : return imagejpeg($gdimage,$dest_file->getFullPath(),92);
            case "png" : return imagepng($gdimage,$dest_file->getFullPath(),8);
            default : throw new ImageException("Estensione ".$extension." non supportata!!");
        }
    }

    private static function close_image($gdimage) {
		imagedestroy($gdimage);
    }

    public static function get_image_data($source_file)
    {
        $f = self::parameterAsFile($source_file);

        $data = getimagesize($f->getFullPath());

        $result = array();
        $result["width"] = $data[0];
        $result["height"] = $data[1];
        $result["mime_type"] = $data["mime"];

        return $result;
    }
}