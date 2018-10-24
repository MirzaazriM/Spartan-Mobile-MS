<?php

namespace Model\Entity;


use Component\Collection;
use Model\Contract\HasId;

class DataIdsCollection extends Collection
{

    private $dataId;
    private $type;


    protected function buildEntity():HasId
    {
        return new DataId;
    }

    /**
     * @return mixed
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     * @param mixed $dataId
     */
    public function setDataId($dataId): void
    {
        $this->dataId = $dataId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }


}