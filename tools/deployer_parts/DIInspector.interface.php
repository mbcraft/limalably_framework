<?php



interface DIInspector {

    public function visit($dir);

    public function getExcludedPaths();

    public function getIncludedPaths();

}