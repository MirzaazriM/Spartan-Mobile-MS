<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 8/1/18
 * Time: 5:21 PM
 */

namespace Model\Service\Helper;


use Model\Mapper\MobileMapper;

class AuthHelper
{

    private $accessToken;
    private $scope;
    private $configuration;

    public function __construct($accessToken, $scope, $configuration)
    {
        $this->accessToken = $accessToken;
        $this->scope = $scope;
        $this->configuration;
    }


    public function checkAuthorization() {
        // call auth MS to check authorization
        $client = new \GuzzleHttp\Client();
        $res = $client->post($this->configuration['auth_url'] . '/auth/send',
            [
                \GuzzleHttp\RequestOptions::JSON => [
                    'access_token' => $this->accessToken,
                    'scope_request' => $this->scope
                ]
            ]);

        // set data to variable
        $res = $res->getStatusCode();

        return $res;
    }
}