<?php

if (!class_exists('LDContentHashInspector')) {
    class LDContentHashInspector implements LDIInspector {

        private $excluded_paths = [];
        private $included_paths = [];

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

                if ($dir->getPath()!="") {
                   $result[$dir->getPath()] = $dir->getContentHash($this->excluded_paths);
                }

                $files = $dir->listFiles();

                foreach ($files as $f) {
                    if (!in_array($f->getPath(),$this->excluded_paths)) {
                        $result[$f->getPath()] = $f->getContentHash($this->excluded_paths);
                    }
                }
            }

            return $result;

        }
    }
}