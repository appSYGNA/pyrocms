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
    protected $table = 'core_domains';

    /**
     * Disable updated_at and created_at on table
     *
     * @var boolean
     */
    public $timestamps = false;

	public function setSiteId($site_id)
	{
		$this->_site_id = $site_id;
	}
	
	/**
     * Find domain by ID
     *
     * @param string $id The id of the domain
     *
     * @return void
     */
	public static function findById($id) //used to be called 'get'
	{
		return static::where('id', $id)
					->where('site_id', $this->_site_id)->first();
	}
	
	public static function getWithDomain()
	{
		return static::where('site_id', $this->_site_id)
					->orderBy('domain', 'asc')
					->get();
	}

	public function countWithDomain()
	{
		return static::where('site_id', $this->_site_id)->count();
		//$this->db->query("SELECT id FROM core_domains WHERE site_id = ".$this->db->escape($this->_site_id))->num_rows();
	}

	public static function insertWithDomain($input = array())
	{
		$input['site_id'] = $this->_site_id;
		return static::insert($input);
	}

	public static function updateByIdWithDomain($id, $input = array())
	{
		
		return static::where('id', $id)
					->where('site_id', $this->_site_id)
					->update($input);
	}

	public static function deleteByIdAndDomain($id)
	{
		return static::where('id', $id)
					->where('site_id', $this->_site_id)
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