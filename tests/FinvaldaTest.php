<?php

namespace Tests;

use Ikeraslt\Finvalda\Exceptions\NotFoundException;
use Ikeraslt\Finvalda\Finvalda;
use Ikeraslt\Finvalda\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

/**
 * @property Finvalda $finvalda
 */
class FinvaldaTest extends TestCase
{
    public $finvalda;

    public function setUp()
    {
        parent::setUp();

        $this->finvalda = new Finvalda(env('FINVALDA_URL', ''), env('FINVALDA_DATA_URL'), env('FINVALDA_USER'), env('FINVALDA_PASSWORD'), env('FINVALDA_COMPANY'));
    }

    public function testInit()
    {
        $url = rtrim(env('FINVALDA_URL'), '/') . '/';
        $this->assertSame($url, $this->finvalda->getBaseUrl());
        $this->assertSame(env('FINVALDA_USER'), $this->finvalda->getUser());
        $this->assertSame(env('FINVALDA_PASSWORD'), $this->finvalda->getPassword());
    }

    public function testGet()
    {
        $response = $this->finvalda->get('GetKlientusSet');

        $this->assertTrue(is_array($response));
    }

    public function testGetWithMapping()
    {
        $response = $this->finvalda->get('GetKlientusSet', Client::class);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertInstanceOf(Client::class, $response->first());
    }

    public function testNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->finvalda->get('notExistingMethod');
    }

    public function testTestMode()
    {
        $finvalda = new Finvalda(env('FINVALDA_URL', ''), env('FINVALDA_DATA_URL'), env('FINVALDA_USER'), env('FINVALDA_PASSWORD'), '');
        $testFinvalda = new Finvalda(env('FINVALDA_URL', ''), env('FINVALDA_DATA_URL'), env('FINVALDA_USER'), env('FINVALDA_PASSWORD'), 'TEST');

        $this->assertNotEquals($finvalda->getClients()->count(), $testFinvalda->getClients()->count());
    }
}
