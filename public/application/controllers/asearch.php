<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* asearch.php
* Controlador da pagina 'advanced search'
* Criado: 19-08-2011
* Modificado: 20-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Asearch extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('class' => NULL,
							'subclass' => NULL,
							'family' => NULL,
							'result' => NULL,
                            'count' => NULL);
		// Carregar os modelos
		$this->load->model('molecule/Molecule_model');
		$this->load->model('molecule/Class_model');
		$this->load->model('molecule/Subclass_model');
		$this->load->model('molecule/Family_model');
        $this->load->model('other/Session_model');
        $this->load->model('statistics/Contador_model');
        // Carregar os modulos
		$this->load->library('Util');
        // Estatistica do metodo
        $this->data['count'] = $this->Contador_model->count_search(2);
        // * Preenchimento das listas de classes, subclasses e familias *
		$x = $this->Class_model->find_all_distinct('c_name');
        $y = $this->Subclass_model->find_all_distinct('sc_name');
        $z = $this->Family_model->find_all_distinct('f_name');
		$this->data['class'] = '<option value="all">All</option>';
        $this->data['subclass'] = '<option value="all">All</option>';
        $this->data['family'] = '<option value="all">All</option>';
        
		foreach ($x as $c) {
			$this->data['class'] .= "<option value='{$c->c_name}'>{$c->c_name}</option>";
		}

		foreach ($y as $s) {
			$this->data['subclass'] .= "<option value='{$s->sc_name}'>{$s->sc_name}</option>";
		}

		foreach ($z as $f) {
			$this->data['family'] .= "<option value='{$f->f_name}'>{$f->f_name}</option>";
		}
		// * Fim do preenchimento *
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
				// Todos os campos
				$term = $this->input->post('compound') ? trim($this->input->post('compound')) : ''; // Termo da pesquisa
				$state = ($this->input->post('state') == 'all') ? '' : $this->input->post('state'); // Estado fisico
				$formula = $this->input->post('formula') ? trim($this->input->post('formula')) : ''; // Formula molecular
				$mwsign = $this->input->post('intervalmw'); // Intervalo do peso molecular
				$mw = $this->input->post('mw') ? trim($this->input->post('mw')) : ''; // Valor do peso molecular
				$smiles = $this->input->post('smiles') ? trim($this->input->post('smiles')) : ''; // SMILES
				$smiles_threshold = $this->input->post('threshold'); // Threshold do SMILES
				$class = ($this->input->post('classe') == 'all') ? '' : $this->input->post('classe'); // Classe
				$subclass = ($this->input->post('subclass') == 'all') ? '' : $this->input->post('subclass'); // Subclasse
				$family = ($this->input->post('family') == 'all') ? '' : $this->input->post('family'); // Familia
				
				if(isset($_POST['ajax']))
				{
					$charac = (isset($_POST['ch']) && ! empty($_POST['ch'])) ? explode(',',$_POST['ch']) : ''; // Caracteristicas
				}
				else
				{
					$charac = isset($_POST['ch']) ? $_POST['ch'] : ''; // Caracteristicas
				}
				
				// Efectua e formata a pesquisa
				$this->data['result'] = $this->_asearch($term, $state, $formula, $mwsign, $mw, $smiles, $smiles_threshold, $class, $subclass, $family, $charac);
				
				if (isset($_POST['ajax'])) 
				{
					$this->output->set_output($this->data['result']);
				}
				else
				{
					$this->load->view('content/asearch_view', $this->data);
				}
			}
			else
			{
				// Codido de seguranca invalido
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
					$this->load->view('content/asearch_view', $this->data);
				}
			}
		}
		else
		{
			// Pesquisa nao submetida (pagina 'Advanced Search')
			$this->load->view('content/asearch_view', $this->data);
		}
	}
	
	/*
	 * Pesquisa avancada
	 * 
	 * @param string $term Nome do composto
	 * @param string $state Estado fisico do composto
	 * @param string $formula Formula do composto
	 * @param string $mwsign Sinal para o peso molecular
	 * @param float $mw Peso molecular do composto
	 * @param string $smiles SMILES do composto
	 * @param string $smilesthreshold Threshold para o SMILES
	 * @param string $class Classe do composto
	 * @param string $subclass Subclasse do composto
	 * @param string $family Familia do composto
	 * @param array $charac Caracteristicas do composto
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _asearch($term, $state, $formula, $mwsign, $mw, $smiles, $smilesthreshold, $class, $subclass, $family, $charac)
	{
		$html_result;
		// * Prepara a mensagem de informacao
		$info = '';
		
		if (! empty($term)) {
			$info .= '- Compound: '. html_escape($term). '<br />';
		}
			
		if (! empty($state)) 
		{
			if ($state == 'l') {
				$s = 'Liquid';
			}
			if ($state == 'g') {
				$s = 'Gas';
			}
			if ($state == 'c') {
				$s = 'Crystal';
			}
			$info .= "- Physical state: {$s} <br />";
		}
		
		if (! empty($formula)) {
			$info .= '- Molecular Formula: '. html_escape($formula) .'<br />';
		}
		
		if (! empty($mw)) {
			$info .= '- Molecular weight: '. $mwsign. html_escape($mw) .'<br />';
		}
		
		if (! empty($smiles)) 
		{
			$info .= '- SMILES: '. html_escape($smiles). '<br />';
			
			if ($smilesthreshold == 1) {
				$info .= '- Identical compounds, with score &cong; 100% <br/>';
			} else if ($smilesthreshold == 'i1') {
				$info .= '- Similar structures, with score between &cong; 90% and 95% <br/>';
			} else if ($smilesthreshold == 'i2') {
				$info .= '- Similar structures, with score between &cong; 80% and 90% <br/>';
			} else if ($smilesthreshold == 'i3') {
				$info .= '- Similar structures, with score between &cong; 70% and 80% <br/>';
			} else {
				$t = $smilesthreshold * 100;
				$info .= "- Similar compounds, with score >= {$t}% <br />";
			}
		}
		
		if (! empty($class)) {
			$info .= "- Class: {$class} <br />";
		}
		
		if (! empty($subclass)) {
			$info .= "- Subclass: {$subclass} <br />";
		}
		
		if (! empty($family)) {
			$info .= "- Family: {$family} <br />";
		}
		
		if (! empty($charac)) 
		{
			foreach($charac as $char) {
				$info .= "- Characteristic: {$char} <br />";
			}
		}
		
		// * Efectua a pesquisa
		$result = $this->Molecule_model->advanced_search($term, $state, $formula, $mwsign, $mw, $smiles, $smilesthreshold, $class, $subclass, $family, $charac);
		
		if (is_array($result)) 
		{
			$linhas = $this->Molecule_model->get_num_results();
			$html_result = '<p><strong>You are searching for:</strong><br /><span class="orangeText">'. $info .'</span><br />';
			
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
				//$this->_search_increment(2);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else if ($result == 0)
		{
			// Erro na BD
			$error_code = 'TI_101'; // ASearch: 1, asearch: 0, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[ASearch] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($result == 1)
		{
			// Campos em branco
			$html_result = '<p class="errorPane"><strong>You have not entered 
			search details. Please go back and try again!</strong></p>';
		}
		else if ($result == 2)
		{
			// Sem moleculas com as carateristicas seleccionadas
			$html_result = '<p class="errorPane"><strong>No compounds with de selected characteristics</strong></p>';
		}
		else if ($result == 3)
		{
			// Formula molecular invalida
			$html_result = '<p class="errorPane"><strong><span class="underlineText">Invalid Molecular Formula</span>: 
			<span class="errorText">'. html_escape($formula) .'</span>. Make sure the Molecular Formula you have entered matches 
			the valid characters: C|H|B|F|I|P|N|O|S.</strong></p>';
		}
		else if ($result == 4)
		{
			// Peso Molecular invalido
			$html_result = '<p class="errorPane"><strong><span class="underlineText">Invalid Molecular Weight</span>: 
			<span class="errorText">'. html_escape($mw) .'</span>. Molecular  Weight has to be a numeric value.</strong></p>';
		}
		else if ($result == 5)
		{
			// SMILES invalido
			$html_result = '<p class="errorPane"><strong>The entered SMILES - 
			<span class="errorText">' . html_escape($smiles) .'</span> - is not valid. 
			Please go back and try again!</strong></p>';
		}
		else if ($result == 6)
		{
			// Nao existe SMILES similares
			$html_result = '<p class="errorPane"><strong>No compounds with similar SMILES: 
			<span class="errorText">'. html_escape($smiles) .'</span></strong></p>';
		}
		else
		{
			// Outra situacao
			$error_code = 'TI_102'; // ASearch: 1, asearch: 0, Other Error: 2
			$error_date = date('Y-m-d');
			log_message('error', "[ASearch] {$error_date} - [{$error_code}]: An error occurred.");
			$html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html_result;
	}
	
    /*
     * Incrementa o contador da procura
     *
     * @param int $search_type Metodo de pesquisa
     *
     * @return void
     */
    private function _search_increment($search_type)
    {
        $fields = array('method' => $search_type);
        
        $record = $this->Contador_model->instantiate($fields);
        $result = $record->save();
        
        if (is_array($result) && ! empty($result))
        {
            if (! $result['result'])
            {
                $error_date = date('Y-m-d');
                $error_code = 'Increment ' .$result['error'];
                log_message('error', "[ASearch] {$error_date} - [{$error_code}]: {$result['e_desc']}.");
            }
        }
        else
        {
            $error_date = date('Y-m-d');
            $error_code = 'Increment error';
			log_message('error', "[ASearch] {$error_date} - [{$error_code}]: An error occurred.");
        }
    }
    
	/*
	 * Formatar o resultado da pesquisa em HTML
	 * 
	 * @param array $data Resultado da pesquisa
	 * @param int $limit Limite do resultado a formatar
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
			$num = $i + 1;
			
			// Numero da linha
			$html .= '<tr class="alt"><td class="index" colspan="2">'. $num .'.</td></tr>';
			
			// ThermInfo ID
			$html .= '<tr><th>ThermInfo ID:</th>';
			$html .= '<td>'. $data[$i]->therminfo_id .'</td></tr>';
			
			// Name
			$html .= '<tr><th>Name:</th>';
			$html .= '<td>'. $data[$i]->name .'</td></tr>';
			
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

/* End of file asearch.php */
/* Location: ./application/controllers/asearch.php */