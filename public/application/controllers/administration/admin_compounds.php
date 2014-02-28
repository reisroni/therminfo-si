<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_compounds.php
* Controlador da administracao (Compounds)
* Criado: 20-01-2014
* Modificado: 28-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_compounds extends CI_Controller {
	
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
        $this->load->model('molecule/Molecule_model');
        $this->load->model('other/Session_model');
        // Carregar os modulos necessarios
        $this->load->library('grocery_CRUD');
        $this->load->library('OBabel');
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

				$this->load->view('content/admin/admin_compounds_view', $this->data);
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
			redirect('/login/redirect/administration/admin_compounds');
		}
	}
		
	//---------------------------------------------------------------
	// Separador 'Compounds'
	//---------------------------------------------------------------
    //
	// ----- Compostos
	/**
	 * Gestao dos compostos (grocery CRUD)
	 * 
	 * @return void
	 */
	public function mols_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Molecule'
            $crud->set_table('molecule');
			$crud->where('validated', 1);
			$crud->where('outdated', 0);
			$crud->set_subject('Molecule');
			$crud->columns('mid', 'therminfo_id', 'casrn', 'name', 'state', 'smiles', 's_inchi', 's_inchikey');
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
			
			$crud->add_fields('casrn','name','formula','mw','state','phi_form','smiles',
			'usmiles','inchi','s_inchi','family','class','subclass','mol_type','img_path',
			'validated','synonyms','characteristics');
			$crud->required_fields('validated');
			$crud->change_field_type('mw', 'integer');
			$crud->change_field_type('state', 'enum', array('','l','s','g'));
			$crud->change_field_type('mol_file', 'text');
			$crud->change_field_type('validated', 'enum', array('',0,1));
			$crud->change_field_type('inchikey', 'readonly');
			$crud->change_field_type('s_inchikey', 'readonly');
			$crud->set_field_upload('img_path', 'public/media/images/molecules');
			$crud->unset_texteditor('smiles', 'usmiles', 'inchi', 's_inchi', 'mol_file');
			$crud->unset_edit_fields('therminfo_id', 'outdated', 'synonyms');
			
			// Relacoes 'Family', 'Class', 'SubClass', 'Image', Molecule_type
			$crud->set_relation('family', 'family', 'f_name');
			$crud->set_relation('class', 'class', 'c_name');
			$crud->set_relation('subclass', 'subclass', 'sc_name');
			$crud->set_relation('mol_type', 'molecule_type', 'mt_name');
			// Relacoes 'Characteristics'
			$crud->set_relation_n_n('characteristics', 'mol_char', 'characteristic', 'molecule', 'charact', 'ch_name');
			
			// Callback functions
			// Inserir
			$crud->callback_insert(array($this, 'callback_mols_insert'));
			// Apagar
			$crud->callback_delete(array($this, 'callback_mols_delete'));
			// Actualizar
			$crud->callback_before_update(array($this, 'callback_mols_before_update'));
			// Campo sinonimos
			$crud->callback_add_field('synonyms', array($this, 'callback_mols_add_field'));
			// Upload
			$crud->callback_before_upload(array($this, 'callback_mols_before_upload'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit molecules';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
    
    /*
	 * Insere um composto na BD (grocery CRUD callback)
	 */
	public function callback_mols_insert($post_array = array())
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            // Tratamento dos dados
            $casrn = ($this->obabel->verify_casrn($post_array['casrn']) === TRUE) ? $post_array['casrn'] : '';
            $name = $post_array['name'];
            $formula = $post_array['formula'];
            $mw = $post_array['mw'] ? str_replace(',', '.' , $post_array['mw']) : '';
            $mw = is_numeric($mw) ? $mw : '';
            $state = $post_array['state'];
            $phi_form = $post_array['phi_form'];
            $smiles = ($this->obabel->calc_MW($post_array['smiles'], 2)) ? $post_array['smiles'] : '';
            $usmiles = $post_array['usmiles'];
            $inchi = ($this->obabel->verify_inchi($post_array['inchi'])) ? $post_array['inchi'] : '';
            $s_inchi = ($this->obabel->verify_inchi($post_array['s_inchi'])) ? $post_array['s_inchi'] : '';
            $family = $post_array['family'];
            $class = $post_array['class'];
            $subclass = $post_array['subclass'];
            $mol_type = $post_array['mol_type'];
            $img_path = $post_array['img_path'];
            $valid = $post_array['validated'];
            $chars = (isset($post_array['characteristics']) && ! empty($post_array['characteristics'])) ? $post_array['characteristics'] : '';
            $user = $_SESSION['user_email'];
            $names = $post_array['synonyms'] ? explode(';', $post_array['synonyms']): '';
            
            //$mol_id = $this->Admin_model->add_compound($casrn, $smiles, $s_inchi, $usmiles, $inchi, $name, $formula, 
            //$mw, $state, $phi_form, $family, $class, $subclass, $mol_type, $img_path, $valid, $user);
            $mol_id = FALSE;
            
            if (! $mol_id)
            {
                $result = FALSE;
            }
            else
            {
                // Associar carateristicas
                if (! empty($chars)) {
                    //$this->Admin_model->add_chars($mol_id, $chars);
                }
                
                // Associar sinonimos
                if (! empty($names)) {
                    //$this->Admin_model->add_synonyms($mol_id, $names);
                }
                
                $result = $mol_id;
            }
		}
        
		return $result;
	}
	
	/*
	 * Elimina um composto da BD (grocery CRUD callback)
	 */
	public function callback_mols_delete($primary_key = 0)
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
        {
            $molecule = $this->Molecule_model->find_by_id($primary_key);
            
            if ($molecule)
            {
                $del_status = $molecule->outdated(); // Elimina o composto (outdated)
                
                if (is_array($del_status)) {
                    if ($del_status['result'] == TRUE) {
                        $result = TRUE;
                    }
                } elseif($del_status == TRUE) {
                    $result = TRUE;
                }
            }
        }
        
        return $result;
	}
	
	/*
	 * Trata os dados para actualizacao (grocery CRUD callback)
	 */
	public function callback_mols_before_update($post_array = array(), $primary_key = 0)
	{
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
        {
            // CAS RN
            if($this->obabel->verify_casrn($post_array['casrn']) !== TRUE) {
                unset($post_array['casrn']);
            }
            
            // Molecular weight
            $post_array['mw'] = str_replace(',', '.' , $post_array['mw']);
            
            if (! is_numeric($post_array['mw'])) {
                unset($post_array['mw']);
            }
            
            // SMILES
            if (! $this->obabel->calc_MW($post_array['smiles'], 2)) {
                unset($post_array['smiles']);
            }
            
            // InChi e InChi Key
            if(! $this->obabel->verify_inchi($post_array['inchi'])) {
                unset($post_array['inchi']);
            } else {
                $inchikey = $this->obabel->inchi_to_inchikey($post_array['inchi']);
                if ($inchikey) {
                    $post_array['inchikey'] = $inchikey;
                }
            }
            
            // Std. InChi e std. InChi Key
            if (! $this->obabel->verify_inchi($post_array['s_inchi'])) {
                unset($post_array['s_inchi']);
            } else {
                $s_inchikey = $this->obabel->inchi_to_inchikey($post_array['s_inchi']);
                if ($s_inchikey) {
                    $post_array['s_inchikey'] = $s_inchikey;
                }
            }
		}
        
		return $post_array;
	}
	
	/*
	 * Modifica o campo para insercao dos sinonimos (grocery CRUD callback)
	 */
	public function callback_mols_add_field()
	{
		return '<textarea id="field-synonyms" name="synonym"></textarea><div>The compound names should be separated by a semicolon (;)</div>';
	}
	
	/*
	 * Verifica se existe a pasta de uploads (grocery CRUD callback)
	 */
	public function callback_mols_before_upload($files_to_upload = '', $field_info = '')
	{
		if (is_dir($field_info->upload_path)) {
			return TRUE;
		} else {
			return 'I am sorry but it seems that the folder that you are trying to upload doesn\'t exist.';    
		}
	}
    
    //---------------------------------------------------------------
	// Separador 'Class'
	//---------------------------------------------------------------
    //
    // ----- Classes
    /**
	 * Gestao das classes (grocery CRUD)
	 * 
	 * @return void
	 */
	public function class_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Class'
			$crud->set_table('class');
			$crud->set_subject('Class');
			$crud->display_as('c_name', 'Name');
			$crud->unset_delete();
			
			$crud->required_fields('c_name');
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add and Edit molecule classes';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
    
    //---------------------------------------------------------------
	// Separador 'Subclass'
	//---------------------------------------------------------------
    //
    // ----- Subclasses
    /**
	 * Gestao das subclasses (grocery CRUD)
	 * 
	 * @return void
	 */
	public function subclass_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Subclass'
			$crud->set_table('subclass');
			$crud->set_subject('Subclass');
			$crud->display_as('sc_name', 'Name');
			$crud->unset_delete();
			
			$crud->required_fields('sc_name');
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add and Edit molecule subclasses';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
    
    //---------------------------------------------------------------
	// Separador 'Family'
	//---------------------------------------------------------------
    //
    // ----- Familias
    /**
	 * Gestao das familias (grocery CRUD)
	 * 
	 * @return void
	 */
	public function family_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Family'
			$crud->set_table('family');
			$crud->set_subject('Family');
			$crud->display_as('f_name', 'Name');
			$crud->unset_delete();
			
			$crud->required_fields('f_name');
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add and Edit molecule families';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
    
    //---------------------------------------------------------------
	// Separador 'Characteristic'
	//---------------------------------------------------------------
    //
    // ----- Caracteristicas
    /**
	 * Gestao das caracteristicas (grocery CRUD)
	 * 
	 * @return void
	 */
	public function chars_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Characteristic'
			$crud->set_table('characteristic');
			$crud->set_subject('Characteristic');
			$crud->display_as('ch_name', 'Name');
			$crud->unset_delete();
			
			$crud->required_fields('ch_name');
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add and Edit molecule characteristics';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
    
    //---------------------------------------------------------------
	// Separador 'Synonym'
	//---------------------------------------------------------------
    //
    // ----- Sinonimos
    /**
	 * Gestao dos sinonimos (grocery CRUD)
	 * 
	 * @return void
	 */
	public function synonym_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'othername'
			$crud->set_table('othername');
			$crud->set_subject('Synonym');
			$crud->columns('molecule', 'synonym');
			
			$crud->required_fields('synonym', 'molecule');
			$crud->change_field_type('synonym', 'text');
			$crud->unset_texteditor('synonym');
			
			// Relacoes 'Molecule'
			$crud->set_relation('molecule', 'molecule', 'therminfo_id');
			
			// Callback functions
			// Inserir
			$crud->callback_insert(array($this, 'callback_synonym_insert'));
			// Campo sinonimos
			$crud->callback_add_field('synonym', array($this, 'callback_synonym_add_field'));
			 
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit molecules synonyms';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
    
    /*
	 * Insere os sinonimos (grocery CRUD callback)
	 */
	public function callback_synonym_insert($post_array = array())
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            $mol_id = $post_array['molecule'];
            $names = $post_array['synonym'] ? explode(';', $post_array['synonym']): '';
            
            //$result = $this->Admin_model->add_synonyms($mol_id, $names);
        }
        
		return  $result;
	}
	
	/*
	 * Modifica o campo para insercao dos sinonimos (grocery CRUD callback)
	 */
	public function callback_synonym_add_field()
	{
		return '<textarea id="field-name" name="synonym"></textarea><div>The compound names should be separated by a semicolon (;)</div>';
	}
    
    //---------------------------------------------------------------
	// Separador 'Others db'
	//---------------------------------------------------------------
    //
    // ----- Outras BD
    /**
	 * Gestao de outras BD (grocery CRUD)
	 * 
	 * @return void
	 */
	public function others_db_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Molecule_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'other_db'
			$crud->set_table('other_db');
			$crud->set_subject('Other DB');
			$crud->order_by('molecule', 'asc');
			
			$crud->required_fields('molecule', 'db');
			
			// Relacoes 'Molecule', 'Other_db_name'
			$crud->set_relation('molecule', 'molecule', 'therminfo_id');
            $crud->set_relation('db', 'other_db_name', 'db_name');
			 
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit others databases';
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

/* End of file admin_compounds.php */
/* Location: ./application/controllers/administration/admin_compounds.php */