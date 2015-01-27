<?php 
namespace Robo\Task\Docker;

class Result extends \Robo\Result
{

    /**
     * Do not print result, as it was already printed
     */
    protected function printResult()
    {
    }

    public function getCid()
    {
        $data = $this->getData();
        if (isset($data['cid'])) return $data['cid'];
        return null;
    }

    public function getContainerName()
    {
        $data = $this->getData();
        if (isset($data['name'])) return $data['name'];
        return null;
    }
} 