<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Data
 * Descricao: Modelo da tabela 'data'
 * Criado: 25-06-2013
 * Modificado: 10-01-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */
require_once(BASE_MODEL);

class Data_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $did;
	public $d_name;
    public $d_full_name;
	public $units;
	public $type;
	public $is_numeric;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('property/Data_type_model');
		// Inicializar alguns atributos
		$this->table_name = 'data';
		$this->id_field = 'did';
		$this->table_fields = array('did', 'd_name', 'd_full_name', 'units', 'type', 'is_numeric');
		$this->id = &$this->did;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Pesquisa pelo nome da propriedade
     *
     * @param string $name Nome da propriedade para pesquisa
     *
     * @return mixed Array com as propriedades 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Data_model))]
     */
	public function find_by_name($name = '')
	{
		$result = 0;
        if (empty($name))
		{
            $result = 1; // Nome em branco
        }
		else
		{
			$name = $this->DB->escape_like_str($name);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE d_name LIKE '%{$name}%' 
                        OR d_full_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Classes encontradas
		}
		return $result;
	}
	
	/**
     * Pesquisa pelo ID do tipo de propriedade
     *
     * @param int $type_id ID do tipo para pesquisa
     *
     * @return mixed Array com as propriedades 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o ID estiver em branco
	 * 
	 * [Array([int] => object(Data_model))]
     */
	public function find_by_type($type_id = 0)
	{
		$result = 0;
        if (empty($type_id))
		{
            $result = 1; // Tipo em branco
        }
		else
		{
			$type_id = $this->DB->escape($type_id);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE type={$type_id}";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Propriedades encontradas
		}
		return $result;
	}
	
	/**
     * Pesquisa pelo nome do tipo de propriedade
     *
     * @param string $name Nome do tipo para pesquisa
     *
     * @return mixed Array com as propriedades 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o ID estiver em branco
	 * 
	 * [Array([int] => object(Data_model))]
     */
	public function find_by_type_name($name = '')
	{
		$result = 0;
        if (empty($name))
		{
            $result = 1; // Tipo em branco
        }
		else
		{
			$name = $this->DB->escape_like_str($name);
			// Query
			$query = "SELECT * FROM {$this->table_name}, data_type WHERE type=dtid AND t_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Propriedades encontradas
		}
		return $result;
	}
	
	/**
     * Pesquisa de propriedades numericas
     *
     * @return mixed Array com as propriedades 
	 * encontradas ou '0' em caso de falha
	 * 
	 * [Array([int] => object(Data_model))]
     */
	public function find_numeric_props()
	{
		$result = 0;
		// Query
		$query = "SELECT * FROM {$this->table_name} WHERE is_numeric = 1";
        
		// Interrogar a BD
		$queryResult = $this->find_by_sql($query);
		if (is_array($queryResult))
			$result = $queryResult; // Propriedades encontradas
		
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
				case 'did':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Name
				case 'd_name':
				{
					if (empty($value) or strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => 'data name',
									'error' => 'is empty or length greater than 100');
					}
				} break;
                // Full name
				case 'd_full_name':
				{
					if (! empty($value) && strlen($value) > 200)
					{
						return array('status' => FALSE, 
									'result' => 'data full name',
									'error' => 'length greater than 200');
					}
				} break;
				// Units
				case 'units':
				{
					if (! empty($value) && strlen($value) > 45)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 45');
					}
				} break;
				// Type
				case 'type':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$type = $this->Data_type_model->find_by_id($value);
						if (! $type)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Numeric
				case 'is_numeric':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'numeric indicator',
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

/* End of file data_model.php */
/* Location: ./application/models/data_model.php */