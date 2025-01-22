<?php

namespace App\Service\ControllerServices;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct( string $redisUrl ){
        $this->redis = new Redis();

        $urlParts = parse_url($redisUrl);
        $host = $urlParts['host'];
        $port = isset($urlParts['port']) ? $urlParts['port'] : 6379;

        $this->redis->connect($host, $port);
    }

    public function set(string $key, string $value, int $ttl = 3600): void
    {
        $this->redis->set($key, $value, $ttl);
    }

    public function get(string $key): ?string
    {
        $value = $this->redis->get($key);
        return $value !== false ? $value : null;
    }

    public function delete(string $key): void
    {
        $this->redis->del($key);
    }

    public function rpush(string $key, $value)
    {
        return $this->redis->rpush($key, $value);
    }

    public function sadd(string $key, $value): bool
    {
        return $this->redis->sAdd($key, $value);
    }

    public function smembers(string $key): array
    {
        return $this->redis->sMembers($key);
    }

    public function sismember(string $key, $value): bool
    {
        return $this->redis->sIsMember($key, $value);
    }  
   

    public function mget(array $keys)
    {
        if (empty($keys)) {
            return [];
        }
        
        $values = $this->redis->mget($keys);

        return $values;
    }

    public function scan(&$iterator, $pattern)
    {
        return $this->redis->scan($iterator, $pattern);
    }

    public function keys($pattern)
    {
        return $this->redis->keys($pattern);
    }    

    public function getKeys($pattern)
    {
        $iterator = null;
        $keys = [];

        do {
            list($iterator, $batch) = $this->redis->scan($iterator, $pattern);

            if (is_array($batch)) {
                $keys = array_merge($keys, $batch);
            }

        } while ($iterator > 0); 

        return $keys;
    }

}
