<?php

class UrlMapResolverTest extends LTestCase {

    function testUrlResolverParentRoute() {

        $this->assertEqual(LUrlMapResolver::getParentRoute('qualcosa'), '_default', "La route parent di 'qualcosa' non è corretta : " . LUrlMapResolver::getParentRoute('qualcosa'));
        $this->assertEqual(LUrlMapResolver::getParentRoute('_default'), null, "La route parent di '_default' non è corretta : " . LUrlMapResolver::getParentRoute('_default'));
        $this->assertEqual(LUrlMapResolver::getParentRoute("qualcosa/qualcosaltro"), "qualcosa/_default", "La route parent di 'qualcosa/qualcosaltro' non è corretta : " . LUrlMapResolver::getParentRoute("qualcosa/qualcosaltro"));
        $this->assertEqual(LUrlMapResolver::getParentRoute("qualcosa/_default"), "_default", "La route parent di 'qualcosa/_default' non è corretta : " . LUrlMapResolver::getParentRoute("qualcosa/_default"));
                
    }

}
