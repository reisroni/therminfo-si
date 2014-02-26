<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- DB Evolution
 * Descricao: Modelo da tabela 'dbevolution'
 * Criado: 02-07-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Dbevolution_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $eid;
	public $month;
	public $year;
	public $nrcompounds;
	public $nrcompusers;
	public $last_update;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'dbevolution';
		$this->id_field = 'eid';
		$this->table_fields = array('eid', 'month', 'year', 'nrcompounds', 
									'nrcompusers', 'last_update');
		$this->id = &$this->eid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
	 * Retorna todos os anos da tabela 'dbevolution'
	 * 
	 * @return mixed Array com os anos
	 * ou 'FALSE' em caso de falha
	 */
	public function find_all_years()
	{
		// Query
		$this->DB->select('year')->distinct()->order_by('year', 'desc');
		$query = $this->DB->get('dbevolution');
		
		if ($query && $query->num_rows() > 0)
		{
			$result = array();
			foreach ($query->result() as $row) 
			{
				if (! empty($row->year))
				{
					array_push($result, $row->year); // Anos
				}
			}
		}
		else
		{
			$result = FALSE;
		}
        return $result;
	}
	
	/**
	 * Retorna o numero de compostos inseridos
	 * na BD (por ano e por mes)
	 * 
	 * @param int $year O ano
	 * @param int $month O mes
	 *
	 * @return mixed Array com o numero dos 
	 * compostos ou '0' em caso de falha ou 
	 * '1' se o ano estiver em branco
	 * 
	 * [Array([int] => object(Dbevolution_model))]
	 */
	public function find_evolution($year = 0, $month = 0)
	{
		$result = 0;
		if (empty($year))
		{
            $result = 1; // Ano em branco
        }
		else
		{
			$year = $this->DB->escape($year);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE year={$year}";
			if (! empty($month))
			{
				$month = $this->DB->escape($month);
				$query .= " AND month={$month}";
			}
			$query .= ' ORDER BY eid DESC';
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Numero de compostos
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
				case 'eid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Month
				case 'month':
				{
					if (empty($value))
					{
						$value = (int) date('m');
					}
					elseif (! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
				} break;
				// Year
				case 'year':
				{
					if (empty($value))
					{
						$value = (int) date('Y');
					}
					elseif (! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
				} break;
				// nrcompounds
				case 'nrcompounds':
				{
					if (! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
					else
					{
						$value = $this->DB->count_all('molecule');
					}
				} break;
				// nrcompusers
				case 'nrcompusers':
				{
					if (! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or is not numeric');
					}
				} break;
				// Last update
				case 'last_update':
				{
					if (empty($value))
					{
						$value = date('Y-m-d H:i:s');
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

/* End of file dbevolution_model.php */
/* Location: ./application/models/dbevolution_model.php */