<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_references.php
* Controlador da administracao (References)
* Criado: 20-01-2014
* Modificado: 28-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_references extends CI_Controller {
	
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
        $this->load->model('reference/Reference_model');
        $this->load->model('reference/Author_model');
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

				$this->load->view('content/admin/admin_references_view', $this->data);
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
			redirect('/login/redirect/administration/admin_references');
		}
	}
		
	//---------------------------------------------------------------
	// Separador 'References'
	//---------------------------------------------------------------
	//
	// ----- Referencias
	/**
	 * Gestao das referencias (grocery CRUD)
	 * 
	 * @return void
	 */
	public function ref_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Reference_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Reference'
			$crud->set_table('reference');
			$crud->set_subject('Reference');
			$crud->columns('refid', 'reference_code','ref_type', 'title', 'year', 'authors');
			$crud->display_as('refid', 'ID')
			->display_as('reference_code', 'Code')
			->display_as('ref_type', 'Type')
			->display_as('bpage', 'Begin page')
			->display_as('epage', 'End page')
			->display_as('ref_all', 'Reference All');
			
			$crud->required_fields('ref_type', 'title', 'year');
			$crud->change_field_type('year', 'integer');
			$crud->change_field_type('ref_type', 'enum', array('Book','Paper'));
			$crud->unset_add_fields('reference_code','ref_all');
			$crud->unset_texteditor('ref_all');
			
			// Relacoes 'Author'
			$crud->set_relation_n_n('authors', 'author_ref', 'author', 'reference', 'author', 'a_last_name');
			
			// Callback functions
			// Inserir
			$crud->callback_insert(array($this, 'callback_ref_insert'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit references';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Inserir nova referencia na BD (grocery CRUD callback)
	 */
	public function callback_ref_insert($post_array = array())
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            // Dados para insercao
            $type = $post_array['ref_type'];
            $title = $post_array['title'];
            $journal = $post_array['journal'];
            $book = $post_array['book'];
            $year = $post_array['year'];
            $vol = $post_array['volume'];
            $issue = $post_array['issue'];
            $bp = $post_array['bpage'];
            $ep = $post_array['epage'];
            $editor = $post_array['editor'];
            $publisher = $post_array['publisher'];
            $authors = (isset($post_array['authors']) && ! empty($post_array['authors'])) ? $post_array['authors'] : '';
            
            $result = FALSE; //$this->Admin_model->add_reference($type, $authors, $title, $year, $journal, $book, $vol, $issue, $bp, $ep, $editor, $publisher);
        }
        
        return $result;
	}
	
	// ----- Autores
	/**
	 * Gestao dos autores (grocery CRUD)
	 * 
	 * @return void
	 */
	public function authors_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->Author_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Author'
			$crud->set_table('author');
			$crud->set_subject('Author');
			$crud->display_as('a_first_name', ' First Name');
            $crud->display_as('a_last_name', 'Last Name');
			$crud->unset_delete();
			
			$crud->required_fields('a_first_name', 'a_last_name');
			
			// Callback functions
			// Apagar
			$crud->callback_delete(array($this, 'callback_author_delete'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add and Edit references authors';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			$this->output->set_output($this->_show_forbidden_msg());
		}
	}
	
	/*
	 * Elimina um autor da BD (grocery CRUD callback)
	 */
	public function callback_author_delete($primary_key = 0)
	{
        $result = FALSE;
        // ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
            $result = FALSE; //$this->Admin_model->remove_author($primary_key);
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

/* End of file admin_references.php */
/* Location: ./application/controllers/administration/admin_references.php */