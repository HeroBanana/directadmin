<?php

/*
 * CopyRight 2016 NHosting.
 * 
 * For the full copyright and license information, please view 
 * the LICENSE file that was distributed with this source code.
 */

namespace NHosting\DirectAdmin;

/**
 * DirectAdmin Class
 * 
 * @author N.J. Vlug <info@ruddy.nl>
 */
class DirectAdmin 
{
    /**
     * @const string Version number of the DirectAdmin API Class.
     */
    const VERSION = '0.1';
    
    /**
     * @const string Command API prefix.
     */
    const CMD_PREFIX = '/CMD_API_';
    
    /**
     * @var DirectAdminClient
     */
    private $client = null;
    
    /**
     * DirectAdmin Constructor
     * 
     * @param string $url           DirectAdmin url.
     * @param string $username      DirectAdmin username.
     * @param string $password      DirectAdmin password.
     */
    public function __construct(string $url, string $username, string $password) 
    {
        $this->client = new DirectAdminClient($url, $username, $password);
    }
    
    /**
     * Send command
     * 
     * @param string $method    Method get or post.
     * @param string $command   DirectAdmin command.
     * @param array $options    Additional options.
     * 
     * @return array
     */
    private function send(string $method, string $command, array $options = []): array 
    {
        $result = $this->client->request($method, self::CMD_PREFIX . $command, $options);
        
        return $result;
    }
}
