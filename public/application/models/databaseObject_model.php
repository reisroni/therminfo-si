<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- DatabaseObject
 * Descricao: Modelo para objectos da base de dados
 * Criado: 13-05-2013
 * Modificado: 23-02-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */

class DatabaseObject_model extends CI_Model
{
	// Atributos da classe
	public $id; // Chave primaria
	public $table_name; // Nome da tabela
    public $DB; // Ligacao da BD
	protected $id_field; // Nome da chave primaria
	protected $table_fields; // Lista dos atributos da tabela
	protected $num_rows; // Numero de linhas de uma query
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Ligacao a base de dados
		$this->setDatabase(HOST, USER, PASS, DB);
		$this->num_rows = 0;
    }
	
	/**
     * Altera o valor do numero de linhas
     *
     * @param int $value Novo valor
	 * 
	 * @return void
     */
	public function set_num_rows($value = 0)
	{
		$this->num_rows = $value;
	}
	
	/**
     * Retorna o numero de linhas
     *
     * @return int Numero de linhas
     */
	public function get_num_rows()
	{
		return $this->num_rows;
	}
	
	//---------------------------------------------------------------
	// Metodos utilitarios da BD
	//---------------------------------------------------------------
	/**
     * Dados para ligacao a base de dados MySQL
     *
     * @param string $host Servidor MySQL
     * @param string $user Utilizador
     * @param string $pass Password do utilizador
	 * @param string $db Base de dados a utilizar
	 * @param boolean $multiple Ligar a varias BD
	 * 
	 * @return void
     */
	public function setDatabase($host = '', $user = '', $pass = '', $db = '', $multiple = FALSE)
	{
		// Configuracao da base de dados
		$config['hostname'] = $host;
		$config['username'] = $user;
		$config['password'] = $pass;
		$config['database'] = $db;
		$config['dbdriver'] = 'mysql';
		$config['pconnect'] = FALSE;
		$config['char_set'] = 'utf8';
		$config['dbcollat'] = 'utf8_general_ci';
		
		// Carrega a configuracao da base de dados
		if ($multiple) {
			$this->DB = $this->load->database($config, TRUE);
		} else {
			$this->load->database($config);
			$this->DB = &$this->db;
		}
	}
	
	/**
	 * Efectua o backup da Base de dados,
	 * e grava um ficheiro gzip no servidor
	 * 
	 * @param string $db_name Nome da base de dados
	 * @param array $ignore_table Tabelas para ignorar
	 * 
	 * @return boolean 'TRUE' para sucesso
	 * ou 'FALSE' em caso de falha
	 */
	public function backup_db($db_name = '', $ignore_table = array())
	{
		if (! is_array($ignore_table)) {
			$ignore_table = array();
        }
		// Base de dados para efectuar o backup
		$this->setDatabase(HOST, USER, PASS, $db_name);
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
		{
			//** Windows
			// Preferencias
			$prefs = array('tables' => array(),
							'ignore' => $ignore_table,
							'format' => 'zip',
							'filename' => $db_name.'_bak.sql',
							'add_drop' => TRUE,
							'add_insert' => TRUE,
							'newline' => "\n");
			// Efectua o backup
			$this->load->dbutil();
			$backup =& $this->dbutil->backup($prefs);
			
			// Grava o ficheiro no servidor
			$this->load->helper('file');
			$file = $db_name.'_'.date("Y-m-d-H-i-s").'.gz';
			if (! write_file("./storage/db_backup/$file", $backup)) {
				$result = FALSE;
			} else {
				$result = TRUE;
			}
		}
		else
		{
			//** Outro SO
			// Dados para o comando
			$backupFile = $db_name. '_' .date("Y-m-d-H-i-s");
			$pwd = FCPATH.'storage/db_backup/';
			$host = HOST;
			$user = USER;
			$pass = PASS;
			// Camando para backup
			$command = "mysqldump --skip-opt -q --add-drop-table -h $host -u $user --password=$pass ";
			
			if (is_array($ignore_table) && ! empty($ignore_table))
			{
				foreach($ignore_table as $table) {
					$command .= "--ignore-table=$db_name.$table ";
				}
			}
			$command .= $db_name. " | gzip > $pwd$backupFile.gz";
			
			// Executar o comando
			system($command, $code);
			if ($code == 0) {
				$result = TRUE;
			} else {
				$result = FALSE;
			}
		}
        
		return $result;
	}
	 
	//---------------------------------------------------------------
	// Metodos comuns
	//---------------------------------------------------------------
	/**
	 * Cria ou actualiza um registo na BD
	 *
	 * @return array Lista com o estado da operacao
	 * 
     * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function save()
	{
		// Um novo registo nao tem ainda um ID
		return isset($this->id) ? $this->update() : $this->create();
	}
	
	/**
	 * Cria um registo na BD
	 *
	 * @return array Lista com o estado da operacao
	 * 
	 * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function create()
	{
		$result = array('result' => FALSE, 'error' => '', 'e_desc' => '');
		$attributes = $this->sanitized_attributes(FALSE);
		if (is_array($attributes))
		{
			if ($attributes['status'])
			{
				$sql = $this->DB->insert_string($this->table_name, $attributes['result']);
				if ($this->DB->query($sql)) {
					$this->id = $this->DB->insert_id();
					$result['result'] = TRUE;
				} else {
					$result['error'] = 'create';
					$result['e_desc'] = 'Error on create operacion';
				}
			}
			else
			{
				$result['error'] = $attributes['result']; 
				$result['e_desc'] = $attributes['error'];
			}
		}
		else
		{
			$result['error'] = 'create';
			$result['e_desc'] = 'Error on sanitize attributes';
		}
        
		return $result;
	}
	
	/**
	 * Actualiza um registo da BD
	 *
	 * @return array Lista com o estado da operacao
	 * 
	 * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function update()
	{
		$result = array('result' => FALSE, 'error' => '', 'e_desc' => '');
		$attributes = $this->sanitized_attributes(FALSE);
		$where = "{$this->id_field}={$this->id}";
		if (is_array($attributes))
		{
			if ($attributes['status'])
			{
				$sql = $this->DB->update_string($this->table_name, $attributes['result'], $where);
				if($this->DB->query($sql)) {
					$result['result'] = TRUE;
				} else {
					$result['error'] = 'update';
					$result['e_desc'] = 'Error on update operacion';
				}
			}
			else
			{
				$result['error'] = $attributes['result'];
				$result['e_desc'] = $attributes['error'];
			}
		}
		else
		{
			$result['error'] = 'update';
			$result['e_desc'] = 'Error on sanitize attributes';
		}
        
		return $result;
	}
	
	/**
	 * Apaga um registo da BD
	 *
	 * @return boolean 'TRUE' para sucesso
	 * ou 'FALSE' em caso de falha
	 */
	public function delete()
	{
		$sql = "DELETE FROM {$this->table_name}";
		$sql .= " WHERE {$this->id_field}=" . $this->DB->escape($this->id);
		$sql .= ' LIMIT 1';
        
		return $this->DB->query($sql);
	}
	
	/**
	 * Retorna todos os registos da BD
	 *
	 * @return mixed Array com os registos
	 * ou 'FALSE' em caso de falha
	 *
	 * [Array([int] => object(registo))]
	 */
	public function find_all()
	{
		return $this->find_by_sql("SELECT * FROM {$this->table_name}");
	}
	
	/**
	 * Retorna todos os registos distintos da BD
	 *
	 * @param string $order Nome da coluna para ordenacao
	 *
	 * @return mixed Array com os registos
	 * ou 'FALSE' em caso de falha
	 *
	 * [Array([int] => object(registo))]
	 */
	public function find_all_distinct($order = '')
	{
		$query = "SELECT DISTINCT * FROM {$this->table_name}";
        $order = $this->db_escape($order);
        
		if (! empty($order)) {
			$query .= " ORDER BY {$order} ASC";
		}
        
		return $this->find_by_sql($query);
	}
	
	/**
	 * Retorna um registo da BD pelo ID
	 *
	 * @param int $id ID do registo
	 *
	 * @return mixed Objecto que representa
	 * o registo ou 'FALSE' em caso de falha
	 *
	 * [Object(registo)]
	 */
	public function find_by_id($id = 0)
	{
		$result_array = $this->find_by_sql("SELECT * FROM {$this->table_name} WHERE {$this->id_field}={$id} LIMIT 1");
		return ! empty($result_array) ? array_shift($result_array) : FALSE;
	}
	
	/**
	 * Executa uma query qualquer 
	 *
	 * @param string $sql A query para executar
	 *
	 * @return mixed Array com os resultados
	 * ou 'FALSE' em caso de falha
	 *
	 * [Array([int] => object(registo))]
	 */
	public function find_by_sql($sql = '')
	{
		$object_array = FALSE;
		$result_set = $this->DB->query($sql);
        
		if ($result_set && $result_set->num_rows() >= 0)
		{
			$object_array = array();
			$this->set_num_rows($result_set->num_rows());
			foreach ($result_set->result_array() as $row) {
				$object_array[] = $this->instantiate($row);
			}
		}
        
		return $object_array;
	}
	
	/**
	 * Retorna o numero de registos
	 * 
	 * @return int Numero de registos
	 */
	public function count_all()
	{
		return $this->DB->count_all($this->table_name);
	}
	
	/**
	 * Retorna o numero de registos distintos
	 * 
	 * @return int Numero de registos
	 */
	public function count_all_distinct()
	{
		$this->DB->distinct();
		return $this->DB->count_all_results($this->table_name);
	}
	
	/**
	 * Retorna o numero de registos distintos 
	 * de uma coluna
	 * 
	 * @param string $column_name Nome da coluna
	 * 
	 * @return int Numero de registos
	 */
	public function count_all_distinct_columns($column_name = '')
	{
		$this->DB->where("{$column_name} IS NOT NULL", NULL);
		return $this->DB->count_all_results($this->table_name);
	}
	
	/**
	 * Instancia um objecto de acordo com
	 * um registo da BD
	 * 
	 * @param array $record Registo da BD
	 *
	 * @return mixed Objecto instanciado
	 * ou 'FALSE' em caso de falha
	 *
	 * [Object(registo)]
	 */
	public function instantiate($record = array())
	{
		if (is_array($record) && ! empty($record))
		{
			$object = new $this;
			foreach ($record as $attribute => $value)
			{
				if ($object->_has_attributes($attribute)) {
					$object->$attribute = $value;
				}
			}
		}
		else
		{
			$object = FALSE;
		}
        
		return $object;
	}
	
	//---------------------------------------------------------------
	// Metodos auxiliares
	//---------------------------------------------------------------
	/*
	 * Retorna uma lista com todos os atributos
	 *
	 * @return array Lista com os atributos
	 *
	 * [Array(['atributo'] => ['valor'])]
	 */
	protected function attributes()
	{
		$attributes = array();
		foreach ($this->table_fields as $field)
		{
			if (property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
        
		return $attributes;
	}
	
	/*
	 * Verifica os atributos e retorna uma lista com 
	 * todos os atributos tratados
	 *
	 * @param boolean $escape Escapa carateres especiais
	 *
	 * @return array Lista com os atributos tratados
	 *
	 * [Array('status' => [boolean], 'result' => [Array])]
	 */
	protected function sanitized_attributes($escape = TRUE)
	{
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value)
		{
			if ($escape) {
				$clean_attributes[$key] = $this->db_escape($value);
			}
			else {
				$clean_attributes[$key] = $value;
			}
		}
        
		return array('status' => TRUE, 'result' => $clean_attributes);
	}
	
	/*
	 * Trata caracteres especias de uma string
	 * para introduzir numa BD
	 *
	 * @param mixed $value Dado para tratar
	 *
	 * @return mixed Dado tratado
	 */
	 protected function db_escape($value = '')
	 {
		return (! is_null($value) && is_string($value)) ? $this->DB->escape_str($value) : $value;
	 }
	
	/*
	 * Verifica se um atributo existe ou nao
	 *
	 * @param string $attribute Nome do atributo
	 *
	 * @return boolean 'TRUE' se existe
	 * ou 'FALSE' se nao existe
	 */
	private function _has_attributes($attribute)
	{
		$object_vars = $this->attributes();
		return array_key_exists($attribute, $object_vars);
	}
}

/* End of file databaseObject_model.php */
/* Location: ./application/models/databaseObject_model.php */