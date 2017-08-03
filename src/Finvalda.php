<?php

namespace Ikeraslt\Finvalda;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Ikeraslt\Finvalda\Exceptions\EmptyResponseException;
use Ikeraslt\Finvalda\Exceptions\NoBaseUrlException;
use Ikeraslt\Finvalda\Exceptions\NotFoundException;
use Ikeraslt\Finvalda\Exceptions\WrongAttributeException;
use Ikeraslt\Finvalda\Models\Client as FinvaldaClient;
use Ikeraslt\Finvalda\Models\Model;
use Illuminate\Support\Collection;
use JsonMapper;

class Finvalda
{
    protected $baserUrl;
    protected $user;
    protected $password;

    /**
     * Finvalda constructor.
     *
     * @param $url
     * @param $user
     * @param $password
     */
    public function __construct($url, $user, $password)
    {
        $this->baserUrl = rtrim($url, '/') . '/';
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

        $client = new Client(['base_uri' => $this->baserUrl]);

        try {
            $response = $client->post($url, compact('headers', 'json'));
        } catch (RequestException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException();
            }

            if ($e->getCode() == 0 && $this->baserUrl == '/') {
                throw new NoBaseUrlException();
            }
            throw $e;
        }

        $response = $this->parseResponse($response);

        return $response;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param String $class
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

                $mapper = new JsonMapper();

                if (count($response) > 1) {
                    $response = $mapper->mapArray($response, collect(), FinvaldaClient::class);
                } elseif (count($response) == 1) {
                    $response = $mapper->map($response[0], new FinvaldaClient);
                } else {
                    return null;
                }
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
     * @return \Illuminate\Support\Collection|mixed
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
     * @return String
     */
    public function getBaserUrl()
    {
        return $this->baserUrl;
    }

    /**
     * @param String $baserUrl
     *
     * @return Finvalda
     */
    public function setBaserUrl($baserUrl)
    {
        $this->baserUrl = $baserUrl;

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
