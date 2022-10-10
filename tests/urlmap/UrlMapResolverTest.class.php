<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class UrlMapResolverTest extends LTestCase {

    function newUrlMapResolver() {
        $resolver = new LUrlMapResolver();
        
        $resolver->init($_SERVER['FRAMEWORK_DIR'],'tests/urlmap/public/static/','tests/urlmap/public/alias_db/','tests/urlmap/private/');
        
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
        $this->assertTrue($urlmap->is_set("/exec"),"L'exec non è impostato nell'urlmap!");
        $this->assertTrue($urlmap->mustGet("/exec/%"),"stop_qualcosa","L'exec do non è impostato nell'urlmap!");
        
    }
    
    function testResolveProva() {
        
        $resolver = $this->newUrlMapResolver();
        
        $urlmap = $resolver->resolveUrlMap("/prova");
        
        $this->assertNotNull($urlmap,"La urlmap non è stata trovata!");
        $this->assertTrue($urlmap->is_set("/exec"),"L'exec non è impostato nell'urlmap!");
        $this->assertEqual($urlmap->mustGet("/exec/%"),"/test/qualcosa/prova","L'exec letto non corrisponde a /test/qualcosa/prova ! : ".var_export($urlmap->mustGet("/exec/%"),true));
    }
    
    function testResolveFolderSubfolderAgain() {
        
        $resolver = $this->newUrlMapResolver();
        
        $urlmap = $resolver->resolveUrlMap("/folder/subfolder/again");
        
        $this->assertNotNull($urlmap,"La urlmap non è stata trovata!");
        $this->assertEqual($urlmap->mustGet('/exec/%'),"/test2/qualcosa2/again","L'exec non corrisponde a /test2/qualcosa2/again nella urlmap! : ".var_export($urlmap->mustGet('/exec/%'),true));
        $this->assertEqual($urlmap->mustGet('/session/my_session_key/rules'),"NotBlank","La regola della chiave nella session non corrisponde nella urlmap!");
        $this->assertEqual($urlmap->mustGet('/input/my_key/rules'),"NotBlank","La regola della chiave nell'input non corrisponde nella urlmap!");
        
        
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
    
    function testResolveSomething() {
        
        $resolver = $this->newUrlMapResolver();
        
        $this->assertNotNull($resolver->resolveUrlMap("something", LUrlMapResolver::FLAGS_SEARCH_PRIVATE),"Non riesco a trovare la route privata something");
        
    }
    
}
