<?php

namespace Ikeraslt\Finvalda;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Ikeraslt\Finvalda\Exceptions\EmptyResponseException;
use Ikeraslt\Finvalda\Exceptions\NoBaseUrlException;
use Ikeraslt\Finvalda\Exceptions\NotFoundException;
use Ikeraslt\Finvalda\Exceptions\WrongAttributeException;
use Ikeraslt\Finvalda\Models\Client as FinvaldaClient;
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
        return $this->get('GetKlientusSet', FinvaldaClient::class);
    }

    /**
     * @param String $url
     * @param String|null $class
     *
     * @return Collection|mixed|\Psr\Http\Message\ResponseInterface
     * @throws NotFoundException
     */
    public function get($url, $class = null)
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

        $client = new Client(['base_uri' => $this->baserUrl]);

        try {
            $response = $client->post($url, compact('headers'));
        } catch (RequestException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException();
            }

            if ($e->getCode() == 0 && $this->baserUrl == '/') {
                throw new NoBaseUrlException();
            }
            throw $e;
        }

        $response = $this->parseResponse($response, $class);

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
    protected function parseResponse($response, $class)
    {
        if (! empty($response)) {
            $response = json_decode($response->getBody()->getContents());

            if (isset($response->Data)) {
                $response = json_decode($response->Data);

                if (isset($response->Table1)) {
                    $response = $response->Table1;

                    if ($class) {
                        $mapper = new JsonMapper();

                        if (is_array($response)) {
                            $response = $mapper->mapArray($response, collect(), $class);
                        } else {
                            $response = $mapper->map($response, new $class);
                        }
                    }
                } else {
                    throw new WrongAttributeException('Table1');
                }
            } else {
                throw new WrongAttributeException('Data');
            }
        } else {
            throw new EmptyResponseException();
        }

        return $response;
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