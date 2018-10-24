<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/2/18
 * Time: 3:10 PM
 */

namespace Application\Controller;


use Application\Controller\Helper\GetAccessToken;
use Model\Entity\ResponseBootstrap;
use Model\Service\MobileService;
use Symfony\Component\HttpFoundation\Request;

class MobileController
{

    private $mobileService;

    public function __construct(MobileService $mobileService)
    {
        $this->mobileService = $mobileService;
    }


    /**
     * Get data controller
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function get(Request $request):ResponseBootstrap{

        // get data from url
        $app = $request->get('app');
        $type = $request->get('type');
        $version = $request->get('version');
        $lang = $request->get('lang');

        // get access token
        $getToken = new GetAccessToken();
        $token = $getToken->accessToken($request);

        // create response object
        $response = new ResponseBootstrap();

        if(isset($token) && isset($app) && isset($type) && isset($version) && isset($lang)){
            return  $this->mobileService->getData($token, $app, $type, $version, $lang);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        return $response;
    }

}