<?php

/**
 * Models and handles the execution mode of the framework. Only 4 execution modes are supported : maintenance, framework_debug, debug and production.
 * Each execution modes makes the framework work in a slightly different way.
 */
class LExecutionMode {
    
    const MAINTENANCE_MODE = 'maintenance';
    const MAINTENANCE_FILENAME = 'maintenance.txt';
    
    const FRAMEWORK_DEBUG_MODE = 'framework_debug';
    const FRAMEWORK_DEBUG_FILENAME = 'framework_debug.txt';
    
    const DEBUG_MODE = 'debug';
    const DEBUG_FILENAME = 'debug.txt';
    
    const PRODUCTION_MODE = 'production';
    const PRODUCTION_FILENAME = 'production.txt';
    
    public static function isMaintenance() {
        return !self::modeFileExists(self::FRAMEWORK_DEBUG_FILENAME) && !self::modeFileExists(self::DEBUG_FILENAME) && !self::modeFileExists(self::PRODUCTION_FILENAME);
    }
    
    public static function isFrameworkDebug() {
        return self::modeFileExists(self::FRAMEWORK_DEBUG_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME);
    }
    
    public static function isDebug() {
        return self::modeFileExists(self::DEBUG_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME) && !self::modeFileExists(self::FRAMEWORK_DEBUG_FILENAME);
    }
    
    public static function isProduction() {
        return self::modeFileExists(self::PRODUCTION_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME) && !self::modeFileExists(self::DEBUG_FILENAME) && !self::modeFileExists(self::FRAMEWORK_DEBUG_FILENAME);
    }
   
    private static function modeFileExists($filename) {
        $file_path = self::getModeDir().$filename;
        
        return is_writable($file_path);
    }
    
    private static function getModeDir() {
        $project_dir = $_SERVER['PROJECT_DIR'];
        if (LConfig::get('PROJECT_DIR',null)!=null) $project_dir = LConfig::get('PROJECT_DIR',null);
        $mode_dir = $project_dir.'config/mode/';
        if (is_dir($mode_dir)) {
            return $mode_dir;
        } else throw new \Exception('/config/mode/ inside the project directory does not exists or is not writable.');
    }
    
    private static function getCurrentUser() {
        return isset($_SERVER['USER']) ? $_SERVER['USER'] : $_ENV['APACHE_RUN_USER'];
    }
    
    private static function listModeFiles() {
        $file_list = scandir(self::getModeDir());
        $final_file_list = [];
        foreach ($file_list as $filename) {
            if ($filename!='.' && $filename!='..') $final_file_list[] = $filename;
        }
        return $final_file_list;
    }
    
    private static function deleteModeFile($filename) {
        return @unlink(self::getModeDir().$filename);
    }
    
    private static function createModeFileAndDeleteOthers($filename) {      
        $result = file_put_contents(self::getModeDir().$filename, self::getCurrentUser()) !== false;
        $mode_files = self::listModeFiles();
        foreach ($mode_files as $mode_file) {
            if ($mode_file!=$filename)  {
                $result &= self::deleteModeFile ($mode_file);
            }
        }
        return $result;
    }
    
    public static function get() {
        if (self::isMaintenance()) return self::MAINTENANCE_MODE;
        if (self::isFrameworkDebug()) return self::FRAMEWORK_DEBUG_MODE;
        if (self::isDebug()) return self::DEBUG_MODE;
        if (self::isProduction()) return self::PRODUCTION_MODE;
    }
    
    private static function invalidExecutionModeException() {
        return new \Exception('Invalid execution mode name. Allowed only : '.implode(',',[self::MAINTENANCE_MODE,self::FRAMEWORK_DEBUG_MODE,self::DEBUG_MODE,self::PRODUCTION_MODE]));
    }
    
    public static function isByName($mode_name) {
        switch ($mode_name) {
            case self::MAINTENANCE_MODE : return self::isMaintenance();
            case self::FRAMEWORK_DEBUG_MODE : return self::isFrameworkDebug();
            case self::DEBUG_MODE : return self::isDebug();
            case self::PRODUCTION_MODE : return self::isProduction();
            default : throw self::invalidExecutionModeException();
        }
    }
    
    public static function setByName($mode_name) {
        switch ($mode_name) {
            case self::MAINTENANCE_MODE : return self::setMaintenance();
            case self::FRAMEWORK_DEBUG_MODE : return self::setFrameworkDebug();
            case self::DEBUG_MODE : return self::setDebug();
            case self::PRODUCTION_MODE : return self::setProduction();
            default : throw self::invalidExecutionModeException();
        }
    }
    
    public static function setMaintenance() {
        return self::createModeFileAndDeleteOthers(self::MAINTENANCE_FILENAME);
    }
    
    public static function setFrameworkDebug() {
        return self::createModeFileAndDeleteOthers(self::FRAMEWORK_DEBUG_FILENAME);
    }
    
    public static function setDebug() {
        return self::createModeFileAndDeleteOthers(self::DEBUG_FILENAME);
    }
    
    public static function setProduction() {
        return self::createModeFileAndDeleteOthers(self::PRODUCTION_FILENAME);
    }
    
}
