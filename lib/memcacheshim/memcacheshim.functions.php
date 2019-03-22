<?php

function memcache_add(MemcacheShim $obj, $key, $var, $flags = 0, $expire = 0)
{
    return $obj->add($key, $var, $flags, $expire);
}


function memcache_add_server(MemcacheShim $obj,
                             $host,
                             $port = 11211,
                             $persistent = false,
                             $weight = 0,
                             $timeout = 0,
                             $retry_interval = 0,
                             $status = true,
                             $failure_callback = false,
                             $timeoutms = 0)
{
    return $obj->addServer($host, $port, $persistent, $weight, $timeout, $retry_interval, $status, $failure_callback, $timeoutms);
}


function memcache_close(MemcacheShim $obj)
{
    return $obj->close();
}


function memcache_connect($host, $port = 11211, $timeout = 0)
{
    $obj = new MemcacheShim();
    $obj->connect($host, $port);
    return $obj;
}

function memcache_decrement(MemcacheShim $obj, $key, $value)
{
    return $obj->decrement($key, $value);
}


function memcache_delete(MemcacheShim $obj, $key)
{
    return $obj->delete($key);
}


function memcache_flush(MemcacheShim $obj)
{
    return $obj->flush();
}


function memcache_get(MemcacheShim $obj, $key, $flags = 0)
{
    return $obj->get($key);
}


function memcache_get_server_status(MemcacheShim $obj, $host, $port = 11211)
{
    return $obj->getServerStatus($host, $port);
}


function memcache_get_version(MemcacheShim $obj)
{
    return $obj->getVersion();
}


function memcache_increment(MemcacheShim $obj, $key, $value)
{
    return $obj->increment($key, $value);
}


function memcache_pconnect($host, $port = 11211, $timeout = 0)
{
    $obj = new MemcacheShim();
    $obj->pconnect($host, $port);
    return $obj;
}


function memcache_replace(MemcacheShim $obj, $key, $flags = 0, $expire = 0)
{
    return $obj->replace($key, $flags, $expire);
}


function memcache_set(MemcacheShim $obj, $key, $flags = 0, $expire = 0)
{
    return $obj->set($key, $flags, $expire);
}


function memcache_set_compress_threshold(MemcacheShim $obj, $threshold, $min_savings = 0)
{
    return $obj->setCompressThreshold($threshold, $min_savings);
}


function memcache_set_server_params(MemcacheShim $obj,
                                    $host,
                                    $port = 11211,
                                    $persistent = false,
                                    $weight = 0,
                                    $timeout = 0,
                                    $retry_interval = 0,
                                    $status = true,
                                    $failure_callback = false,
                                    $timeoutms = 0)
{
    return $obj->setServerParams($host, $port, $persistent, $weight, $timeout, $retry_interval, $status, $failure_callback, $timeoutms);
}









