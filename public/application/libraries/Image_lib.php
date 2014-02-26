<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Image_lib
 * Descricao: Geracao da imagem do captcha e das figuras.
 * Criado: 23-04-2012
 * Modificado: 21-02-2014
 * @author Rony Reis, Ana Teixeira
 * @version 0.1
 * @package Therminfo
 * @copyright Copyright (c) 2014, ThermInfo
 */
class Image_lib
{
	/**
	 * Gera uma imagem catpcha
	 * 
	 * @return void
	 */
	public function get_captcha()
	{
        // Sessao
        $CI =& get_instance();
        $CI->load->model('other/Session_model');
        
		// Imagem JPEG
		header('Content-type: image/jpeg');

		// Altura
		$height = 40;
		// Largura 
		$width = 96;
		// Conjunto dos caracteres
		$charset = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
		$charset .= '123456789123456789123456789';
		$charset .= '~@#$%^*()_+-={}][';
		// Numero aleatorio
		$key = mt_rand(1, 9);
		// Chave real (apenas numeros)
		$realkey = null;

		// Escolher aleatoriamente os caracteres
		for ($i = 0; $i < 3; $i++) {
			$key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
		}

		// Preencher a chave real
		$keyArray = str_split($key);
		foreach ($keyArray as $char)
		{
			if (is_numeric($char)) {
				$realkey .= $char;
			}
		}

		// Guardar a chave real em sessao
		$CI->Session_model->capcha_code($realkey);

		// Gerar a imagem
		$image_p = imagecreate($width, $height);
		$color = imagecolorallocate($image_p, 203, 197, 92); //171, 197, 120
		$white = imagecolorallocate($image_p, 247, 247, 247);
		$font_path = FCPATH. 'public'. DS .'media'. DS .'fonts'. DS .'arial.ttf';
		imagettftext($image_p, 15, 2, 6, 25, $white, $font_path, $key);
		// Mostrar a imagem
		imagejpeg($image_p, null, 80);
		// Destruir a imagem
		imagedestroy($image_p);
	}
	
	/**
	 * Procura e gera uma imagem de um composto
	 * 
     * @param int $therm_id Therminfo ID do composto a procurar a imagem
	 * 
	 * @return void
	 */
	public function display_img($therm_id = 0)
	{
		$CI =& get_instance();
		
		$CI->load->model('molecule/Molecule_model');
		$result = $CI->Molecule_model->find_by_thermid($therm_id);
		$error_img_path = FCPATH. 'public'. DS .'media'. DS .'images'. DS .'chemstruct.jpg';
        
		// Imagem JPEG
		header("Content-type: image/jpeg");
		
		if (is_array($result) && ! empty($result) && $result[0]->get_image())
		{
			// Caminho da imagem
            $img_path = FCPATH. 'public'. DS .'media'. DS .'images'. DS .'molecules'. DS . $result[0]->get_image();
            
            if (is_file($img_path)) {
                // Criar a imagem
                $img = imagecreatefromjpeg($img_path);
            } else {
                // Criar a imagem de erro
                $img = imagecreatefromjpeg($error_img_path);
            }
		}
		else
		{
			// Criar a imagem de erro
			$img = imagecreatefromjpeg($error_img_path);
		}
        
        // Mostrar a imagem
        $CI->output->set_output(imagejpeg($img));
        // Destruir a imagem
        imagedestroy($img);
	}
}

/* End of file Image_lib.php */
/* Location: ./application/libraries/Image_lib.php */