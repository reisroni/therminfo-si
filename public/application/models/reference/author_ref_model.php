<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Author_ref
 * Descricao: Modelo da tabela 'author_ref'
 * Criado: 19-08-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Author_ref_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $reference;
	public $author;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('reference/Reference_model');
		$this->load->model('reference/Author_model');
		// Inicializar alguns atributos
		$this->table_name = 'author_ref';
		$this->table_fields = array('reference', 'author');
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
		$sql .= ' WHERE reference=' . $this->DB->escape($this->reference);
		$sql .= ' AND author=' . $this->DB->escape($this->author);
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
		$result_array = $this->find_by_sql("SELECT * FROM {$this->table_name} WHERE reference={$id1} AND author={$id2} LIMIT 1");
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
				// Reference
				case 'reference':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$reference = $this->Reference_model->find_by_id($value);
						if (! $reference)
						{
							return array('status' => FALSE, 
										'result' => $key,
										'error' => 'does not exist');
						}
					}
				} break;
				// Author
				case 'author':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
					else
					{
						$author = $this->Author_model->find_by_id($value);
						if (! $author)
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

/* End of file author_ref_model.php */
/* Location: ./application/models/author_ref_model.php */