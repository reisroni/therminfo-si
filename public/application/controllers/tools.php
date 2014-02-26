<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* tools.php
* Controlador da pagina 'tools'
* Criado: 24-02-2012
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Tools extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
							'term' => NULL,
							'option_name' => NULL,
							'option_smiles' => NULL,
							'option_inchi' => NULL,
							'option_form' => NULL);
        // Carregar os modulos
        $this->load->library('OBabel');
        $this->load->library('Cactus');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		$this->struct2img();
	}
	
	/**
	 * Pagina '2D Structure -> Image'
	 */
	public function struct2img()
	{
		// ** Verifica se foi submetido o formulario
		if (! isset($_POST['submit']))
		{
			// Formulario nao submetido (pagina 'Tools')
			$this->load->view('content/struct2img_view', $this->data);
		}
		else
		{
			$smiles = $this->input->post('smiles'); // SMILES
			$molfile = $this->input->post('molfile'); // MOL
			
			if (! $smiles && ! $molfile)
			{
				// SMILES em branco
				$this->data['result'] = '<p class="errorPane"><strong>You have not drawn a 
				structure or entered a SMILES. Please try again!</strong></p>';
			}
			else
			{
				// Calcula e verifica o peso molecular
				$mw = $this->obabel->calc_MW($smiles, 2);
				
				if (! $mw)
				{
					// SMILES invalido
					$this->data['result'] = '<p class="errorPane"><strong>The generated SMILES - <span class="errorText">'. 
					html_escape($smiles) .'</span> - is not valid. Please go back and try again!</strong>';
					$this->data['result'] .= '<br><br><button type="button" onclick="load_smiles(\''. $smiles .'\')" 
					title="Reload the last drawn structure" class="btTxt clickable">Reload the drawn structure</button></p>';
				}
				else
				{
					// Gerar a imagem
					$file = $this->obabel->get_imgfile($smiles);
					
					if ($file)
					{
						$this->data['result'] = '<p><img src="'. $file .'.png" class="ImgBorder_1" style="max-width:500px" 
						alt="Generated image" title="Generated image" /><br><br>';
						$this->data['result'] .= '<button type="button" title="Open as PNG" onClick="open_window(\''. base_url($file.'.png').
						'\')" class="btTxt clickable">Open as PNG</button>';
						$this->data['result'] .= '<button type="button" title="Open as PDF" onClick="open_window(\''. base_url($file.'.pdf').
						'\')" class="btTxt clickable">Open as PDF</button>';
						$this->data['result'] .= '<button type="button" onclick="load_smiles(\''. $smiles .'\')" 
						title="Reload the last drawn structure" class="btTxt clickable">Reload the drawn structure</button></p>';
					}
					else
					{
						// Imagem nao gerado
						$this->data['result'] = '<p class="errorPane"><strong>It was not possible to generate an image. 
						Please verify the structure drawn and try again.</strong>';
						$this->data['result'] .= '<br><br><button type="button" onclick="load_smiles(\''. $smiles .'\')" 
						title="Reload the last drawn structure" class="btTxt clickable">Reload the drawn structure</button></p>';
					}
				}
			}

			$this->load->view('content/struct2img_view', $this->data);
		}
	}
	
	/**
	 * Pagina 'MW Calculator'
	 */
	public function mw()
	{
		// ** Verifica se foi submetido o formulario
		if (! isset($_POST['submit']))
		{
			// Formulario nao submetido (pagina 'Tools')
			$this->load->view('content/mw_view', $this->data);
		}
		else
		{
			$termtype = $this->input->post('termtype'); // Tipo de termo
			$term = trim($this->input->post('term')); // Termo para calculo
			$this->data['term'] = html_escape($term);
			
			// ** calculo de acordo com o tipo selecionado
			switch ($termtype)
			{
				// ********************************
				// Peso molecular pelo nome
				// ********************************
				case 'name' :
				{
					// Calcular o peso molecular
					$mw_result = $this->obabel->calc_MW($term, 1);
					$this->data['option_name'] = ' selected="selected"';
					// Mostrar o resultado
					if ($mw_result)
					{
                        // Formula molecular (Cactus)
                        $formula = $this->_get_formula($term, TRUE, 'name');
                        
						$this->data['result'] = '<p class="normalText">The Molecular Weight of <strong>'. html_escape($term) .
						"{$formula}</strong> is:</p><p class='msgPane normalText boldText'>{$mw_result}</p>";
					}
					else
					{
						$this->data['result'] = '<p>No result for <strong>'. html_escape($term). '</strong></p>';
					}
				} break;
				
				// ********************************
				// Peso molecular pelo SMILES
				// ********************************
				case 'smiles' :
				{
					// Calcular o peso molecular
					$mw_result = $this->obabel->calc_MW($term, 2);
					$this->data['option_smiles'] = ' selected="selected"';
					// Mostrar o resultado
					if ($mw_result)
					{
                        // Formula molecular (OBabel)
						$formula = $this->_get_formula($term, TRUE, 'smiles');
                        
						$this->data['result'] = '<p class="normalText">The Molecular Weight of <strong>'. html_escape($term) .
						"{$formula}</strong> is:</p><p class='msgPane normalText boldText'>{$mw_result}</p>";
					}
					else
					{
						$this->data['result'] = '<p>No result for <strong>'. html_escape($term) .'</strong></p>';
					}
				} break;
				
				// ********************************
				// Peso molecular pelo InChi
				// ********************************
				case 'inchi' :
				{
					// Calcular o peso molecular
					$mw_result = $this->obabel->calc_MW($term, 3);
					$this->data['option_inchi'] = ' selected="selected"';
					// Mostrar o resultado
					if ($mw_result)
					{
                        // Formula molecular (OBabel)
                        $formula = $this->_get_formula($term, TRUE);
                        
						$this->data['result'] = '<p class="normalText">The Molecular Weight of <strong>'. html_escape($term) .
						"{$formula}</strong> is:</p><p class='msgPane normalText boldText'>{$mw_result}</p>";
					}
					else
					{
						$this->data['result'] = '<p>No result for <strong>'. html_escape($term) .'</strong></p>';
					}
				} break;
				
				// ********************************
				// Peso molecular pela formula
				// ********************************
				case 'formula' :
				{
					// Calcular o peso molecular
					$mw_result = $this->obabel->calc_MW($term, 4);
					$this->data['option_form'] = ' selected="selected"';
					// Formatar a formula
					$formula = $this->_get_formula($term);
                    
					// Mostrar o resultado
					if ($mw_result) {
						$this->data['result'] = '<p class="normalText">The Molecular Weight of <strong>'. $formula .
						"</strong> is:</p><p class='msgPane normalText boldText'>{$mw_result}</p>";
					} else {
						$this->data['result'] = '<p>No result for <strong>'. $formula .'</strong></p>';
					}
				} break;
			}
			
			if (isset($_POST['ajax'])) {
				$this->output->set_output($this->data['result']);
			} else {
				$this->load->view('content/mw_view', $this->data);
			}
		}
	}
	
	/*
	 * Procura no cactus a formula de um composto
	 * e formata a formula
	 *
	 * @param string $term O composto
	 * @param boolean $cactus Para procurar no cactus
	 * @param string $type Tipo de composto
	 *
	 * @return string A formula formatada, se existir
	 */
	private function _get_formula($term = '', $cactus = FALSE, $type = '')
	{
		$html_formula = '';
		
		if ($cactus) {
			$formula = trim($this->cactus->get_formula($term, $type));
		} else {
			$formula = html_escape($term);
		}
		
		if ($formula && ! empty($formula))
		{
			$tmp = str_split($formula);
			$html_formula .= ' [';
			
			foreach ($tmp as $char)
			{
				if (is_numeric($char)) {
					$char = '<sub>'. $char .'</sub>';
					$html_formula .= $char;
				} else {
					$html_formula .= $char;
				}
			}
			
			$html_formula .= ']';
		}
		
		return $html_formula;
	}
}

/* End of file tools.php */
/* Location: ./application/controllers/tools.php */