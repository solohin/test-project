<?php

namespace Solohin\ToptalExam\Services;

class BaseService
{
    /** @var $db \Doctrine\DBAL\Connection */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

}
