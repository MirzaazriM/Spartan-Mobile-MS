<?php

namespace Model\Entity;

use Model\Contract\HasId;

class DataId implements HasId
{

    private $id;
    private $type;

    /**
     * @return mixed
     */
    public function getId():int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType():String
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(String $type): void
    {
        $this->type = $type;
    }




}