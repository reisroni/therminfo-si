<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* logout.php
* Fazer logout da sessao
* Criado: 03-09-2011
* Modificado: 23-01-2013
* Copyright (c) 2014, ThermInfo 
***********************************/

class Logout extends CI_Controller {
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		// Carregar os modelos
		$this->load->model('other/Session_model');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{	
		redirect('logout/redirect/');
	}
	
	/**
	 * Efectuar logout e redirectionar para o login
	 * da pagina correspondente
	 *
	 * @param string $main_url Pagina/pasta login a redireccionar
     * @param string $sub_url Pagina login de uma pasta a redireccionar
	 * 
	 * @return void
	 */
	public function redirect($main_url = 'main', $sub_url = '')
	{
		$url = empty($sub_url) ? $main_url : $main_url .'/'. $sub_url;
		
		if($this->Session_model->is_logged_in()) {
			$this->Session_model->logout();
		}
        
		redirect('/login/redirect/'. $url);
	}
}

/* End of file logout.php */
/* Location: ./application/controllers/logout.php */