<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_db_control.php
* Controlador da administracao (DB Control)
* Criado: 20-01-2014
* Modificado: 25-01-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_db_control extends CI_Controller {
	
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
        // Carregar o modelo
        $this->load->model('other/Session_model');
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
			if ($_SESSION['type'] == 'superadmin')
			{
				// Dados do utilizador
				$this->data['user_name'] = $_SESSION['name'];
				$this->data['user_type'] = $_SESSION['type'];
				$this->data['user_inst'] = $_SESSION['user_inst'];
				$this->data['user_email'] = $_SESSION['user_email'];

				$this->load->view('content/admin/admin_control_view', $this->data);
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
			redirect('/login/redirect/administration/admin_db_control');
		}
	}
}

/* End of file admin_db_control.php */
/* Location: ./application/controllers/administration/admin_db_control.php */