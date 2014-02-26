<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * qsearch.php
 * Quick Search Service API
 * Criado: 29-07-2012
 * Modificado: 01-08-2012
 * Copyright (c) 2012, ThermInfo
 */
require APPPATH.'/libraries/REST_Controller.php';

class Qsearch extends REST_Controller
{
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->load->model('Qsearch_model');
		$this->Qsearch_model->setDatabase(HOST, USER, PASS, DB);
    }
	
	/**
	 * Pesquisa pela formula molecular
	 */
	public function formula_get()
	{
		if(! $this->get('query'))
        {
			// Query em branco
        	$data = array('status' => 'error', 'message' => 'you have not entered a query');
			$http_code = 400;
        }
		else
		{
			$search_query = $this->get('query');
			
			// * Efectua a pesquisa
			$search = $this->Qsearch_model->searchByFormula($search_query);
			if (is_array($search))
			{
				// * Resultado
				$result = $this->_format_result($search_query, $search);
				$data = $result['data'];
				$http_code = $result['http_code'];
			}
			else if ($search == 0)
			{
				// Erro na BD
				$data = array('status' => 'error', 'message' => 'an error occurred in the database');
				$http_code = 500;
			}
			else if ($search == 1)
			{
				// Formula Molecular invalida
				$data = array('status' => 'error', 'message' => 'invalid molecular formula');
				$http_code = 400;
			}
			else if ($search == 2)
			{
				// Query em branco
				$data = array('status' => 'error', 'message' => 'you have not entered a query');
				$http_code = 400;
			}
			else
			{
				// Outra situacao
				$data = array('status' => 'error', 'message' => 'it is currently not possible to show results');
				$http_code = 500;
			}
		}
		// * Mostra o resultado
		$this->response($data, $http_code);
	}
	
	/**
	 * Pesquisa pelo ThermInfo ID
	 */
	public function id_get()
	{
		if(! $this->get('query'))
        {
			// Query em branco
        	$data = array('status' => 'error', 'message' => 'you have not entered a query');
			$http_code = 400;
        }
		else
		{
			$search_query = $this->get('query');
			
			// * Efectua a pesquisa
			$search = $this->Qsearch_model->searchByID($search_query);
			if (is_array($search))
			{
				// * Resultado
				$result = $this->_format_result($search_query, $search);
				$data = $result['data'];
				$http_code = $result['http_code'];
			}
			else if ($search == 0)
			{
				// Erro na BD
				$data = array('status' => 'error', 'message' => 'an error occurred in the database');
				$http_code = 500;
			}
			else if ($search == 1)
			{
				// ThermInfo ID invalido
				$data = array('status' => 'error', 'message' => 'invalid ThermInfo ID');
				$http_code = 400;
			}
			else if ($search == 2)
			{
				// Query em branco
				$data = array('status' => 'error', 'message' => 'you have not entered a query');
				$http_code = 400;
			}
			else
			{
				// Outra situacao
				$data = array('status' => 'error', 'message' => 'it is currently not possible to show results');
				$http_code = 500;
			}
		}
		// * Mostra o resultado
		$this->response($data, $http_code);
	}
	
	/**
	 * Pesquisa pelo CAS RN
	 */
	public function casrn_get()
	{
		if(! $this->get('query'))
        {
			// Query em branco
        	$data = array('status' => 'error', 'message' => 'you have not entered a query');
			$http_code = 400;
        }
		else
		{
			$search_query = $this->get('query');
			
			// * Efectua a pesquisa
			$search = $this->Qsearch_model->searchByCasrn($search_query);
			if (is_array($search))
			{
				// * Resultado
				$result = $this->_format_result($search_query, $search);
				$data = $result['data'];
				$http_code = $result['http_code'];
			}
			else if ($search == 0)
			{
				// Erro na BD
				$data = array('status' => 'error', 'message' => 'an error occurred in the database');
				$http_code = 500;
			}
			else if ($search == 1)
			{
				// CASRN invalido
				$data = array('status' => 'error', 'message' => 'invalid CAS Registry Number format');
				$http_code = 400;
			}
			else if ($search == 2)
			{
				// Digito de controle do CASRN invalido
				$data = array('status' => 'error', 'message' => 'invalid CAS Registry Number');
				$http_code = 400;
				
			}
			else if ($search == 3)
			{
				// Campo em branco
				$data = array('status' => 'error', 'message' => 'you have not entered a query');
				$http_code = 400;
			}
			else
			{
				// Outra situacao
				$data = array('status' => 'error', 'message' => 'it is currently not possible to show results');
				$http_code = 500;
			}
		}
		// * Mostra o resultado
		$this->response($data, $http_code);
	}
	
	/**
	 * Pesquisa pelo SMILES
	 */
	public function smiles_get()
	{
		if(! $this->get('query'))
        {
			// Query em branco
        	$data = array('status' => 'error', 'message' => 'you have not entered a query');
			$http_code = 400;
        }
		else
		{
			// Descodificar a query
			$search_query = urldecode($this->get('query'));
			// Threshold
			if (! $this->get('threshold'))
			{
				$smiles_threshold = 1;
			}
			else
			{
				$smiles_threshold = $this->get('threshold');
			}
			
			// * Efectua a pesquisa
			$search = $this->Qsearch_model->searchBySmiles($search_query, $smiles_threshold);
			if (is_array($search))
			{
				// * Resultado
				$result = $this->_format_result($search_query, $search, 'smiles');
				$data = $result['data'];
				$http_code = $result['http_code'];
			}
			else if ($search === 0)
			{
				// Erro na BD
				$data = array('status' => 'error', 'message' => 'an error occurred in the database');
				$http_code = 500;
			}
			else if ($search === 1)
			{
				// SMILES invalido
				$data = array('status' => 'error', 'message' => 'the entered SMILES is not valid');
				$http_code = 400;
			}
			else if ($search === 2)
			{
				// Sem resultado, nao existe SMILES similares
				$data = array('status' => 'no results', 'message' => 'your query returned zero results');
				$http_code = 404;
			}
			else if ($search === 3)
			{
				// Query em branco
				$data = array('status' => 'error', 'message' => 'you have not entered a query');
				$http_code = 400;
			}
			else
			{
				// Outra situacao
				$data = array('status' => 'error', 'message' => 'it is currently not possible to show results');
				$http_code = 500;
			}
		}
		// * Mostra o resultado
		$this->response($data, $http_code);
	}
	
	/*
	 * Formata o resultado.
	 * 
	 * @param string $search_query Query
	 * @param array $search_result Resultado da query
	 * @param string $type Tipo do resultado
	 * 
	 * @return array Resultado formatado.
	 * 
	 * [Array('data' => [Array], 'http_code' => [int])]
	 */
	private function _format_result($search_query, $search_result, $type = NULL)
	{
		$linhas = $this->Qsearch_model->getNumOfFields();
		if ($linhas == 0)
		{
			// Sem resultado
			$data = array('query' => $search_query, 
						'status' => 'no results', 
						'message' => 'your query returned zero results');
			$result = array('data' => $data, 'http_code' => 404);
		}
		else
		{
			if ($linhas > 100)
			{
				// Mais de 100 resultados
				$limit = 100;
			}
			else
			{
				$limit = sizeof($search_result);
			}
			// * Formata o resultado
			$data = array('query' => $search_query, 'status' => "results $limit", 
						'result' => $this->_array_result($search_result, $limit, $type));
			$result = array('data' => $data, 'http_code' => 200); 
		}
		return $result;
	}
	
	/*
	 * Transformar o resultado da pesquisa em um array.
	 * 
	 * @param array $data Resultado da pesquisa
	 * @param int $limit Limite do resultado a formatar
	 * @param string $type Tipo de resultado
	 * 
	 * @return array Resultado.
	 * 
	 * [Array([int] => Array('row','name','formula',
	 * 'casrn','inchi','smiles','similarity','image_path',
	 * 'more_info'))]
	 */
	private function _array_result($data, $limit, $type = NULL)
	{
		// * Mostrar os campos do resultado
		$result = array();
		for ($i = 0; $i < $limit; ++$i)
		{
			$values = array();
			$num = $i + 1;
			$mid = $data[$i]->mid;
			
			// Numero da linha
			$values['row'] = $num;
			// ThermInfo ID
			$values['therminfo_id'] = $data[$i]->therminfo_id;
			// Name
			$values['name'] = $data[$i]->name;
			// Formula
			$values['formula'] = $data[$i]->formula;
			// CAS RN
			$values['casrn'] = $data[$i]->casrn;
			// InChi
			$values['inchi'] = $data[$i]->s_inchi;
			// SMILES
			$values['smiles'] = $data[$i]->smiles;
			// Similaridade
			if ($type == 'smiles')
			{
				$similar_smiles = $this->Qsearch_model->getSimilarSmiles();
				foreach ($similar_smiles as $ss)
				{
					$sss = explode(' ', $ss);
					$sim = $sss[0];
					$midsmi = $sss[1];
					
					if ($mid == $midsmi)
						$similaridade = $sim;
				}
			
				$sim_f = $similaridade * 100;
				$values['similarity'] = "$sim_f %";
			}
			// Imagem
			$values['image_path'] = base_url("image/compound/$mid");
			// Ficha do composto
			$values['more_info'] = base_url("api/compound/id/query/$mid");
			
			array_push($result, $values);
		}
		return $result;
	}
}

/* End of file qsearch.php */
/* Location: ./application/controllers/api/qsearch.php */