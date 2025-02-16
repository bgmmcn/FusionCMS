<?php

defined('BASEPATH') OR exit('不允许直接脚本访问。');

class CI_Cache_wincache extends CI_Driver {

	/**
	 * Class constructor
	 *
	 * Only present so that an error message is logged
	 * if APC is not available.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		if ( ! $this->is_supported())
		{
			log_message('error', 'Cache: Failed to initialize Wincache; extension not loaded/enabled?');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Look for a value in the cache. If it exists, return the data,
	 * if not, return FALSE
	 *
	 * @param	string	$id	Cache Ide
	 * @return	mixed	Value that is stored/FALSE on failure
	 */
	public function get($id)
	{
		$success = FALSE;
		$data = wincache_ucache_get($id, $success);

		// Success returned by reference from wincache_ucache_get()
		return ($success) ? $data : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data to store
	 * @param	int	$ttl	Time to live (in seconds)
	 * @param	bool	$raw	Whether to store the raw value (unused)
	 * @return	bool	true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		return wincache_ucache_set($id, $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	mixed	unique identifier of the item in the cache
	 * @return	bool	true on success/false on failure
	 */
	public function delete($id)
	{
		return wincache_ucache_delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function increment($id, $offset = 1)
	{
		$success = FALSE;
		$value = wincache_ucache_inc($id, $offset, $success);

		return ($success === TRUE) ? $value : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function decrement($id, $offset = 1)
	{
		$success = FALSE;
		$value = wincache_ucache_dec($id, $offset, $success);

		return ($success === TRUE) ? $value : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		return wincache_ucache_clear();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @return	mixed	array on success, false on failure
	 */
	public function cache_info()
	{
		return wincache_ucache_info(TRUE);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	mixed	key to get cache metadata on
	 * @return	mixed	array on success/false on failure
	 */
	public function get_metadata($id)
	{
		if ($stored = wincache_ucache_info(FALSE, $id))
		{
			$age = $stored['ucache_entries'][1]['age_seconds'];
			$ttl = $stored['ucache_entries'][1]['ttl_seconds'];
			$hitcount = $stored['ucache_entries'][1]['hitcount'];

			return array(
				'expire'	=> $ttl - $age,
				'hitcount'	=> $hitcount,
				'age'		=> $age,
				'ttl'		=> $ttl
			);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported()
	 *
	 * Check to see if WinCache is available on this system, bail if it isn't.
	 *
	 * @return	bool
	 */
	public function is_supported()
	{
		return (extension_loaded('wincache') && ini_get('wincache.ucenabled'));
	}
}
