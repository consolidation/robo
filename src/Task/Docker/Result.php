<?php 
namespace Robo\Task\Docker;

class Result extends \Robo\Result
{
    public function getCid()
    {
        $data = $this->getData();
        if (isset($data['cid'])) return $data['cid'];
        return null;
    }
} 