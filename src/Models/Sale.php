<?php

namespace Ikeraslt\Finvalda\Models;


use Ikeraslt\Finvalda\Facades\Finvalda;

class Sale extends Model
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
    /**
     * @var \Carbon\Carbon
     */
    public $op_date;
    /**
     * @var \Carbon\Carbon
     */
    public $payment_date;
    /**
     * @var \Carbon\Carbon
     */
    public $issue_date;
    public $op_series;
    public $order_number;
    public $client;
    public $client_title;
    public $client_adress;
    public $op_adress;
    public $op_type;
    public $op_currency;
    public $op_contract;
    public $op_amount_currency;
    public $user_name;
    public $object1;
    public $object2;
    public $object3;
    public $object4;
    public $items;

    /**
     * @return \Ikeraslt\Finvalda\Models\SaleItem[]
     */
    public function setItems()
    {
        return $this->items = Finvalda::getSaleItems($this->op_series, $this->op_number);
    }

    public function loadItems()
    {
        $this->setItems();
    }
}