<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* ssearch.php
* Controlador da pagina 'structural search'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Ssearch extends CI_Controller {
	
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
		$this->load->library('Cactus');
        // Estatistica do metodo
        $this->data['count'] = $this->Contador_model->count_search(3);
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
			$threshold = $this->input->post('threshold'); // Threshold
            $file_name = date('d-m-Y_His');
			$newsmiles = $this->obabel->molfile_to_smiles($molfile, $file_name); // SMILES para pesquisa
			
			if ($newsmiles)
			{
				// * Efectua e formata a pesquisa
				$this->data['result'] = $this->_ssearch($newsmiles, $threshold);
			}
			else
			{
				// Estrutura incorrecta
				$this->data['info'] = '<p class="errorPane"><strong>Unable to proceed! (structure is not valid or not entered search details)</strong></p>';
			}
			$this->load->view('content/ssearch_view', $this->data);
		}
		else
		{
			// Pesquisa nao submetida (pagina 'Structural Search')
			$this->load->view('content/ssearch_view', $this->data);
		}
	}
	
	/*
	 * Pesquisa por estrutura do composto
	 * 
	 * @param string $smiles SMILES do composto
	 * @param string $threshold Threshold do SMILES
	 * 
	 * @return Resultado da pesquisa
	 */
	private function _ssearch($smiles, $threshold)
	{
		$html_result;
		$mw = $this->obabel->calc_MW($smiles, 2);
		$name = $this->cactus->get_name($smiles, 'smiles');
		$smile_img = $this->util->replace_char($smiles, 3);
				
		// * Efectua a pesquisa
		$result = $this->Molecule_model->find_by_smiles($smiles, $threshold);
		
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_results();
			$this->data['info'] = '<h2>You are searching for:</h2><p>';
			
			if ($name) {
				$this->data['info'] .= '<strong>Name:</strong> <span class="orangeText">'. html_escape($name) .'</span><br />';
			}
			// Verifica se o cactus retorna algum resultado (imagem)
			$cactus = $this->util->get_url_contents("http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image");
			$this->data['info'] .= '<strong>SMILES:</strong> <span class="orangeText">'. html_escape($smiles) .'</span></p>';
			
			if ($cactus)
			{
				$this->data['info'] .= "<p><img id='cactusImg' class='ImgBorder_2' alt='Chemical Structure Image (By CACTUS)' 
				src='http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image' title='{$name} Image (By CACTUS)' /></p>";
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
				
				$html_result .= '<p><strong>';
				if ($threshold == 1) {
					$html_result .= ' - Identical structures, with score &cong; 100% <br />';
				} else if ($threshold == 'i1') {
					$html_result .= ' - Similar structures, with score between &cong; 90% and 95% <br />';
				} else if ($threshold == 'i2') {
					$html_result .= ' - Similar structures, with score between &cong; 80% and 90% <br />';
				} else if ($threshold == 'i3') {
					$html_result .= ' - Similar structures, with score between &cong; 70% and 80% <br />';
				} else {
					$t = $threshold * 100;
					$html_result .= " - Similar structures, with score >= {$t}% <br />";
				}
				$html_result .= '</strong></p>';
				// Incrementa a pesquisa
				//$this->_search_increment(3, $smiles);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_201'; // SSearch: 2, ssearch: 0, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[SSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
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
			// Estrutura invalida (SMILES invalido)
			$this->data['info'] = '<p class="errorPane"><strong><span class="underlineText">The entered structure</span>
			- <span class="errorText">'. html_escape($smiles) .'</span> - is not valid. Please go back and try again!</strong></p>';
		}
		else if ($result == 3)
		{
			// Nao existe estruturas similares (SMILES similares)
			$this->data['info'] = '<p class="errorPane"><strong>No compounds with similar SMILES: 
			<span class="errorText">'. html_escape($smiles) .'</span></strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_202'; // SSearch: 2, ssearch: 0, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[SSearch] {$error_date} - [{$error_code}]: An error occurred.");
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
                log_message('error', "[SSearch] {$error_date} - [{$error_code}]: {$result['e_desc']}.");
            }
        }
        else
        {
            $error_date = date('Y-m-d');
            $error_code = 'Increment error';
			log_message('error', "[SSearch] {$error_date} - [{$error_code}]: An error occurred.");
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
			$mid = $data[$i]->mid;
			$name = $data[$i]->name;
			$num = $i + 1;
			
			// Numero da linha
			$html .= '<tr class="alt"><td class="index" colspan="3">'. $num .'.</td></tr>';
			
			// ThermInfo ID
			$html .= '<tr><th>ThermInfo ID:</th>';
			$html .= '<td>'. $data[$i]->therminfo_id .'</td>';
			
			// Imagem
			$html .= "<td rowspan='7' class='img'><img class='ImgBorder_1' alt='Chemical Structure Image' 
			title='{$data[$i]->therminfo_id} Image' src='image/compound/{$data[$i]->therminfo_id}' /></td></tr>";
			
			// Name
			$html .= '<tr><th>Name:</th>';
			$html .= '<td>'. $name .'</td></tr>';
			
			// Formula
			$tmp = str_split(stripslashes($data[$i]->formula));
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
			
			// CAS RN
			$html .= '<tr><th>CAS RN:</th>';
			$html .= '<td>'. $data[$i]->casrn .'</td></tr>';
			
			// SMILES
			$smi = $data[$i]->smiles;
			$html .= '<tr><th>SMILES:</th>';
			$html .= "<td><div class='smiles'><a class='lnk' name='{$smi}' title='{$smi}'>{$smi}</a></div></td></tr>";
			
			// Similaridade
			$similar_smiles = $this->Molecule_model->get_similar_smiles();
		
			foreach ($similar_smiles as $ss)
			{
				$sss = explode(' ', $ss);
				$sim = $sss[0];
				$midsmi = $sss[1];
				
				if ($mid == $midsmi) {
					$similaridade = $sim;
				}
			}
		
			$sim_f = $similaridade * 100;
			$html .= '<tr><th>Similarity:</th>';
			$html .= "<td>{$sim_f} %</td></tr>";
			
			// Ficha do composto
			$html .= '<tr><th>More Info:</th>';
			$html .= "<td><form action='". base_url("compound/view/{$data[$i]->therminfo_id}") ."' 
			method='post' target='_blank'><input type='submit' class='btTxt clickable' 
			value='View' title='View more' /></form></td></tr>";
		}
		$html .= '</tbody></table>';
		
		return $html;
	}
}

/* End of file ssearch.php */
/* Location: ./application/controllers/ssearch.php */