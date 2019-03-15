<?php

class LStringUtils {
    
    static function underscoredToCamelCase($string)
    {
            $string[0] = strtoupper($string[0]);

            $func = create_function('$c', 'return strtoupper($c[1]);');
            return preg_replace_callback('/_([a-z])/', $func, $string);
    }
    /*
     * Questa funzione splitta i nomi camelcase mettendo gli underscore secondo la seguente regola :
     * 
     * FPDF -> fpdf
     * ContenutiTestualiController -> contenuti_testuali_controller
     * */
    static function camelCaseSplit($string,$skip_last=false,$join_part="_")
    {
        $matches = array();
        preg_match_all("/([A-Z]+[A-Z](?![a-z]))|([A-Z]+[a-z]*)/",$string,$matches); //black magic, do not touch ...
        $real_matches = $matches[0];

        $lower_matches = array();
        foreach ($real_matches as $mtc)
            $lower_matches[] = strtolower($mtc);

        if ($skip_last)
            array_pop($lower_matches);

        return join($join_part,$lower_matches);
    }

    static function trimEndingChars($string,$num)
    {
        if ($num>strlen($string)) throw new \LInvalidParameterException("Numero di caratteri piu' lungo della stringa!!");
        return substr($string,0,-$num);
    }
    
    static function startsWith($string,$needle) {
        return strpos($string,$needle)===0;
    }
    
    static function endsWith($string,$needle) {
        return strpos($string,$needle,strlen($string)-strlen($needle))===(strlen($string)-strlen($needle));
    }
    
    static function contains($string,$needle) {
        return strpos($string,$needle)!==false;
    }
    
    static function getExceptionMessage(\Exception $ex,bool $print_stack_trace = true) {
        $message = 'Exception : '.$ex->getMessage()."\n";
        $message .= 'File : '.$ex->getFile().' Line : '.$ex->getLine()."\n";
        if ($print_stack_trace) {
            $message .= 'Stack Trace : '.$ex->getTraceAsString();
            if ($ex->getPrevious()) $message .= self::getExceptionMessage($ex->getPrevious ());
        }
        return $message;
    }
        
}
