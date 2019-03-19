<?php

/**
 * Models and handles the execution mode of the framework. Only 4 execution modes are supported : maintenance, framework_debug, debug and production.
 * Each execution modes makes the framework work in a slightly different way.
 */
class LExecutionMode {
    
    const MAINTENANCE_MODE = 'maintenance';
    const MAINTENANCE_FILENAME = 'maintenance.txt';
    
    const FRAMEWORK_DEVELOPMENT_MODE = 'framework_development';
    const FRAMEWORK_DEVELOPMENT_FILENAME = 'framework_development.txt';
    
    const DEVELOPMENT_MODE = 'development';
    const DEVELOPMENT_FILENAME = 'development.txt';
    
    const TESTING_MODE = 'testing';
    const TESTING_FILENAME = 'testing.txt';
    
    const PRODUCTION_MODE = 'production';
    const PRODUCTION_FILENAME = 'production.txt';
    
    private static $my_mode = null;
    
    public static function isMaintenance() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::MAINTENANCE_MODE;
        
        return !self::modeFileExists(self::FRAMEWORK_DEVELOPMENT_FILENAME) && !self::modeFileExists(self::DEVELOPMENT_FILENAME) && !self::modeFileExists(self::TESTING_FILENAME) && !self::modeFileExists(self::PRODUCTION_FILENAME);
    }
    
    public static function isFrameworkDevelopment() {
        if (!isset($_SERVER['PROJECT_DIR'])) return true;
        
        if (self::$my_mode) return self::$my_mode == self::FRAMEWORK_DEVELOPMENT_MODE;
        
        return self::modeFileExists(self::FRAMEWORK_DEVELOPMENT_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME);
    }
    
    public static function isDevelopment() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::DEVELOPMENT_MODE;
        
        return self::modeFileExists(self::DEVELOPMENT_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME) && !self::modeFileExists(self::FRAMEWORK_DEVELOPMENT_FILENAME);
    }
    
    public static function isTesting() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::TESTING_MODE;
        
        return self::modeFileExists(self::TESTING_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME) && !self::modeFileExists(self::FRAMEWORK_DEVELOPMENT_FILENAME) && !self::modeFileExists(self::DEVELOPMENT_FILENAME);
    }
    
    public static function isProduction() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::PRODUCTION_MODE;
        
        return self::modeFileExists(self::PRODUCTION_FILENAME) && !self::modeFileExists(self::MAINTENANCE_FILENAME) && !self::modeFileExists(self::DEVELOPMENT_FILENAME) && !self::modeFileExists(self::FRAMEWORK_DEVELOPMENT_FILENAME);
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
        $result = file_put_contents(self::getModeDir().$filename, LEnvironmentUtils::getServerUser()) !== false;
        $mode_files = self::listModeFiles();
        foreach ($mode_files as $mode_file) {
            if ($mode_file!=$filename)  {
                $result &= self::deleteModeFile ($mode_file);
            }
        }
        return $result;
    }
    
    public static function init() {
        self::$my_mode = self::get();
    }
    
    public static function get() {
        if (!isset($_SERVER['PROJECT_DIR'])) return self::FRAMEWORK_DEVELOPMENT_MODE;
        
        if (self::$my_mode) return self::$my_mode;
        
        if (self::isMaintenance()) return self::MAINTENANCE_MODE;
        if (self::isFrameworkDevelopment()) return self::FRAMEWORK_DEVELOPMENT_MODE;
        if (self::isDevelopment()) return self::DEVELOPMENT_MODE;
        if (self::isTesting()) return self::TESTING_MODE;
        if (self::isProduction()) return self::PRODUCTION_MODE;
    }
    
    private static function invalidExecutionModeException() {
        return new \Exception('Invalid execution mode name. Allowed only : '.implode(',',[self::MAINTENANCE_MODE,self::FRAMEWORK_DEVELOPMENT_MODE,self::DEVELOPMENT_MODE,self::TESTING_MODE,self::PRODUCTION_MODE]));
    }
    
    public static function isByName($mode_name) {
        switch ($mode_name) {
            case self::MAINTENANCE_MODE : return self::isMaintenance();
            case self::FRAMEWORK_DEVELOPMENT_MODE : return self::isFrameworkDevelopment();
            case self::DEVELOPMENT_MODE : return self::isDevelopment();
            case self::TESTING_MODE : return self::isTesting();
            case self::PRODUCTION_MODE : return self::isProduction();
            default : throw self::invalidExecutionModeException();
        }
    }
    
    public static function setByName($mode_name) {
        switch ($mode_name) {
            case self::MAINTENANCE_MODE : return self::setMaintenance();
            case self::FRAMEWORK_DEVELOPMENT_MODE : return self::setFrameworkDevelopment();
            case self::DEVELOPMENT_MODE : return self::setDevelopment();
            case self::TESTING_MODE : return self::setTesting();
            case self::PRODUCTION_MODE : return self::setProduction();
            default : throw self::invalidExecutionModeException();
        }
    }
    
    public static function setMaintenance() {
        return self::createModeFileAndDeleteOthers(self::MAINTENANCE_FILENAME);
    }
    
    public static function setFrameworkDevelopment() {
        return self::createModeFileAndDeleteOthers(self::FRAMEWORK_DEVELOPMENT_FILENAME);
    }
    
    public static function setDevelopment() {
        return self::createModeFileAndDeleteOthers(self::DEVELOPMENT_FILENAME);
    }
    
    public static function setTesting() {
        return self::createModeFileAndDeleteOthers(self::TESTING_FILENAME);
    }
    
    public static function setProduction() {
        return self::createModeFileAndDeleteOthers(self::PRODUCTION_FILENAME);
    }
    
}
