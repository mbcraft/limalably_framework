<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LFolderPermissionChecker {

    private $errors = null;
    private $framework_folders_spec_list = null;
    private $project_folders_spec_list = null;

    function __construct() {
        $this->errors = [];

        clearstatcache();
    }

    private function getFrameworkFoldersSpecList() {
        
        
        
        return [
            new LFolderCheck(LConfigReader::simple('/classloader/framework_folder_list'),"?,r")
        ];
    }

    private function getProjectFoldersSpecList() {
        
        $r1 = array();

        $engine_list = LConfigReader::simple('/template');

        foreach ($engine_list as $engine_name => $engine_specs) {
            $r1 [] = new LFolderCheck($engine_specs['root_folder'],"?,r");
            $r1[] = new LFolderCheck($engine_specs['root_folder'].LConfigReader::simple('/format/html/error_templates_folder'),"?,r");
            $r1[] = new LFolderCheck($engine_specs['root_folder'].LConfigReader::simple('/format/json/error_templates_folder'),"?,r");

            $template_source_factory = $engine_specs['source_factory_class'];

            $factory_instance = new $template_source_factory($engine_name);
            if ($factory_instance->supportsCache()) {
                $r1 [] = new LFolderCheck($engine_specs['cache_folder'],"?,r,w,x");
            }
        }

        $r2 = [
            new LFolderCheck(LConfigReader::simple('/urlmap/static_routes_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/urlmap/alias_db_routes_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/urlmap/private_routes_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/classloader/project_folder_list'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/classloader/map_cache_file_path'),"f"),
            new LFolderCheck(LConfigReader::simple('/classloader/class_cache_folder'),"f"),
            new LFolderCheck(LConfigReader::simple('/misc/proc_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/misc/data_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/i18n/translations_root_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/i18n/cache_folder'),"?,r,w,x"),
        ];
        
        $result = array_merge($r1,$r2);

        $log_type = LConfigReader::executionMode('/logging/type');
                
        if (LStringUtils::endsWith($log_type, 'file')) {
            $result[] = new LFolderCheck(LConfigReader::executionModeWithType(LConfigReader::executionMode('/logging/type'), '/logging/%type%/log_folder'),"?,w");
        }
        
        return $result;
    }

    public function checkFrameworkFolders() {
        $this->framework_folders_spec_list = $this->getFrameworkFoldersSpecList();

        foreach ($this->framework_folders_spec_list as $check) {
            $spec_array = $check->getSpecList();
            foreach ($spec_array as $spec) {
                $this->checkFolderBySpec($_SERVER['FRAMEWORK_DIR'], $check->getFolderList(), true, $spec);
            }
        }
    }

    public function checkProjectFolders() {
        $this->project_folders_spec_list = $this->getProjectFoldersSpecList();

        foreach ($this->project_folders_spec_list as $check) {

            $spec_array = $check->getSpecList();
            foreach ($spec_array as $spec) {
                $this->checkFolderBySpec($_SERVER['PROJECT_DIR'], $check->getFolderList(), false, $spec);
            }
        }
    }

    private function checkFolderBySpec(string $parent_folder, $relative_folder_path_or_list, bool $is_framework_folder, string $spec) {

        if (!is_array($relative_folder_path_or_list))
            $relative_folder_path_or_list = [$relative_folder_path_or_list];

        foreach ($relative_folder_path_or_list as $relative_folder) {
            $full_folder_path = $parent_folder . $relative_folder;
            switch ($spec) {
                case '?': {
                        if (!file_exists($full_folder_path)) {
                            $result = mkdir($full_folder_path,0777,true);
                            if ($result) chmod($full_folder_path,0777);
                        }
                        if (!file_exists($full_folder_path)) {
                            $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not exist!';
                        }
                        break;
                    }
                case 'r': {
                        if (!is_readable($full_folder_path)) {
                            if (LConfigReader::simple('/misc/autofix_folder_permissions')) {
                                $fixed = chmod($full_folder_path,0440);
                            } else {
                                $fixed = false;
                            }
                            if (!$fixed) {
                                $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not have read permission!';
                            }
                        }
                            
                        break;
                    }
                case 'w': {
                        if (!is_writable($full_folder_path)) {
                            if (LConfigReader::simple('/misc/autofix_folder_permissions')) {
                                $fixed = chmod($full_folder_path,0660);
                            } else {
                                $fixed = false;
                            }
                        
                            if (!$fixed) {
                                $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not have write permission!';
                            }
                        }
                        break;
                    }
                case 'x': {
                        if (!is_dir($full_folder_path)) {
                            if (LConfigReader::simple('/misc/autofix_folder_permissions')) {
                                $fixed = chmod($full_folder_path,0770);
                            } else {
                                $fixed = false;
                            }
                        
                            if (!$fixed) {
                                $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not have execution permission!';
                            }
                        }
                        break;
                    }
                case 'f': {
                        if (file_exists($full_folder_path))
                            break;
                        $current_dir = $full_folder_path;
                        do {
                            $current_dir = dirname($current_dir);
                            if (is_dir($current_dir))
                                break 2;
                        } while ($current_dir != '.');
                        $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not permits directory and file creation!';
                        break;
                    }
                default : throw new \Exception("Unable to correctly check folders : unable to handle spec '" . $spec . "'.");
            }
        }
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

}
