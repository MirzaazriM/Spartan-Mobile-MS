<?php
namespace Model\Service\Facade\DataTypes;

use Model\Service\Facade\DataFacade;

class NutritionsPlanType
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
        // tags
        foreach($this->dataRaw as $data){            
            // add tags
            $this->parent->tagsType->setDataRaw($data['tags']);
            
            // add recepies
            $this->parent->recepiesType->setDataRaw($data['recipes']);
            
            // return tag ids
            $data['tags'] = $this->parent->tagsType->handleIds($data['tags']);
            
            // return recepie ids
            $data['recipes'] = $this->parent->tagsType->handleIds($data['recipes']);

            $data = array_merge($data,["kind" => $this->parent->lockingState->nutritionPlanKind($data['id'])]);

            // form nutrition plans
            if(!in_array($data, $this->dataTemp)){
                array_push($this->dataTemp, $data);
            }
        }
    }
    
}

