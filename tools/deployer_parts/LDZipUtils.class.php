<?php


if (!class_exists('LDZipUtils')) {
    class LDZipUtils
    {
        public static function expandArchive($zip_file,$target_folder)
        {
            $zip_archive = new ZipArchive();
         
            if ($zip_file instanceof LDFile)
                $real_zip_file = $zip_file;
            else
                $real_zip_file = new LDFile($zip_file);
            
            
            if ($target_folder instanceof LDDir)
                $target_dir = $target_folder;
            else
                $target_dir = new LDDir($target_folder);
            
            $zip_archive->open($real_zip_file->getFullPath());
            
            $zip_archive->extractTo($target_dir->getFullPath());
            
            $zip_archive->close();
        }
        
        public static function createArchive($save_file,$folder_to_zip,$local_dir="/")
        {
            if ($save_file->exists()) $save_file->delete(); 

            if ($folder_to_zip instanceof LDDir)
                $dir_to_zip = $folder_to_zip;
            else
                $dir_to_zip = new LDDir($folder_to_zip);
            
            if (!class_exists('ZipArchive')) throw new \Exception("Can't use zip files, ZipArchive class missing.");

            $zip_archive = new ZipArchive();

            $zip_archive->open($save_file->getFullPath(),  ZipArchive::CREATE);

            LDZipUtils::recursiveZipFolder($zip_archive, $dir_to_zip,$local_dir);

            $zip_archive->close();
        }
        
        private static function recursiveZipFolder($zip_archive,$current_folder,$local_dir)
        {        
            foreach ($current_folder->listAll() as $dir_entry)
            {
                if ($dir_entry->isFile())
                {
                    $zip_archive->addFile($dir_entry->getFullPath(),$local_dir.$dir_entry->getFilename());
                }
                else
                {
                    $zip_archive->addEmptyDir($local_dir.$dir_entry->getName().'/');
                    LDZipUtils::recursiveZipFolder($zip_archive, $dir_entry,$local_dir.$dir_entry->getName().'/');
                }
            }
        }
    }
}