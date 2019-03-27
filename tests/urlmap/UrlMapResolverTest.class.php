<?php

class UrlMapResolverTest extends LTestCase {

    function newUrlMapResolver() {
        $resolver = new LUrlMapResolver($_SERVER['FRAMEWORK_DIR'],'tests/urlmap/public/static/','tests/urlmap/public/hash_db/','tests/urlmap/private/');
        return $resolver; 
    }
    
    function testUrlResolverParentRoute() {

        $resolver = $this->newUrlMapResolver();
        
        $this->assertEqual($resolver->getParentRoute('qualcosa'), '_default', "La route parent di 'qualcosa' non è corretta : " . $resolver->getParentRoute('qualcosa'));
        $this->assertEqual($resolver->getParentRoute('_default'), null, "La route parent di '_default' non è corretta : " . $resolver->getParentRoute('_default'));
        $this->assertEqual($resolver->getParentRoute("qualcosa/qualcosaltro"), "qualcosa/_default", "La route parent di 'qualcosa/qualcosaltro' non è corretta : " . $resolver->getParentRoute("qualcosa/qualcosaltro"));
        $this->assertEqual($resolver->getParentRoute("qualcosa/_default"), "_default", "La route parent di 'qualcosa/_default' non è corretta : " . $resolver->getParentRoute("qualcosa/_default"));
                
    }

    function testResolveProva() {
        
        $resolver = $this->newUrlMapResolver();
        
        $urlmap = $resolver->resolveUrlMap("/prova");
        
        $this->assertTrue($urlmap->is_set("/exec/do"),"L'exec do non è impostato nell'urlmap!");
        $this->assertEqual($urlmap->mustGet("/exec/do"),"/test/qualcosa","L'exec letto non corrisponde!");
    }
}
