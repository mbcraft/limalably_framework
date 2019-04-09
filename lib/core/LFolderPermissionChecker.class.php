<?php

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
        return [
            new LFolderCheck(LConfigReader::simple('/urlmap/static_routes_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/urlmap/hash_db_routes_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/urlmap/private_routes_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/template/root_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/template/cache_folder'),"?,r,w,x"),
            new LFolderCheck(LConfigReader::simple('/classloader/project_folder_list'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/classloader/map_cache_file_path'),"f"),
            new LFolderCheck(LConfigReader::simple('/classloader/class_cache_folder'),"f"),
            new LFolderCheck(LConfigReader::simple('/template/root_folder').LConfigReader::simple('/format/html/error_templates_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/template/root_folder').LConfigReader::simple('/format/json/error_templates_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/classloader/proc_folder'),"?,r"),
            new LFolderCheck(LConfigReader::simple('/classloader/data_folder'),"?,r"),
            new LFolderCheck(LConfigReader::executionModeWithType(LConfigReader::executionMode('/logging/type'), '/logging/%type%/log_folder'),"?,w")
        ];
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
                        if (!file_exists($full_folder_path))
                            $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not exist!';
                        break;
                    }
                case 'r': {
                        if (!is_readable($full_folder_path))
                            $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not have read permission!';

                        break;
                    }
                case 'w': {
                        if (!is_writable($full_folder_path))
                            $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not have write permission!';

                        break;
                    }
                case 'x': {
                        if (!is_dir($full_folder_path))
                            $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not have execution permission!';
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
