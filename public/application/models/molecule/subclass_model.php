<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Subclass
 * Descricao: Modelo da tabela 'subclass'
 * Criado: 17-05-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Subclass_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $scid;
	public $sc_name;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'subclass';
		$this->id_field = 'scid';
		$this->table_fields = array('scid', 'sc_name');
		$this->id = &$this->scid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Pesquisa pelo nome da subclasse
     *
     * @param string $name Nome da subclasse para pesquisa
     *
     * @return mixed Array com as subclasses 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Subclass_model))]
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
			$query = "SELECT * FROM {$this->table_name} WHERE sc_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Subclasses encontradas
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
				case 'scid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Name
				case 'sc_name':
				{
					if (empty($value) or strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => 'subclass name',
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

/* End of file subclass_model.php */
/* Location: ./application/models/subclass_model.php */