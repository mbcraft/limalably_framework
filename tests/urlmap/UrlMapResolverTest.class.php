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
    
    function testResolveStop() {
        $resolver = $this->newUrlMapResolver();
        
        $urlmap = $resolver->resolveUrlMap('folder/cisiamononesisto');
        
        $this->assertNotNull($urlmap,"La urlmap non è stata trovata!");
        $this->assertTrue($urlmap->is_set("/exec/do"),"L'exec do non è impostato nell'urlmap!");
        $this->assertTrue($urlmap->mustGet("/exec/do"),"stop_qualcosa","L'exec do non è impostato nell'urlmap!");
        
    }

    function testResolveProva() {
        
        $resolver = $this->newUrlMapResolver();
        
        $urlmap = $resolver->resolveUrlMap("/prova");
        
        $this->assertNotNull($urlmap,"La urlmap non è stata trovata!");
        $this->assertTrue($urlmap->is_set("/exec/do"),"L'exec do non è impostato nell'urlmap!");
        $this->assertEqual($urlmap->mustGet("/exec/do"),"/test/qualcosa/prova","L'exec letto non corrisponde!");
    }
    
    function testResolveFolderSubfolderAgain() {
        
        $resolver = $this->newUrlMapResolver();
        
        $urlmap = $resolver->resolveUrlMap("/folder/subfolder/again");
        
        $this->assertNotNull($urlmap,"La urlmap non è stata trovata!");
        $this->assertEqual($urlmap->mustGet('/exec/do'),"/test2/qualcosa2/again","L'exec do non corrisponde nella urlmap!");
        $this->assertEqual($urlmap->mustGet('/session/my_session_key/cardinality'),"required","La cardinalità della chiave nella session non corrisponde nella urlmap!");
        $this->assertEqual($urlmap->mustGet('/input/my_key/cardinality'),"required","La cardinalità della chiave nell'input non corrisponde nella urlmap!");
        
        
    }
    
    function testNextSearchedRoute() {
        
        $resolver = $this->newUrlMapResolver();
        
        $this->assertNull($resolver->getNextSearchedRoute('_stop'),"La route cercata non è corretta!");
        $this->assertEqual($resolver->getNextSearchedRoute('miaroute'),'_stop',"La route cercata non è corretta!");
        $this->assertEqual($resolver->getNextSearchedRoute('miaroute/cartella/'),'miaroute/_stop',"La route cercata non è corretta!");
        $this->assertEqual($resolver->getNextSearchedRoute('miaroute/cartella/qualcosa'),'miaroute/cartella/_stop',"La route cercata non è corretta!");
        $this->assertEqual($resolver->getNextSearchedRoute('miaroute/cartella/_stop'),'miaroute/_stop',"La route cercata non è corretta!");
        
        $this->assertEqual($resolver->getNextSearchedRoute('miaroute'),'_stop',"La route cercata non è corretta!");
        
    }
}
