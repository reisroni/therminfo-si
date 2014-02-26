<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Characteristic
 * Descricao: Modelo da tabela 'characteristic'
 * Criado: 17-05-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Characteristic_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $cid;
	public $ch_name;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'characteristic';
		$this->id_field = 'cid';
		$this->table_fields = array('cid', 'ch_name');
		$this->id = &$this->cid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Pesquisa pelo nome da caracteristica
     *
     * @param string $name Nome da caracteristica para pesquisa
     *
     * @return mixed Array com as caracteristicas 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Characteristic_model))]
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
			$query = "SELECT * FROM {$this->table_name} WHERE ch_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Caracteristicas encontradas
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
				case 'cid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Name
				case 'ch_name':
				{
					if (empty($value) or strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => 'char name',
									'error' => 'is empty or length greater than 100');
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

/* End of file characteristic_model.php */
/* Location: ./application/models/characteristic_model.php */