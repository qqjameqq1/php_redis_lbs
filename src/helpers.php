<?php namespace LBS;

class Helper
{
    public static function dd($var=null)
    {
        if (is_null($var)) {
            die();
        }
        $param = func_get_args();

        foreach ($param as $item) {
            dump($item);
            echo PHP_EOL;
        }
        die();
    }
}
