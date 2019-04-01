<?php

class LCallExecutor {
        
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
    private function executeProcFile($call_spec,$all_param_data) {
        if (!$this->isValidProcFileRoute($call_spec)) throw new \Exception("Unable to find valid proc : ".$call_spec);
        
        $path = $this->base_dir.$this->proc_folder.$call_spec.$this->proc_extension;
        $path = str_replace('//', '/', $path);
        
        $parameters = $all_param_data['parameters'];
        $capture = $all_param_data['capture'];
        $in = $all_param_data['in'];
        $session = $all_param_data['session'];
        $context_path = $all_param_data['context_path'];
        
        return include $path;
    }
    
    private function executeClassMethod($call_spec,$all_param_data) {
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
            
            $param_name = $mp->getName();
            if (in_array($param_name,$all_param_data)) {
                $prepared_parameters[] = $all_param_data[$param_name];
                continue;
            }
            
            if (in_array($param_name,$all_param_data['capture'])) {
                $prepared_parameters[] = $all_param_data['capture'][$param_name];
                continue;
            }
            if ($all_param_data['in']->is_set($param_name)) {
                $prepared_parameters[] = $all_param_data['in']->mustGet($param_name);
                continue;
            }
            
            if ($all_param_data['in']->is_set('/'.$param_name)) {
                $prepared_parameters[] = $all_param_data['in']->mustGet('/'.$param_name);
                continue;
            }
            
            if ($all_param_data['out']->is_set($param_name)) {
                $prepared_parameters[] = $all_param_data['out']->mustGet($param_name);
                continue;
            }
            
            if ($all_param_data['out']->is_set('/'.$param_name)) {
                $prepared_parameters[] = $all_param_data['out']->mustGet('/'.$param_name);
                continue;
            }

            if ($mp->isDefaultValueAvailable()) {
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
    
    private function internalExecute($call_spec,$all_param_data) {
        if (self::isProcExec($call_spec)) {
            return $this->executeProcFile($call_spec,$all_param_data);
        }
        if (self::isClassMethodExec($call_spec)) {
            return $this->executeClassMethod($call_spec,$all_param_data);
        }
        throw new \Exception("Unable to process call to execute : ".$call_spec);
    }
    
    public function execute(string $call_spec,array $all_param_data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $result = $this->internalExecute($call_spec,$all_param_data);
        
        if ($result==null) return [];
        return $result;
    }
    
}
