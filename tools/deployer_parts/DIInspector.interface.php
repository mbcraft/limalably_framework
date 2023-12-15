<?php


if (!class_exists('DIInspector')) {
    interface DIInspector {

        public function visit($dir);

        public function getExcludedPaths();

        public function getIncludedPaths();

    }
}