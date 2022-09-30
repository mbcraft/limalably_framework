<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LImageUtils
{
    /*
     * Ridimensiona un'immagina per larghezza
     * */
    public static function multi_resize_by_width($source,$resize_array)
    {
        if ($source instanceof LFile)
            $source_file = $source;
        else
            $source_file = new LFile($source);

        if ($dest instanceof LFile)
            $dest_file = $dest;
        else
            $dest_file = new LFile($dest);

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
     * Ridimensiona un'immagina per larghezza
     * */
    public static function resize_by_width($source,$dest,$width)
    {
        if ($source instanceof LFile)
            $source_file = $source;
        else
            $source_file = new LFile($source);

        if ($dest instanceof LFile)
            $dest_file = $dest;
        else
            $dest_file = new LFile($dest);

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
        if ($source instanceof LFile)
            $source_file = $source;
        else
            $source_file = new LFile($source);

        if ($dest instanceof LFile)
            $dest_file = $dest;
        else
            $dest_file = new LFile($dest);

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
    private static function load_image($source_file)
    {
    	if (is_string($source_file)) $source_file = new LFile($source_file);

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
    private static function save_image($image,$dest_file)
    {
    	if (is_string($dest_file)) $dest_file = new LFile($dest_file);

        $extension = $dest_file->getExtension();

        $lower_case_extension = strtolower($extension);

        switch ($lower_case_extension)
        {
            case "gif" : return imagegif($image,$dest_file->getFullPath());
            case "jpg":
            case "jpeg": 
            case "jfif" : return imagejpeg($image,$dest_file->getFullPath(),92);
            case "png" : return imagepng($image,$dest_file->getFullPath(),8);
            default : throw new ImageException("Estensione ".$extension." non supportata!!");
        }
    }

    private static function close_image($image) {
		imagedestroy($image);
    }

    public static function get_image_data($source_file)
    {
        if ($source_file instanceof LFile)
            $f = $source_file;
        else
            $f = new LFile($source_file);

        $data = getimagesize($f->getFullPath());

        $result = array();
        $result["width"] = $data[0];
        $result["height"] = $data[1];
        $result["mime_type"] = $data["mime"];

        return $result;
    }
}

?>
