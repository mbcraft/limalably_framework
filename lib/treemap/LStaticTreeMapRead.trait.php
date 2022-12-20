<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

trait LStaticTreeMapRead {
    
    public static function getRootTreeMap() {
        self::setupIfNeeded();
        
        return self::$tree_map;
    }
    
    static function mustGetOriginal($path) {
        self::setupIfNeeded();
        
        return self::$my_view->mustGetOriginal($path);
    }
    
    static function getOriginal($path,$default_value = null) {
        self::setupIfNeeded();
        
        return self::$my_view->getOriginal($path,$default_value);
    }
    
    public static function mustGetBoolean($path) {
        self::setupIfNeeded();
        
        return self::$my_view->mustGetBoolean($path);
    }
    
    /**
     * Ritorna un valore booleano o il valore di default nel caso in cui
     * @param type $path
     * @param type $default_value
     * @return boolean
     */
    public static function getBoolean($path,$default_value = null) {
        self::setupIfNeeded();
        
        return self::$my_view->getBoolean($path,$default_value);
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
        self::setupIfNeeded();
        
        return self::$my_view->get($path,$default_value);
    }
    
    /**
     * Ritorna il valore contenuto nella posizione specificata e se non trova nulla lancia un'eccezione.
     * 
     * @param string $path Il percorso di ricerca
     * @return mixed
     * @throws \Exception Se il percorso specificato non contiene niente
     */
    public static function mustGet($path) {
        self::setupIfNeeded();
        
        return self::$my_view->mustGet($path);
    }
    

    
    /*
     * Ritorna true se un nodo dell'albero è stato definito, false altrimenti.
     */
    public static function is_set($path)
    {
        self::setupIfNeeded();
        
        return self::$my_view->is_set($path);
    }

    /*
     * Ritorna tutte le chiavi trovate nella posizione specificata.
     *
     * -- DA TESTARE --
     */
    public static function keys($path)
    {
        self::setupIfNeeded();
        
        return self::$my_view->keys($path);

    }
    
    public static function getCurrentView() {
        self::setupIfNeeded();
        
        return self::$my_view;
    }
    
    /*
     * Crea una vista sul percorso specificato.
     * 
     */
    public static function setCurrentView($path)
    {
        self::setupIfNeeded();
        
        self::$my_view = self::$tree_map->view($path);
    }
    
}
