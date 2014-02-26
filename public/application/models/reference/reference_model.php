<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Reference
 * Descricao: Modelo da tabela 'reference'
 * Criado: 01-07-2013
 * Modificado: 08-12-2013
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2013, ThermInfo
 */
require_once(BASE_MODEL);

class Reference_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $refid;
	public $reference_code;
	public $ref_type;
	public $title;
	public $issue;
	public $chapter;
	public $journal;
	public $book;
	public $year;
	public $volume;
	public $bpage;
	public $epage;
	public $editor;
	public $publisher;
	public $ref_all;
	public $doi;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modelos necessarios
		$this->load->model('reference/Author_model');
		// Inicializar alguns atributos
		$this->table_name = 'reference';
		$this->id_field = 'refid';
		$this->table_fields = array('refid', 'reference_code', 'ref_type', 'title', 
                                    'issue', 'chapter', 'journal', 'book', 'year', 
                                    'volume', 'bpage', 'epage', 'editor', 'publisher', 
                                    'ref_all', 'doi');
		$this->id = &$this->refid;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
	/**
     * Retorna a referencia completa
	 *
	 * @return string Referencia completa
     */
	public function full_ref()
	{
		if (isset($this->ref_all))
		{
			return trim($this->ref_all);
		}
		else
		{
			$full_ref = $this->_make_full_ref();
			if ($full_ref)
				$this->ref_all = $full_ref;
			else
				$full_ref = '';
			
			return $full_ref;
		}
	}
	
	/**
     * Pesquisa pelo codigo da referencia
     *
     * @param string $code Codigo para pesquisa
     *
     * @return mixed Array com as referencias 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o codigo estiver em branco
	 * 
	 * [Array([int] => object(Reference_model))]
     */
	public function find_by_ref_code($code = '')
	{
		$result = 0;
        if (empty($code))
		{
            $result = 1; // Codigo em branco
        }
		else
		{
			$code = $this->DB->escape_like_str($code);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE reference_code='{$code}'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Referencias encontradas
		}
		return $result;
	}
	
	/**
     * Pesquisa pelo Tipo da referencia
     *
     * @param string $type Tipo para pesquisa
     *
     * @return mixed Array com as referencias 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o tipo estiver em branco
	 * 
	 * [Array([int] => object(Reference_model))]
     */
	public function find_by_ref_type($type = '')
	{
		$result = 0;
        if (empty($type))
		{
            $type = 1; // Tipo em branco
        }
		else
		{
			$type = $this->DB->escape_like_str($type);
			// Query
			$query = "SELECT * FROM {$this->table_name} WHERE ref_type='{$type}'";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Referencias encontradas
		}
		return $result;
	}
	
	/**
	 * Pesquisa pelo ID do autor
	 *
	 * @param int $id ID do autor para pesquisa
	 *
	 * @return mixed Array com as referencias 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o ID do autor estiver em branco
	 * 
	 * [Array([int] => object(Reference_model))]
	 */
	public function find_by_author($id = 0)
	{
		$result = 0;
        if (empty($id))
		{
            $id = 1; // Tipo em branco
        }
		else
		{
			$id = $this->DB->escape($id);
			// Query
			$query = "SELECT * FROM {$this->table_name}, author_ref WHERE refid=reference AND author={$id}";
			
			// Interrogar a BD
			$queryResult = $this->find_by_sql($query);
			if (is_array($queryResult))
				$result = $queryResult; // Referencias encontradas
		}
		return $result;
	}
	
	/**
	 * Gera um codigo para uma referencia
	 * 
	 * @param array $authors Os ids dos autores da referencia
	 * @param int $year O ano da referencia
	 *
	 * @return string O codigo da referencia
	 */
	public function generate_ref_code($authors = '', $year = 0)
	{
		return $this->_make_reference_code($authors, $year);
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
				case 'refid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// Reference code
				case 'reference_code':
				{
					if (empty($value) or strlen($value) > 50)
					{
						return array('status' => FALSE, 
									'result' => 'reference code',
									'error' => 'is empty or length greater than 50');
					}
				} break;
				// Reference type
				case 'ref_type':
				{
					if (empty($value) or strlen($value) > 45)
					{
						return array('status' => FALSE, 
									'result' => 'reference type',
									'error' => 'is empty or length greater than 45');
					}
				} break;
				// Title
				case 'title':
				{
					if (empty($value) or strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or length greater than 100');
					}
				} break;
				// Issue
				case 'issue':
				{
					if (! empty($value) && strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 100');
					}
				} break;
				// Chapter
				case 'chapter':
				{
					if (! empty($value) && strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 100');
					}
				} break;
				// Journal
				case 'journal':
				{
					if (! empty($value) && strlen($value) > 200)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 200');
					}
				} break;
				// Book
				case 'book':
				{
					if (! empty($value) && strlen($value) > 200)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 200');
					}
				} break;
				// Year
				case 'year':
				{
					if (empty($value) or ! is_numeric($value) or ($value < 1901 && $value > 2155))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is empty or is not numeric or is invalid');
					}
				} break;
				// Volume
				case 'volume':
				{
					if (! empty($value) && strlen($value) > 45)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 45');
					}
				} break;
				// Begin page
				case 'bpage':
				{
					if (! empty($value) && strlen($value) > 15)
					{
						return array('status' => FALSE, 
									'result' => 'begin page',
									'error' => 'length greater than 15');
					}
				} break;
				// End page
				case 'epage':
				{
					if (! empty($value) && strlen($value) > 15)
					{
						return array('status' => FALSE, 
									'result' => 'end page',
									'error' => 'length greater than 15');
					}
				} break;
				// Editor
				case 'editor':
				{
					if (! empty($value) && strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 100');
					}
				} break;
				// Publisher
				case 'publisher':
				{
					if (! empty($value) && strlen($value) > 100)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 100');
					}
				} break;
				// Complete reference
				case 'ref_all':
				{
					if (empty($value))
					{
						$ref = $this->_make_full_ref();
						if($ref)
						{
							$value = $ref;
						}
						else
						{
							return array('status' => FALSE, 
										'result' => 'Complete reference',
										'error' => 'is empty');
						}
					}
				} break;
				// DOI
				case 'doi':
				{
					if (! empty($value) && strlen($value) > 50)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 50');
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
	 * Gera a referencia completa
	 * 
	 * @return mixed A referencia completa
	 * ou 'FALSE' em caso de falha
	 */
	 private function _make_full_ref()
	 {
		$full_ref = FALSE;
		switch ($this->ref_type)
		{
			// Livro
			case 'Book':
			{
				$book = empty($this->book) ? NULL : ", {$this->book}";
				$year = empty($this->year) ? NULL : ", {$this->year}";
				$vol = empty($this->volume) ? NULL : ", {$this->volume}";
				$issue = empty($this->issue) ? NULL : ", {$this->issue}";
				$chapter = empty($this->chapter) ? NULL : ", {$this->chapter}";;
				$bp = empty($this->bpage) ? NULL : $this->bpage;
				$ep = empty($this->epage) ? NULL : $this->epage;
				$editor = empty($this->editor) ? NULL : ", {$this->editor}";
				$pub = empty($this->publisher) ? NULL : ", {$this->publisher}";
				$pages = ((is_null($bp) or empty($bp)) && (is_null($ep) or empty($ep))) ? NULL : 
						((! empty($bp) && ! empty($ep)) ? ", p. {$bp}-{$ep}" : 
						((! empty($bp) && empty($ep)) ? ", p. {$bp}" : ", p. {$ep}"));
				
				$full_ref = $this->title.$book.$vol.$issue.$year.$chapter.$pages.$editor.$pub;
			} break;
			// Artigo
			case 'Paper':
			{
				$journal = empty($this->journal) ? NULL : ", {$this->journal}";
				$issue = empty($this->issue) ? NULL : ", {$this->issue}";
				$year = empty($this->year) ? NULL : ", {$this->year}";
				$vol = empty($this->volume) ? NULL : ", {$this->volume}";
				$bp = empty($this->bpage) ? NULL : $ref_bp;
				$ep = empty($this->epage) ? NULL : $ref_ep;
				$pages = ((is_null($bp) or empty($bp)) && (is_null($ep) or empty($ep))) ? NULL : 
						((! empty($bp) && ! empty($ep)) ? ", p. {$bp}-{$ep}" : 
						((! empty($bp) && empty($ep)) ? ", p. {$bp}" : " p. {$ep}"));
				
				$full_ref = $this->title.$journal.$vol.$issue.$year.$pages;
			} break;
		}
		return trim($full_ref);
	 }
	 
	/*
	 * Gera um codigo para uma referencia
	 * 
	 * @param array $authors Os ids dos autores da referencia
	 * @param int $year O ano da referencia
	 * 
	 * @return string O codigo da referencia
	 */
	private function _make_reference_code($authors = '', $year = 0)
	{
		// Gera o codigo para uma referencia
		if (empty($year))
			$year = date('Y');
		// Data
		$ref_code = $year;
		// Autores
		if (! is_array($authors))
		{
			if (! empty($authors))
				$ref_code .= '/'.$authors;
			else
				$ref_code .= '/TI-'.substr(uniqid(), -5); // Sem autor
		}
		else
		{
			foreach ($authors as $id)
			{
				$author = $this->Author_model->find_by_id($id);
				if ($author)
					$ref_code .= '/'.$author->$a_last_name;
				else
					$ref_code .= '/TI-'.substr(uniqid(), -5); // Sem autor
			}
		}
		// Codigo
		$ref_code = strtoupper(str_replace(' ', '', $ref_code));
		
		// Verifica se o codigo existe na BD
		$query = $this->find_by_ref_code($ref_code);
		if (is_array($query))
		{
			// Caso existe, acrescenta um id unico
			if (count($query) > 0)
				$ref_code .= '[TI-'.substr(uniqid(), -2).']';
		}
		return trim($ref_code);
	}
}

/* End of file reference_model.php */
/* Location: ./application/models/reference_model.php */