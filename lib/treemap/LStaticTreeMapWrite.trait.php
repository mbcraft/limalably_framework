<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*
Some help from :
 * https://stackoverflow.com/questions/36515677/php-instanceof-for-traits
 * https://stackoverflow.com/users/5732351/rod-elias
 *  */

trait LStaticTreeMapWrite {
    
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
        self::setupIfNeeded();
        
        self::$tree_map->set($path,$value);
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
        self::setupIfNeeded();
        
        self::$tree_map->add($path,$value);
    }
    
    /*
     * Effettua il merge di un'array di valori all'interno di un'altro array.
     * La differenza rispetto ad add sta nel fondere i due array.
     * Da usare se non si vogliono aggiungere i valori ad un array.
     */
    public static function merge($path,$value)
    {
        self::setupIfNeeded();
        
        self::$tree_map->merge($path,$value);
        
    }
    
    /*
     * Rimuove le chiavi trovate nel path specificato.
     */
    public static function purge($path,$keys)
    {
        self::setupIfNeeded();
        
        self::$tree_map->purge($path,$keys);
    }
    
    public static function remove($path)
    {
        self::setupIfNeeded();
        
        self::$tree_map->remove($path);
    }
    
    /**
     * Svuota completamente la struttura dati.
     */
    public static function clear()
    {
        self::setupIfNeeded();
        
        self::$tree_map->clear();
    } 
}
