<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/2/18
 * Time: 3:11 PM
 */

namespace Model\Mapper;

use PDO;
use PDOException;
use Component\DataMapper;

class GetDataMapper extends DataMapper
{
    
    public function getConfiguration()
    {
        return $this->configuration;
    }


    /**
     * Check if response is cached
     *
     * @param string $identifier
     * @param string $language
     * @param string $version
     * @return bool|null|string
     */
    public function checkIfResponseIsCached(string $identifier, string $language, string $version) {

        try {

            // set database instructions
            $sql = "SELECT path 
                        FROM app_response_caches
                    WHERE app_identifier = ?
                    AND language = ?
                   /* AND kind = ? */
                    AND version = ?
                    AND cached = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $identifier,
                $language,
                $version,
                'Y'
            ]);

            $path = null;
            $data = null;

            if($statement->rowCount() > 0){
                $path = $statement->fetch(PDO::FETCH_ASSOC)['path'];
                // read cached response
                $handle = fopen($path, 'r');
                $data = fread($handle,filesize($path));
            }

        }catch(PDOException $e){

        }

        // return cached data if any
        return $data;
    }


    /**
     * Set path in database for new cached response
     *
     * @param string $identifier
     * @param string $language
     * @param string $version
     * @param string $path
     */
    public function writeCachedRecord(string $identifier, string $language, string $version, string $path){

        try {

            // set database instructions
            $sql = "INSERT INTO app_response_caches 
                      (app_identifier, cached, language, path, version)
                    VALUES (?,?,?,?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $identifier,
                'Y',
                $language,
                $path,
                $version
            ]);

        }catch(PDOException $e){

        }

    }

}