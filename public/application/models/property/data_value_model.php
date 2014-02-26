<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Data_value
 * Descricao: Modelo da tabela 'molecule_data_ref'
 * Criado: 02-07-2013
 * Modificado: 23-01-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */
require_once(BASE_MODEL);

class Data_value_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $value_id;
	public $molecule;
	public $data;
	public $reference;
	public $value;
	public $error;
	public $obs;
	public $advised;
	public $validated;
	public $outdated;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('molecule/Molecule_model');
		$this->load->model('property/Data_model');
		$this->load->model('reference/Reference_model');
		// Inicializar alguns atributos
		$this->table_name = 'molecule_data_ref';
		$this->id_field = 'value_id';
		$this->table_fields = array('value_id', 'molecule', 'data', 
									'reference', 'value', 'error', 
									'obs', 'advised', 'validated', 'outdated');
		$this->id = &$this->value_id;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
	 * Retorna o numero de valores de uma proriedade da BD
	 * 
	 * @param int $prop_id ID da propriedade
	 * 
	 * @return int Numero dos registos
	 */
	public function count_prop_values($prop_id = 0)
	{
		$this->DB->where('data', $prop_id);
		$this->DB->where('value IS NOT NULL', NULL);
		return $this->DB->count_all_results($this->table_name);
	}
    
    /**
	 * Altera o valor do atributo 'validated' (validated = 1)
	 *
	 * @return mixed 'TRUE' se o utilizador valido
	 * ou Array com o estado da operacao
     *
     * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function validate()
	{
        if ($this->validated == 0) {
            $this->validated = 1;
            $result = $this->save();
        } else {
            $result = TRUE;
        }
		return $result;
	}
	
	/**
	 * Altera o valor do atributo 'outdated' (outdated = 1)
	 *
	 * @return mixed 'TRUE' se o utilizador desactualizado
	 * ou Array com o estado da operacao
     *
     * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function outdated()
	{
        if ($this->outdated == 0) {
            $this->outdated = 1;
            $result = $this->save();
        } else {
            $result = TRUE;
        }
		return $result;
	}
	
	/**
	 * Pesquisa de valores validados, por composto
     *
     * @param int $mol_id ID do composto para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limites)
     *
     * @return mixed Array com os valores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se o ID estiver em branco
	 * 
	 * [Array([int] => object(Data_value_model))] 
	 */
	public function find_by_mol($mol_id = 0, $limit = 0)
	{
		$result = 0;
		if (empty($mol_id))
		{
			$result = 1; // ID em branco
		}
		else
		{
			$mol_id = $this->DB->escape($mol_id);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE molecule={$mol_id}";
			$query .= " AND validated=1 AND outdated=0 GROUP BY data";
			if ($limit !== 0)
				$query .= " LIMIT {$limit}";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Valores encontrados
		}
		return $result;
	}
	
	/**
	 * Pesquisa de valores validados, por propriedade
     *
     * @param int $prop_id ID da propriedade para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limites)
     *
     * @return mixed Array com os valores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se o ID estiver em branco
	 * 
	 * [Array([int] => object(Data_value_model))] 
	 */
	public function find_by_prop($prop_id = 0, $limit = 0)
	{
		$result = 0;
		if (empty($prop_id))
		{
			$result = 1; // ID em branco
		}
		else
		{
			$prop_id = $this->DB->escape($prop_id);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE data={$prop_id}";
			$query .= " AND validated=1 AND outdated=0";
			if ($limit !== 0)
				$query .= " LIMIT {$limit}";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Valores encontrados
		}
		return $result;
	}
	
	/**
	 * Pesquisa de valores validados, por referencia
     *
     * @param int $ref_id ID da referencia para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limites)
     *
     * @return mixed Array com os valores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se o ID estiver em branco
	 * 
	 * [Array([int] => object(Data_value_model))] 
	 */
	public function find_by_reference($ref_id = 0, $limit = 0)
	{
		$result = 0;
		if (empty($ref_id))
		{
			$result = 1; // ID em branco
		}
		else
		{
			$ref_id = $this->DB->escape($ref_id);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE reference={$ref_id}";
			$query .= " AND validated=1 AND outdated=0";
			if ($limit !== 0)
				$query .= " LIMIT {$limit}";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Valores encontrados
		}
		return $result;
	}
	
	/**
	 * Pesquisa de valores validados, de uma propriedade 
	 * de um composto
     *
     * @param int $mol_id ID do composto
	 * @param int $prop_id ID da propriedade
     *
     * @return mixed Array com os valores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se algum ID estiver em branco
	 * 
	 * [Array([int] => object(Data_value_model))] 
	 */
	public function find_by_mol_prop($mol_id = 0, $prop_id = 0)
	{
		$result = 0;
		if (empty($mol_id) or empty($prop_id))
		{
			$result = 1; // ID em branco
		}
		else
		{
			$mol_id = $this->DB->escape($mol_id);
			$prop_id = $this->DB->escape($prop_id);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE molecule={$mol_id}";
			$query .= " AND data={$prop_id} AND validated=1 AND outdated=0";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Valores encontrados
		}
		return $result;
	}
	
	/**
	 * Pesquisa valores validados, das entalpias de um composto
     *
     * @param int $mol_id ID do composto para pesquisa
     *
     * @return mixed Array com os valores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se o ID estiver em branco
	 * 
	 * [Array([int] => object(Data_value_model))] 
	 */
	public function find_ent_values($mol_id = 0)
	{
		$result = 0;
		if (empty($mol_id))
		{
			$result = 1; // ID em branco
		}
		else
		{
			$mol_id = $this->DB->escape($mol_id);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE molecule={$mol_id}";
			$query .= " AND data IN(7, 8, 19) AND validated=1 AND outdated=0 AND advised='yes'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Valores encontrados
		}
		return $result;
	}
		
	//---------------------------------------------------------------
	// Metodos auxiliares
	//---------------------------------------------------------------
	/*
	 * @override
	 * Verifica os atributos e retorna uma lista com 
	 * todos os atributos tratados
	 *
	 * @param boolean $escape Escapa carateres especiais
	 *
	 * @return array Lista com os atributos tratados
	 *
	 * [Array('status' => [boolean], 'result' => [Array/String], 'error' => [String])]
	 */
	protected function sanitized_attributes($escape = TRUE)
	{
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value)
		{
			switch ($key)
			{
				// ID
				case 'value_id':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Molecule
				case 'molecule':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$mol = $this->Molecule_model->find_by_id($value);
						if (! $mol)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Data
				case 'data':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$data = $this->Data_model->find_by_id($value);
						if (! $data)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Reference
				case 'reference':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$ref = $this->Reference_model->find_by_id($value);
						if (! $ref)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Value
				case 'value':
				{
					if (! empty($value) && strlen($value) > 50)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 50');
					}
				} break;
				// Error
				case 'Error':
				{
					if (! empty($value) && strlen($value) > 10)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 10');
					}
				} break;
				// Advised
				case 'advised':
				{
					if (! empty($value) && ($value != 'yes' or $value != 'no'))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => "is not 'yes', 'no'");
					}
				} break;
				// Validated
				case 'validated':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
				} break;
				// Outdated
				case 'outdated':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
				} break;
			}
			
			if ($escape)
			{
				$clean_attributes[$key] = $this->db_escape($value);
			}
			else
			{
				$clean_attributes[$key] = $value;
			}
		}
		return array('status' => TRUE, 'result' => $clean_attributes, 'error' => NULL);
	}
}

/* End of file data_value_model.php */
/* Location: ./application/models/data_value_model.php */