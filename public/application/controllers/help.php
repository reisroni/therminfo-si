<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* help.php
* Controlador da pagina 'help'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Help extends CI_Controller {

	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		$this->load->view('content/help_view');
	}
}

/* End of file help.php */
/* Location: ./application/controllers/help.php */