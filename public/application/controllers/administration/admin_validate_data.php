<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_validate_data.php
* Controlador da administracao (Validate New Data)
* Criado: 20-01-2014
* Modificado: 26-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_validate_data extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('user_name' => NULL,
							'user_type' => NULL,
							'user_inst' => NULL,
							'user_email' => NULL);
        // Carregar os modelos e inicializar a BD
        $this->load->model('molecule/Molecule_model');
        $this->load->model('property/Data_value_model');
        $this->load->model('user/User_model');
        $this->load->model('other/Session_model');
        // Carregar os modulos necessarios
        $this->load->library('grocery_CRUD');
		$this->load->library('Util');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// ** Verifica se ja esta logado **
		if ($this->Session_model->is_logged_in())
		{
			// Verifica o tipo de utilizador
			if ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin')
			{
				// Dados do utilizador
				$this->data['user_name'] = $_SESSION['name'];
				$this->data['user_type'] = $_SESSION['type'];
				$this->data['user_inst'] = $_SESSION['user_inst'];
				$this->data['user_email'] = $_SESSION['user_email'];

				$this->load->view('content/admin/admin_validate_data_view', $this->data);
			}
			else
			{
				// Area proibida
				set_status_header(401, 'Forbidden Area');
				$this->load->view('content/forbidden_view');
			}
		}
		else
		{
			// Volta a pagina de login
			redirect('/login/redirect/administration/admin_validate_data');
		}
	}
		
	//---------------------------------------------------------------
	// Separador 'Validate New Entry'
	//---------------------------------------------------------------
    //
	// ----- Validar novos compostos
	/**
	 * Gestao dos novos compostos (grocery CRUD)
	 * 
	 * @return void
	 */
	public function validate_molecule()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Molecule'
            $crud->set_table('molecule');
			$crud->where('validated', 0);
			$crud->where('outdated', 0);
			$crud->set_subject('Molecule');
			$crud->columns('mid', 'therminfo_id', 'casrn', 'name', 'state', 'smiles', 's_inchi', 's_inchikey', 'depositer');
			$crud->display_as('mid', 'ID')
			->display_as('therminfo_id', 'Therminfo ID')
			->display_as('casrn', 'CAS RN')
			->display_as('mw', 'Molecular Weight')
			->display_as('phi_form', 'Physical Form')
			->display_as('smiles', 'SMILES')
			->display_as('usmiles', 'Unique SMILES')
			->display_as('inchi', 'InChi')
			->display_as('inchikey', 'InChiKey')
			->display_as('s_inchi', 'Std. InChi')
			->display_as('s_inchikey', 'Std. InChiKey')
			->display_as('img_path', 'Image')
			->display_as('mol_type', 'Type');
			$crud->unset_add();
			
			$crud->required_fields('validated');
			$crud->change_field_type('mw', 'integer');
			$crud->change_field_type('state', 'enum', array('','l','s','g'));
			$crud->change_field_type('mol_file', 'text');
			$crud->change_field_type('validated', 'enum', array(0,1));
			$crud->set_field_upload('img_path', 'public/media/images/molecules');
			$crud->unset_texteditor('smiles', 'usmiles', 'inchi', 's_inchi', 'mol_file');
			$crud->unset_edit_fields('therminfo_id', 'outdated', 'depositer');
			
			// Relacoes 'Family', 'Class', 'SubClass', 'Image', 'Molecule_type'
			$crud->set_relation('family', 'family', 'f_name');
			$crud->set_relation('class', 'class', 'c_name');
			$crud->set_relation('subclass', 'subclass', 'sc_name');
			$crud->set_relation('mol_type', 'molecule_type', 'mt_name');
			// Relacoes 'Characteristics', 'Properties', 'User'
			$crud->set_relation_n_n('characteristics', 'mol_char', 'characteristic', 'molecule', 'charact', 'ch_name');
			$crud->set_relation_n_n('properties', 'molecule_data_ref', 'data', 'molecule', 'data', 'd_name');
			$crud->set_relation_n_n('depositer', 'mol_user', 'user', 'molecule', 'user', 'email');
			
			// Callback functions
			// Validar
			$img_url = base_url('assets/grocery_crud/themes/flexigrid/css/images/add.png');
			$crud->add_action('Validate', $img_url, 'admin/action_val_mol_insert');
			// Descartar
			$crud->callback_before_delete(array($this, 'callback_val_mol_before_delete'));
			// Upload
			$crud->callback_before_upload(array($this, 'callback_val_mol_before_delete'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Validate, and Reject new molecules';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Valida um novo composto na BD (grocery CRUD action)
	 */
	public function action_val_mol_insert($mol_id = 0)
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			if (! empty($mol_id))
			{
				// Valida o novo composto
				$result = false; //$this->Admin_model->update_compound($mol_id,'','','','','','','','','','','','','','','','', 1, 0);
				
				if ($result)
				{
					// O utilizador associado ao composto
					$user = $this->User_model->find_user_from_compound($mol_id);
					$email = 'E-mail not sent';
					
					if ($user)
					{
                        // Envio do e-mail
                        $today_date = date('l, F j, Y, g:i a');
                        $user_email = $user->email;
                        $email_subject = 'Compound Validation';
                        $email_msg = "$today_date\n\nDear user,\nThe compound inserted has been validated by the ThermInfo Team.\n\n";
                        $email_msg .= 'If you need more informations, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
                        $email_msg .= "\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
                        
                        $send = $this->util->send_mail('noreply@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
                        
                        if ($send) {
                            $email = "E-mail sent to $user_email";
                        }
					}
                    
					$html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #DFF2BF;
					border-color: #008000;">Added with success - <a href="'. base_url() .'administration/admin_validate_data/validate_molecule">Back to List</a><br />
					<span>'.$email.'</span></p>';
				}
				else
				{
					$html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
					border-color: #D893A1;">Could not add - <a href="'. base_url() .'administration/admin_validate_data/validate_molecule">Back to List</a></p>';
				}
			}
			else
			{
				$html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
				border-color: #D893A1;">Lack ID - <a href="'. base_url() .'administration/admin_validate_data/validate_molecule">Back to List</a></p>';
			}
            
			$this->output->set_output($html);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Envio do e-mail antes de descartar o novo 
	 * composto nao validado (grocery CRUD callback)
	 */
	public function callback_val_mol_before_delete($primary_key = 0)
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            // O utilizador associado ao composto
            $user = $this->User_model->find_user_from_compound($primary_key);
            
            if ($user)
            {
                // Envio do e-mail
                $today_date = date('l, F j, Y, g:i a');
                $user_email = $user->email;
                $email_subject = 'Compound Validation';
                $email_msg = "{$today_date}\n\nDear user,\nThe compound inserted has not been validated by the ThermInfo Team.\n\n";
                $email_msg .= 'If you need more informations, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
                $email_msg .= "\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
                
                $result = $this->util->send_mail('noreply@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
            }
		}
        
		return $result;
	}
    
    // ----- Validar novos valores de propriedades
	/**
	 * Gestao dos novos valores (grocery CRUD)
	 * 
	 * @return void
	 */
	public function validate_prop_value()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Data_value_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'molecule_data_ref'
            $crud->set_table('molecule_data_ref');
			$crud->where('molecule_data_ref.validated', 0);
			$crud->where('molecule_data_ref.outdated', 0);
			$crud->set_subject('Value');
			$crud->columns('molecule', 'data', 'reference', 'value', 'error', 'obs', 'advised', 'depositer');
			$crud->display_as('data', 'Property');
			$crud->unset_add();
			
			$crud->required_fields('molecule', 'data', 'reference', 'value', 'advised', 'validated');
			$crud->change_field_type('validated', 'enum', array(0,1));
			$crud->change_field_type('error', 'integer');
			$crud->unset_texteditor('obs');
			$crud->unset_edit_fields('outdated', 'depositer');
			
			// Relacoes 'Molecule', 'Data', 'Reference'
			$crud->set_relation('molecule', 'molecule', 'therminfo_id');
			$crud->set_relation('data', 'data', '{d_name} - {units}');
			$crud->set_relation('reference', 'reference', 'reference_code');
			// Relacao 'User'
			$crud->set_relation_n_n('depositer', 'entry_user', 'user', 'value_entry', 'user', 'email');
			
			
			// Callback functions
			// Validar
			$img_url = base_url('assets/grocery_crud/themes/flexigrid/css/images/add.png');
			$crud->add_action('Validate', $img_url, 'admin/action_val_value_insert');
			// Descartar
			$crud->callback_before_delete(array($this, 'callback_val_value_before_delete'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Validate and Reject new property values';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Valida um novo valor na BD (grocery CRUD action)
	 */
	public function action_val_value_insert($id = 0)
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			if (! empty($id))
			{
				// Valida o novo valor
				$result = false; //$this->Admin_model->update_property($id, NULL, NULL, NULL, NULL, 1, 0);
				
				if ($result)
				{
					// O utilizador associado ao valor
					$user = $this->User_model->find_user_from_property($id);
					$email = 'E-mail not sent';
					
					if ($user)
					{
                        // Envio do e-mail
                        $today_date = date('l, F j, Y, g:i a');
                        $user_email = $user->email;
                        $email_subject = 'Compound Validation';
                        $email_msg = "{$today_date}\n\nDear user,\nThe Property Value inserted has been validated by the ThermInfo Team.\n\n";
                        $email_msg .= 'If you need more informations, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
                        $email_msg .= "\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
                        
                        $send = $this->util->send_mail('noreply@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
                        
                        if ($send) {
                            $email = "E-mail sent to $user_email";
                        }
					}
                    
					$html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #DFF2BF;
					border-color: #008000;">Added with success - <a href="'. base_url() .'administration/admin_validate_data/action_val_value_insert">Back to List</a><br />
					<span>'.$email.'</span></p>';
				}
				else
				{
					$html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
					border-color: #D893A1;">Could not add - <a href="'. base_url() .'administration/admin_validate_data/action_val_value_insert">Back to List</a></p>';
				}
			}
			else
			{
				$html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
				border-color: #D893A1;">Lack ID - <a href="'. base_url() .'administration/admin_validate_data/action_val_value_insert">Back to List</a></p>';
			}
            
			$this->output->set_output($html);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Envio do e-mail antes de descartar o novo 
	 * valor nao validado (grocery CRUD callback)
	 */
	public function callback_val_value_before_delete($primary_key = 0)
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            // O utilizador associado ao valor
            $user = $this->User_model->find_user_from_property($primary_key);
            
            if ($user)
            {
                // Envio do e-mail
                $today_date = date('l, F j, Y, g:i a');
                $user_email = $user->email;
                $email_subject = 'Property Value Validation';
                $email_msg = "{$today_date}\n\nDear user,\nThe Property Value inserted has not been validated by the ThermInfo Team.\n\n";
                $email_msg .= 'If you need more informations, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
                $email_msg .= "\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
                
                $result = $this->util->send_mail('noreply@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
            }
		}
        
		return $result;
	}
    
    /*
     * Mostra a mensagem de 'area proibida'
     *
     * @return string Mensagem HTML
     */
    private function _show_forbidden_msg()
    {
        set_status_header(401, 'Forbidden Area');
        $html = '<div style="padding:10px; border:1px solid #D893A1; background-color:#FBE6F2;
        text-align:center"><h2>Forbidden Area</h2></div>';
        return $html;
    }
}

/* End of file admin_validate_data.php */
/* Location: ./application/controllers/administration/admin_validate_data.php */