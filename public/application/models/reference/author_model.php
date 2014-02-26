<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Author
 * Descricao: Modelo da tabela 'author'
 * Criado: 01-07-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Author_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $athid;
	public $a_first_name;
	public $a_last_name;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'author';
		$this->id_field = 'athid';
		$this->table_fields = array('athid', 'a_first_name', 'a_last_name');
		$this->id = &$this->athid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Retorna o nome completo do author
	 *
	 * @return string Nome completo
     */
	public function full_name()
	{
		if (isset($this->a_first_name) && isset($this->a_last_name))
		{
			return trim($this->a_first_name) . ' ' . trim($this->a_last_name);
		}
		else
		{
			return '';
		}
	}
	
	/**
     * Pesquisa pelo primeiro nome do autor
     *
     * @param string $name Nome do autor para pesquisa
     *
     * @return mixed Array com os autores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Author_model))]
     */
	public function find_by_first_name($name = '')
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
			$query = "SELECT * FROM {$this->table_name} WHERE a_first_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Autores encontrados
		}
		return $result;
	}
	
	/**
     * Pesquisa pelo ultimo nome do autor
     *
     * @param string $name Nome do autor para pesquisa
     *
     * @return mixed Array com os autores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Author_model))]
     */
	public function find_by_last_name($name = '')
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
			$query = "SELECT * FROM {$this->table_name} WHERE a_last_name LIKE '%{$name}%'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Autores encontrados
		}
		return $result;
	}
    
    /**
     * Pesquisa pelos nomes do autor
     *
     * @param string $first_name 1.º nome do autor para pesquisa
     * @param string $last_name 2.º nome do autor para pesquisa
     *
     * @return mixed Array com os autores 
	 * encontrados ou '0' em caso de falha 
	 * ou '1' se os nomes estiverem em branco
	 * 
	 * [Array([int] => object(Author_model))]
     */
	public function find_by_names($first_name = '', $last_name = '')
	{
		$result = 0;
        if (empty($first_name) && empty($last_name))
		{
            $result = 1; // Nomes em branco
        }
		else
		{
			$first_name = $this->DB->escape_like_str($first_name);
            $last_name = $this->DB->escape_like_str($last_name);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE a_first_name LIKE '%{$first_name}%' ";
			$query .= "AND a_last_name LIKE '%{$last_name}%'";
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Autores encontrados
		}
		return $result;
	}
	
	/**
     * Pesquisa pelo ID de uma referencia
     *
     * @param int $id ID da referencia para pesquisa
     *
     * @return mixed Array com os autores 
	 * encontrados ou '0' em caso de falha ou 
	 * '1' se o ID da referencia estiver em branco
	 * 
	 * [Array([int] => object(Author_model))]
     */
	public function find_by_reference($id = 0)
	{
		$result = 0;
        if (empty($id))
		{
            $result = 1; // ID em branco
        }
		else
		{
			$id = $this->DB->escape($id);
			// Query
			$query = "SELECT * FROM {$this->table_name}, author_ref WHERE athid=author AND reference={$id}";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Autores encontrados
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
				case 'athid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// First Name
				case 'a_first_name':
				{
					if (empty($value) or strlen($value) > 40)
					{
						return array('status' => FALSE, 
									'result' => 'first name',
									'error' => 'is empty or length greater than 40');
					}
				} break;
				// Last Name
				case 'a_last_name':
				{
					if (empty($value) or strlen($value) > 40)
					{
						return array('status' => FALSE, 
									'result' => 'last name',
									'error' => 'is empty or length greater than 40');
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

/* End of file author_model.php */
/* Location: ./application/models/author_model.php */