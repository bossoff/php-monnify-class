<?php

namespace Djunehor\Monnify;

/**Class to interact with monnify API
 *
 * @param array $requirements
 */
class Monnify
{

    private $baseUrl = 'https://sandbox.monnify.com/api/v1';
    private $username = '';
    private $password = '';
    private $contractCode = '';
    private $accessToken = null;
    private $tokenExpiry = 0;

    /**Initialise API credentials
     * Monnify constructor.
     * @param $username
     * @param $password
     * @param $contractCode
     */
    public function __construct($username, $password, $contractCode)
    {
        $this->username = $username;
        $this->password = $password;
        $this->contractCode = $contractCode;
    }


    /**Return base64 encoded string of username and password
     * @return string
     */
    private function encodedString()
    {
        return base64_encode("$this->username:$this->password");
    }

    /**Attempt to login using default credentials
     * @return $this|bool
     */
    public function authenticate()
    {
        $header = [
            "Authorization: Basic " . $this->encodedString()
        ];

        $decodedResponse = $this->request('auth/login', 'POST', $header);

        if ($decodedResponse && $decodedResponse->responseBody && $decodedResponse->responseBody->accessToken) {
            $this->accessToken = $decodedResponse->responseBody->accessToken;
            $this->tokenExpiry = time() + $decodedResponse->responseBody->expiresIn;

            return $this;
        }

        return false;

    }

    /**Check if token is still valid, re-authenticate if token expired
     * and return valid token
     * @return string
     */
    public function getRefreshToken()
    {
        if ($this->isTokenValid()) {
            $token = $this->accessToken;
        } else {
            $token = $this->authenticate()->getAccessToken();
        }

        return $token;
    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return null|string
     */
    public function getTokenExpiry()
    {
        return $this->tokenExpiry;
    }

    /**check if the current token
     * hasn't expired
     * @return bool
     */
    public function isTokenValid()
    {
        return ($this->accessToken && time() < $this->tokenExpiry);
    }

    /**I'm using this so as not to rely on third package request package
     * like guzzle
     * @param string $endpoint
     * @param string $method
     * @param array $headers
     * @param array $body
     * @return mixed
     */
    private function request(string $endpoint, $method = 'POST', array $headers = [], array $body = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/' . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    /**
     * @param array $body
     * @return bool|object
     */
    public function reserveAccount(array $body = [])
    {

        $headers = [
            'Content-Type:"application/json"',
            "Authorization:Bearer " . $this->getRefreshToken()

        ];


        $response = $this->request('bank-transfer/reserved-accounts', 'POST', $headers, $body);


        if ($response->requestSuccessful) {
            return $response->responseBody;
        }
        return false;

    }

    /**
     * @param string $reference
     * @return bool|object
     */
    public function getTransactionStatus(string $reference)
    {

        $headers = [
            'Content-Type:"application/json"',
            "Authorization:Bearer " . $this->getRefreshToken()
        ];

        $body = [
            "paymentReference" => $reference
        ];

        $response = $this->request('merchant/transactions/query', 'GET', $headers, $body);

        if ($response->requestSuccessful) {
            return $response->responseBody;
        }
        return false;

    }

    /**
     * @param string $accountNumber
     * @return bool|object
     */
    public function unReserveAccount(string $accountNumber)
    {

        $headers = [
            "Authorization:Bearer " . $this->getRefreshToken()
        ];

        $response = $this->request("bank-transfer/reserved-accounts/$accountNumber", 'DELETE', $headers);

        if ($response->requestSuccessful) {
            return $response->responseBody;
        }
        return false;

    }


}

