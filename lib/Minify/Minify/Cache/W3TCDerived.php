<?php
/**
 * Class Minify_Cache_APC
 * @package Minify
 */

/**
 * APC-based cache class for Minify
 * 
 * <code>
 * Minify::setCache(new Minify_Cache_APC());
 * </code>
 * 
 * @package Minify
 * @author Chris Edwards
 **/
class Minify_Cache_W3TCDerived {
    private $_cache;
    /**
     * Create a Minify_Cache_APC object, to be passed to
     * Minify::setCache().
     */
    public function __construct($cache) {
        $this->_cache = $cache;
    }

    /**
     * Write data to cache.
     *
     * @param string $id cache id
     *
     * @param string $data
     *
     * @return bool success
     */
    public function store($id, $data)
    {
        $data['created_time'] = $_SERVER['REQUEST_TIME'];
        return $this->_cache->set($id, $data);
    }

    /**
     * Get the size of a cache entry
     *
     * @param string $id cache id
     *
     * @return int size in bytes
     */
    public function getSize($id)
    {
        $v = $this->fetch($id);
        if (!isset($v['content'])) {
            return false;
        }

        return (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2))
            ? mb_strlen($v['content'], '8bit')
            : strlen($v['content']);
    }

    /**
     * Does a valid cache entry exist?
     *
     * @param string $id cache id
     *
     * @param int $srcMtime mtime of the original source file(s)
     *
     * @return bool exists
     */
    public function isValid($id, $srcMtime)
    {
        $v = $this->fetch($id);
        if (!isset($v['created_time']))
            return false;

        return ($v['created_time'] >= $srcMtime);
    }

    /**
     * Send the cached content to output
     *
     * @param string $id cache id
     */
    public function display($id)
    {
        $v = $this->fetch($id);
        if (isset($v['content']))
            echo $v['content'];
    }

    private $loaded_id = null;
    private $loaded_value = null;

    /**
     * Fetch the cached content
     *
     * @param string $id cache id
     *
     * @return string
     */
    public function fetch($id)
    {
        if ($this->loaded_id == $id) {
            return $this->loaded_value;
        }
        $v = $this->_cache->get($id);

        if (!is_array($v) || !isset($v['content']))
            return false;

        $this->loaded_id = $id;
        $this->loaded_value = $v;
        return $this->loaded_value;
    }

    /**
     * Flushes all data
     *
     * @return boolean
     */
    function flush() {
        return $this->_cache->flush();
    }
}
