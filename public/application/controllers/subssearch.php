<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* subssearch.php
* Controlador da pagina 'substructure search'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo
***********************************/

class Subssearch extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('info' => NULL,
							'result' => NULL,
                            'count' => NULL);
		// Carregar os modelos
		$this->load->model('molecule/Molecule_model');
        $this->load->model('statistics/Contador_model');
		// Carregar os modulos
		$this->load->library('OBabel');
		$this->load->library('Util');
        // Estatistica do metodo
        $this->data['count'] = $this->Contador_model->count_search(4);
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			$molfile = $this->input->post('molfile'); // MOL file
			$smiles = $this->input->post('smiles'); // SMILES
            $file_name = date('d-m-Y_His');
			$newsmiles = $this->obabel->molfile_to_smiles($molfile, $file_name); // SMILES para pesquisa
			
			if ($newsmiles)
			{
				// * Efectua e formata a pesquisa
				$this->data['result'] = $this->_subs_search($newsmiles);
			}
			else
			{
				// Estrutura incorrecta
				$this->data['info'] = '<p class="errorPane"><strong>Unable to proceed! (structure is not valid or not entered search details)</strong></p>';
			}
			$this->load->view('content/subssearch_view', $this->data);
		}
		else
		{
			// Pesquisa nao submetida (pagina 'Substructure Search')
			$this->load->view('content/subssearch_view', $this->data);
		}
	}
	
	/*
	 * Pesquisa por subestrutura
	 * 
	 * @param string $smiles SMILES para pesquisa
	 * 
	 * @return Resultado da pesquisa
	 */
	private function _subs_search($smiles)
	{
		$html_result;
		$mw = $this->obabel->calc_MW($smiles, 2);
		$smile_img = $this->util->replace_char($smiles, 3);
		
		// * Efectua a pesquisa
		$result = $this->Molecule_model->subs_search($smiles);
		
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_results();
			
			$this->data['info'] = '<h2>You are searching for compounds with the following substructure:</h2>';
			$this->data['info'] .= '<p><strong>SMILES:</strong> <span class="orangeText">'. html_escape($smiles) .'</span></p>';
			// Verifica se o cactus retorna algum resultado (imagem)
			$cactus = $this->util->get_url_contents("http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image");
			
			if ($cactus)
			{
				$this->data['info'] .= "<p><img id='cactusImg' class='ImgBorder_2' alt='Chemical Structure Image (By CACTUS)' 
				src='http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image' title='Image (By CACTUS)' /></p>";
			}
			
			$this->data['info'] .= '<p><strong>Molecular Weight:</strong> <span class="orangeText">'. html_escape($mw) .'</span></p>';
			
			if ($cactus)
			{
				$this->data['info'] .= '<p>* Compound Name and Structure provided by the <a href="http://cactus.nci.nih.gov/chemical/structure" 
				target="_blank" title="CACTUS Link"><strong>Chemical Identifier Resolver</strong></a>.</p>';
			}
			
			if ($linhas == 0) 
			{
				// Sem resultado
				$html_result = '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result = '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				if ($linhas > 100) 
				{
					// Mais de 100 resultados
					$html_result .= '<p>* Displaying the <span class="orangeText">100</span> 
					most relevant compounds retrieved from database *</p>';
					$limit = 100;
				}
				else
				{
					$limit = sizeof($result);
				}
				// Incrementa a pesquisa
				//$this->_search_increment(4, $smiles);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_301'; // Subsearch: 3, subs_search: 0, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[Subsearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$this->data['info'] = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$this->data['info'] = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// Estrutura invalida
			$this->data['info'] = '<p class="errorPane"><strong><span class="underlineText">The entered SMILES</span> - 
			<span class="errorText">'. html_escape($smiles) .'</span> - is not valid. Please go back and try again!</strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_302'; // Subsearch: 3, subs_search: 0, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[Subsearch] {$error_date} - [{$error_code}]: An error occurred.");
			$this->data['info'] = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
    /*
     * Incrementa o contador da procura
     *
     * @param int $search_type Metodo de pesquisa
     * @param string $search_term Termo da pesquisa
     *
     * @return void
     */
    private function _search_increment($search_type, $search_term = '')
    {
        $fields = array('method' => $search_type, 'search_detail' => NULL);
        
        if (! empty($search_term)) {
            $fields['search_detail'] = $search_term;
        }
        
        $record = $this->Contador_model->instantiate($fields);
        $result = $record->save();
        
        if (is_array($result) && ! empty($result))
        {
            if (! $result['result'])
            {
                $error_date = date('Y-m-d');
                $error_code = 'Increment ' .$result['error'];
                log_message('error', "[SubSearch] {$error_date} - [{$error_code}]: {$result['e_desc']}.");
            }
        }
        else
        {
            $error_date = date('Y-m-d');
            $error_code = 'Increment error';
			log_message('error', "[SubSearch] {$error_date} - [{$error_code}]: An error occurred.");
        }
    }
    
	/*
	 * Mostrar os resultados em HTML
	 * 
	 * @param array $data Resultado da pesquisa
	 * @param int $limit Limite para mostrar
	 * 
	 * @return string Resultado em HTML
	 */ 
	private function _html_result($data, $limit) 
	{
		// * Mostrar os campos do resultado
		$html = '<table id="resultTable"><tbody>';
		for ($i = 0; $i < $limit; ++$i)
		{
			$mol = $data[$i];
			$name = $mol['molecule']->name;
			$num = $i + 1;
			
			// Numero da linha
			$html .= '<tr class="alt"><td class="index" colspan="3">'. $num .'.</td></tr>';
			
			// ThermInfo ID
			$html .= '<tr><th>ThermInfo ID:</th>';
			$html .= '<td>'. $mol['molecule']->therminfo_id. '</td>';
			
			// Imagem
			$html .= "<td rowspan='7' class='img'><img class='ImgBorder_1' alt='Chemical Structure Image' 
			title='{$mol['molecule']->therminfo_id} Image' src='image/compound/{$mol['molecule']->therminfo_id}' /></td></tr>";
			
			// Name
			$html .= '<tr><th>Name:</th>';
			$html .= '<td>'. $name .'</td></tr>';
			
			// Formula
			$tmp = str_split(stripslashes($mol['molecule']->formula));
			$formula = '';
			foreach ($tmp as $chars)
			{
				if (is_numeric($chars))
				{
					$chars = '<sub>'. $chars .'</sub>';
					$formula .= $chars;
				}
				else
				{
					$formula .= $chars;
				}
			}
			
			$html .= '<tr><th>Formula:</th>';
			$html .= '<td>'. $formula .'</td></tr>';
			
			// Molecular Weight
			$html .= '<tr><th>Mol. Weight:</th>';
			$html .= '<td>'. $mol['molecule']->mw .'</td></tr>';
			
			// SMILES
			$smi = $mol['molecule']->smiles;
			$html .= '<tr><th>SMILES:</th>';
			$html .= "<td><div class='smiles'><a class='lnk' name='{$smi}' title='{$smi}'>{$smi}</a></div></td></tr>";
			
			// SMARTS
			$html .= '<tr><th>SMARTS:</th>';
			$html .= '<td>'. $mol['smarts'] .'</td></tr>';
			
			// Ficha do composto
			$html .= '<tr><th>More Info:</th>';
			$html .= "<td><form action='". base_url("compound/view/{$mol['molecule']->therminfo_id}") ."' 
			method='post' target='_blank'><input type='submit' class='btTxt clickable' 
			value='View' title='View more' /></form></td></tr>";
		}
		$html .= '</tbody></table>';
		
		return $html;
	}
}

/* End of file subssearch.php */
/* Location: ./application/controllers/subssearch.php */