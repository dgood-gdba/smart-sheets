<?php

namespace GDBA\SmartSheets\Facades;

use Illuminate\Support\Facades\Facade;

class SmartSheets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'smart-sheets';
    }
}
