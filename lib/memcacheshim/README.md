MemcacheShim
============

MemcacheShim is a backfill that provides compatability for code that requires
the Memcache class, but is running on a server to a server that does not
have it installed and _does_ have Memcached.

It exists because the dotdeb repos for PHP7 do not have php7.0-memcache, but
they do have PHP7.0-memcached.  In all cases, it is better to use php-memcache
if it is available.


Structure
---------

    -- memcacheshim
      +-- README.md: this README file
      +-- memcacheshim.class.php: the shim class that maps memcache functions to memcached
      +-- memcacheshim.functions.php: backfills for the procedural memcache_* functions
      +-- memcacheshim.php: the file that bootstraps and configures the shim


Usage
-----

    require 'path/to/memcacheshim/memcacheshim.php';


Configuration
-------------

Configuration is handled with a couple of constants and a function that you.
can optionally define. Set them before including the bootstrap file.

Constants:

  - MEMCACHESHIM_CLASS: Alias the MemacheShim class to Memcache. Default true
  - MEMCACHESHIM_FUNCTIONS: create the memcache_* functions using the
    MemcacheShim class. Default true.
  - MEMCACHESHIM_PERSISTENT_ID: If you wish to use persistent connections, set
    this to a unique string. Default false.

Function: create a function named "memcacheshim_options" that returns an array
of key => value pairs that will be passed to the Memcached object(s) via
Memcached::setOption(). For example:

    function memcacheshim_options()
    {
        return array(
            Memcached::OPT_PREFIX_KEY => "widgets",
        );
    }



Limitations
-----------

Due to differences in the way that Memcache and Memcached work, there are some
behaviours and options that will not work with this shim. These include:

 - advanced server pool management: Although the shim contains functions for
   adding and changing server options, in practice they only add. You won't be
   able to do anything advanced in this regard. This affects the methods
   Memcache::addServer and Memcache::setServerParams. Further, getting server
   connection statuses from Memcache::connect and Memcache::pconnect is sketchy
   at best.

 - Server-by-server persistent connections : Memcache allows you to set
   persistent connections on a server-by-server basis, in Memcached it is a
   object-level setting. For the MemcacheShim, it is a global setting.

 - Variable-by-variable options: the get/set/replace methods for memcached allow
   you to set compression variables on a variable-by-variable basis with
   a flags option. This is an object-level parameter in Memcached, and a global
   option in the MemcacheShim.

 - Mixed environments hitting the same Memcached server. There are known issues
   mixing Memcache and Memcached. You need to use one or the other to connect to
   the Memcached server.  See http://php.net/manual/en/book.memcached.php#115667





