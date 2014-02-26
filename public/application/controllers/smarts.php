<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* smarts.php
* Controlador da pagina 'SMARTS Search'
* Criado: 12-07-2012
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Smarts extends CI_Controller {
	
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
		$this->load->library('OBabel');
        $this->load->library('Util');
        // Estatistica do metodo
        $this->data['count'] = $this->Contador_model->count_search(5);
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			// ** Verifica o codigo de seguranca
			if ($this->Session_model->capcha_code() == $_POST['vercode'] && $this->Session_model->check_capcha())
			{
				$option_1 = $this->input->post('option_1'); // Opcao 1
				$option_2 = $this->input->post('option_2'); // Opcao 2
				$smarts_1 = trim($this->input->post('smarts_1')); // SMARTS 1
				$smarts_2 = ($option_1 != 'none') ? trim($this->input->post('smarts_2')) : ''; // SMARTS 2
				$smarts_3 = ($option_2 != 'none') ? trim($this->input->post('smarts_3')) : ''; // SMARTS 3
				
				// * Efectua e formata a pesquisa
				$this->data['result'] = $this->_search_smarts($smarts_1, $smarts_2, $smarts_3, $option_1, $option_2);
				
				
				if (isset($_POST['ajax'])) 
				{
					$this->output->set_output($this->data['result']);
				}
				else
				{
					$this->load->view('content/smarts_view', $this->data);
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
					$this->load->view('content/smarts_view', $this->data);
				}
			}
		}
		else
		{
			// Pesquisa nao submetida (pagina 'Smarts')
			$this->load->view('content/smarts_view', $this->data);
		}
	}
	
	/*
	 * Pesquisa por SMARTS
	 * 
	 * @param string $smarts_1 SMARTS para pesquisa
	 * @param string $smarts_2 SMARTS para pesquisa
	 * @param string $smarts_3 SMARTS para pesquisa
	 * @param string $option_1 Opcao para pesquisa (AND, OR)
	 * @param string $option_2 Opcao para pesquisa (AND, OR)
	 * 
	 * @return string Resultado da pesquisa
	 */
	private function _search_smarts($smarts_1, $smarts_2, $smarts_3, $option_1, $option_2)
	{
        $html_result;
		if (empty($smarts_1))
		{
			$linhas = FALSE;
			$result = FALSE;
			// Erro no campo 1
			$field_1_error = 2;
		}
		else
		{
			// Pesquisa SMARTS 1
			$search_result_1 = $this->obabel->calc_smarts($smarts_1);
			if (empty($smarts_2) && empty($smarts_3))
			{
				if (is_array($search_result_1))
				{
					$result = $search_result_1;
					$linhas_1 = sizeof($search_result_1);
					$linhas = sizeof($result);
				}
				else
				{
					$linhas = FALSE;
					$result = FALSE;
					// Erro no campo 1
					$field_1_error = 1;
				}
			}
			else if (! empty($smarts_2) && empty($smarts_3))
			{
				// Pesquisa SMARTS 2
				$search_result_2 = $this->obabel->calc_smarts($smarts_2);
				if ($option_1 == 'AND')
				{
					if (is_array($search_result_1) && is_array($search_result_2))
					{
						$result = $this->_array_add($search_result_1, $search_result_2);
						$linhas_1 = sizeof($search_result_1);
						$linhas_2 = sizeof($search_result_2);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 2
						if (! is_array($search_result_2)) {
							$field_2_error = 1;
                        }
					}
				}
				else
				{
					if (is_array($search_result_1) && is_array($search_result_2))
					{
						$result = $this->_array_union($search_result_1, $search_result_2);
						$linhas_1 = sizeof($search_result_1);
						$linhas_2 = sizeof($search_result_2);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 2
						if (! is_array($search_result_2)) {
							$field_2_error = 1;
                        }
					}
				}
			}
			else if (empty($smarts_2) && ! empty($smarts_3))
			{
				// Pesquisa SMARTS 3
				$search_result_3 = $this->obabel->calc_smarts($smarts_3);
				if ($option_2 == 'AND')
				{
					if (is_array($search_result_1) && is_array($search_result_3))
					{
						$result = $this->_array_add($search_result_1, $search_result_3);
						$linhas_1 = sizeof($search_result_1);
						$linhas_3 = sizeof($search_result_3);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 3
						if (! is_array($search_result_3)) {
							$field_3_error = 1;
                        }
					}
				}
				else
				{
					if (is_array($search_result_1) && is_array($search_result_3))
					{
						$result = $this->_array_union($search_result_1, $search_result_3);
						$linhas_1 = sizeof($search_result_1);
						$linhas_3 = sizeof($search_result_3);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 3
						if (! is_array($search_result_3)) {
							$field_3_error = 1;
                        }
					}
				}
			}
			else if (! empty($smarts_2) && ! empty($smarts_3))
			{
				// Pesquisa SMARTS 2 e 3
				$search_result_2 = $this->obabel->calc_smarts($smarts_2);
				$search_result_3 = $this->obabel->calc_smarts($smarts_3);
				if ($option_1 == 'AND' && $option_2 == 'AND')
				{
					if (is_array($search_result_1) && is_array($search_result_2) && is_array($search_result_3))
					{
						$result = $this->_array_add($search_result_1, $search_result_2, $search_result_3);
						$linhas_1 = sizeof($search_result_1);
						$linhas_2 = sizeof($search_result_2);
						$linhas_3 = sizeof($search_result_3);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 2
						if (! is_array($search_result_2)) {
							$field_2_error = 1;
                        }
						// Erro no campo 3
						if (! is_array($search_result_3)) {
							$field_3_error = 1;
                        }
					}
				}
				else if ($option_1 == 'AND' && $option_2 == 'OR')
				{
					if (is_array($search_result_1) && is_array($search_result_2) && is_array($search_result_3))
					{
						$result = $this->_array_add($search_result_1, $search_result_2);
						$result = $this->_array_union($result, $search_result_3);
						$linhas_1 = sizeof($search_result_1);
						$linhas_2 = sizeof($search_result_2);
						$linhas_3 = sizeof($search_result_3);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 2
						if (! is_array($search_result_2)) {
							$field_2_error = 1;
                        }
						// Erro no campo 3
						if (! is_array($search_result_3)) {
							$field_3_error = 1;
                        }
					}
				}
				else if ($option_1 == 'OR' && $option_2 == 'AND')
				{
					if (is_array($search_result_1) && is_array($search_result_2) && is_array($search_result_3))
					{
						$result = $this->_array_union($search_result_1, $search_result_2);
						$result = $this->_array_add($result, $search_result_3);
						$linhas_1 = sizeof($search_result_1);
						$linhas_2 = sizeof($search_result_2);
						$linhas_3 = sizeof($search_result_3);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 2
						if (! is_array($search_result_2)) {
							$field_2_error = 1;
                        }
						// Erro no campo 3
						if (! is_array($search_result_3)) {
							$field_3_error = 1;
                        }
					}
				}
				else if ($option_1 == 'OR' && $option_2 == 'OR')
				{
					if (is_array($search_result_1) && is_array($search_result_2) && is_array($search_result_3))
					{
						$result = $this->_array_union($search_result_1, $search_result_2, $search_result_3);
						$linhas_1 = sizeof($search_result_1);
						$linhas_2 = sizeof($search_result_2);
						$linhas_3 = sizeof($search_result_3);
						$linhas = sizeof($result);
					}
					else
					{
						$linhas = FALSE;
						$result = FALSE;
						// Erro no campo 1
						if (! is_array($search_result_1)) {
							$field_1_error = 1;
                        }
						// Erro no campo 2
						if (! is_array($search_result_2)) {
							$field_2_error = 1;
                        }
						// Erro no campo 3
						if (! is_array($search_result_3)) {
							$field_3_error = 1;
                        }
					}
				}
			}
		}
		
		if ($result !== FALSE)
		{
			$html_result = '<p><strong>You are searching for compounds with the following substructure(s):</strong><br />';
			$html_result .= '<strong>SMARTS:</strong> <span class="orangeText">'. html_escape($smarts_1) .'</span>';
			if (! empty($smarts_2)) {
				$html_result .= " {$option_1} <span class='orangeText'>". html_escape($smarts_2) .'</span>';
            }
			if (! empty($marts_3)) {
				$html_result .= " {$option_2} <span class='orangeText'>". html_escape($smarts_3) .'</span> ';
            }
			$html_result .= '<br /><br /></p>';
			
			if ($linhas == 0)
			{
				// Sem resultado
				$html_result .= '<p><strong>Sorry, your search returned <span class="underlineText">zero</span> results<strong></p>';
			}
			else
			{
				if (empty($smarts_2) && empty($smarts_2))
				{
					$html_result .= '<p><strong>.::.Number of compounds containing <span class="orangeText">'. html_escape($smarts_1) ."</span>: {$linhas} .::.</strong><br /><br /></p>";
				}
				else
				{
					$html_result .= '<p>Number of compounds containing <span class="orangeText">'. html_escape($smarts_1) ."</span>: {$linhas_1}</p>";
					$str = '<span class="orangeText">'. html_escape($smarts_1) .'</span>';
				}
				
				if (! empty($smarts_2))
				{
					$html_result .= '<p>Number of compounds containing <span class="orangeText">'. html_escape($smarts_2) ."</span>: {$linhas_2}</p>";
					$str .= " {$option_1} <span class='orangeText'>". html_escape($smarts_2) .'</span>';
				}
				if (! empty($smarts_3))
				{
					$html_result .= '<p>Number of compounds containing <span class="orangeText">'. html_escape($smarts_3) ."</span>: {$linhas_3}</p>";
					$str .=  " {$option_2} <span class='orangeText'>". html_escape($smarts_3) .'</span>';
				}
				if (! empty($smarts_2) or ! empty($smarts_3))
					$html_result .= "<p><strong>.::. Number of compounds containing {$str}: {$linhas} .::.</strong><br /><br /></p>";
				
				if ($linhas > 100) 
				{
					// Mais de 100 resultados
					$html_result .= '<p>* Displaying the <span class="orangeText">100</span> 
					most relevant compounds retrieved from database *</p>';
					$limit = 100;
				}
				else
				{
					$limit = $linhas;
				}
				// Incrementa a pesquisa
				//$this->_search_increment(5);
				// * Formata o resultado
				$html_result .= $this->_html_result($result, $limit);
			}
		}
		else
		{
			$html_result = '<p class="errorPane">';
			if (isset($field_1_error)) {
				$html_result .= $this->_html_error($field_1_error, 1);
			}
			
			if (isset($field_2_error)) {
				$html_result .= $this->_html_error($field_2_error, 2);
			}
			
			if (isset($field_3_error)) {
				$html_result .= $this->_html_error($field_3_error, 3);
			}
			$html_result .= '</p>';
		}
        return $html_result;
	}
	
	/*
	 * Adicionar 2 ou 3 array (AND)
	 * 
	 * @param array $a1 Primeiro array
	 * @param array $a2 Segundo array
	 * @param array $a3 Terceiro array
	 * 
	 * @return array O resultado da adicao
	 */
	private function _array_add($a1, $a2, $a3 = NULL)
	{
		$result = array();
		if (! isset($a3))
		{
			foreach ($a1 as $k => $v)
			{
				if (array_key_exists($k, $a2))
				{
					$result[$k] = $v + $a2[$k];
				}
			}
		}
		else
		{
			foreach ($a1 as $k => $v)
			{
				if (array_key_exists($k, $a2) && array_key_exists($k, $a3))
				{
					$result[$k] = $v + $a2[$k] + $a3[$k];
				}
			}
		}
		return $result;
    }
	
	/*
	 * Unir 2 ou 3 array (OR)
	 * 
	 * @param array $a1 Primeiro array
	 * @param array $a2 Segundo array
	 * @param array $a3 Terceiro array
	 * 
	 * @return array O resultado da uniao
	 */
	private function _array_union($a1, $a2, $a3 = NULL)
	{
		$result = $a2;
		foreach ($a1 as $k => $v)
		{
			if (array_key_exists($k, $a2))
			{
				$result[$k] = $v + $a2[$k];
			}
			else
			{
				$result[$k] = $v;
			}
		}
		
		if (isset($a3))
		{
			foreach ($a3 as $k => $v)
			{
				if (array_key_exists($k, $result))
				{
					$result[$k] = $v + $a3[$k];
				}
				else
				{
					$result[$k] = $v;
				}
			}
		}
		return $result;
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
                log_message('error', "[SMARTS] {$error_date} - [{$error_code}]: {$result['e_desc']}.");
            }
        }
        else
        {
            $error_date = date('Y-m-d');
            $error_code = 'Increment error';
			log_message('error', "[SMARTS] {$error_date} - [{$error_code}]: An error occurred.");
        }
    }
    
	/*
	 * Mostra os erros em HTML
	 * 
	 * @param int $erro Codigo do erro
	 * @param int $field O campo que emitiu o erro
	 * 
	 * @return string Resultado em HTML
	 * 
	 */
	private function _html_error($error, $field)
	{
		$html = '';
		if ($error == 0)
		{
			// Erro na BD
            $error_code = 'TI_401'; // Smarts: 4, search_smarts: 0, DB Error: 1
			$error_date = date('Y-m-d');
			log_message('error', "[Smarts] {$error_date} - [{$error_code}]: An error occurred in the database.");
			$html .= '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
			'. If persists send us an email</strong></p>';
		}
		else if ($error == 1)
		{
			// Campo invalido
			$html .= '<strong><span class="underlineText">The field '. $field .'</span></strong> is not valid. Please go back and try again!<br />';
		}
		else if ($error == 2)
		{
			// Campo em branco
			$html .= '<strong>You have not entered search details. Please try again!</strong><br />';
		}
		else
		{
			// Outra situacao
            $error_code = 'TI_402'; // Smarts: 4, search_smarts: 0, Other Error: 2
			$error_date = date('Y-m-d');
            log_message('error', "[Smarts] {$error_date} - [{$error_code}]: An error occurred.");
			$html .= '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
			If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
		}
		return $html;
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
		arsort($data);
		$html = '<table id="resultTable"><tbody>';
		$i = 1;
		foreach ($data as $mid => $num_smarts)
		{
			$mol = $this->Molecule_model->find_by_id($mid);
			if (! empty($mol))
			{
				$num = $i;
				
				// Numero da linha
				$html .= '<tr class="alt"><td class="index" colspan="3">'. $num .'.</td></tr>';
				
				// ThermInfo ID
				$html .= '<tr><th>ThermInfo ID:</th>';
				$html .= '<td>'. $mol->therminfo_id .'</td>';
				
				// Imagem
				$html .= "<td rowspan='7' class='img'><img class='ImgBorder_1' alt='Chemical Structure Image' 
				title='{$mol->therminfo_id} Image' src='image/compound/{$mol->therminfo_id}' /></td></tr>";
				
				// Name
				$html .= '<tr><th>Name:</th>';
				$html .= '<td>'. $mol->name .'</td></tr>';
				
				// Formula
				$tmp = str_split(stripslashes($mol->formula));
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
				$html .= '<td>'. $mol->mw .'</td></tr>';
				
				// SMILES
				$smi = $mol->smiles;
				$html .= '<tr><th>SMILES:</th>';
				$html .= "<td><div class='smiles'><a class='lnk' name='{$smi}' title='{$smi}'>{$smi}</a></div></td></tr>";
				
				// Numero de SMARTS
				$html .= '<tr><th>SMARTS:</th>';
				$html .= '<td>'. $num_smarts .'</td></tr>';
				
				// Ficha do composto
				$html .= '<tr><th>More Info:</th>';
				$html .= "<td><form action='". base_url("compound/view/{$mol->therminfo_id}") ."' 
				method='post' target='_blank'><input type='submit' class='btTxt clickable' 
				value='View' title='View more' /></form></td></tr>";
			}
			
			if ($i < $limit)
			{
				++$i;
			}
			else
			{
				break;
			}
		}
		$html .= '</tbody></table>';
		
		return $html;
	}
}

/* End of file smarts.php */
/* Location: ./application/controllers/smarts.php */