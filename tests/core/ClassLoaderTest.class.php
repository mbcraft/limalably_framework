<?php

class ClassLoaderTest extends LTestCase {
    
    private $data1 = <<<EOS
    
   namespace \Pippo\Pluto\Paperino;
            
   class Something extends SomethingElse {
            
   ...
   }
            
EOS;
    
    private $data2 = <<<EOS

    namespace \Pippo\Pluto\Paperino{
            
   class Something extends SomethingElse {
   
        function method() {
            echo "Hello!";
        }
   }
            
   trait AgainATrait {
       
        private \$variable;
   }
}
            
EOS;
    
    private $data3 = <<<EOS

     namespace \Pippo\Pluto\Topolino ;
            
   class Something extends SomethingElse {
   
        function method() {
            echo "Hello!";
        }
   }

   namespace \Ancora\Un\Namespace\Particolare;
            
   interface ANewInterface 
   {
       
        function someFunction();
        function someOtherFunction();
   }
           
            
EOS;
    
    private $data4 = <<<EOS
    
   interface Topolino 
   {
       
   }
            
EOS;
    
    private $data5 = <<<EOS
   
   namespace ProvaDiNamespace ;
            
    trait Paperino {
        
   }
        
EOS;
        
    const PATTERN_NAMESPACE = "/namespace[ ]+(?<namespace>[a-zA-Z_0-9\\\\]+)[;{ ]+/";
    const PATTERN_CLASS = "/class[ ]+(?<class>[a-zA-Z_0-9]+)[{ ]+/";
    const PATTERN_TRAIT = "/trait[ ]+(?<trait>[a-zA-Z_0-9]+)[{ ]+/";
    const PATTERN_INTERFACE = "/interface[ ]+(?<interface>[a-zA-Z_0-9]+)[{ ]+/";
    
    function testMatchingNamespace1() {
        
        $namespace_pattern = self::PATTERN_NAMESPACE;
        
        $matches = null;
        
        preg_match_all($namespace_pattern,$this->data1,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['namespace'][0],'\Pippo\Pluto\Paperino',"Il namespace trovato non corrisponde!");
        
        
    }
    
    function testMatchingNamespace2() {
        
        $namespace_pattern = self::PATTERN_NAMESPACE;
        
        $matches = null;
        
        preg_match_all($namespace_pattern,$this->data3,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),2,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['namespace'][0],'\Pippo\Pluto\Topolino',"Il namespace trovato non corrisponde!");
        $this->assertEqual($matches[1]['namespace'][0],'\Ancora\Un\Namespace\Particolare',"Il namespace trovato non corrisponde!");
        
        
    }
    
    function testMatchingClass1() {
        $class_pattern = self::PATTERN_CLASS;
        
        $matches = null;
        
        preg_match_all($class_pattern,$this->data1,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['class'][0],'Something',"La classe trovata non corrisponde!");
    }
    
    function testMatchingTrait1() {
        $trait_pattern = self::PATTERN_TRAIT;
        
        $matches = null;
        
        preg_match_all($trait_pattern,$this->data2,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['trait'][0],'AgainATrait',"Il trait trovato non corrisponde!");
    }
    function testMatchingTrait2() {
        $trait_pattern = self::PATTERN_TRAIT;
        
        $matches = null;
        
        preg_match_all($trait_pattern,$this->data5,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['trait'][0],'Paperino',"Il trait trovato non corrisponde!");
    }
    
    function testMatchingInterface1() {
        $interface_pattern = self::PATTERN_INTERFACE;
        
        $matches = null;
        
        preg_match_all($interface_pattern,$this->data3,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['interface'][0],'ANewInterface',"L'interfaccia trovata non corrisponde!");
    }
    function testMatchingInterface2() {
        $interface_pattern = self::PATTERN_INTERFACE;
        
        $matches = null;
        
        preg_match_all($interface_pattern,$this->data4,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['interface'][0],'Topolino',"L'interfaccia trovata non corrisponde!");
    }
        
}
