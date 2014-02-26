<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* image.php
* Controlador das imagens
* Criado: 01-05-2012
* Modificado: 21-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Image extends CI_Controller {
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		// Carregar os modulos
		$this->load->library('Image_lib');
    }
	
	/**
	 * Gerar um Captcha
	 * 
	 * @return void
	 */
	public function captcha()
	{
		$this->image_lib->get_captcha();
	}
	
	/**
	 * Imagem do composto
	 * 
	 * @param int $therm_id Therminfo ID do composto
	 * 
	 * @return void
	 */
	public function compound($therm_id = 0)
	{
		$this->image_lib->display_img($therm_id);
	}
}

/* End of file image.php */
/* Location: ./application/controllers/image.php */