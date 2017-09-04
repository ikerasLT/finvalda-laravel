<?php

namespace Ikeraslt\Finvalda\Models;


class SaleItem extends Model
{
    protected $finvalda_class = 'PardDokPaslaugaDetEil';

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

    public function toInsertArray()
    {
        $object = [
            'sKodas' => $this->code,
            'dSumaV' => $this->amount_currency,
            'dSumaL' => $this->amount_litas,
            'dSumaPVMV' => $this->vat_currency,
            'dSumaPVML' => $this->vat_litas,
            'dSumaNV' => $this->discount_curency,
            'dSumaNL' => $this->discount_litas,
            'nKiekis' => $this->quantity,
            'sObjektas1' => $this->object1,
            'sObjektas2' => $this->object2,
            'sObjektas3' => $this->object3,
            'sObjektas4' => $this->object4,
        ];

        foreach ($object as $key => $value) {
            if (is_null($value)) {
                unset($object[$key]);
            }
        }

        return $object;
    }
}
