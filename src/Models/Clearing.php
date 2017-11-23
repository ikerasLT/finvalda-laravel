<?php

namespace Ikeraslt\Finvalda\Models;


class Clearing extends Model
{
    /**
     * @var \Carbon\Carbon
     */
    public $clearing_date_created;
    /**
     * @var \Carbon\Carbon
     */
    public $clearing_date_edited;
    public $clearing_number;
    public $clearing_journal;
    /**
     * @var \Carbon\Carbon
     */
    public $clearing_date;
    public $op_type;
    public $client;
    public $doc_type;
    public $doc_number;
    public $doc_journal;
    public $amount_clearing_litas;
    public $amount_clearing_curency;
    public $object1;
    public $object2;
    public $object3;
    public $object4;
}