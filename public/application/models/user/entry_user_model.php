<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Entry_user
 * Descricao: Modelo da tabela 'entry_user'
 * Criado: 19-08-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Entry_user_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $user;
	public $value_entry;
	public $create_date;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('user/User_model');
		$this->load->model('property/Data_value_model');
		// Inicializar alguns atributos
		$this->table_name = 'entry_user';
		$this->table_fields = array('user', 'value_entry', 'create_date');
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
		$sql .= ' WHERE user=' . $this->DB->escape($this->user);
		$sql .= ' AND value_entry=' . $this->DB->escape($this->value_entry);
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
		$result_array = $this->find_by_sql("SELECT * FROM {$this->table_name} WHERE user={$id1} AND value_entry={$id2} LIMIT 1");
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
				// User
				case 'user':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$user = $this->User_model->find_by_id($value);
						if (! $user)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Value Entry
				case 'value_entry':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$value = $this->Data_value_model->find_by_id($value);
						if (! $value)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Date
				case 'create_date':
				{
					if (! empty($value) && ! preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not a valid date or in YYYY-MM-DD format');
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

/* End of file entry_user_model.php */
/* Location: ./application/models/entry_user_model.php */