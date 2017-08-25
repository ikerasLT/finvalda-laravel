<?php

namespace Ikeraslt\Finvalda\Models;


class Inflow extends Model
{
//    protected $finvalda_class = 'Fvs.Klientas';

    /**
     * @var \Carbon\Carbon
     */
    public $inflow_date_created;
    /**
     * @var \Carbon\Carbon
     */
    public $inflow_date_edited;
    public $inflow_number;
    public $inflow_journal;
    /**
     * @var \Carbon\Carbon
     */
    public $inflow_date;
    /**
     * @var \Carbon\Carbon
     */
    public $inflow_issue_date;
    public $client;
    public $client_title;
    public $inflow_title;
    public $inflow_series;
    public $inflow_order_number;
    public $inflow_currency;
    public $doc_currency;
    public $doc_type;
    public $doc_number;
    public $doc_journal;
    public $doc_series;
    public $doc_order_number;
    public $amount_litas;
    public $discount_litas;
    public $amount_inflow_curency;
    public $discount_inflow_curency;
    public $amount_doc_curency;
    public $discount_doc_curency;
    public $object1;
    public $object2;
    public $object3;
    public $object4;
    public $object5;
    public $Object6;

}