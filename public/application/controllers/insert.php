<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* insert.php
* Controlador da pagina 'insert data'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Insert extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('name' => NULL);
        // Carregar os modelos
        $this->load->model('other/Session_model');
    }
	
	/**
	 * Index Page for this controller
	 */
	public function index()
	{
		// ** Verifica se ja esta logado
		if ($this->Session_model->is_logged_in())
		{
			$this->data['name'] = $_SESSION['name'];
			$this->load->view('content/insert_view', $this->data);
		}
		else
		{
			// Volta a pagina de login
			redirect('/login/redirect/insert');
		}
	}
}

/* End of file insert.php */
/* Location: ./application/controllers/insert.php */