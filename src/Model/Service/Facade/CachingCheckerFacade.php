<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 8/12/18
 * Time: 1:19 PM
 */

namespace Model\Service\Facade;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

class CachingCheckerFacade
{

    /**
     * Check if there is already cached response
     *
     * @param $identifier
     * @return mixed
     */
    public function checkCachedResponses($identifier){
        // create cashing adapeter
        $cache = new PhpArrayAdapter(
        // single file where values are cached
            __DIR__ . '/cached_files/' . $identifier . '.cache',
            // a backup adapter, if you set values after warmup
            new FilesystemAdapter()
        );

        // get cached identifier if exists
        $mobile_identifier = $cache->getItem($identifier);

        // loop through cached responses and check if there is an identifier match
        $dir = "../src/Model/Service/cached_files/*";
        foreach(glob($dir) as $file)
        {
            $filenamePartOne = substr($file, 34);
            $position = strpos($filenamePartOne, 'cache');
            $filename = substr($filenamePartOne, 0, ($position - 1));

            // check if filename is equal to the given ids
            if($mobile_identifier->getKey() == $filename){
                // if yes get cached data
                $cacheItem = $cache->getItem('raw.mobile');
                $data = $cacheItem->get();
            }
        }

        // return cached data if exists
        return $data;
    }
}