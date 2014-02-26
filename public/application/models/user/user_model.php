<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- User
 * Descricao: Modelo da tabela 'User'
 * Criado: 20-04-2013
 * Modificado: 20-01-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */
require_once(BASE_MODEL);

class User_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $uid;
	public $u_first_name;
	public $u_last_name;
	public $email;
	public $institution;
	public $password;
	public $type;
	public $validated;
	public $outdated;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modulos necessarios
		$this->load->helper('security');
		$this->load->helper('email');
		// Inicializar alguns atributos
		$this->table_name = 'user';
		$this->id_field = 'uid';
		$this->table_fields = array('uid', 'u_first_name', 'u_last_name',
									'email', 'institution', 'password', 
                                    'type', 'validated', 'outdated');
		$this->id = &$this->uid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
	 * Retorna o nome completo do utilizador
	 *
	 * @return string Nome completo
	 */
	public function full_name()
	{
		if (isset($this->u_first_name) && isset($this->u_last_name)) {
			return trim($this->u_first_name) . ' ' . trim($this->u_last_name);
		} else {
			return '';
		}
	}
	
	/**
	 * Altera o valor do atributo 'validated' (validated = 1)
	 *
	 * @return mixed 'TRUE' se o utilizador valido
	 * ou Array com o estado da operacao
     *
     * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function validate()
	{
        if ($this->validated == 0) {
            $this->validated = 1;
            $result = $this->save();
        } else {
            $result = TRUE;
        }
		return $result;
	}
	
	/**
	 * Altera o valor do atributo 'outdated' (outdated = 1)
	 *
	 * @return mixed 'TRUE' se o utilizador desactualizado
	 * ou Array com o estado da operacao
     *
     * [Array('result' => [boolean], 'error' => [String], 'e_desc' => [String])]
	 */
	public function outdated()
	{
        if ($this->outdated == 0) {
            $this->outdated = 1;
            $result = $this->save();
        } else {
            $result = TRUE;
        }
		return $result;
	}
	
	/**
	 * Autentica um utilizador
	 *
	 * @param string $email E-mail do utilizador
	 * @param string $pass Palavra-passe do utilizador
	 *
	 * @return mixed Objecto utilizador ou 
	 * 'FALSE' em caso de falha
	 */
	public function authenticate($email = '', $pass = '')
	{
		$email = $this->DB->escape($email);
		$pass = do_hash($pass, 'md5');
		$pass = $this->DB->escape($pass);
		
		$sql = "SELECT * FROM {$this->table_name} ";
		$sql .= "WHERE email = {$email} ";
		$sql .= "AND password = {$pass} ";
		$sql .= 'LIMIT 1';
		
		$result_array = $this->find_by_sql($sql);
		return ! empty($result_array) ? array_shift($result_array) : FALSE;
	}
	
	/**
	 * Pesquisa pelo e-mail do utilizador
     *
     * @param string $email E-mail para pesquisa
     *
     * @return mixed Objecto utilizador ou
	 * 'FALSE' em caso de falha
	 * 
	 * [Object(User_model)]
	 */
	public function find_by_email($email = '')
	{
		if (valid_email($email))
		{
			$email = $this->DB->escape($email);
			$result_array = $this->find_by_sql("SELECT * FROM {$this->table_name} WHERE email={$email} LIMIT 1");
		}
		return (isset($result_array) && ! empty($result_array)) ? array_shift($result_array) : FALSE;
	}
	
	/**
	 * Pesquisa pelo nome do utilizador
     *
     * @param string $first_name 1.º nome para pesquisa
     * @param string $last_name 2.º nome para pesquisa
     *
     * @return mixed Objecto utilizador ou
	 * 'FALSE' em caso de falha
	 * 
	 * [Object(User_model)]
	 */
	public function find_by_username($first_name = '', $last_name = '')
	{
		$first_name = $this->DB->escape($first_name);
        $last_name = $this->DB->escape($last_name);
        $sql_query = "SELECT * FROM {$this->table_name} WHERE u_first_name={$first_name} AND u_last_name={$last_name} LIMIT 1";
		$result_array = $this->find_by_sql($sql_query);
		return ! empty($result_array) ? array_shift($result_array) : FALSE;
	}
	
	/**
	 * Pesquisa o utilizador associado
	 * a um composto
     *
     * @param int $mol_id ID do composto
     *
     * @return mixed Objecto utilizador ou
	 * 'FALSE' em caso de falha
	 * 
	 * [Object(User_model)]
	 */
	public function find_user_from_compound($mol_id = 0)
	{
		$mol_id = $this->DB->escape($mol_id);
		$result_set = $this->DB->query("SELECT * FROM mol_user WHERE molecule={$mol_id} LIMIT 1");	
		if ($result_set && $result_set->num_rows() > 0)
		{
			$row = $result_set->row_array();
			$result = $this->find_by_id($row['user']);
		}
		else
		{
			$result = FALSE;
		}
		return $result;
	}
	
	/**
	 * Pesquisa o utilizador associado
	 * a valores de uma propriedade
     *
     * @param int $reg_id ID do registo do valor
     *
     * @return mixed Objecto utilizador ou
	 * 'FALSE' em caso de falha
	 * 
	 * [Object(User_model)]
	 */
	public function find_user_from_property($reg_id = 0)
	{
		$reg_id = $this->DB->escape($reg_id);
		$result_set = $this->DB->query("SELECT * FROM entry_user WHERE value_entry={$reg_id} LIMIT 1");
		if ($result_set && $result_set->num_rows() > 0)
		{
			$row = $result_set->row_array();
			$result = $this->find_by_id($row['user']);
		}
		else
		{
			$result = FALSE;
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
	public function sanitized_attributes($escape = TRUE)
	{
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value)
		{
			switch ($key)
			{
				// ID
				case 'uid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// User First Name
				case 'u_first_name':
				{
					if (empty($value) or strlen($value) > 40)
					{
						return array('status' => FALSE, 
									'result' => 'user first name',
									'error' => 'is empty or length greater than 40');
					}
				} break;
                // User Last Name
				case 'u_last_name':
				{
					if (empty($value) or strlen($value) > 40)
					{
						return array('status' => FALSE, 
									'result' => 'user last name',
									'error' => 'is empty or length greater than 40');
					}
				} break;
				// E-mail
				case 'email':
				{
					if (empty($value) or ! valid_email($value) or strlen($value) > 45)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or invalid or length greater than 45');
					}
				} break;
				// Institution
				case 'institution':
				{
					if (! empty($value) && strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 100');
					}
				} break;
				// Password
				case 'password':
				{
					if (empty($value) or strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or length greater than 100');
					}
					elseif (strlen($value) != 32)
					{
						$value = do_hash($value, 'md5');
					}
				} break;
				// User type
				case 'type':
				{
					if (empty($value) or ($value != 'guest' && $value != 'admin' && $value != 'superadmin'))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or invalid');
					}
				} break;
				// Validated
				case 'validated':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
				} break;
				// Outdated
				case 'outdated':
				{
					if (! isset($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
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

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */