<?php

use Pyro\Module\Domains\Model\Domain;

/**
 * CMS controller for the domains module
 *
 * @author      PyroCMS Dev Team
 * @package     PyroCMS\Core\Modules\Domains\Controllers
 */

class Admin extends Admin_Controller
{
	/**
	 * Array containing the validation rules.
	 *
	 * @var array
	 */
	protected $validation_rules = array(
		array(
			'field' => 'domain',
			'label' => 'lang:domains:domain',
			'rules' => 'trim|required|max_length[250]|callback__check_unique'
		),
		array(
			'field' => 'type',
			'label' => 'lang:domains:type',
			'rules' => 'trim|required'
		)
	);

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();

		// Load the required classes
		$this->load->library('form_validation');
		$this->load->helper('domains');
		$this->lang->load('domains');

		$this->form_validation->set_rules($this->validation_rules);

		Domain::setSiteId(site_id());

	}

	/**
	 * List all domains
	 */
	public function index()
	{
        // Create pagination links
        
		$total_rows = Domain::countWithDomain();
		$this->template->pagination = create_pagination('admin/domains/index', $total_rows);

		// Using this data, get the relevant results
        $this->template->domains = Domain::getWithDomain();

		$this->template->build('admin/index');
	}

	/**
	 * Create a new domain
	 */
	public function add()
	{
		$messages = array();
		// Got validation?
		if ($this->form_validation->run())
		{
			if (Domain::insertWithDomain($_POST))
			{
				$this->session->set_flashdata('success', lang('domains:add_success'));

				redirect('admin/domains');
			}

			$messages['error'] = lang('domains:add_error');
		}

		$domain = new stdClass();

		// Loop through each validation rule
		foreach($this->validation_rules as $rule)
		{
			$domain->$rule['field'] = set_value($rule['field']);
		}

		$this->template
			->set('domain', $domain)
			->set('messages', $messages)
			->build('admin/form');
	}

	/**
	 * Edit an existing domain
	 *
	 * @param int $id The ID of the domain
	 *
	 * @return void
	 */
	public function edit($id = 0)
	{
		$messages = array();
		// Got ID?
		$id or redirect('admin/domains');

		// Get the domain
		if ( !$domain = Domain::findById($id) )
		{
			redirect('admin/domains');
		}

		if ($this->form_validation->run())
		{
			if (Domain::updateByIdWithDomain($id, $this->input->post()))
			{
				$this->session->set_flashdata('success', $this->lang->line('domains:edit_success'));

				redirect('admin/domains');
			}

			$messages['error'] = lang('domains:edit_error');
		}

		$this->template
			->set('domain', $domain)
			->set('messages', $messages)
			->build('admin/form');
	}

	/**
	 * Delete an existing domain
	 *
	 * @param int $id The ID of the domain
	 *
	 * @return void
	 */
	public function delete($id = 0)
	{
		$id_array = ( ! empty($id)) ? array($id) : $this->input->post('action_to');

		// Delete multiple
		if( ! empty($id_array))
		{
			$deleted = 0;
			$to_delete = 0;
			foreach ($id_array as $id)
			{
				if (Domain::deleteByIdAndDomain($id))
				{
					$deleted++;
				}
				else
				{
					$this->session->set_flashdata('error', sprintf($this->lang->line('domains:mass_delete_error'), $id));
				}
				$to_delete++;
			}

			if ($deleted > 0)
			{
				$this->session->set_flashdata('success', sprintf($this->lang->line('domains:mass_delete_success'), $deleted, $to_delete));
			}
		}
		else
		{
			$this->session->set_flashdata('error', $this->lang->line('domains:no_select_error'));
		}		
		
		redirect('admin/domains');
	}

	/**
	 * Callback method for validating the domain's name
	 *
	 * @param string $domain
	 * @return bool
	 */
	public function _check_unique($domain)
	{
		$id = $this->uri->segment(4);

		if (Domain::check_domain($domain, $id))
		{
			$this->form_validation->set_message('_check_unique', sprintf(lang('domains:request_conflict_error'), $domain));
			return false;
		}

		return true;
	}
}