<?php

namespace Solohin\ToptalExam\Services;

abstract class BaseService
{
    /** @var $db \Doctrine\DBAL\Connection */
    protected $db;
    protected $lastErrorType = '';

    public function getLastErrorType()
    {
        return $this->lastErrorType;
    }

    public function __construct($db)
    {
        $this->db = $db;
    }

    public abstract function delete($id);

    public abstract function getOne($id);

    public abstract function getAll();

    public abstract function insert($data);

    public abstract function update($id, $data);

}
