<?php

namespace Ikeraslt\Finvalda;


use Artisaninweb\SoapWrapper\SoapWrapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Ikeraslt\Finvalda\Exceptions\EmptyResponseException;
use Ikeraslt\Finvalda\Exceptions\NoBaseUrlException;
use Ikeraslt\Finvalda\Exceptions\NotFoundException;
use Ikeraslt\Finvalda\Exceptions\WrongAttributeException;
use Ikeraslt\Finvalda\Models\Client as FinvaldaClient;
use Ikeraslt\Finvalda\Models\Inflow;
use Ikeraslt\Finvalda\Models\Model;
use Ikeraslt\Finvalda\Models\Payment;
use Ikeraslt\Finvalda\Models\Sale;
use Ikeraslt\Finvalda\Models\SaleItem;
use Illuminate\Support\Collection;
use JsonMapper;
use Nathanmac\Utilities\Parser\Parser;

class Finvalda
{
    protected $baseUrl;
    protected $dataUrl;
    protected $user;
    protected $password;

    /**
     * Finvalda constructor.
     *
     * @param $url
     * @param $user
     * @param $password
     */
    public function __construct($url, $dataUrl, $user, $password)
    {
        $this->baseUrl = rtrim($url, '/') . '/';
        $this->dataUrl = rtrim($dataUrl, '/');
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return Collection|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getClients()
    {
        $response =  $this->get('GetKlientusSet');

        return $this->parseClientsResponse($response);
    }

    /**
     * @param string $kodas
     *
     * @return Collection|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getClient($kodas)
    {
        $response =  $this->get('GetKlientusSet', ['sKliKod' => $kodas]);

        return $this->parseClientsResponse($response);
    }

    /**
     * @param \Ikeraslt\Finvalda\Models\Client $client
     *
     * @return \Ikeraslt\Finvalda\Models\Client|\Psr\Http\Message\ResponseInterface
     */
    public function insertClient(FinvaldaClient $client)
    {
        if (! $client->kodas) {
            $client->kodas = $this->generateCode($client->pavadinimas);
        }

        $response = $this->insertItem($client);

        if ($response->InsertNewItemResult == 2) {
            return $client;
        } else {
            return $response;
        }
    }

    /**
     * @param \Ikeraslt\Finvalda\Models\Client $client
     *
     * @return \Ikeraslt\Finvalda\Models\Client|\Psr\Http\Message\ResponseInterface
     */
    public function updateClient(FinvaldaClient $client)
    {
        $response = $this->updateItem($client, $client->kodas);

        if ($response->EditItemResult == 2) {
            return $client;
        } else {
            return $response;
        }
    }

    /**
     * @param \Ikeraslt\Finvalda\Models\Model $item
     *
     * @return Collection|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function insertItem(Model $item)
    {
        $json = [
            'ItemClassName' => $item->getFinvaldaClass(),
            'xmlString' => $item->toString(),
        ];
        
        $response = $this->get('InsertNewItem', $json);

        return $response;
    }

    /**
     * @param \Ikeraslt\Finvalda\Models\Model $item
     *
     * @return Collection|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function updateItem(Model $item, $code)
    {
        $json = [
            'ItemClassName' => $item->getFinvaldaClass(),
            'sItemCode' => $code,
            'xmlString' => $item->toString(),
        ];

        $response = $this->get('EditItem', $json);

        return $response;
    }

    /**
     * @return mixed|null|Collection|Sale[]
     */
    public function getSales()
    {
        $response = $this->getSoap('GetSales');

        return $this->parseSalesResponse($response);
    }

    public function getSale($series, $opNumber)
    {
        $data = [
            'sSeries'   => $series,
            'nOpNumber' => $opNumber,
        ];

        $response = $this->getSoap('GetSales', $data);

        $sale = $this->parseSaleResponse($response);
        $sale->loadItems();
        return $sale;
    }

    public function getSaleItems($series, $opNumber)
    {
        $data = [
            'sSeries'   => $series,
            'nOpNumber' => $opNumber,
        ];

        $response = $this->getSoap('GetSalesDet', $data);

        return $this->parseSaleItemsResponse($response);
    }

    public function GetInflows()
    {
        $response = $this->getSoap('GetInflowsDet');

        $inflows = $this->parseInflowsResponse($response);

        return $inflows;
    }

    public function getPaymentForDoc($series, $doc)
    {
        $data = ['sSerija' => $series, 'sDokumentas' => $doc];

        $response =  $this->get('GetAtsiskaitymaiUzDokDataNuoDet', $data);

        return $this->parsePaymentsResponse($response);
    }

    /**
     * @param String $url
     * @param array|null $json
     *
     * @return Collection|mixed|\Psr\Http\Message\ResponseInterface
     * @throws NotFoundException
     * @throws NoBaseUrlException
     */
    public function get($url, $json = null)
    {
        $url = ltrim($url, '/');
        $headers = [
            'content-type' => 'application/json; charset=utf-8;',
            'username' => $this->user,
            'password' => $this->password,
            'removenewlines' => 'false',
            'removezeronumbertags' => 'false',
            'removeemptystringtags' => 'false',
            'cache-control' => 'no-cache',
        ];

        if (! $json) {
            unset($json);
        }

        $base_uri = $this->baseUrl;

        $client = new Client(['base_uri' => $base_uri]);

        try {
            $response = $client->post($url, compact('headers', 'json'));
        } catch (RequestException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException();
            }

            if ($e->getCode() == 0 && $base_uri == '/') {
                throw new NoBaseUrlException();
            }
            throw $e;
        }

        $response = $this->parseResponse($response);

        return $response;
    }

    public function getSoap($method, $data = [])
    {
        $soap = new SoapWrapper();

        $soap->add('FvsWsData', function ($service) {
            $service->wsdl($this->dataUrl)
                    ->header('http://www.fvs.lt/webservices', 'AuthHeader', [
                        'UserName' => $this->user,
                        'Password' => $this->password,
                        'RemoveEmptyStringTags' => false,
                        'RemoveZeroNumberTags' => false,
                        'RemoveNewLines' => false,
                    ]);
        });

        if ($data) {
            $data = ['parameters' => $data];
        }

        $response = $soap->call('FvsWsData.' . $method, $data);

        $response = $this->parseSoapResponse($response);

        return $response;
    }

    /**
     * @param array $response
     *
     * @return mixed|null|Collection|Sale[]
     */
    public function parseSalesResponse($response)
    {
        $result = arr_find('Sales', $response);

        if ($result) {
            $result = head($result);

            if ($result) {
                $result = json_decode(json_encode($result));
            }
        }

        return $result ? $this->map($result, Sale::class) : $response;
    }

    /**
     * @param $response
     *
     * @return mixed|null|Sale
     */
    public function parseSaleResponse($response)
    {
        $result = arr_find('Sales', $response);

        if ($result) {
            $result = json_decode(json_encode($result));
        }

        return $result ? $this->map($result, Sale::class) : $response;
    }

    public function parseSaleItemsResponse($response)
    {
        $result = arr_find('SalesDet', $response);

        if ($result) {
            $result = head($result);

            if ($result) {
                $result = json_decode(json_encode($result));
            }
        }

        if (! is_array($result)) {
            $result = [$result];
        }

        $result = $result ? $this->map($result, SaleItem::class) : $response;

        if ($result instanceof SaleItem) {
            $result = collect([$result]);
        }

        return $result;
    }

    /**
     * @param array $response
     *
     * @return mixed|null|Collection|Inflow[]
     */
    public function parseInflowsResponse($response)
    {
        $result = arr_find('InflowsDet', $response);

        if ($result) {
            $result = head($result);

            if ($result) {
                $result = json_decode(json_encode($result));
            }
        }

        return $result ? $this->map($result, Inflow::class) : $response;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Illuminate\Support\Collection|mixed
     * @throws EmptyResponseException
     * @throws WrongAttributeException
     */
    protected function parseClientsResponse($response)
    {
        if (isset($response->Data)) {
            $response = json_decode($response->Data);

            if (isset($response->Table1)) {
                $response = $response->Table1;

                $response = $this->map($response, FinvaldaClient::class);
            } else {
                throw new WrongAttributeException('Table1');
            }
        } else {
            throw new WrongAttributeException('Data');
        }

        return $response;
    }

    protected function parsePaymentsResponse($response)
    {
        if (isset($response->Data)) {
            $response = json_decode($response->Data);

            if (isset($response->Table1)) {
                $response = $response->Table1;
                $response = $this->map($response, Payment::class);
            } else {
                throw new WrongAttributeException('Table1');
            }
        } else {
            throw new WrongAttributeException('Data');
        }

        return $response;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     * @throws EmptyResponseException
     * @throws WrongAttributeException
     */
    protected function parseResponse($response)
    {
        if (! empty($response)) {
            $response = json_decode($response->getBody()->getContents());
        } else {
            throw new EmptyResponseException();
        }

        return $response;
    }

    /**
     * @param $response
     *
     * @return array
     * @throws EmptyResponseException
     * @throws WrongAttributeException
     */
    protected function parseSoapResponse($response)
    {
        if (! empty($response)) {
            if (! empty($response->Data) && ! empty($response->Data->any)) {
                $parser = new Parser;
                $response = $parser->xml($response->Data->any);
            } else {
                throw new WrongAttributeException('Data.any');
            }
        } else {
            throw new EmptyResponseException();
        }

        return $response;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function generateCode($name)
    {
        $i = 0;
        $code = strtoupper(trim(substr(str_replace(' ', '_', $name), 0, 15)));
        while ($this->getClient($code)) {
            $i++;

            if ($i > 1) {
                $code = substr($code, 0, 0 - strlen($i));
            }

            if (strlen($code . $i) > 15) {
                $code = substr($code, 0, 15 - strlen($i));
            }

            $code .= $i;
        }
        return $code;
    }

    /**
     * @param array $response
     * @param string $class
     *
     * @return mixed|null|object|Collection
     */
    protected function map($response, $class) {
        $mapper = new JsonMapper();
        $mapper->bStrictNullTypes = false;

        if (count($response) > 1) {
            return $mapper->mapArray($response, collect(), $class);
        } elseif (count($response) == 1) {
            return $mapper->map(head($response), new $class);
        } else {
            return null;
        }
    }

    /**
     * @return String
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getDataUrl(): string
    {
        return $this->dataUrl;
    }

    /**
     * @param string $dataUrl
     */
    public function setDataUrl(string $dataUrl)
    {
        $this->dataUrl = $dataUrl;
    }

    /**
     * @param String $baseUrl
     *
     * @return Finvalda
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return String
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param String $user
     *
     * @return Finvalda
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param String $password
     *
     * @return Finvalda
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
