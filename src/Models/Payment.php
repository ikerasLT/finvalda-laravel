<?php

namespace Ikeraslt\Finvalda\Models;


class Payment extends Model
{
//    protected $finvalda_class = 'Fvs.Klientas';

    public $operacija;
    public $zurnalas;
    public $numeris;
    /**
     * @var \Carbon\Carbon
     */
    public $Data;
    /**
     * @var \Carbon\Carbon
     */
    public $DataIsr;
    public $serija;
    public $dokumentas;
    public $op_valiuta;
    public $dok_valiuta;
    public $ats_op;
    public $ats_sum;
    public $ats_sist;
    public $operacija2;
    public $zurnalas2;
    public $numeris2;
    public $operacijosid;
    public $im_kodas;
    public $kli_kodas;
    public $perkainuota;
}