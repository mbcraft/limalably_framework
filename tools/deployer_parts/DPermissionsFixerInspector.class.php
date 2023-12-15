<?php


if (!class_exists('DPermissionsFixerInspector')) {
    class DPermissionsFixerInspector implements DIInspector {

        private $excluded_paths = [];
        private $included_paths = [];

        function __construct($permissions_to_set) {
            $this->permissions_to_set = $permissions_to_set;
        }

        public function setExcludedPaths($excluded_paths) {
            $this->excluded_paths = $excluded_paths;
        }

        public function getExcludedPaths() {
            return $this->excluded_paths;
        }

        public function setIncludedPaths($included_paths) {
            $this->included_paths = $included_paths;
        }

        public function getIncludedPaths() {
            return $this->included_paths;
        }

        public function visit($dir) {

            $result = [];

            if ($dir->exists() && !in_array($dir->getPath(),$this->excluded_paths)) {

                $files = $dir->listFiles();

                foreach ($files as $f) {
                    if (!in_array($f->getPath(),$this->excluded_paths)) {
                        $f->setPermissions($this->permissions_to_set);
                    }
                }
            }

            return $result;

        }
    }
}