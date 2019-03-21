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
            
   interface ANewInterface {
       
        function someFunction();
        function someOtherFunction();
   }
           
            
EOS;
    
    private $data4 = <<<EOS
    
   interface Topolino {
       
   }
            
EOS;
    
    private $data5 = <<<EOS
   
   namespace ProvaDiNamespace ;
            
    trait Paperino {
        
   }
        
EOS;
        
    
    
    function testMatchingNamespace1() {
        
        $namespace_pattern = "/namespace[ ]+(?<namespace>[a-zA-Z_0-9\\\\]+)[;{ ]+/";
        
        $matches = null;
        
        preg_match_all($namespace_pattern,$this->data1,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['namespace'][0],'\Pippo\Pluto\Paperino',"Il namespace trovato non corrisponde!");
        
        
    }
    
    function testMatchingClass1() {
        $class_pattern = "/class[ ]+(?<class>[a-zA-Z_0-9]+)[{ ]+/";
        
        $matches = null;
        
        preg_match_all($class_pattern,$this->data1,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['class'][0],'Something',"La classe trovata non corrisponde!");
    }
    
    function testMatchingTrait1() {
        $trait_pattern = "/trait[ ]+(?<trait>[a-zA-Z_0-9]+)[{ ]+/";
        
        $matches = null;
        
        preg_match_all($trait_pattern,$this->data2,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['trait'][0],'AgainATrait',"Il trait trovato non corrisponde!");
    }
    
    function testMatchingInterface1() {
        $interface_pattern = "/interface[ ]+(?<interface>[a-zA-Z_0-9]+)[{ ]+/";
        
        $matches = null;
        
        preg_match_all($interface_pattern,$this->data3,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        $this->assertEqual(count($matches),1,"Il numero di match non corrisponde!");
        $this->assertEqual($matches[0]['interface'][0],'ANewInterface',"L'interfaccia trovata non corrisponde!");
    }
    
}
