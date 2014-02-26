<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Other_db
 * Descricao: Modelo da tabela 'other_db'
 * Criado: 17-05-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Other_db_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $odb_id;
	public $molecule;
	public $db;
	public $value;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('molecule/Molecule_model');
		$this->load->model('molecule/Other_db_name_model');
		// Inicializar alguns atributos
		$this->table_name = 'other_db';
		$this->id_field = 'odb_id';
		$this->table_fields = array('odb_id', 'molecule', 'db', 'value');
		$this->id = &$this->odb_id;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Pesquisa pelo ID da molecula
     *
     * @param int $mol_id ID da molecula para pesquisa
     *
     * @return mixed Array com os dados de outras 
	 * BD encontradas ou '0' em caso de falha ou 
	 * '1' se o ID da molecula estiver em branco
	 * 
	 * [Array([int] => object(Other_db_model))]
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
			$name = $this->DB->escape($mol_id);
			// Query
			$query = "SELECT o.odb_id, o.molecule, n.db_name AS db, o.value FROM other_db AS o, other_db_name AS n WHERE o.molecule={$mol_id} AND o.db = n.odbn_id";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // BD encontradas
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
				case 'odb_id':
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
				// Database
				case 'db':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or is not numeric');
					}
					else
					{
						$db = $this->Other_db_name_model->find_by_id($value);
						if (! $db)
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
					if (! empty($value) && strlen($value) > 200)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 200');
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

/* End of file other_db_model.php */
/* Location: ./application/models/other_db_model.php */