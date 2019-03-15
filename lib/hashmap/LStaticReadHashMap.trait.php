<?php

trait LStaticReadHashMap {
    
    static function mustGetOriginal($path) {
        if (!self::is_set($path))
            throw new \Exception('Value not found in path : '.$path);
        
        return self::getOriginal($path);
    }
    
    static function getOriginal($path,$default_value = null) {
        if (!self::is_set($path))
            return $default_value;
        
        $path_parts = self::path_tokens($path);
        
        $current_node = self::$data;
        foreach ($path_parts as $p)
        {
            $current_node = $current_node[$p];
        }
        
        return $current_node;
    }
    
    public static function mustGetBoolean($path) {
        if (!self::is_set($path))
            throw new \Exception('Value not found in path : '.$path);
        
        return self::getBoolean($path);
    }
    
    /**
     * Ritorna un valore booleano o il valore di default nel caso in cui
     * @param type $path
     * @param type $default_value
     * @return boolean
     */
    public static function getBoolean($path,$default_value = null) {
        if (!self::is_set($path)) return $default_value;
        
        $value = self::get($path,$default_value);
        
        $false_values = LConfig::mustGet('/defaults/hashmaps/false_strings');
        if (in_array($value, $false_values)) return false;
        else return true;
    }
    
    /*
     * Ritorna il contenuto nella posizione specificata.
     * 
     * Es: 
     * path : /html/head/keywords
     * -> ritorna l'array delle keywords
     * 
     * path : /html/head/description
     * -> ritorna la descrizione
     */
    
    public static function get($path,$default_value=null)
    {
        if (!self::is_set($path))
            return $default_value;
        
        $path_parts = self::path_tokens($path);
        
        $current_node = self::$data;
        foreach ($path_parts as $p)
        {
            $current_node = $current_node[$p];
        }
        
        return filter_var($current_node);
    }
    
    /**
     * Ritorna il valore contenuto nella posizione specificata e se non trova nulla lancia un'eccezione.
     * 
     * @param string $path Il percorso di ricerca
     * @return mixed
     * @throws \Exception Se il percorso specificato non contiene niente
     */
    public static function mustGet($path) {
        if (!self::is_set($path))
            throw new \Exception('Value not found in path : '.$path);
        
        return self::get($path);
    }
    

    
    /*
     * Ritorna true se un nodo dell'albero è stato definito, false altrimenti.
     */
    public static function is_set($path)
    {
        $path_parts = self::path_tokens($path);
        
        $current_node = self::$data;
        foreach ($path_parts as $p)
        {
            if (!isset($current_node[$p]))
                return false;

            $current_node = $current_node[$p];
        }
        
        return true;
    }

    /*
     * Ritorna tutte le chiavi trovate nella posizione specificata.
     *
     * -- DA TESTARE --
     */
    public static function keys($path)
    {
        if (!self::is_set($path))
            return null;

        $path_parts = self::path_tokens($path);

        $current_node = self::$data;
        foreach ($path_parts as $p)
        {
            $current_node = $current_node[$p];
        }

        return array_keys($current_node);

    }
    
}
