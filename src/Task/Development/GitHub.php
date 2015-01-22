<?php
namespace Robo\Task\Development;

use Robo\Exception\TaskException;
use Robo\Task\BaseTask;

/**
 * @method \Robo\Task\Development\GitHub repo(string)
 * @method \Robo\Task\Development\GitHub owner(string)
 */
abstract class GitHub extends BaseTask
{
    use \Robo\Common\DynamicParams;

    const GITHUB_URL = 'https://api.github.com';

    protected static $user;
    protected static $pass;

    protected $needs_auth = false;
    protected $repo;
    protected $owner;

    public function uri($uri)
    {
        list($this->owner, $this->repo) = explode('/', $uri);
        return $this;
    }

    protected function getUri()
    {
        return $this->owner . '/' . $this->repo;
    }

    public function askAuth()
    {
        self::$user = $this->ask('GitHub User');
        self::$pass = $this->askHidden('Password');
        return $this;
    }

    protected function sendRequest($uri, $params = [], $method = 'POST')
    {
        if (!$this->owner or !$this->repo) {
            throw new TaskException($this, 'Repo URI is not set');
        }

        $ch = curl_init();
        $url = sprintf('%s/repos/%s/%s', self::GITHUB_URL, $this->getUri(), $uri);
        $this->printTaskInfo("$method $url");

        if (!self::$user) {
            $this->askAuth();
            curl_setopt($ch, CURLOPT_USERPWD, self::$user . ':' . self::$pass);
        }

        curl_setopt_array(
            $ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => $method != 'GET',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => self::$user ?: "Robo"
            )
        );

        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = json_decode($output);

        $this->printTaskInfo($output);
        return [$code, $response];
    }
}