<?php

@include('../vendor/smsapi-client/vendor/autoload.php');
use \SMSApi\Proxy\Http\Native;

abstract class AbstractsSmsapi
{
    private $username;
    private $password;
    private $url;

    /**
     * @param $username
     * @param $password
     * @param $url
     */

    protected function __construct($username, $password, $url)
    {
        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
    }

    /**
     * @return \SMSApi\Client
     * @throws \SMSApi\Exception\ClientException
     */

    public function getSmsapiClient()
    {

        $client = new \SMSApi\Client($this->username);
        $client->setPasswordHash($this->password);

        return $client;
    }

    /**
     * @return Native
     */

    public function getSmsapiProxy()
    {
        return new Native($this->url);
    }

}