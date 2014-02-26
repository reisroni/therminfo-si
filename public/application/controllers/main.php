<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* main.php
* Controlador da pagina 'index'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/
	
class Main extends CI_Controller {
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
        $this->output->cache(20);
		$this->load->view('content/index_view');
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */