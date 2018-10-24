<?php
namespace Model\Service\Facade\DataTypes;

use Model\Service\Facade\DataFacade;

class ExercisesType
{
    
    private $parent;
    private $dataTemp = [];
    private $dataRaw = [];
    
    public function __construct(DataFacade $parent)
    {
        $this->parent = $parent;
    }
    
    
    public function getDataTemp():array
    {
        return $this->parent->tagsType->handleVersions($this->dataTemp);
    }
    
    
    public function setDataRaw($raw)
    {
        $this->dataRaw = array_merge($this->dataRaw, (array)$raw);
    }
    
    
    public function handleData()
    {
        //die(print_r($this->dataRaw));
        // tags
        foreach($this->dataRaw as $data){
            
            // add tags
            $this->parent->tagsType->setDataRaw($data['tags']);
            
            // return ids in tags
            $data['tags'] = $this->parent->tagsType->handleIds($data['tags']);

            if(!in_array($data, $this->dataTemp)){
                // die(print_r($data));
                array_push($this->dataTemp, $data);
            }
        }
    }
    
}

