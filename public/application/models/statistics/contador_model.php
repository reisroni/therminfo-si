<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Contador
 * Descricao: Modelo da tabela 'contador'
 * Criado: 02-07-2013
 * Modificado: 13-04-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */
require_once(BASE_MODEL);

class Contador_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $contid;
	public $day;
	public $month;
	public $year;
	public $hour;
	public $minute;
	public $second;
	public $ip;
	public $method;
	public $method_type;
	public $search_detail;
	public $country;
	public $city;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'contador';
		$this->id_field = 'contid';
		$this->table_fields = array('contid', 'day', 'month', 'year', 
									'hour', 'minute', 'second', 'ip', 
									'method', 'method_type', 'search_detail', 
									'country', 'city');
		$this->id = &$this->contid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
	 * Retorna as vezes que uma pesquisa foi utilizada
	 * (por metodo, por ano, por mes e por dia)
	 * 
	 * @param int $method ID do metodo da pesquisa
	 * @param int $year Ano
	 * @param int $month Mes
	 * @param int $day Dia
	 *
	 * @return int Total de registos
	 */
	public function count_search($method = 0, $year = 0, $month = 0, $day = 0)
	{
		// Ano
		if (! empty($year)) {
			$this->DB->where('year', $year);
        }
		// Mes
		if (! empty($month)) {
			$this->DB->where('month', $month);
        }
		// Dia
		if (! empty($day)) {
			$this->DB->where('day', $day);
        }
		// Metodo utilizado
		$this->DB->where('method', $method);
		// Total
		return $this->DB->count_all_results('contador');
	}
	
	/**
	 * Retorna todos os anos da tabela 'contador'
	 * 
	 * @return mixed Array com os anos
	 * ou 'FALSE' em caso de falha
	 */
	public function find_all_years()
	{
		// Query
		$this->DB->select('year')->distinct()->order_by('year', 'desc');
		$query = $this->DB->get('contador');
		
		if ($query && $query->num_rows() > 0)
		{
			$result = array();
			foreach ($query->result() as $row) 
			{
				if (! empty($row->year)) {
					array_push($result, $row->year); // Anos
				}
			}
		}
		else
		{
			$result = FALSE;
		}
        return $result;
	}
	
	/**
	 * Retorna os ultimos 100 IPs que visitaram o site
	 * 
	 * @return mixed Array com os dados 
	 * dos IPs ou 'FALSE' em caso de Falha
	 * 
	 * [Array([int] => Array('day','month','ip','country','city'))]
	 */
	public function find_last_ips()
	{
		// Query
		$this->DB->select('day, month, ip, country, city')
		->order_by('contid', 'desc')->limit(100);
		$query = $this->DB->get('contador');
		
		if ($query && $query->num_rows() > 0)
		{
			$result = array();
			foreach ($query->result() as $row) 
			{
				if (! empty($row))
				{
					array_push($result, array('day' => $row->day, 'month' => $row->month, 
					'ip' => $row->ip, 'country' => $row->country, 'city' => $row->city)); // IPs
				}
			}
		}
		else
		{
			$result = FALSE;
		}
        return $result;
	}
	
	/**
     * Pesquisa um contador pelo IP
     *
     * @param string $ip IP para pesquisa
     *
     * @return mixed Objecto com o contador 
	 * encontrado ou '0' em caso de falha ou 
	 * '1' se o IP estiver em branco ou 2 se
	 * nao ha resultado
	 * 
	 * [Object(Contador_model)]
     */
	public function find_by_ip($ip = '')
	{
		$result = 0;
        if (empty($ip))
		{
            $result = 1; // IP em branco
        }
		else
		{
			$ip = $this->DB->escape_like_str($ip);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE ip LIKE '{$ip}' LIMIT 1";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
			{
				if (count($queryResult) > 0) {
					$result = array_shift($queryResult); // Contador encontrado
				} else {
					$result = 2; // Sem resultado
                }
			}
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
				case 'contid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Day
				case 'day':
				{
					if (empty($value))
					{
						$value = (int) date('d');
					}
					elseif (! is_numeric($value))
					{
						return array('status' => FALSE, 'result' => $key, 'error' => 'is not numeric');
					}
				} break;
				// Month
				case 'month':
				{
					if (empty($value))
					{
						$value = (int) date('m');
					}
					elseif (! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
				} break;
				// Year
				case 'year':
				{
					if (empty($value))
					{
						$value = (int) date('Y');
					}
					elseif (! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
				} break;
				// Hour
				case 'hour':
				{
					if (! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
					else
					{
						$value = (int) date('H');
					}
				} break;
				// Minute
				case 'minute':
				{
					if (! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
					else
					{
						$value = (int) date('i');
					}
				} break;
				// Second
				case 'second':
				{
					if (! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
					else
					{
						$value = (int) date('s');
					}
				} break;
				// IP
				case 'ip':
				{
					if (empty($value) or ! $this->input->valid_ip($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not valid');
					}
					else
					{
						$value = $this->input->ip_address();
					}
				} break;
				// Method
				case 'method':
				{
					if (empty($value) or ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or not numeric');
					}
				} break;
				// Method type
				case 'method_type':
				{
					if (! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'method type',
									'error' => 'is not numeric');
					}
				} break;
				// Search detail
				case 'search_detail':
				{
					if (! empty($value) && strlen($value) > 255)
					{
						return array('status' => FALSE, 
									'result' => 'search',
									'error' => 'length greater than 255');
					}
				} break;
				// Country
				case 'country':
				{
					if (! empty($value) && strlen($value) > 45)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 45');
					}
					else
					{
						$value = $this->_search_country_ip($value);
					}
				} break;
				// City
				case 'city':
				{
					if (! empty($value) && strlen($value) > 45)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 45');
					}
					else
					{
						$value = $this->_search_city_ip($value);
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
	
	/*
	 * Procura o pais pelo IP
	 *
	 * @param string $ip IP para procura
	 *
	 * @return mixed string com o pais 
	 * ou NULL caso nao existe
	 */
	private function _search_country_ip($ip = '')
	{
		// Procurar na BD
		$search = $this->find_by_ip($ip);
		if (is_object($search))
		{
			$result = (isset($search->country) && ! empty($search->country)) ? $search->country : NULL;
		}
		else
		{
			// Procurar no 'IP Locator'
			$URL = "http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress={$ip}";
			log_message('info', 'Acessing IP Locator');
			$location = get_meta_tags($URL);
			
			if (! empty($location))
			{
				$result = (empty($location['country']) or $location['country'] == '' 
							or $location['country'] == 'Limit Exceeded') ? NULL : $location['country'];
			}
			else
			{
				$result = NULL;
			}
		}
        return $result;
	}
	
	/*
	 * Procura a cidade pelo IP
	 *
	 * @param string $ip IP para procura
	 *
	 * @return mixed string com a cidade 
	 * ou NULL caso nao existe
	 */
	private function _search_city_ip($ip = '')
	{
		// Procurar na BD
		$search = $this->find_by_ip($ip);
		
		if (is_object($search))
		{
			$result = (isset($search->city) && ! empty($search->city)) ? $search->city : NULL;
		}
		else
		{
			// Procurar no 'IP Locator'
			$URL = "http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress={$ip}";
			log_message('info', 'Acessing IP Locator');
			$location = get_meta_tags($URL);
			
			if (! empty($location))
			{
				$result = (empty($location['city']) or $location['city'] == '' 
							or $location['city'] == 'Limit Exceeded') ? NULL : $location['city'];
			}
			else
			{
				$result = NULL;
			}
		}
        return $result;
	}
}

/* End of file contador_model.php */
/* Location: ./application/models/contador_model.php */