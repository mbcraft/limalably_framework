<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/**
 * Models and handles the execution mode of the framework. Only 4 execution modes are supported : maintenance, framework_debug, debug and production.
 * Each execution modes makes the framework work in a slightly different way.
 */
class LExecutionMode {
    
    const MODE_MAINTENANCE = 'maintenance';
    const MODE_MAINTENANCE_SHORT = 'm';
    const FILENAME_MAINTENANCE = 'maintenance.txt';
    
    const MODE_FRAMEWORK_DEVELOPMENT = 'framework_development';
    const MODE_FRAMEWORK_DEVELOPMENT_SHORT = 'fd';
    const FILENAME_FRAMEWORK_DEVELOPMENT = 'framework_development.txt';
    
    const MODE_DEVELOPMENT = 'development';
    const MODE_DEVELOPMENT_SHORT = 'd';
    const FILENAME_DEVELOPMENT = 'development.txt';
    
    const MODE_TESTING = 'testing';
    const MODE_TESTING_SHORT = 't';
    const FILENAME_TESTING = 'testing.txt';
    
    const MODE_PRODUCTION = 'production';
    const MODE_PRODUCTION_SHORT = 'p';
    const FILENAME_PRODUCTION = 'production.txt';
    
    private static $my_mode = null;

/*    
    public static function logErrors() {
        return !self::isFrameworkDevelopment();
    }
    
    public static function displayErrors() {
        return self::isFrameworkDevelopment() || self::isDevelopment() || self::isTesting();
    }
*/    
    public static function isMaintenance() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::MODE_MAINTENANCE;
        
        return !self::modeFileExists(self::FILENAME_FRAMEWORK_DEVELOPMENT) && !self::modeFileExists(self::FILENAME_DEVELOPMENT) && !self::modeFileExists(self::FILENAME_TESTING) && !self::modeFileExists(self::FILENAME_PRODUCTION);
    }
    
    public static function isFrameworkDevelopment() {
        if (!isset($_SERVER['PROJECT_DIR'])) return true;
        
        if (self::$my_mode) return self::$my_mode == self::MODE_FRAMEWORK_DEVELOPMENT;
        
        return self::modeFileExists(self::FILENAME_FRAMEWORK_DEVELOPMENT) && !self::modeFileExists(self::FILENAME_MAINTENANCE);
    }
    
    public static function isDevelopment() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::MODE_DEVELOPMENT;
        
        return self::modeFileExists(self::FILENAME_DEVELOPMENT) && !self::modeFileExists(self::FILENAME_MAINTENANCE) && !self::modeFileExists(self::FILENAME_FRAMEWORK_DEVELOPMENT);
    }
    
    public static function isTesting() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::MODE_TESTING;
        
        return self::modeFileExists(self::FILENAME_TESTING) && !self::modeFileExists(self::FILENAME_MAINTENANCE) && !self::modeFileExists(self::FILENAME_FRAMEWORK_DEVELOPMENT) && !self::modeFileExists(self::FILENAME_DEVELOPMENT);
    }
    
    public static function isProduction() {
        if (!isset($_SERVER['PROJECT_DIR'])) return false;
        
        if (self::$my_mode) return self::$my_mode == self::MODE_PRODUCTION;
        
        return self::modeFileExists(self::FILENAME_PRODUCTION) && !self::modeFileExists(self::FILENAME_MAINTENANCE) && !self::modeFileExists(self::FILENAME_DEVELOPMENT) && !self::modeFileExists(self::FILENAME_FRAMEWORK_DEVELOPMENT);
    }
   
    private static function modeFileExists($filename) {
        $file_path = self::getModeDir().$filename;
        
        return file_exists($file_path);
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
        if (!isset($_SERVER['PROJECT_DIR'])) return self::MODE_FRAMEWORK_DEVELOPMENT;
        
        if (self::$my_mode) return self::$my_mode;
        
        if (self::isMaintenance()) return self::MODE_MAINTENANCE;
        if (self::isFrameworkDevelopment()) return self::MODE_FRAMEWORK_DEVELOPMENT;
        if (self::isDevelopment()) return self::MODE_DEVELOPMENT;
        if (self::isTesting()) return self::MODE_TESTING;
        if (self::isProduction()) return self::MODE_PRODUCTION;
        
        throw new \Exception("Invalid state : unable to determine the current execution mode.");
    }
    
    public static function getShort() {
        $mode_long = self::get();
        
        switch ($mode_long) {
            case self::MODE_FRAMEWORK_DEVELOPMENT : return self::MODE_FRAMEWORK_DEVELOPMENT_SHORT;
            case self::MODE_DEVELOPMENT : return self::MODE_DEVELOPMENT_SHORT;
            case self::MODE_TESTING : return self::MODE_TESTING_SHORT;
            case self::MODE_PRODUCTION : return self::MODE_PRODUCTION_SHORT;
            case self::MODE_MAINTENANCE : return self::MODE_MAINTENANCE_SHORT;
            default : throw new \Exception("Invalid state : unable to determine the current short execution mode.");
        }
    }
    
    private static function invalidExecutionModeException() {
        return new \Exception('Invalid execution mode name. Allowed only : '.implode(',',[self::MODE_MAINTENANCE,self::MODE_FRAMEWORK_DEVELOPMENT,self::MODE_DEVELOPMENT,self::MODE_TESTING,self::MODE_PRODUCTION]));
    }
    
    public static function isByName($mode_name) {
        switch ($mode_name) {
            case self::MODE_MAINTENANCE : return self::isMaintenance();
            case self::MODE_FRAMEWORK_DEVELOPMENT : return self::isFrameworkDevelopment();
            case self::MODE_DEVELOPMENT : return self::isDevelopment();
            case self::MODE_TESTING : return self::isTesting();
            case self::MODE_PRODUCTION : return self::isProduction();
            default : throw self::invalidExecutionModeException();
        }
    }
    
    public static function setByName($mode_name) {
        switch ($mode_name) {
            case self::MODE_MAINTENANCE : return self::setMaintenance();
            case self::MODE_FRAMEWORK_DEVELOPMENT : return self::setFrameworkDevelopment();
            case self::MODE_DEVELOPMENT : return self::setDevelopment();
            case self::MODE_TESTING : return self::setTesting();
            case self::MODE_PRODUCTION : return self::setProduction();
            default : throw self::invalidExecutionModeException();
        }
    }
    
    public static function setMaintenance() {
        return self::createModeFileAndDeleteOthers(self::FILENAME_MAINTENANCE);
    }
    
    public static function setFrameworkDevelopment() {
        return self::createModeFileAndDeleteOthers(self::FILENAME_FRAMEWORK_DEVELOPMENT);
    }
    
    public static function setDevelopment() {
        return self::createModeFileAndDeleteOthers(self::FILENAME_DEVELOPMENT);
    }
    
    public static function setTesting() {
        return self::createModeFileAndDeleteOthers(self::FILENAME_TESTING);
    }
    
    public static function setProduction() {
        return self::createModeFileAndDeleteOthers(self::FILENAME_PRODUCTION);
    }
    
}
