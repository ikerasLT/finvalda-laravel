<?php

namespace Ikeraslt\Finvalda\Models;


class Inflow extends Model
{
    const TYPE_ADVANCE = 0;
    const TYPE_FIFO = 1;
    const TYPE_SALE = 3;

    protected $finvalda_class = 'IplDok';
    protected $finvalda_param = 'PARAM3';

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
    public $type;

    public function toString()
    {
        $object = [
            'sValiuta' => $this->inflow_currency,
            'sKlientas' => $this->client,
            'nTipas' => $this->type,
            'sSerija' => $this->inflow_series,
            'sDokumentas' => $this->inflow_order_number,
            'tData' => $this->inflow_date->toDateString(),
            'tIsrasymoData' => $this->inflow_issue_date,
            'sPavadinimas' => $this->inflow_title,
            'IplDokDetEil' => [
                'dSumaV' => $this->amount_inflow_curency,
                'sSerija' => $this->doc_series,
                'sDokumentas' => $this->doc_order_number,
                'sObjektas1' => $this->object1,
                'sObjektas2' => $this->object2,
                'sObjektas3' => $this->object3,
                'sObjektas4' => $this->object4,
            ]
        ];

        $object = $this->dropNullValues($object);

        return json_encode([$this->getFinvaldaClass() => $object]);
    }
}