<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

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
        if (is_array($needle)) {
            $result = false;
            foreach ($needle as $n) {
                $result |= strpos($string,$n)===0;
            }
            return $result;
        } else {
            return strpos($string,$needle)===0;
        }
    }
    
    static function endsWith($string,$needle) {
        if (is_array($needle)) {
            $result = false;
            foreach ($needle as $n) {
                $result |= strpos($string,$n,strlen($string)-strlen($n))===(strlen($string)-strlen($n));
            }
            return $result;
        }
        else {
            return strpos($string,$needle,strlen($string)-strlen($needle))===(strlen($string)-strlen($needle));
        }
    }
    
    static function contains($string,$needle) {
        if (is_array($needle)) {
            $result = false;
            foreach ($needle as $n) {
                $result |= strpos($string,$n)!==false;
            }
            return $result;
        }
        else {
            return strpos($string,$needle)!==false;
        }
    }
    
    static function getErrorMessage(string $error,string $file,int $line,bool $use_newline=true) {
        $NL = $use_newline ? "\n" : '<br>';
        $message = 'Error : '.$error.$NL;
        $message .= 'File : '.$file.' Line : '.$line.$NL;
        return $message;
    }
    
    static function getExceptionMessage(\Exception $ex,bool $print_stack_trace=true,bool $use_newline=true) {
        $exceptions = [$ex];
        if ($print_stack_trace) {
            while ($ex->getPrevious()!=null) {
                $ex = $ex->getPrevious();
                array_unshift ($exceptions, $ex);
            }
        }
        $message = '';
        foreach ($exceptions as $ex) {
            $message .= self::internalGetExceptionMessage($ex, $print_stack_trace, $use_newline);
        }
        return $message;
    }
    
    private static function internalGetExceptionMessage(\Exception $ex,bool $print_stack_trace,bool $use_newline) {
        $NL = $use_newline ? "\n" : '<br>';
        $message = $ex->getMessage().$NL;
        $message .= 'File : '.$ex->getFile().' Line : '.$ex->getLine().$NL;
        if ($print_stack_trace) {
            $message .= 'Stack Trace : '.$ex->getTraceAsString().$NL;
        }
        return $message;
    }
    
    
    public static function getNewlineString() {
        if ($_SERVER['ENVIRONMENT'] == 'script')
            return "\n";
        else
            return '<br>';
    }    
}
