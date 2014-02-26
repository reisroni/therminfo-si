<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Othername
 * Descricao: Modelo da tabela 'othername'
 * Criado: 17-05-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Othername_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $oid;
	public $synonym;
	public $molecule;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('molecule/Molecule_model');
		// Inicializar alguns atributos
		$this->table_name = 'othername';
		$this->id_field = 'oid';
		$this->table_fields = array('oid', 'synonym', 'molecule');
		$this->id = &$this->oid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Pesquisa pelo ID da molecula
     *
     * @param int $mol_id ID da molecula para pesquisa
     *
     * @return mixed Array com os sinonimos 
	 * encontrados ou '0' em caso de falha ou 
	 * '1' se o ID da molecula estiver em branco
	 * 
	 * [Array([int] => object(Othername_model))]
     */
	public function find_by_mol($mol_id = 0)
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
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Sinonimos encontrados
		}
		return $result;
	}
	
	/**
     * Pesquisa pelo nome do sinonimo
     *
     * @param string $name Nome do sinonimo para pesquisa
     *
     * @return mixed Array com os sinonimos 
	 * encontrados ou '0' em caso de falha ou 
	 * '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Othername_model))]
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
			$query = "SELECT *, INSTR(synonym, '{$name}') AS pos FROM {$this->table_name} WHERE synonym LIKE '%{$name}%' ORDER BY pos";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Sinonimos encontrados
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
				case 'oid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Synonym
				case 'synonym':
				{
					if (empty($value) or strlen($value) > 255)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or length greater than 255');
					}
				} break;
				// Molecule
				case 'molecule':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or is not numeric');
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

/* End of file othername_model.php */
/* Location: ./application/models/othername_model.php */