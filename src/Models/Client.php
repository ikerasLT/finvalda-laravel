<?php

namespace Ikeraslt\Finvalda\Models;


class Client extends Model
{
    protected $finvalda_class = 'Fvs.Klientas';

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
    public $debt_sask;
    public $kred_sask;

    /**
     * @var \Carbon\Carbon
     */
    public $kurimo_data;

    /**
     * @var \Carbon\Carbon
     */
    public $koregavimo_data;

    public function toString()
    {
        $object = [
            'sFvsImportoParametras' => 'PARAM1',
            'sKodas' => $this->kodas,
            'sPavadinimas' => $this->pavadinimas,
            'sTelefonas' => $this->telefonas,
            'sFaksas' => $this->faksas,
            'sAtsakingasAsmuo' => $this->atskaitingas_asmuo,
            'sBankas' => $this->bankas,
            'sBankoSaskaita' => $this->banko_sask,
            'sKorespSaskBank' => $this->korsp_sask_bank,
            'sImKodas' => $this->im_kodas,
            'sPvmMoketojoKod' => $this->pvm_moketojo_kodas,
            'sPastabos' => $this->pastabos,
            'sRusis' => $this->rusis,
            'sEMail' => $this->el_pastas,
            'sPasiulomaValiuta' => $this->pasiuloma_valiuta,
            'sYraKlientoPadalinys' => $this->yra_padalinys,
            'sPozymis1' => $this->pozymis_1,
            'sPozymis2' => $this->pozymis_2,
            'sPozymis3' => $this->pozymis_3,
            'dPapildomaInf' => $this->papildoma_inf,
            'sDebtSask' => $this->debt_sask,
            'sKredSask' => $this->kred_sask,
        ];

        foreach ($object as $key => $value) {
            if (is_null($value)) {
                unset($object[$key]);
            }
        }

        return json_encode([$this->getFinvaldaClass() => $object]);
    }
}