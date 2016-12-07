<?php

/*
 * CopyRight 2016 NHosting.
 * 
 * For the full copyright and license information, please view 
 * the LICENSE file that was distributed with this source code.
 */

namespace NHosting\DirectAdmin;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use NHosting\DirectAdmin\Exceptions\DirectAdminRequestException;
use NHosting\DirectAdmin\Exceptions\DirectAdminBadContentException;

/**
 * DirectAdminClient Class
 * 
 * @author N.J. Vlug <info@ruddy.nl>
 */
class DirectAdminClient extends Client
{
    /**
     * @var string Base URL.
     */
    private $baseUrl = null;
    
    /**
     * @var int Last request timestamp.
     */
    private $lastRequest = 0;
    
    /**
     * DirectAdminClient Constructor
     * 
     * @param string $url           DirectAdmin URL.
     * @param string $username      DirectAdmin username.
     * @param string $password      DirectAdmin password.
     */
    public function __construct(string $url, string $username, string $password) {
        
        $this->baseUrl = trim($url) . '/';
        
        $config = [
            'base_url' => $this->baseUrl,
            'auth' => [
                $username,
                $password
            ]
        ];

        parent::__construct($config);
    }
    
    /**
     * Get last request timestamp.
     * 
     * @return int
     */
    public function getLastRequest():int 
    {
        return $this->lastRequest;
    }
    
    /**
     * Request to Server.
     * 
     * @param string $method    Method.
     * @param string $uri       Request URL.
     * @param array $options    Additional options.
     * 
     * @return mixed
     */
    public function request(string $method, string $uri = '', array $options = []): array
    {
        $this->lastRequest = microtime(true);
        
        try {
            $response = parent::request($method, $uri, $options);
        }
        catch(\Exception $e) {
            throw new DirectAdminRequestException(sprintf('DirectAdmin %s %s request failed.', $method, $url), $e->getCode(), $e);
        }
        
        if($response->getHeader('Content-Type')[0] === 'text/html') {
            throw new DirectAdminBadContentException(sprintf('DirectAdmin %s %s returned text/html.', $method, $uri));
        }

        $body = $response->getBody()->getContents();

        return $this->responseToArray($body);
    }
    
    /**
     * Processes DirectAdmin style encoded responses into a sane array.
     * 
     * @param string $data
     * 
     * @return array
     */
    private function responseToArray(string $data): array 
    {
        $unescaped = preg_replace_callback('/&#([0-9]{2})/', function ($val) {
            return chr($val[1]);
        }, $data);
        
        $result = Psr7\parse_query($unescaped);
        
        return $this->sanitizeArray($result);
    }
    
    /**
     * Ensures a DA-style response element is wrapped properly as an array.
     *
     * @param mixed $result     Messy input
     * 
     * @return array            Sane output
     */
    private function sanitizeArray($result): array 
    {
        if (count($result) == 1 && isset($result['list[]'])) {
            $result = $result['list[]'];
        }
        
        return is_array($result) ? $result : [$result];
    }
}