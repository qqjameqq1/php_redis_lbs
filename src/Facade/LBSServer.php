<?php
namespace LBS\Facade;

class LBSServer extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LBSServer';
    }
}
