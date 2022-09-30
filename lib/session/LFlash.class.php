<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LFlash
{
    const FLASH_FORM_PARAMS_KEY = "form_params";

    const FLASH_OK_MESSAGES_KEY = "ok_messages";
    const FLASH_WARNING_MESSAGES_KEY = "warning_messages";
    const FLASH_ERROR_MESSAGES_KEY = "error_messages";

    const SESSION_FLASH_KEY = "__flash_vars";
    
    private static $keep_messages = false;

    public static $current = null;
    public static $next = null;

    private $my_vars=array();

    public static function clean()
    {
        self::$current = new LFlash();
        self::$next = new LFlash();
    }
    /*
     * Carico le flash vars dalla sessione. Quelle che erano le 'next' diventano così le 'current'.
     */
    public static function __load_from_session()
    {
        $f = new LFlash();
        if (LSession::is_set(self::SESSION_FLASH_KEY))
            $f->my_vars = LSession::get(self::SESSION_FLASH_KEY);

        self::$current = $f;

        self::$next = new LFlash();
    }

  /*
     * Salvo le flash vars 'next' in sessione, che diventeranno le 'current' alla prossima richiesta.
     */
    public static function __save_to_session()
    {
        if (self::$keep_messages)
            $vars = self::$current->my_vars;
        else
            $vars = self::$next->my_vars;

        LSession::set(self::SESSION_FLASH_KEY, $vars);
    }

    private static function push_key_value($section,$key,$value)
    {
        if (!isset(self::$current->my_vars[$section])) self::$current->my_vars[$section] = array();
        self::$current->my_vars[$section][$key] = $value;

        if (!isset(self::$next->my_vars[$section])) self::$next->my_vars[$section] = array();
        self::$next->my_vars[$section][$key] = $value;
    }

    private static function push_elem($section,$elem)
    {        
        if (!isset(self::$current->my_vars[$section])) self::$current->my_vars[$section] = array();
        self::$current->my_vars[$section][] = $elem;

        if (!isset(self::$next->my_vars[$section])) self::$next->my_vars[$section] = array();
        self::$next->my_vars[$section][] = $elem;
    }
    
    /*
     * Funzioni di scrittura
     */
    
    public static function ok($message)
    {
        self::push_elem(self::FLASH_OK_MESSAGES_KEY, $message);
    }
    
    public static function warning($message)
    {
        self::push_elem(self::FLASH_WARNING_MESSAGES_KEY, $message);
    }

    public static function error($message)
    {
        self::push_elem(self::FLASH_ERROR_MESSAGES_KEY, $message);
    }
    
    /*
     * Funzioni di controllo
     */
    
    public static function hasOks()
    {
        if (isset(self::$current->my_vars[self::FLASH_OK_MESSAGES_KEY]))
        {
            return count(self::$current->my_vars[self::FLASH_OK_MESSAGES_KEY])>0;
        }
        else
        {
            return false;
        } 
    }

    public static function hasWarnings()
    {
        if (isset(self::$current->my_vars[self::FLASH_WARNING_MESSAGES_KEY]))
        {
            return count(self::$current->my_vars[self::FLASH_WARNING_MESSAGES_KEY])>0;
        }
        else
        {
            return false;
        } 
    }

    public static function hasErrors()
    {
        if (isset(self::$current->my_vars[self::FLASH_ERROR_MESSAGES_KEY]))
        {
            return count(self::$current->my_vars[self::FLASH_ERROR_MESSAGES_KEY])>0;
        }
        else
        {
            return false;
        } 
    }

    public static function getOkMessages()
    {
        if (isset(self::$current->my_vars[self::FLASH_OK_MESSAGES_KEY]))
            return self::$current->my_vars[self::FLASH_OK_MESSAGES_KEY];
        else
            return array();
    }
    
    public static function getWarningMessages()
    {
        if (isset(self::$current->my_vars[self::FLASH_WARNING_MESSAGES_KEY]))
            return self::$current->my_vars[self::FLASH_WARNING_MESSAGES_KEY];
        else
            return array();
    }
    
    public static function getErrorMessages()
    {
        if (isset(self::$current->my_vars[self::FLASH_ERROR_MESSAGES_KEY]))
            return self::$current->my_vars[self::FLASH_ERROR_MESSAGES_KEY];
        else
            return array();
    }
    
    public static function keep()
    {
        self::$keep_messages = true;
    }

    public static function getLastFormParams()
    {
        if (isset(self::$current->my_vars[self::FLASH_FORM_PARAMS_KEY]))
            return self::$current->my_vars[self::FLASH_FORM_PARAMS_KEY];
        else
            return array();
    }

}

?>