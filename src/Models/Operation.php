<?php

namespace Ikeraslt\Finvalda\Models;


interface Operation
{
    function getFinvaldaClass($delete = false);
    function getFinvaldaParam();
    function getJournal();
    function getNumber();
}