<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

abstract class LHttpResponse extends \Exception {

    protected $urlmap = null;
    protected $input = null;
    protected $session = null;
    protected $capture = null;
    protected $parameters = null;
    protected $output = null;

    function setup($treemap_urlmap, $treemap_input, $treemap_session, $capture, $parameters, $treemap_output) {
        $this->urlmap = $treemap_urlmap;
        $this->input = $treemap_input;
        $this->session = $treemap_session;
        $this->capture = $capture;
        $this->parameters = $parameters;
        if (!$treemap_output) {
            $this->output = new LTreeMap();
        } else {
            $this->output = $treemap_output;
        }
    }

    abstract function execute($format = null);
}
