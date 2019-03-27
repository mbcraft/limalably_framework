<?php

class UrlMapResolverTest extends LTestCase {

    function testUrlResolverParentRoute() {

        $resolver = new LUrlMapResolver($_SERVER['FRAMEWORK_DIR']);
        
        $this->assertEqual($resolver->getParentRoute('qualcosa'), '_default', "La route parent di 'qualcosa' non è corretta : " . $resolver->getParentRoute('qualcosa'));
        $this->assertEqual($resolver->getParentRoute('_default'), null, "La route parent di '_default' non è corretta : " . $resolver->getParentRoute('_default'));
        $this->assertEqual($resolver->getParentRoute("qualcosa/qualcosaltro"), "qualcosa/_default", "La route parent di 'qualcosa/qualcosaltro' non è corretta : " . $resolver->getParentRoute("qualcosa/qualcosaltro"));
        $this->assertEqual($resolver->getParentRoute("qualcosa/_default"), "_default", "La route parent di 'qualcosa/_default' non è corretta : " . $resolver->getParentRoute("qualcosa/_default"));
                
    }

}
