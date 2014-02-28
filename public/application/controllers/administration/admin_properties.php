<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_properties.php
* Controlador da administracao (Properties)
* Criado: 20-01-2014
* Modificado: 28-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_properties extends CI_Controller {
	
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
        // Carregar os modelos
        $this->load->model('property/Data_value_model');
        $this->load->model('other/Session_model');
        // Carregar os modulos necessarios
        $this->load->library('grocery_CRUD');
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

				$this->load->view('content/admin/admin_properties_view', $this->data);
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
			redirect('/login/redirect/administration/admin_properties');
		}
	}
    
    //---------------------------------------------------------------
	// Separador 'Properties'
	//---------------------------------------------------------------
	//
	// ----- Valores
	/**
	 * Gestao dos valores das propriedades (grocery CRUD)
	 * 
	 * @return void
	 */
	public function props_vals_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Data_value_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'molecule_data_ref'
            $crud->set_table('molecule_data_ref');
			$crud->where('molecule_data_ref.validated', 1);
			$crud->where('molecule_data_ref.outdated', 0);
			$crud->set_subject('Value');
			$crud->columns('molecule', 'data', 'reference', 'value', 'error', 'obs', 'advised');
			$crud->display_as('data', 'Property');
			
			$crud->add_fields('molecule', 'data', 'reference', 'value', 'numeric', 'error', 'obs',
			'advised', 'validated');
			$crud->required_fields('molecule', 'data', 'reference', 'value', 'advised', 'validated');
			$crud->change_field_type('validated', 'enum', array('',0,1));
			$crud->unset_texteditor('obs');
			$crud->unset_add_fields('outdated');
			$crud->unset_edit_fields('numeric', 'outdated');
			
			// Relacoes 'Molecule', 'Data', 'Reference'
			$crud->set_relation('molecule', 'molecule', 'therminfo_id');
			$crud->set_relation('data', 'data', '{d_name} - {units}');
			$crud->set_relation('reference', 'reference', 'reference_code');
			
			// Callback functions
			// Inserir
			$crud->callback_insert(array($this, 'callback_prop_vals_insert'));
			// Apagar
			$crud->callback_delete(array($this, 'callback_prop_vals_delete'));
			// Campo 'numeric'
			$crud->callback_add_field('numeric', array($this, 'callback_prop_vals_add_field'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit properties values';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Insere um valor na BD (grocery CRUD callback)
	 */
	public function callback_prop_vals_insert($post_array = array())
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            // Dados para insercao
            $mol_id = $post_array['molecule'];
            $property = $post_array['data'];
            $ref = $post_array['reference'];
            $value = str_replace(',', '.' , $post_array['value']);
            $numeric = $post_array['numeric'] == 'yes' ? TRUE : FALSE;
            $error = str_replace(',', '.' , $post_array['error']);
            $obs = $post_array['obs'];
            $advised = $post_array['advised'] == 'yes' ? TRUE : FALSE;
            $validated = $post_array['validated'];
            $user = $_SESSION['user_email'];
            // Validacao dos dados
            //$valid = $this->Admin_model->validate_data(5, $mol_id, $ref, $property, $value, $error, $numeric);
            $valid = false;
            if ($valid === 1)
            {
                $data = array('molecule' => $mol_id, 'data' => $property, 
                        'reference' => $ref, 'value' => $value, 
                        'error' => $error, 'obs' => $obs, 
                        'advised' => $advised, 'validated' => $validated, 'outdated' => 0);
                
                // Adiciona o novo valor
                $value = $this->Data_value_model->instantiate($data);
                $add = $value->save();
                
                if (is_array($add)) {
                    $result = $add['result'];
                }
            }
        }
        
		return $result;
	}
	
	/*
	 * Elimina um valor da BD (grocery CRUD callback)
	 */
	public function callback_prop_vals_delete($primary_key = 0)
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            $value = $this->Data_value_model->find_by_id($primary_key);
            
            if ($value)
            {
                $del = $value->outdated(); // Elimina o valor da propriedade
                if (is_array($del)) {
                    if ($del['result'] == TRUE) {
                        $result = TRUE;
                    }
                } elseif($del == TRUE) {
                    $result = TRUE;
                }
            }
		}
        
		return $result;
	}
	
	/*
	 * Modifica o campo para a insercao (grocery CRUD callback)
	 */
	public function callback_prop_vals_add_field()
	{
		return '<select id="field-numeric" name="numeric"><option value="yes">yes</option><option value="no">no</option></select>';
	}
	
	// ----- Propriedades
	/**
	 * Gestao das propriedades (grocery CRUD)
	 * 
	 * @return void
	 */
	public function props_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Data_value_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Family'
			$crud->set_table('data');
			$crud->set_subject('Property');
			$crud->display_as('d_name', 'Name');
            $crud->display_as('d_full_name', 'Full Name');
			$crud->unset_delete();
			
			$crud->required_fields('d_name', 'type', 'is_numeric');
			
			// Relacoes 'data_type'
			$crud->set_relation('type', 'data_type', 't_name');
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add and Edit properties';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
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

/* End of file admin_properties.php */
/* Location: ./application/controllers/administration/admin_properties.php */