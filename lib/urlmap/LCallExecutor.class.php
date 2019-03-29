<?php

class LCallExecutor {
    
    const IN_PARAMETER_NAME = 'in';
    const OUT_PARAMETER_NAME = 'out';
    
    private $initialized = false;
    private $base_dir = null;
    private $proc_folder = null;
    private $proc_extension = null;
    
    private function isInitialized() {
        return $this->initialized;
    }
    
    public function init($base_dir,$proc_folder,$proc_extension) {
        $this->initialized = true;
        
        $this->base_dir = $base_dir;
        $this->proc_folder = $proc_folder;
        $this->proc_extension = $proc_extension;
    }
    
    private function initWithDefaults() {
        $this->initialized = true;
        
        $this->proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $this->proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $this->base_dir = $_SERVER['PROJECT_DIR']; 
    }
    /**
     * 
     * @param type $exec
     * @return type
     */
    public static function isProcExec($call_spec) {
        return !self::isClassMethodExec($call_spec);
    }
    
    /**
     * 
     * @param type $exec
     * @return type
     */
    public static function isClassMethodExec($call_spec) {
        return strpos($call_spec,'#')!==false || strpos($call_spec,'::')!==false;
    }
    
    /**
     * Ritorna un valore booleano che indica se la route è valida come shortcut per un file di proc.
     * 
     * @param string $route La route
     * @return boolean true se lo shortcut alla proc è valido, false altrimenti
     */
    private function isValidProcFileRoute($call_spec) {

        $path = $this->base_dir.$this->proc_folder.$call_spec.$this->proc_extension;
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    /**
     * Include il file di una proc
     * 
     * @param string $route La route al proc
     */
    private function executeProcFile($call_spec) {
        if (!$this->isValidProcFileRoute($call_spec)) throw new \Exception("Unable to find valid proc : ".$call_spec);
        
        $path = $this->base_dir.$this->proc_folder.$call_spec.$this->proc_extension;
        $path = str_replace('//', '/', $path);
        return include $path;
    }
    
    private function executeClassMethod($call_spec) {
        $call_spec = str_replace('%','\\',$call_spec);
        $call_spec = str_replace('/','\\',$call_spec);
        if (LStringUtils::startsWith($call_spec, '\\')) $call_spec = substr($call_spec,1);
        
        if (strpos($call_spec,'#')!==false) {
            $static_call = false;
            list($class_name,$method_name) = explode('#',$call_spec);
        }
        if (strpos($call_spec,'::')!==false) {
            $static_call = true;
            list($class_name,$method_name) = explode('::',$call_spec);
        }
        try {
            $reflection_class = new ReflectionClass($class_name);
        } catch (\Exception $ex) {
            throw new \Exception("Unable to find class to call : ".$call_spec);
        }
        if (!$static_call && !$reflection_class->isInstantiable()) throw new \Exception("Can't call method on abstract class : ".$call_spec);
        if (!$reflection_class->hasMethod($method_name)) throw new \Exception("Unable to find method on class : ".$call_spec);
        $reflection_method = $reflection_class->getMethod($method_name);
        if ($reflection_method->isAbstract()) throw new \Exception("Method to call is abstract and can't be called : ".$call_spec);
        if ($static_call && !$reflection_method->isStatic()) throw new \Exception("Method to call is not static, use '#' : ".$call_spec);
        if (!$static_call && $reflection_method->isStatic()) throw new \Exception("Method to call is static, use '::' : ".$call_spec);
        
        $method_parameters = $reflection_method->getParameters();
        $prepared_parameters = [];
        foreach ($method_parameters as $mp) {
            if ($mp->getName()==self::IN_PARAMETER_NAME) {
                $prepared_parameters[] = LInput::getCurrentView();
                continue;
            }
            if ($mp->getName()==self::OUT_PARAMETER_NAME) {
                $prepared_parameters[] = LOutput::getCurrentView();
                continue;
            }
            if (LInput::is_set($mp->getName())) {
                $prepared_parameters[] = LInput::get($mp->getName());
                continue;
            } elseif ($mp->isDefaultValueAvailable()) {
                $prepared_parameters[] = $mp->getDefaultValue();
                continue;
            }
            
            throw new \Exception("Missing parameter ".$mp->getName()." from input, call : ".$call_spec);
        }
        //parameters are ready, now execute the call
        if ($static_call) {
            $class_instance = null;
        } else {
            try {
                $class_instance = $reflection_class->newInstance();
            } catch (\Exception $ex) {
                throw new \Exception("Unable to instantiate class for call : ".$call_spec,$ex);
            }
        }
        
        return $reflection_method->invokeArgs($class_instance, $prepared_parameters);
        
    }
    
    private function internalExecute($call_spec) {
        if (self::isProcExec($call_spec)) {
            return $this->executeProcFile($call_spec);
        }
        if (self::isClassMethodExec($call_spec)) {
            return $this->executeClassMethod($call_spec);
        }
        throw new \Exception("Unable to process call to execute : ".$call_spec);
    }
    
    public function execute($call_spec) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $result = $this->internalExecute($call_spec);
        
        if ($result==null) return [];
        return $result;
    }
}
