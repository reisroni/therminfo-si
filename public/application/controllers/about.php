<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* about.php
* Controlador da pagina 'about'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class About extends CI_Controller {

	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
        $this->output->cache(20);
		$this->load->view('content/about_view');
	}
}

/* End of file about.php */
/* Location: ./application/controllers/about.php */