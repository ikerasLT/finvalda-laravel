<?php

namespace Ikeraslt\Finvalda\Models;


class SaleItem extends Model
{
    /**
     * @var \Carbon\Carbon
     */
    public $date_created;
    /**
     * @var \Carbon\Carbon
     */
    public $date_edited;
    public $op_number;
    public $journal;
    public $type;
    public $code;
    public $title;
    public $quantity;
    public $code_n;
    public $storage_code;
    public $amount_litas;
    public $amount_currency;
    public $vat_litas;
    public $vat_currency;
    public $discount_litas;
    public $discount_curency;
    public $neto;
    public $bruto;
    public $volume;
    public $object1;
    public $object2;
    public $object3;
    public $object4;
}
