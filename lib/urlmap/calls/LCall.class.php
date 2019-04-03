<?php

class LCall {
        
    const DATA_IMPORT_PREFIX = '=';
    const ROUTE_CALL_PREFIX = '->';
    const OBJECT_METHOD_CALL_SEPARATOR = '#';
    const STATIC_METHOD_CALL_SEPARATOR = '::';
    
    
    private $initialized = false;
    private $base_dir = null;
    private $proc_folder = null;
    private $proc_extension = null;
    private $data_folder = null;
    
    public function isInitialized() {
        return $this->initialized;
    }
    
    public function init($base_dir,$proc_folder,$proc_extension,$data_folder) {
        $this->initialized = true;
        
        $this->base_dir = $base_dir;
        $this->proc_folder = $proc_folder;
        $this->proc_extension = $proc_extension;
        $this->data_folder = $data_folder;
    }
    
    public function initWithDefaults() {
        $this->initialized = true;
        
        $this->base_dir = $_SERVER['PROJECT_DIR'];
        $this->proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $this->proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $this->data_folder = LConfigReader::simple('/classloader/data_folder');
         
    }
    
    public static function isDataImport($call_spec) {
        return strpos($call_spec,self::DATA_IMPORT_PREFIX)!==false;
    }
    
    public static function isRoute($call_spec) {
        return strpos($call_spec,self::ROUTE_CALL_PREFIX)!==false;
    }
    
    /**
     * 
     * @param type $exec
     * @return type
     */
    public static function isProcExec($call_spec) {
        return !(self::isClassMethodExec($call_spec) || self::isRoute($call_spec) || self::isDataImport($call_spec));
    }
    
    /**
     * 
     * @param type $exec
     * @return type
     */
    public static function isClassMethodExec($call_spec) {
        return strpos($call_spec,self::OBJECT_METHOD_CALL_SEPARATOR)!==false || strpos($call_spec,self::STATIC_METHOD_CALL_SEPARATOR)!==false;
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
    
    private function executeDataImport($call_spec) {
        $data_path = substr($call_spec,strlen(self::DATA_IMPORT_PREFIX));
        
        $data_storage = new LDataStorage();
        
        if (!$data_storage->is_saved($data_path)) throw new \Exception("Unable to find data file at path : ".$data_path);
        
        return $data_storage->load($data_path);
    }
    
    private function executeRoute($call_spec,$all_param_data) {
        
        $route = substr($call_spec, strlen(self::ROUTE_CALL_PREFIX));
        
        $route_resolver = new LUrlMapResolver();
        $url_map = $route_resolver->resolveUrlMap($route, LUrlMapResolver::FLAGS_SEARCH_PRIVATE);
        
        if ($all_param_data && isset($all_param_data['parameters'])) {
            $parameters = $all_param_data['parameters'];
        } else {
            $parameters = [];
        }
        if ($all_param_data && isset($all_param_data['capture'])) {
            $capture = $all_param_data['capture'];
        } else {
            $capture = [];
        }
        if ($all_param_data && isset($all_param_data['input'])) {
            $input_view = $all_param_data['input'];
        } else {
            $input_view = new LTreeMap();
        }
        if ($all_param_data && isset($all_param_data['session'])) {
            $session_view = $all_param_data['session'];
        } else {
            $session_view = new LTreeMap();
        }
        
        $url_map_executor = new LUrlMapExecutor($url_map);
        
        return $url_map_executor->execute($route, $parameters, $capture, $input_view, $session_view);
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
        if ($all_param_data && isset($all_param_data['parameters'])) {
            $parameters = $all_param_data['parameters'];
        }
        if ($all_param_data && isset($all_param_data['capture'])) {
            $capture = $all_param_data['capture'];
        }
        if ($all_param_data && isset($all_param_data['input'])) {
            $input = $all_param_data['input'];
        }
        if ($all_param_data && isset($all_param_data['session'])) {
            $session = $all_param_data['session'];
        }
        if ($all_param_data && isset($all_param_data['context_path'])) {
            $context_path = $all_param_data['context_path'];
        }
        
        return include $path;
    }
    
    private function executeClassMethod($call_spec,$all_param_data) {
        
        $call_spec = str_replace('/','\\',$call_spec);
        if (LStringUtils::startsWith($call_spec, '\\')) $call_spec = substr($call_spec,1);
        
        if (strpos($call_spec,self::OBJECT_METHOD_CALL_SEPARATOR)!==false) {
            $static_call = false;
            list($class_name,$method_name) = explode(self::OBJECT_METHOD_CALL_SEPARATOR,$call_spec);
        }
        if (strpos($call_spec,self::STATIC_METHOD_CALL_SEPARATOR)!==false) {
            $static_call = true;
            list($class_name,$method_name) = explode(self::STATIC_METHOD_CALL_SEPARATOR,$call_spec);
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
        if ($static_call && !$reflection_method->isStatic()) throw new \Exception("Method to call is not static, use '".self::OBJECT_METHOD_CALL_SEPARATOR."' : ".$call_spec);
        if (!$static_call && $reflection_method->isStatic()) throw new \Exception("Method to call is static, use '".self::STATIC_METHOD_CALL_SEPARATOR."' : ".$call_spec);
        
        $method_parameters = $reflection_method->getParameters();
        $prepared_parameters = [];
        $method_parameter_search_list = LConfigReader::simple('/exec/method_parameter_search_list');
            
        foreach ($method_parameters as $mp) {
            
            $param_name = $mp->getName();
            
            foreach ($method_parameter_search_list as $method_search_spec) {
            
                switch ($method_search_spec) {
                    case 'meta':{
                        if (in_array($param_name,$all_param_data)) {
                            $prepared_parameters[] = $all_param_data[$param_name];
                            continue 3;
                        }
                        break;
                    }
                    case 'capture':{
                        if (isset($all_param_data['capture']) && in_array($param_name,$all_param_data['capture'])) {
                            $prepared_parameters[] = $all_param_data['capture'][$param_name];
                            continue 3;
                        }
                        break;
                    }
                    case 'relative_input':{
                        if ($all_param_data['input']->is_set($param_name)) {
                            $prepared_parameters[] = $all_param_data['input']->mustGet($param_name);
                            continue 3;
                        }
                        break;
                    }
                    case 'absolute_input':{
                        if ($all_param_data['input']->is_set('/'.$param_name)) {
                            $prepared_parameters[] = $all_param_data['input']->mustGet('/'.$param_name);
                            continue 3;
                        }
                        break;
                    }
                    case 'relative_session':{
                        if ($all_param_data['session']->is_set($param_name)) {
                            $prepared_parameters[] = $all_param_data['session']->mustGet($param_name);
                            continue 3;
                        }
                        break;
                    }
                    case 'absolute_session':{
                        if ($all_param_data['session']->is_set('/'.$param_name)) {
                            $prepared_parameters[] = $all_param_data['session']->mustGet('/'.$param_name);
                            continue 3;
                        }
                        break;
                    }
                    case 'relative_output':{
                        if ($all_param_data['output']->is_set($param_name)) {
                            $prepared_parameters[] = $all_param_data['output']->mustGet($param_name);
                            continue 3;
                        }
                        break;
                    }
                    case 'absolute_output':{
                        if ($all_param_data['output']->is_set('/'.$param_name)) {
                            $prepared_parameters[] = $all_param_data['output']->mustGet('/'.$param_name);
                            continue 3;
                        }
                        break;
                    }
                    case 'default_value':{
                        if ($mp->isDefaultValueAvailable()) {
                            $prepared_parameters[] = $mp->getDefaultValue();
                            continue 3;
                        }
                        break;
                    }
                }
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
    
    public function execute($call_spec,$all_param_data,$dynamic_call) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (self::isDataImport($call_spec)) {
            if ($dynamic_call) {
                throw new \Exception("It is not possible to use a data import in dynamic calls : ".$call_spec);
            } else {
                return $this->executeDataImport($call_spec);
            }
        }
        if (self::isRoute($call_spec)) {
            if ($dynamic_call) {
                throw new \Exception("It is not possible to use a route in dynamic calls : ".$call_spec);
            } else {
                return $this->executeRoute($call_spec, $all_param_data);
            }
        }
        if (self::isClassMethodExec($call_spec)) {
            return $this->executeClassMethod($call_spec,$all_param_data);
        }
        if (self::isProcExec($call_spec)) {
            return $this->executeProcFile($call_spec,$all_param_data);
        }

        throw new \Exception("Unable to process call to execute : ".$call_spec);
    }
        
}
