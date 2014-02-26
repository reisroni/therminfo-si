<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- News
 * Descricao: Modelo da tabela 'news'
 * Criado: 11-09-2013
 * Modificado: 22-10-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class News_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $nid;
	public $date;
	public $year;
	public $month;
	public $title;
	public $content;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Inicializar alguns atributos
		$this->table_name = 'news';
		$this->id_field = 'nid';
		$this->table_fields = array('nid', 'date', 'year', 'month', 'title', 'content');
		$this->id = &$this->nid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
	 * Procura as noticias
	 * 
	 * @return mixed Array com as noticias 
	 * ou '0' em caso de falha
	 * 
	 * [Array([int] => object(News_model))]
	 */
	public function find_news()
	{
		$result = 0;
		// Query
		$query = "SELECT * FROM {$this->table_name} ORDER BY year DESC, month DESC";
		// Interrogar a BD
		$queryResult = $this->find_by_sql($query);
		if (is_array($queryResult))
		{
			$result = $queryResult; // News encontradas
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
				case 'nid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Date
				case 'date':
				{
					if (empty($value))
					{
						$value = date('F');
					}
					elseif (strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 100');
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
				// Title
				case 'title':
				{
					if (empty($value) or strlen($value) > 255)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or length greater than 255');
					}
				} break;
				// Content
				case 'content':
				{
					if (empty($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty');
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

/* End of file news_model.php */
/* Location: ./application/models/news_model.php */