<?php



class UrlMapResolver2Test extends LTestCase {

	function newUrlMapResolver() {
        $resolver = new LUrlMapResolver();
        
        $resolver->init($_SERVER['FRAMEWORK_DIR'],'tests/urlmap2/public/static/','tests/urlmap2/public/alias_db/','tests/urlmap2/private/');
        
        return $resolver; 
    }

    function testResolveWithMatchAll() {

		$resolver = $this->newUrlMapResolver();

        $urlmap = $resolver->resolveUrlMap('folder/qualcosa/ancora');
        
        $this->assertNotNull($urlmap,"La urlmap non è stata trovata!");
    }
}