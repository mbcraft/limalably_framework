<?php


if (!class_exists('DFileWriter')) {
    class DFileWriter extends DFileReader
    {
        const CR = "\r";
        const LF = "\n";

        static function newTmpFile()
        {
            return new DFileWriter(tmpfile());
        }

        /*
         * Uso eval per simulare printf
         * */
        function printf($format)
        {
            $this->checkClosed();

            $args = func_get_args();
            $printf_args = array_slice($args,1);

            $p = 'fprintf($this->my_handle,$format';
            $i = 0;
            foreach ($printf_args as $arg)
            {

                $p.=',$printf_args['.$i.']';
                $i++;
            }
            $p.=");";
            eval($p);
        }
        
        function write($string)
        {
            $this->checkClosed();

            fwrite($this->my_handle, $string);
        }

        function writeln($string)
        {
            $this->checkClosed();

            fwrite($this->my_handle,$string.self::CR.self::LF);
        }
        
        function writeCSV($values,$delimiter=",")
        {
            $this->checkClosed();

            fputcsv($this->my_handle, $values,$delimiter);
        }

        function truncate($size)
        {
            $this->checkClosed();

            ftruncate($this->my_handle, $size);
        }
        
    }
}