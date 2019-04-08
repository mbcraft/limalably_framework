<?php

class LFolderPermissionChecker {

    private $errors = null;
    private $framework_folders_spec_list = null;
    private $project_folders_spec_list = null;

    function __construct() {
        $this->errors = [];
        $this->framework_folders_spec_list = $this->getFrameworkFoldersSpecList();
        $this->project_folders_spec_list = $this->getProjectFoldersSpecList();

        clearstatcache();
    }

    private function getFrameworkFoldersSpecList() {
        return [
            LConfigReader::simple('/classloader/framework_folder_list') => "?,r",
        ];
    }

    private function getProjectFoldersSpecList() {
        return [
            LConfigReader::simple('/urlmap/static_routes_folder') => "?,r",
            LConfigReader::simple('/urlmap/hash_db_routes_folder') => "?,r",
            LConfigReader::simple('/urlmap/private_routes_folder') => "?,r",
            LConfigReader::simple('/template/root_folder') => "?,r",
            LConfigReader::simple('/template/cache_folder') => "?,r,w,x",
            LConfigReader::simple('/classloader/project_folder_list') => "?,r",
            LConfigReader::simple('/classloader/map_cache_file_path') => "f",
            LConfigReader::simple('/classloader/class_cache_folder_') => "",
            LConfigReader::simple('/format/html/error_templates_folder') => "?,r",
            LConfigReader::simple('/format/json/error_templates_folder') => "?,r",
            LConfigReader::simple('/classloader/proc_folder') => "?,r",
            LConfigReader::simple('/classloader/data_folder') => "?,r",
            LConfigReader::executionMode('/logging/log_folder') => "?,w"
        ];
    }

    public function checkFrameworkFolders() {
        foreach ($this->framework_folders_spec_list as $folder_or_list => $spec_list) {
            $spec_array = explode(',', $spec_list);
            foreach ($spec_array as $spec) {
                $this->checkFolderBySpec($_SERVER['FRAMEWORK_DIR'], $folder_or_list, $folder, true, $spec);
            }
        }
    }

    public function checkProjectFolders() {
        foreach ($this->project_folders_spec_list as $folder_or_list => $spec_list) {
            $spec_array = explode(',', $spec_list);
            foreach ($spec_array as $spec) {
                $this->checkFolderBySpec($_SERVER['PROJECT_DIR'], $folder_or_list, $folder_or_list, true, $spec);
            }
        }
    }

    private function checkFolderBySpec(string $parent_folder, $relative_folder_path_or_list, bool $is_framework_folder, string $spec) {

        if (!is_array($relative_folder_path_or_list))
            $relative_folder_path_or_list = [$relative_folder_path_or_list];

        foreach ($relative_folder_path_or_list as $relative_folder) {
            $full_folder_path = $parent_folder.$relative_folder;
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
                    if (file_exists($full_folder_path)) break;
                    $current_dir = $full_folder_path;
                    do {
                        $current_dir = dirname($current_dir);
                        if (is_dir($current_dir)) break;
                    } while ($current_dir != '.');
                    $this->errors[] = 'The ' . ($is_framework_folder ? 'framework' : 'project') . ' folder "' . $relative_folder . '" does not permits directory and file creation!';
                    break;
                }
                default : throw new \Exception("Unable to correctly check folders : unable to handle spec '".$spec."'.");
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
