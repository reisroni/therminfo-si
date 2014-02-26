<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Data_type
 * Descricao: Modelo da tabela 'data_type'
 * Criado: 25-06-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Data_type_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $dtid;
	public $t_name;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'data_type';
		$this->id_field = 'dtid';
		$this->table_fields = array('dtid', 't_name');
		$this->id = &$this->dtid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Pesquisa pelo nome do tipo
     *
     * @param string $name Nome do tipo para pesquisa
     *
     * @return mixed Array com as tipos 
	 * encontrados ou '0' em caso de falha ou 
	 * '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Data_type_model))]
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
			$query = "SELECT * FROM {$this->table_name} WHERE t_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Tipos encontrados
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
				case 'dtid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Name
				case 't_name':
				{
					if (empty($value) or strlen($value) > 50)
					{
						return array('status' => FALSE, 
									'result' => 'class name',
									'error' => 'is empty or length greater than 50');
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

/* End of file data_type_model.php */
/* Location: ./application/models/data_type_model.php */