<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Serialize_values
 * Descricao: Modelo da tabela 'serialize_values'
 * Criado: 11-09-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Serialize_values_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $s_id;
	public $date;
	public $value;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'serialize_values';
		$this->id_field = 's_id';
		$this->table_fields = array('s_id', 'date', 'value');
		$this->id = &$this->s_id;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	
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
				case 's_id':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Date
				case 'date':
				{
					if (! empty($value) && ! preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not a valid date or in YYYY-MM-DD format');
					}
				} break;
				// Value
				case 'value':
				{
					if (empty($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty');
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

/* End of file serialize_values_model.php */
/* Location: ./application/models/serialize_values_model.php */