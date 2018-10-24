<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/2/18
 * Time: 3:16 PM
 */

namespace Model\Service;

use Model\Core\Helper\Monolog\MonologSender;
use Model\Entity\ResponseBootstrap;
use Model\Mapper\GetDataMapper;
use Model\Service\Facade\CachingCheckerFacade;
use Model\Service\Facade\DataFacade;
use Model\LockedState\LockedState;
use Model\Service\Helper\AuthHelper;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Cache\Simple\FilesystemCache;

class MobileService
{

    private $getDataMapper;
    private $configuration;
    private $monologHelper;

    public function __construct(GetDataMapper $getDataMapper)
    {
        $this->getDataMapper = $getDataMapper;
        $this->configuration = $getDataMapper->getConfiguration();
        $this->monologHelper = new MonologSender();
    }


    /**
     * Get data
     *
     * @param string $app
     * @param string $type
     * @param int $version
     * @param string $lang
     * @return ResponseBootstrap
     */
    public function getData(string $token, string $app, string $type, int $version, string $lang):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // check authorization
//          $authController = new AuthHelper($token, $scope = 'all', $this->configuration);
//          $allowed = $authController->checkAuthorization();
            $allowed = 200; // DEMO

            if($allowed == 200){
                // set identifier
                $identifier = $app . '_' .  $lang . '_'  . $version;

                // check if response is cached, and if yes fetch cached data
               // $cachedData = $this->getDataMapper->checkIfResponseIsCached($app, $lang, $version);

                $cachedData = [];

                if(!empty($cachedData)){
                    $data = json_decode($cachedData);
                }else {
                    // call packages MS for data
                    $client = new \GuzzleHttp\Client();
                    $result = $client->request('GET', $this->configuration['packages_url'] . '/packages/packages?lang=' . $lang . '&state=R&app=' . $app . '&type=' . $type, []);
                    $data = json_decode($result->getBody()->getContents(), true);

                    $dataFacade = new DataFacade($data, $version, $type);
                    $data = $dataFacade->handle();

                    // cache response
                    $cachedFile = 'cached_responses/' . $app . '_' .  $lang . '_'  . $version .  '.txt';
                    // create file if not exists
                    $handle = fopen($cachedFile, 'w');
                    // write data in this file
                    fwrite($handle, json_encode($data));

                    // call mapper for setting cached record in the database
                    $this->getDataMapper->writeCachedRecord($app, $lang, $version, $cachedFile);
                }

                // check data and set response
                if(!empty($data)){
                    $response->setStatus(200);
                    $response->setMessage('Success');
                    $response->setData([
                        'status' => 200,
                        'response' =>
                            $data
                    ]);
                }else {
                    $response->setStatus(204);
                    $response->setMessage('No content');
                }

            }else {
                $response->setStatus(200);
                $response->setMessage('Bad credentials');
            }

            // return data
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Get data service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }

//    public function getDataByApp(string $token, string $app, string $lang, string $state, string $type):ResponseBootstrap {
//
//        try {
//            // create response object
//            $response = new ResponseBootstrap();
//
//            $sku = "pro";
//
//            // check authorization
////        $authController = new AuthHelper($token, $scope = 'all', $this->configuration);
////        $allowed = $authController->checkAuthorization();
//            $allowed = 200; // DEMO
//
//            if($allowed == 200){
//
//                // call packages MS for data
//                $client = new \GuzzleHttp\Client();
//                $result = $client->request('GET', $this->configuration['packages_url'] . '/packages/packages?lang=' . $lang . '&state=R&app=' . $app . '&type=' . $sku, []);
//                $data = json_decode($result->getBody()->getContents(), true);
//
//                $dataFacade = new DataFacade($data, 0, $type);
//                $data = $dataFacade->handle();
//
//                // set response
//                if(!empty($data)){
//                    $response->setStatus(200);
//                    $response->setMessage('Success');
//                    $response->setData([
//                        'status' => 200,
//                        'response' =>
//                            $data[$type]
//                    ]);
//                }else {
//                    $response->setStatus(204);
//                    $response->setMessage('No content');
//                }
//            }else {
//                $response->setStatus(200);
//                $response->setMessage('Bad credentials');
//            }
//
//            // return data
//            return $response;
//
//        }catch (\Exception $e){
//            // send monolog record
//            $this->monologHelper->sendMonologRecord($this->configuration, 1000, $e->getMessage());
//
//            $response->setStatus(404);
//            $response->setMessage('Invalid data');
//            return $response;
//        }
//    }
}