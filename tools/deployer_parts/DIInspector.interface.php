<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface DIInspector {

    public function visit($dir);

    public function getExcludedPaths();

    public function getIncludedPaths();

}