<?php

if (!class_exists('Memcache') && class_exists('Memcached')) :

function memcacheshim_ifdef($const, $default_value)
{
    if (!defined($const)) {
        define($const, $default_value);
    }
}

memcacheshim_ifdef('MEMCACHESHIM_CLASS', true);
memcacheshim_ifdef('MEMCACHESHIM_FUNCTIONS', true);
memcacheshim_ifdef('MEMCACHESHIM_PERSISTENT_ID', false);

require __DIR__ . '/memcacheshim.class.php';

if (MEMCACHESHIM_CLASS) {
    class_alias('MemcacheShim', 'Memcache');
}

if (MEMCACHESHIM_FUNCTIONS) {
    require __DIR__ . '/memcacheshim.functions.php';
}

endif;




