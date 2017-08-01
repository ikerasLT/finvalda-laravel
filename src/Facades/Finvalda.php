<?php

namespace Ikeraslt\Finvalda\Facades;


use Illuminate\Support\Facades\Facade;

class Finvalda extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'finvalda';
    }
}