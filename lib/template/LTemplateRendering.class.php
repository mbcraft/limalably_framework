<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTemplateRendering {

    private $template_factory = null;
    private $template_source = null;
    
    function __construct() {
        
        $this->template_factory = new LUrlMapTemplateSourceFactory();
    }

    public function setupTemplateSource($engine_name) {

        $this->template_source = $this->template_factory->createFileTemplateSource($engine_name);
    }

    function searchTemplate($path) {
        return $this->template_source->searchTemplate($path);
    }

    function hasRootFolder() {
        return $this->template_source->hasRootFolder();
    }

    function getRootFolder() {
        return $this->template_source->getRootFolder();
    }
    
    function render($template_path,$parameters=[]) {

        try 
        {
            if (!$template_path) {
                LErrorList::saveFromErrors('template', 'Unable to find file template at path : ' . $template_path);
            } else {
                $template = $this->template_source->getTemplate($template_path);

                //LResult::trace("Data is ready, now doing real rendering ...");
                return $template->render($parameters);
            }
        } catch (\Exception $ex) {
            
            echo $ex->getTraceAsString();

            LErrorList::saveFromException('template', $ex);
        }
    }
}


