<?php


if (!class_exists('LDIInspector')) {
    interface LDIInspector {

        public function visit($dir);

        public function getExcludedPaths();

        public function getIncludedPaths();

    }
}