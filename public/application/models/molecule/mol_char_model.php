<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Mol_char
 * Descricao: Modelo da tabela 'mol_char'
 * Criado: 11-09-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Mol_char_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $molecule;
	public $charact;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('molecule/Molecule_model');
		$this->load->model('molecule/Characteristic_model');
		// Inicializar alguns atributos
		$this->table_name = 'mol_char';
		$this->table_fields = array('molecule', 'charact');
		// Chave primaria composta
		$this->id_field = '';
		$this->id = 0;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
	 * @override
	 * Cria ou actualiza um registo na BD
	 *
	 * @return boolean 'TRUE' para sucesso
	 * ou 'FALSE' em caso de falha
	 */
	public function save()
	{
		// Cria um registo
		return $this->create();
	}
	
	/**
	 * @override
	 * Sem implementacao
	 *
	 * @return boolean 'FALSE'
	 */
	public function update()
	{
		return FALSE;
	}
	
	/**
	 * @override
	 * Apaga um registo da BD
	 *
	 * @return boolean 'TRUE' para sucesso
	 * ou 'FALSE' em caso de falha
	 */
	public function delete()
	{
		$sql = "DELETE FROM {$this->table_name}";
		$sql .= ' WHERE molecule=' . $this->DB->escape($this->molecule);
		$sql .= ' AND charact=' . $this->DB->escape($this->charact);
		$sql .= ' LIMIT 1';
		return $this->DB->query($sql);
	}
	
	/**
	 * @override
	 * Retorna um registo da BD pelo ID
	 *
	 * @param int $id1 ID 1 do registo
	 * @param int $id2 ID 2 do registo
	 *
	 * @return mixed Objecto que representa
	 * o registo ou 'FALSE' em caso de falha
	 *
	 * [Object(registo)]
	 */
	public function find_by_id($id1 = 0, $id2 = 0)
	{
		$result_array = $this->find_by_sql("SELECT * FROM {$this->table_name} WHERE molecule={$id1} AND charact={$id2} LIMIT 1");
		return ! empty($result_array) ? array_shift($result_array) : FALSE;
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
				// Molecule
				case 'molecule':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$molecule = $this->Molecule_model->find_by_id($value);
						if (! $molecule)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Charact
				case 'charact':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$char = $this->Characteristic_model->find_by_id($value);
						if (! $char)
						{
							return array('status' => FALSE, 
										'result' => 'characteristic',
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

/* End of file mol_char_model.php */
/* Location: ./application/models/mol_char_model.php */