<?php

namespace Ikeraslt\Finvalda\Models;


use Ikeraslt\Finvalda\Facades\Finvalda;

class Sale extends Model
{
    protected $finvalda_class = 'PardDok';
    protected $finvalda_param = 'PARAM1';

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
     * @param \Ikeraslt\Finvalda\Models\SaleItem[] $items
     *
     * @return \Ikeraslt\Finvalda\Models\SaleItem[]
     */
    public function setItems($items)
    {
        return $this->items = $items;
    }

    /**
     * @param \Ikeraslt\Finvalda\Models\SaleItem $item
     *
     * @return \Ikeraslt\Finvalda\Models\SaleItem[]
     */
    public function addItem($item)
    {
        $this->items[] = $item;

        return $this->items;
    }

    public function loadItems()
    {
        $this->setItems(Finvalda::getSaleItems($this->op_series, $this->op_number));
    }

    public function toString()
    {
        $object = [
            'sKlientas' => $this->client,
            'sSerija' => $this->op_series,
            'sDokumentas' => $this->order_number,
            'sValiuta' => $this->op_currency,
            'sSutartis' => $this->op_contract,
            'tData' => $this->op_date->toDateString(),
            'tMokejimoData' => $this->payment_date->toDateString(),
            'sObjektas1' => $this->object1,
            'sObjektas2' => $this->object2,
            'sObjektas3' => $this->object3,
            'sObjektas4' => $this->object4,
            'tIsrasymoData' => $this->issue_date->toDateString(),
        ];

        if ($this->items) {
            foreach ($this->items as $item) {
                $object[$item->getFinvaldaClass()][] = $item->toInsertArray();
            }
        }

        foreach ($object as $key => $value) {
            if (is_null($value)) {
                unset($object[$key]);
            }
        }

        return json_encode([$this->getFinvaldaClass() => $object]);
    }
}