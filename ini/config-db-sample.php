<?php

define( 'W3TC_CONFIG_DATABASE', true );

// optional - specify table to store
define( 'W3TC_CONFIG_DATABASE_TABLE', 'wp_options' );

// cache config in cache to prevent db queries on each http request.
// if multiple http servers used - use only shared cache storage used by all
// machines, since distributed flush operations are not supported for config
// cache

//
// memcached cache config
//
define( 'W3TC_CONFIG_CACHE_ENGINE', 'memcached');
define( 'W3TC_CONFIG_CACHE_MEMCACHED_SERVERS', '127.0.0.1:11211' );

// optional memcached settings
define( 'W3TC_CONFIG_CACHE_MEMCACHED_PERSISTENT', true );
define( 'W3TC_CONFIG_CACHE_MEMCACHED_AWS_AUTODISCOVERY', false );
define( 'W3TC_CONFIG_CACHE_MEMCACHED_USERNAME', '' );
define( 'W3TC_CONFIG_CACHE_MEMCACHED_PASSWORD', '' );

//
// redis config cache
//
define( 'W3TC_CONFIG_CACHE_ENGINE', 'redis');
define( 'W3TC_CONFIG_CACHE_REDIS_SERVERS', '127.0.0.1:6379' );

// optional redis settings
define( 'W3TC_CONFIG_CACHE_REDIS_PERSISTENT', true );
define( 'W3TC_CONFIG_CACHE_REDIS_DBID', 0 );
define( 'W3TC_CONFIG_CACHE_REDIS_PASSWORD', '' );
