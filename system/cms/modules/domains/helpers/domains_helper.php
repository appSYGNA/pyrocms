<?php

/**
 * @author 		Ryan Thompson - AI Web Systems, Inc.
 * @package		PyroCMS\Core\Modules\Domains\Helpers
 */

/**
 * Get the site's ID based on SITE_REF
 */
if(!function_exists('site_id'))
{
	function site_id()
	{
		return DB::table('core_sites')->select('id')
			->where('ref', SITE_REF)
			->first();
		// Run query
		//$r = $CI->db->query("SELECT id FROM core_sites WHERE ref = '".$CI->db->escape_str(SITE_REF)."' LIMIT 1")->row(0);

		//return $r->id;
	}
}