<?php
namespace Model\Service\Facade\DataTypes;

use Model\Service\Facade\DataFacade;

class PackagesType
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
            //die(print_r($this->dataRaw));
            // add package tags
            $this->parent->tagsType->setDataRaw($data['tags']);
            
            // add workout plans
            $this->parent->workoutsPlanType->setDataRaw($data['training_plans']);
            
            // add nutrition plan tags
            $this->parent->nutritionsPlanType->setDataRaw($data['nutrition_plans']);
            
            // return tags ids
            $data['tags'] = $this->parent->tagsType->handleIds($data['tags']);
            
            // return recepis ids
            $data['nutrition_plans'] = $this->parent->tagsType->handleIds($data['nutrition_plans']);
            
            // return workout ids
            $data['training_plans'] = $this->parent->tagsType->handleIds($data['training_plans']);
            //die(print_r($data));
            // form packages data
            if(!in_array($data, $this->dataTemp)){
                array_push($this->dataTemp, $data);
            }
        }
    }
    
    
}

