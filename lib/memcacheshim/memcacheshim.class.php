<?php

/**
 * A class that implements most Memcache functionality.
 */
class MemcacheShim
{

    private $_memcached;

    public function __construct()
    {
        $this->_memcached = defined('MEMCACHESHIM_PERSISTENT_ID')
                          ? new Memcached(MEMCACHESHIM_PERSISTENT_ID)
                          : new Memcached();
        $options = array(
            Memcached::OPT_SERIALIZER  => Memcached::SERIALIZER_PHP,
            Memcached::OPT_COMPRESSION => true,
        );
        if (function_exists('memcacheshim_options')) {
            $options = array_merge($options, memcacheshim_option());
        }
        foreach ($options as $key => $value) {
            $this->_memcached->setOption($key, $value);
        }
    }

    /**
     * Functions that will not be implemented will use this noop
     *
     * @param mixed $return the value to return
     *
     * @return mixed value of $return
     */
    private function noop($return = false)
    {
        return $return;
    }

    /**
     * Check if a server is connected
     *
     * @param string $host
     * @param int $port
     *
     * @return bool
     */
    private function server_is_connected($host, $port)
    {
        $statuses = $this->_memcached->getStats();
        return !empty($statuses[$host.":".$port]) && $statuses[$host.":".$port]['pid'] > 0;
    }


    /**
     * Check if a server is is in the server pool
     *
     * @param string $host
     * @param int $port
     *
     * @return bool
     */
    private function server_is_in_pool($host, $port)
    {
        $existing = $this->_memcached->getServerList();
        if (is_array($existing)) {
            foreach ($existing as $e) {
                if($e['host'] == $host and $e['port'] == $port) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Add an item to the server.
     *
     * @param string $key the key that will be associated with the item.
     * @param mixed $var the variable to store
     * @param int $flags ignored, use Memcached::OPT_COMPRESSION in memcacheshim_options
     * @param int $expire
     *
     * @return bool true on success, false on failure
     */
    public function add($key, $var, $flag = 0, $expire = 0)
    {
        return $this->_memcached->add($key, $var, $expire);
    }


    /**
     * Add a server to the pool
     *
     * @param string $host the memcached server host
     * @param int $port the port to connect to
     * @param bool $persistent ignored, use constant MEMCACHESHIM_PERSISTENT_ID
     * @param int weight the weight of the server
     * @param int timeout ignored, set Memcached::OPT_CONNECT_TIMEOUT in memcacheshim_options
     * @param int retry_interval ignored, set Memcached::OPT_RETRY_TIMEOUT in memcacheshim_options
     * @param bool status set to false and the server will not be added
     * @param callable $failure callback callback to call if connection failed
     * @param int timeoutms ignored
     *
     * @return bool true on success, false on failure
     */
    public function addServer($host,
                              $port = 11211,
                              $persistent = false,
                              $weight = 0,
                              $timeout = 0,
                              $retry_interval = 0,
                              $status = true,
                              $failure_callback = false,
                              $timeoutms = 0)
    {
        if (!$status) {
            return true;
        }
        if ($this->server_is_in_pool($host, $port)) {
            return true;
        }
        $return = $this->_memcached->addServer($host, $port, $weight);
        if (!$this->server_is_connected($host, $port) && is_callable($failure_callback)) {
            call_user_func($failure_callback, $host, $port);
        }
        return $return;
    }


    /**
     * Close memcached server connection
     *
     * @return bool
     */
    public function close()
    {
        return $this->if_connected(function () {
            $status = $this->_memcached->quit();
            unset($this->_memcached);
            return $status;
        }, false);
    }

    /**
     * Connect to a server
     *
     * @param string $host
     * @param int $port
     * @param int $timeout ignored
     *
     */
    public function connect($host, $port = 11211, $timeout = 0)
    {
        $this->addServer($host, $port);
        return $this->server_is_connected($host, $port);
    }

    /**
     * Connect to a server using persistent connection
     *
     * @param string $host
     * @param int $port
     * @param int $timeout ignored
     *
     */
    public function pconnect($host, $port, $timeout = 0)
    {
        return $this->connect($host, $port, $timeout);
    }

    /**
     * Decrement item's value
     *
     * @param string $key
     * @param int $value
     *
     * @return mixed item's new value on success or FALSE on failure.
     */
    public function decrement($key, $value = 1)
    {
        return $this->_memcached->get($key) === false
             ? false
             : $this->_memcached->decrement($key, $value)
             ;
    }

    /**
     * Delete item from the server
     *
     * @param string $key the item key
     * @param int $timeout deprecated
     *
     * @return bool true on success or false on failure
     */
    public function delete($key, $timeout = 0)
    {
        return $this->_memcached->delete($key, $timeout);
    }

    /**
     * Flush all existing items at the server
     *
     * @return bool true on success or false on failure
     */
    public function flush()
    {
        return $this->_memcached->flush();
    }

    /**
     * Retrieve item from the server
     *
     * @param string|array $key the item key or array of keys
     * @param int $flags ignored, use Memcached::OPT_COMPRESSION in memcacheshim_options
     *
     * @return the item value, or false if not set
     */
    public function get($key, $flags = 0)
    {
        if (is_string($key)) {
            return $this->_memcached->get($key);
        }
        if (is_array($key) && !empty($key)) {
            $return = array();
            foreach ($key as $k) {
                $return[$k] = $this->_memcached->get($k);
            }
            return $return;
        }
        return false;
    }


    /**
     * @see MemcacheShim::noop
     */
    public function getExtendedStats()
    {
        return $this->noop();
    }


    /**
     * Returns server status
     *
     * @param string $host
     * @param int port
     *
     * @return int the servers status. 0 if server is failed, non-zero otherwise
     */
    public function getServerStatus($host, $port)
    {
        $statuses = $this->_memcached->getStats();
        return empty($statuses[$host.":".$port])
             ? 0
             : (int) $statuses[$host.":".$port]['pid']
             ;
    }


    /**
     * Get the version of the last connected version
     *
     * @note: Memcache returns only one, memcached returns an array for each
     * server, so here we just return the last server from memcached
     *
     * @return string
     */
    public function getVersion()
    {
        $versions = $this->_memcached->getVersion();
        return is_array($versions) ? array_pop($versions) : false;
    }

    /**
     * Replace an existing item's value
     *
     * @param string $key
     * @param int $value
     * @param int $flags ignored, use Memcached::OPT_COMPRESSION in memcacheshim_options
     * @param int $expire
     *
     * @return mixed item's new value on success or FALSE on failure.
     */
    public function replace($key, $var, $flags = 0, $expire = 0)
    {
        return $this->_memcached->replace($key, $var, $expire);
    }

    /**
     * Store data at the server
     *
     * @param string $key
     * @param int $value
     * @param int $flags ignored, use Memcached::OPT_COMPRESSION in memcacheshim_options
     * @param int $expire
     *
     * @return mixed item's new value on success or FALSE on failure.
     */
    public function set($key, $var, $flags = 0, $expire = 0)
    {
        return $this->_memcached->set($key, $var, $expire);
    }

    /**
     * @see MemcacheShim::noop
     */
    public function setCompressThreshold()
    {
        return $this->noop();
    }


    /**
     * In practice, simply an alias of addServer
     *
     */
    public function setServerParams($host,
                                    $port = 11211,
                                    $persistent = false,
                                    $weight = 0,
                                    $timeout = 0,
                                    $retry_interval = 0,
                                    $status = true,
                                    $failure_callback = false)
    {
        return $this->addServer($host,
                                $port,
                                $persistent,
                                $weight,
                                $timeout,
                                $retry_interval,
                                $status,
                                $failure_fallback);
    }
}
