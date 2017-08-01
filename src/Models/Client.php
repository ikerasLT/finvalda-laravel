<?php

namespace Ikeraslt\Finvalda\Models;


class Client
{
    public $kodas;
    public $pavadinimas;
    public $skola;
    public $ardesas;
    public $telefonas;
    public $faksas;
    public $atskaitingas_asmuo;
    public $atsiskaityti_per;
    public $mokestis;
    public $bankas;
    public $banko_sask;
    public $korsp_sask_bank;
    public $im_kodas;
    public $pvm_moketojo_kodas;
    public $pastabos;
    public $rusis;
    public $el_pastas;
    public $pasiuloma_valiuta;
    public $yra_padalinys;
    public $papildoma_inf;
    public $pozymis_1;
    public $pozymis_2;
    public $pozymis_3;

    /**
     * @var \Carbon\Carbon
     */
    public $kurimo_data;

    /**
     * @var \Carbon\Carbon
     */
    public $koregavimo_data;
}