<?php

/*
Some help from :
 * https://stackoverflow.com/questions/36515677/php-instanceof-for-traits
 * https://stackoverflow.com/users/5732351/rod-elias
 *  */

trait LStaticWriteHashMap {
    
    /*
     * Imposta un valore. L'ultima parte del path diventa la chiave.
     * Se il valore è un Tree viene creato un link.
     * Esempio :
     * 
     * path : /html/head/title
     * value = "Benvenuti nel sito XYZ!!"
     */
    public static function set($path,$value)
    {
        $path_used = self::all_but_last_path_tokens($path);
        
        $current_node = &self::$data;
        
        foreach ($path_used as $p)
        {            
            if (!isset($current_node[$p]))
                $current_node[$p] = array();

            $current_node = &$current_node[$p];
            
        }
        
        if (is_object($value) && in_array('LStaticReadHashMap',class_uses(get_class($value))))
            $current_node[self::last_path_token($path)] = get_class($value)::get("/");
        else
            $current_node[self::last_path_token($path)] = $value;
    }
    
    /*
     * Aggiunge un valore all'array nella posizione corrente.
     * Se il valore è un albero viene creato un link.
     * Esempio :
     * 
     * path : /html/head/keywords
     * value : ravenna
     * 
     * Viene aggiunta "ravenna" alle keywords. Keywords deve essere un array.
     * 
     * 
     */
    public static function add($path,$value)
    {
        $path_parts = self::path_tokens($path);
        
        $current_node = &self::$data;
        
        foreach ($path_parts as $p)
        {
            if (!isset($current_node[$p]))
                $current_node[$p] = array();
            $current_node = &$current_node[$p];
        }
        
        if (is_object($value) && in_array('LStaticReadHashMap',class_uses(get_class($value))))
        {
            $current_node[] = get_class($value)::get("/");
        }
        else {
            $current_node[] = $value;
        }
    }
    
    /*
     * Effettua il merge di un'array di valori all'interno di un'altro array.
     * La differenza rispetto ad add sta nel fondere i due array.
     * Da usare se non si vogliono aggiungere i valori ad un array.
     */
    public static function merge($path,$value)
    {
        $real_value = $value;

        if (!is_array($real_value)) throw new InvalidParameterException("Il parametro passato non e' un array!!");

        $path_parts = self::path_tokens($path);
        
        $current_node = &self::$data;
        
        foreach ($path_parts as $p)
        {
            if (!isset($current_node[$p]))
                $current_node[$p] = array();
            $current_node = &$current_node[$p];
        }
        
        $current_node = array_merge($current_node,$real_value);
    }
    
    /*
     * Rimuove le chiavi trovate nel path specificato.
     */
    public static function purge($path,$keys)
    {
        $path_parts = self::path_tokens($path);
        
        $current_node = &self::$data;
        
        foreach ($path_parts as $p)
        {
            if (!isset($current_node[$p]))
                $current_node[$p] = array();
            $current_node = &$current_node[$p];
        }
        
        $current_node = array_diff($current_node,$keys);
    }
    
    public static function remove($path)
    {
        if (!self::is_set($path)) return;
        else
        {
            $path_parts = self::all_but_last_path_tokens($path);
        
            $current_node = &self::$data;
            foreach ($path_parts as $p)
            {
                $current_node = &$current_node[$p];
            }
            unset($current_node[self::last_path_token($path)]);
        
        }
    }
    
    /**
     * Svuota completamente la struttura dati.
     */
    public static function clear()
    {
        self::$data = array();
    } 
}
