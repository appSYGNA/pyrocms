<?php namespace Pyro\Module\Domains\Model;

/**
 * Redirect model
 *
 * @author      PyroCMS Dev Team
 * @package     PyroCMS\Core\Modules\Redirects\Models 
 */
class Domain extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Define the table name
     *
     * @var string
     */
    protected $table = 'domains';

    /**
     * Disable updated_at and created_at on table
     *
     * @var boolean
     */
    public $timestamps = false;

	/**
     * Find domain by ID
     *
     * @param integer $id The id of the domain
     *
     * @return void
     */
	public static function findById($id, $site_id) //used to be called 'get'
	{
		return static::where('id', $id)
					->where('site_id', $site_id)->first();
	}
	
	/**
     * Get all domains for this site
     *
     * @return array
     */
	public static function getWithDomain($site_id)
	{
		return static::where('site_id', $site_id)
					->orderBy('domain', 'asc')
					->get();
	}

	/**
     * Count domains for this site
     *
     * @return integer
     */
	public static function countWithDomain($site_id)
	{
		return static::where('site_id', $site_id)->count();
		//$this->db->query("SELECT id FROM core_domains WHERE site_id = ".$this->db->escape($this->_site_id))->num_rows();
	}

	/**
     * Insert into table with the site ID
     *
     * @param array $input The field values to insert
     *
     * @return object
     */
	public static function insertWithDomain($input = array(), $site_id)
	{
		$input['site_id'] = $site_id;
		return static::insert($input);
	}

	/**
     * Update table for this site with the given values
     *
     * @param integer $id The id of the domain
	 * @param array $input The field values to update
     *
     * @return object
     */
	public static function updateByIdWithDomain($id, $input = array(), $site_id)
	{
		
		return static::where('id', $id)
					->where('site_id', static::getSiteId())
					->update($input);
	}

	/**
     * Delete by ID and the site ID
     *
     * @param integer $id The id of the domain
     *
     * @return object
     */
	public static function deleteByIdAndDomain($id)
	{
		return static::where('id', $id)
					->where('site_id', static::getSiteId())
					->delete();
	}

	// Callbacks
	public static function checkDomain($domain, $id)
	{
		/*
			We are working with core_ so prefixes are weird for validation.
			We will use two different manual queries here to avoid complications.
		*/
		if($id > 0)
		{
			return static::select('id')
						->where('id', '!=', $id)
						->where('domain', $domain)
						->count();
		}
		else
		{
			return static::select('id')
					->where('domain', $domain)
					->count();
		}
	}
}