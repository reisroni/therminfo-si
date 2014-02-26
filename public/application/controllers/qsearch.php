<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* qsearch.php
* Controlador da pagina 'quick search'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Qsearch extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
                            'count' => NULL);
		// Carregar os modelos
		$this->load->model('molecule/Molecule_model');
        $this->load->model('other/Session_model');
        $this->load->model('statistics/Contador_model');
        // Carregar os modulos
		$this->load->library('Util');
        // Estatistica do metodo
        $this->data['count'] = $this->Contador_model->count_search(1);
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// * Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			// * Verifica o codigo de seguranca
			if ($this->Session_model->capcha_code() == $_POST['vercode'] && $this->Session_model->check_capcha())
			{
				$searchtype = $this->input->post('searchtype'); // Tipo de pesquisa
				$term = trim($this->input->post('searchterm')); // Termo da pesquisa
				
				// * Pesquisa de acordo com o tipo selecionado
				switch ($searchtype)
				{
					// ********************************
					// Pesquisa pelo nome
					// ********************************
					case 'name':
					{
						// Efectua e formata a pesquisa
						$this->data['result'] = $this->_qsearch_name($term);
						
						if (isset($_POST['ajax']))
						{
							$this->output->set_output($this->data['result']);
						}
						else
						{
							$this->load->view('content/qsearch_view', $this->data);
						}
					} break;
					
					// ********************************
					// Pesquisa pela formula molecular
					// ********************************
					case 'formula':
					{
						// Efectua e formata a pesquisa
						$this->data['result'] = $this->_qsearch_formula($term);
						
						if (isset($_POST['ajax']))
						{
							$this->output->set_output($this->data['result']);
						}
						else
						{
							$this->load->view('content/qsearch_view', $this->data);
						}
					} break;
					
					// ********************************
					// Pesquisa pelo thermInfo ID
					// ********************************
					case 'thermId':
					{
						// Efectua e formata a pesquisa
						$this->data['result'] = $this->_qsearch_thermid($term);
						
						if (isset($_POST['ajax']))
						{
							$this->output->set_output($this->data['result']);
						}
						else
						{
							$this->load->view('content/qsearch_view', $this->data);
						}
					} break;
					
					// ********************************
					// Pesquisa pelo CAS RN
					// ********************************
					case 'casrn':
					{
						// Efectua e formata a pesquisa
						$this->data['result'] = $this->_qsearch_casrn($term);
						
						if (isset($_POST['ajax'])) 
						{
							$this->output->set_output($this->data['result']);
						}
						else
						{
							$this->load->view('content/qsearch_view', $this->data);
						}
					} break;
					
					// ********************************
					// Pesquisa pelo SMILES
					// ********************************
					case 'smiles':
					{	
						// Efectua e formata a pesquisa
						$this->data['result'] = $this->_qsearch_smiles($term, $this->input->post('smilesthreshold'));
						
						if (isset($_POST['ajax'])) 
						{
							$this->output->set_output($this->data['result']);
						}
						else
						{
							$this->load->view('content/qsearch_view', $this->data);
						}
					} break;
					
					// ********************************
					// Pesquisa pelo InChi
					// ********************************
					case 'inchi':
					{	
						// Efectua e formata a pesquisa
						$this->data['result'] = $this->_qsearch_inchi($term, $this->input->post('inchiLayer'));
						
						if (isset($_POST['ajax']))
						{
							$this->output->set_output($this->data['result']);
						}
						else
						{
							$this->load->view('content/qsearch_view', $this->data);
						}
					} break;
				}
			}
			else
			{
				// Codigo de seguranca invalido
				$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
				Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span> 
				numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
				if (! $this->Session_model->check_capcha()) {
                    // Verifica se o utilizador esta a utilizar o browser IE
                    $this->data['result'] .= $this->util->verify_ie_browser();
                }
                
				if (isset($_POST['ajax'])) 
				{
					$this->output->set_output($this->data['result']);
				}
				else
				{
					$this->load->view('content/qsearch_view', $this->data);
				}
			}
		}
		else
		{
			// Pesquisa nao submetida (pagina 'Quick Search')
			$this->load->view('content/qsearch_view', $this->data);
		}
	}
	
	/*
	 * Pesquisa rapida pelo nome do composto
	 * 
	 * @param string $name Nome do composto
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _qsearch_name($name)
	{
		$html_result;
		// * Efectua a pesquisa
		$result = $this->Molecule_model->find_by_name($name);
		$linhas_tmp = $this->Molecule_model->get_num_results();
		$sound = FALSE;
		
		// Pesquisa por som
		if ($linhas_tmp == 0) 
		{
			$result = $this->Molecule_model->find_by_sound($name);
			$sound = TRUE;
		}
		
		// * Processamento do resultado
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_results();
			$html_result = '<p><strong>You are searching for:</strong> <span class="orangeText">'. html_escape($name) .'</span><br />';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
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
				
				if ($sound) 
				{
					$html_result .= '<p><strong>Maybe the compound name is misspelled. Did you mean:</strong></p>';
				}
				// Incrementa a pesquisa
				//$this->_search_increment(1, 1, $name);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na DB
			$error_code = 'TI_001'; // QSearch: 0, Name: 0, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_002'; // QSearch: 0, Name: 0, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
	/*
	 * Pesquisa rapida pela formula do composto
	 * 
	 * @param string $formula Formula do composto
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _qsearch_formula($formula)
	{
		$html_result;
		// * Efectua a pesquisa
		$formula = strtoupper($formula);
		$result = $this->Molecule_model->find_by_formula($formula);
		
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_rows();
			$html_result = '<p><strong>You are searching for:</strong> <span class="orangeText">'. html_escape($formula) .'</span><br />';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
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
				//$this->_search_increment(1, 2, $formula);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_011'; // QSearch: 0, Formula: 1, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// Formula Molecular invalida
			$html_result = '<p class="errorPane"><strong><span class="underlineText">Invalid Molecular Formula</span>: 
			<span class="errorText">'. html_escape($formula) .'</span>. Make sure the Molecular Formula you have entered 
			matches the valid characters: C|H|B|F|I|P|N|O|S.</strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_012'; // QSearch: 0, Formula: 1, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
	/*
	 * Pesquisa rapida pelo Therminfo ID
	 * 
	 * @param string $therm_id Therminfo ID do composto
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _qsearch_thermid($therm_id)
	{
		$html_result;
		// * Efectua a pesquisa
		$therm_id = strtoupper($therm_id);
		$result = $this->Molecule_model->find_by_thermid($therm_id);
		
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_rows();
			$html_result = '<p><strong>You are searching for:</strong> <span class="orangeText">'. html_escape($therm_id) .'</span><br />';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
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
				//$this->_search_increment(1, 3, $therm_id);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_021'; // QSearch: 0, TherminfoID: 2, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date. ', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// ThermInfo ID invalido
			$html_result = '<p class="errorPane"><strong><span class="underlineText">
			Invalid ThermInfo ID</span>: <span class="errorText">'. html_escape($therm_id) .
			'</span>. Make sure the ThermInfo ID you have entered matches the correct format: CONNNNNNN (CO and 7 digits).</strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_022'; // QSearch: 0, TherminfoID: 2, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
	/*
	 * Pesquisa rapida pelo CAS RN do composto
	 * 
	 * @param string $casrn CAS RN do composto
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _qsearch_casrn($casrn)
	{
		$html_result;
		// * Efectua a pesquisa
		$result = $this->Molecule_model->find_by_casrn($casrn);
		
		if (is_array($result)) 
		{
			$linhas = $this->Molecule_model->get_num_rows();
			$html_result = '<p><strong>You are searching for:</strong> <span class="orangeText">'. html_escape($casrn) .'</span><br />';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
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
				//$this->_search_increment(1, 4, $casrn);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_031'; // QSearch: 0, CASRN: 3, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// CASRN invalido
			$html_result = '<p class="errorPane"><strong><span class="underlineText">
			Invalid CAS Registry Number format</span>: <span class="errorText">'. html_escape($casrn) .'</span>.
			Make sure the CAS Registry Number you have entered matches the standard format: 
			NNNNNNN-NN-N (1-7 digits, hyphen, 2 digits, hyphen, 1 digit).</strong></p>';
		}
		else if ($result == 3)
		{
			// Digito de controle do CASRN invalido
			$html_result = '<p class="errorPane"><strong><span class="underlineText">Invalid CAS Registry Number</span>: 
			<span class="errorText">'. html_escape($casrn) .'</span>. It does not verify the check digit.</strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_032'; // QSearch: 0, CASRN: 3, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
	/*
	 * Pesquisa rapida pelo SMILES do composto
	 * 
	 * @param string $smiles SMILES do composto
	 * @param string $threshold Threshold do SMILES
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _qsearch_smiles($smiles, $threshold)
	{
		$html_result;
		// * Efectua a pesquisa
		$result = $this->Molecule_model->find_by_smiles($smiles, $threshold);
		
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_results();
			$html_result = '<p><strong>You are searching for:</strong> <span class="orangeText">'. html_escape($smiles) .'</span><br />';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
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
				//$this->_search_increment(1, 5, $smiles);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit, 'smiles');
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_041'; // QSearch: 0, SMILES: 4, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// SMILES invalido
			$html_result = '<p class="errorPane"><strong><span class="underlineText">The entered SMILES</span>
			- <span class="errorText">'. html_escape($smiles) .'</span> - is not valid. 
			Please go back and try again!</strong></p>';
		}
		else if ($result == 3)
		{
			// Nao existe SMILES similares
			$html_result = '<p class="errorPane"><strong>No compounds with similar SMILES: 
			<span class="errorText">'. html_escape($smiles) .'</span></strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_042'; // QSearch: 0, SMILES: 4, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
	/*
	 * Pesquisa rapida pelo InChi do composto
	 * 
	 * @param string $inchi InChi do composto
	 * 
	 * @return string Resultado da pesquisa
	 * 
	 */
	private function _qsearch_inchi($inchi, $layer)
	{
		$html_result;
		// Escolher a camada
		if ($layer > 0)
		{
			$inchi_array = explode('/', $inchi);
			if (count($inchi_array) > 1)
			{
				$inchi = '';
				for ($i = 0; $i <= $layer; $i++)
				{
					if ($i == $layer) {
						$inchi .= $inchi_array[$i];
					} else {
						$inchi .= $inchi_array[$i].'/';
					}
					
				}
			}
		}
		
		// * Efectua a pesquisa
		if ($layer == 1) {
			// Nao valida o InChi (1.ª camada)
			$result = $this->Molecule_model->find_by_inchi($inchi, FALSE);
		} else {
			$result = $this->Molecule_model->find_by_inchi($inchi);
		}
		
		if (is_array($result))
		{
			$linhas = $this->Molecule_model->get_num_rows();
			$html_result = '<p><strong>You are searching for:</strong> <span class="orangeText">'. html_escape($inchi) .'</span><br />';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				$html_result .= '<strong>.::. Number of compounds found: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
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
				//$this->_search_increment(1, 6, $inchi);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_051'; // QSearch: 0, InChi: 5, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// InChi invalido
			$html_result = '<p class="errorPane"><strong><span class="underlineText">
			Invalid InChi format</span>: <span class="errorText">'. html_escape($inchi) .'</span>.
			Make sure the InChi you have entered matches the format: InChi=1/... or InChi=1S/...</strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_052'; // QSearch: 0, InChi: 5, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
    /*
     * Incrementa o contador da procura
     *
     * @param int $search_type Metodo de pesquisa
     * @param int $term_type Tipo do termo
     * @param string $search_term Termo da pesquisa
     *
     * @return void
     */
    private function _search_increment($search_type, $term_type = 0, $search_term = '')
    {
        $fields = array('method' => $search_type, 'method_type' => NULL, 'search_detail' => NULL);
        
        if (! empty($term_type)) {
            $fields['method_type'] = $term_type;
        }
        
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
                log_message('error', "[QSearch] {$error_date} - [{$error_code}]: {$result['e_desc']}.");
            }
        }
        else
        {
            $error_date = date('Y-m-d');
            $error_code = 'Increment error';
			log_message('error', "[QSearch] {$error_date} - [{$error_code}]: An error occurred.");
        }
    }
    
	/*
	 * Formatar o resultado da pesquisa em HTML
	 * 
	 * @param array $data Resultado da pesquisa
	 * @param int $limit Limite do resultado a formatar
	 * @param string $type Tipo de resultado
	 * 
	 * @return string Resultado em HTML
	 */ 
	private function _html_result($data, $limit, $type = NULL) 
	{
		// * Mostrar os campos do resultado
		$html = '<table id="resultTable"><tbody>';
		for ($i = 0; $i < $limit; ++$i)
		{
			$mid = $data[$i]->mid;
			$name = $data[$i]->name;
			$colspan = ($type == 'smiles') ? 3 : 2;
			$num = $i + 1;
			
			// Numero da linha
			$html .= "<tr class='alt'><td class='index' colspan='{$colspan}'>{$num}.</td></tr>";
			
			// ThermInfo ID
			$html .= '<tr><th>ThermInfo ID:</th>';
			$html .= '<td>'. $data[$i]->therminfo_id .'</td>';
			
			// Imagem
			if ($type == 'smiles')
			{
				$html .= "<td rowspan='7' class='img'><img class='ImgBorder_1' alt='Chemical 
				Structure Image' title='{$data[$i]->therminfo_id} Image' src='image/compound/{$data[$i]->therminfo_id}' /></td>";
			}
			$html.= '</tr>';
			
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
			if ($type == 'smiles')
			{
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
			}
			
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

/* End of file qsearch.php */
/* Location: ./application/controllers/qsearch.php */