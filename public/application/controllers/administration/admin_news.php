<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_news.php
* Controlador da administracao (News)
* Criado: 20-01-2014
* Modificado: 24-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_news extends CI_Controller {
	
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
        $this->load->model('other/News_model');
        $this->load->model('other/Session_model');
		$this->News_model->setDatabase(HOST, USER, PASS, DB);
        // Carregar o modulo necessario
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

				$this->load->view('content/admin/admin_news_view', $this->data);
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
			redirect('/login/redirect/administration/admin_news');
		}
	}
		
	//---------------------------------------------------------------
	// Separador 'News'
	//---------------------------------------------------------------
	//
	// ----- Noticias
	/**
	 * Gestao das noticias (grocery CRUD)
	 * 
	 * @return void
	 */
	public function news_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->News_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'Reference'
			$crud->set_table('news');
			$crud->set_subject('News');
			$crud->order_by('year', 'asc');
			$crud->columns('year', 'date', 'title');
			
			$crud->required_fields('date', 'year', 'title', 'content');
			$crud->change_field_type('year', 'integer');
			$crud->change_field_type('date', 'enum', array('','January','February','March','April',
			'May','June','July','August','September','October','November','December'));
			$crud->change_field_type('month', 'invisible');
			//$crud->unset_texteditor('content');
			
			// Callback functions
			// Inserir
			$crud->callback_before_insert(array($this, 'callback_news_before_insert'));
			// Actualizar
			$crud->callback_before_update(array($this, 'callback_news_before_insert'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit news';
			$this->load->view('content/admin/admin_frame_view', $output);
		}
		else
		{
			// Area proibida
			set_status_header(401, 'Forbidden Area');
			$html = '<div style="padding:10px; border:1px solid #D893A1; background-color:#FBE6F2;
                    text-align:center"><h2>Forbidden Area</h2></div>';
			$this->output->set_output($html);
		}
	}
	
	/*
	 * Preenche o campo 'month' da BD (grocery CRUD callback)
	 */
	public function callback_news_before_insert($post_array = array(), $primary_key = 0)
	{
		// Meses
		$month = array(1 => 'January', 2 => 'February',
		3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 
		7 => 'July', 8 => 'August', 9 => 'September', 
		10 => 'October', 11 => 'November', 12 => 'December');
		// Escolher o campo 'month'
		$result = array_search($post_array['date'], $month);
		if ($result) {
			$post_array['month'] = $result;
		}
		
		return $post_array;
	}
}

/* End of file admin_news.php */
/* Location: ./application/controllers/administration/admin_news.php */