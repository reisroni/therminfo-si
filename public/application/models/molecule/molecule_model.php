<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Molecule
 * Descricao: Modelo da tabela 'molecule'
 * Criado: 10-05-2013
 * Modificado: 21-01-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */
require_once(BASE_MODEL);

class Molecule_model extends DatabaseObject_model
{
	// Atributos da tabela
	public $mid;
	public $therminfo_id;
	public $casrn;
	public $name;
	public $formula;
	public $mw;
	public $state;
	public $phi_form;
	public $smiles;
	public $usmiles;
	public $inchi;
	public $inchikey;
	public $s_inchi;
	public $s_inchikey;
	public $mol_file;
	public $img_path;
	public $validated;
	public $outdated;
	public $mol_type;
	public $family;
	public $class;
	public $subclass;
	// Numero de resultados obtidos de uma consulta
	private $_num_result;
	// Conjunto de SMILES similares da consulta por SMILES
	private $_similar_smiles;
										
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Carregar alguns modulos necessarios
		$this->load->library('OBabel');
		$this->load->library('Molecular_formula_parser');
		// Inicializar alguns atributos
		$this->table_name = 'molecule';
		$this->id_field = 'mid';
		$this->table_fields = array('mid', 'therminfo_id', 'casrn', 'name', 
										'formula','mw', 'state', 'phi_form', 'smiles', 
										'usmiles', 'inchi', 'inchikey', 's_inchi', 
										's_inchikey', 'mol_file', 'family', 'class', 
										'subclass', 'mol_type', 'img_path', 
										'validated', 'outdated');
		$this->id = &$this->mid;
		$this->_num_result = 0;
		$this->_similar_smiles = array();
    }
	
	/**
     * Altera o valor do numero de resultados obtidos
     *
     * @param int $value Novo valor
	 * 
	 * @return void
     */
	public function set_num_results($value = 0)
	{
		$this->_num_result = $value;
	}
	
	/**
     * Retorna o numero de resultados obtidos
     * (metodos: find_by_name, find_by_sound, 
	 * find_by_smiles, find_by_smiles_pred, 
	 * find_compounds_from_user, find_by_othername,
	 * advanced_search, subs_search)
	 * 
     * @return int Numero de resultados obtidos
     */
	public function get_num_results()
	{
		return $this->_num_result;
	}
	
	/**
     * Retorna o array de SMILES similares
     *
     * @return array SMILES similares obtido
	 * (cada indice mostra o 'mid' e o valor da similaridade.
	 * metodos: find_by_smiles, find_by_smiles_pred,
	 * advanced_search)
	 * 
	 * [Array([int] => ['sim mid'])]
     */
    public function get_similar_smiles()
	{
        return $this->_similar_smiles;
    }
	
	/**
     * Altera o array de SMILES similares
     *
     * @param array $value Novo array de SMILES similares
	 * 
	 * @return void
     */
    public function set_similar_smiles($value = array())
	{
        $this->_similar_smiles = $value;
    }
	
	//---------------------------------------------------------------
	// Metodos da tabela
	//---------------------------------------------------------------
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
	 * Retorna o caminho do ficheiro da 
	 * imagem do composto
	 *
	 * @return string O caminho do ficheiro 
	 * ou 'FALSE' caso nao existe
	 */
	public function get_image()
	{
		return isset($this->img_path) && ! empty($this->img_path) ? $this->img_path : FALSE;
	}
	
	/**
	 * Gera um ThermInfo ID (COXXXXXXX)
	 * 
	 * @return string ThermInfo ID
	 */
	public function generate_therminfoID()
	{
		$num = $this->count_all();
		return sprintf('CO%07s', ($num + 1));
	}
	
	/**
     * Pesquisa pelo nome do composto
     *
     * @param string $name Nome do composto para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limite)
     *
     * @return mixed Array com as moleculas 
	 * encontradas ou '0' em caso de falha ou 
	 * '1' se o nome estiver em branco
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
	public function find_by_name($name = '', $limit = 0) 
	{
        $result = 0;
        if (empty($name))
		{
            $result = 1; // Nome em branco
        }
		else
		{
            $mw = $this->obabel->calc_MW($name, 1);
			$name = $this->DB->escape_like_str($name);
            // Construir a query final
            $query = 'SELECT a.mid';
            if ($mw) {
                $query .= ", ABS(mol.mw-{$mw}) AS dif"; // Diferenca de peso
			}
            $query .= " FROM (SELECT mid, name, INSTR(name, '{$name}') AS pos 
			FROM molecule WHERE name LIKE '%{$name}%' UNION SELECT molecule, 
			synonym, INSTR(synonym, '{$name}') AS pos FROM othername WHERE synonym 
			LIKE '%{$name}%') AS a, molecule AS mol WHERE a.mid = mol.mid";
			$query .= ' AND mol.validated = 1 AND mol.outdated = 0';
            $query .= ' GROUP BY a.mid ORDER BY a.pos'; // Ordenar pela posicao do termo da pesquisa
            if ($mw) {
                $query .= ', dif'; // Ordenar pela diferenca de peso
			}
            $query .= ', a.name'; // Ordenar pelo nome
			if ($limit !== 0) {
				$query .= " LIMIT {$limit}";
			}
			
            // Interrogar a BD
			$queryResult = $this->DB->query($query);
			if ($queryResult && $queryResult->num_rows() >= 0) 
			{
				$this->set_num_results($queryResult->num_rows());
				$field = array();
				foreach ($queryResult->result_array() as $row)
				{
					$data = $this->find_by_id($row['mid']);
					if (! empty($data)) {
						array_push($field, $data);
					}
				}
				$result = $field; // Moleculas encontradas
			}
        }
        return $result;
    }
	
	/**
     * Pesquisa fonetica
     * 
     * @param string $name Nome para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limite)
	 *
     * @return mixed Array com as moleculas encontradas 
	 * ou '0' em caso de falha ou '1' se o nome 
	 * estiver em branco
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
    public function find_by_sound($name = '', $limit = 0) 
	{
        $result = 0;
        if (empty($name))
		{
            $result = 1; // Nome em branco
        }
		else
		{
			$field = array();
			$name = $this->DB->escape_like_str($name);
            // Query final
            $query = "SELECT a.mid FROM ((SELECT mid FROM molecule WHERE name SOUNDS LIKE '{$name}')
			UNION (SELECT molecule FROM othername WHERE synonym SOUNDS LIKE '{$name}' GROUP BY molecule)) 
			AS a, molecule AS mol WHERE a.mid = mol.mid AND mol.validated = 1 AND mol.outdated = 0";
            if ($limit !== 0) {
				$query .= " LIMIT {$limit}";
			}
			
            // Interrogar a BD
			$queryResult = $this->DB->query($query);
			if ($queryResult && $queryResult->num_rows() >= 0)
			{
				$this->set_num_results($queryResult->num_rows());
				foreach ($queryResult->result_array() as $row)
				{
					$data = $this->find_by_id($row['mid']);
					if (! empty($data)) {
						array_push($field, $data);
					}
				}
				$result = $field; // Moleculas encontradas
			}
        }
        return $result;
    }
	
	/**
     * Pesquisa pelo SMILES do composto
     *
     * @param string $smiles SMILES para pesquisa
	 * @param string $threshold Threshold de similaridade
	 * @param int $limit Limite dos resultados (0 - sem limite)
     * 
     * @return mixed Array com as moleculas encontradas ou 
	 * '0' em caso de falha ou '1' se o SMILES estiver em 
	 * branco ou '2' se o SMILES for invalido ou '3' se o 
	 * numero de SMILES similares for zero
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
    public function find_by_smiles($smiles = '', $threshold = '', $limit = 0) 
	{
        $result = 0;
        if (empty($smiles))
		{
            $result = 1; // SMILES em branco
        }
		else
		{
            $field = array();
			$mw = $this->obabel->calc_MW($smiles, 2);
            // Verificar o peso molecular
            if (! $mw) 
			{
				$result = 2; // SMILES invalido
            }
			else
			{
                // Verificar o intervalo do threshold
                $inter = 0;
                if ($threshold == 'i1')
				{
                    $threshold1 = '0.9';
                    $threshold2 = '0.95';
                    $inter = 1;
                }
				elseif ($threshold == 'i2')
				{
                    $threshold1 = '0.8';
                    $threshold2 = '0.9';
                    $inter = 1;
                }
				elseif ($threshold == 'i3')
				{
                    $threshold1 = '0.7';
                    $threshold2 = '0.8';
                    $inter = 1;
                }

                // Calcular os SMILES similares
                if ($inter == 1)
				{
                    $similar_smiles = $this->obabel->calc_similarity($smiles, $threshold1, $threshold2, 1);
                    $this->set_similar_smiles($similar_smiles);
                    $linhas = count($similar_smiles);
                }
				else
				{
                    $similar_smiles = $this->obabel->calc_similarity($smiles, $threshold);
                    $this->set_similar_smiles($similar_smiles);
                    $linhas = count($similar_smiles);
                }
				
                if ($linhas <= 0)
				{
					$result = 3; // Numero de SMILES similares zero
                }
				else
				{
					// Guardar os SMILES similares num tabela temporaria
					$sql = 'DROP TEMPORARY TABLE IF EXISTS sim_sum';
					$this->DB->query($sql) or die("TEMP DB DROP 1 Error");
					$sql = 'CREATE TEMPORARY TABLE sim_sum(mid INT NOT NULL, sim FLOAT(6,3) NOT NULL)';
					$this->DB->query($sql) or die("TEMP DB CREATE Error");

					foreach ($similar_smiles as $ss)
					{
						$sss = explode(' ', $ss);
						$sim = $sss[0];
						$midsmi = $sss[1];
						$sql = "INSERT INTO sim_sum(mid, sim) VALUES ({$midsmi}, {$sim})";
						$this->DB->query($sql) or die("TEMP DB INSERT Error");
					}
					// Construir a query final
					$consulta = $this->DB->query('SELECT mid FROM sim_sum') or die("TEMP DB SELECT Error");
					$query = "SELECT m.mid, ABS(m.mw-{$mw}) AS dif, sim_sum.sim AS similarity 
					FROM molecule AS m, sim_sum WHERE m.mid IN (";
					
					$i = 0;
					foreach ($consulta->result_array() as $row)
					{
						if ($i == 0) {
							$query .= $row['mid'];
						} else {
							$query .= ', '. $row['mid'];
						}
						++$i;
					}
					$query .= ') AND sim_sum.mid = m.mid ORDER BY similarity DESC, dif ASC';
					if ($limit !== 0) {
						$query .= " LIMIT {$limit}";
					}
					
					// Interrogar a BD
					$queryResult = $this->DB->query($query);
					if ($queryResult && $queryResult->num_rows() >= 0)
					{
						$this->set_num_results($queryResult->num_rows());
						foreach ($queryResult->result_array() as $row)
						{
							$data = $this->find_by_id($row['mid']);
							if (! empty($data)) {
								array_push($field, $data);
							}
						}
						$result = $field; // Moleculas encontradas
					}
					// Eliminar a tabela temporaria
					$sql = 'DROP TEMPORARY TABLE IF EXISTS sim_sum';
					$this->DB->query($sql) or die("TEMP DB DROP 2 Error");
                }
            }
        }
        return $result;
    }
	
	/**
     * Pesquisa pelo CAS RN do composto
     *
     * @param string $casrn CAS RN para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limite)
     *
     * @return mixed Array com as moleculas encontradas 
	 * ou '0' em caso de falha ou '1' se o CAS RN estiver 
	 * em branco ou '2' se o CAS RN for invalido ou '3' 
	 * se o digito de controlo for invalido
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
    public function find_by_casrn($casrn = '', $limit = 0)
	{
        $result = 0;
        if (empty($casrn))
		{
            $result = 1; // CAS em branco
        }
		else
		{
            // Verificacao do formato do CASRN
			$casrn_valid = $this->obabel->verify_casrn($casrn);
			if ($casrn_valid === 1)
			{
				$result = 2; // CAS invalido
			}
			elseif ($casrn_valid === 2)
			{
				$result = 3; // Digito de controle invalido
			}
			else
			{
				$casrn = $this->DB->escape_like_str($casrn);
				// Query
				$query = "SELECT * FROM molecule WHERE casrn='{$casrn}'";
				if ($limit !== 0) {
					$query .= " LIMIT {$limit}";
				}
				// Interrogar a BD
				$queryResult = $this->find_by_sql($query);
				if (is_array($queryResult)) {
					$result = $queryResult; // Moleculas encontradas
				}
            }
        }
        return $result;
    }
	
	/**
     * Pesquisa pelo ThermInfo ID do composto
     *
     * @param string $thermid ThermInfo ID para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limite)
     *
     * @return mixed Array com as moleculas encontradas 
	 * ou '0' em caso de falha ou '1' se o ID estiver 
	 * em branco ou '2' se o ID for invalido
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
    public function find_by_thermid($thermid = '', $limit = 0) 
	{
        $result = 0;
        if (empty($thermid))
		{
            $result = 1; // ID em branco
        }
		else
		{
            // Verificacao do formato do ThermInfo ID
			$thermid_valid = $this->obabel->verify_thermid($thermid);
            if (! $thermid_valid)
			{
				$result = 2; // ThermInfo ID invalido
            }
			else
			{
                $thermid = $this->DB->escape_like_str($thermid);
				// Query final
                $query = "SELECT * FROM molecule WHERE therminfo_id='{$thermid}'";
				if ($limit !== 0) {
					$query .= " LIMIT {$limit}";
				}
				// Interrogar a BD
				$queryResult = $this->find_by_sql($query);
				if (is_array($queryResult)) {
					$result = $queryResult; // Moleculas encontradas
				}
            }
        }
        return $result;
    }
	
	/**
     * Pesquisa pela formula molecular do composto
     *
     * @param string $mol_form Formula para pesquisa
	 * @param int $limit Limite dos resultados (0 - sem limite)
     *
     * @return mixed Array com as moleculas encontradas 
	 * ou '0' em caso de falha ou '1' se a formula molecular 
	 * estiver em branco ou '2' se a formula for invalida
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
    public function find_by_formula($mol_form = '', $limit = 0) 
	{
        $result = 0;
        if (empty($mol_form))
		{
            $result = 1; // Formula em branco
        }
		else
		{
            // Verificacao da formula
            $original_form = $mol_form;
            $res = $this->molecular_formula_parser->parse_mol_f($mol_form);
            $f = '';
            foreach (array_slice($res, 0, 10) as $key => $value)
			{
                if ($value != '0')
				{
                    if ($value == '1') {
                        $f .= $key;
					} else {
                        $f .= $key . $value;
					}
                }
            }

            $mol_form = $f;
            $pattern = '?';
            $replacement = '_';
            $mol_form = str_replace($pattern, $replacement, $mol_form);
            if ($mol_form == '')
			{
				$result = 2; // Formula molecular invalida
            }
			else
			{
                $original_form = $this->DB->escape_like_str($original_form);
				// Query final
                $query = "SELECT * FROM molecule WHERE formula LIKE '%{$original_form}%'";
				if ($limit !== 0) {
					$query .= " LIMIT {$limit}";
				}
                // Interrogar a BD
				$queryResult = $this->find_by_sql($query);
				if (is_array($queryResult)) {
					$result = $queryResult; // Moleculas encontradas
				}
            }
        }
        return $result;
    }
	
	/**
	 * Pesquisa pelo SMILES do composto, para predicao 
	 * de propriedades
     *
     * @param string $searchterm SMILES para pesquisa
	 * @param string $threshold Threshold de similaridade
	 * @param int $limit Limite dos resultados (0 - sem limite)
     * 
     * @return mixed Array com as moleculas encontradas ou 
	 * '0' em caso de falha ou '1' se o SMILES estiver em 
	 * branco ou '2' se o SMILES for invalido ou '3' se o 
	 * numero de SMILES similares for zero
	 * 
	 * [Array([int] => object(Molecule_model))]
	 */
	public function find_by_smiles_pred($smiles = '', $threshold = '', $limit = 100)
	{
		$result = 0;
        if (empty($smiles))
		{
            $result = 1; // SMILES em branco
        }
		else
		{
            $field = array();
			$mw = $this->obabel->calc_MW($smiles, 2);
            // Verificar o peso molecular
            if (! $mw) 
			{
				$result = 2; // SMILES invalido
            }
			else
			{
                // Verificar o intervalo do threshold
                $inter = 0;
                if ($threshold == 'i1')
				{
                    $threshold1 = '0.9';
                    $threshold2 = '0.95';
                    $inter = 1;
                }
				elseif ($threshold == 'i2')
				{
                    $threshold1 = '0.8';
                    $threshold2 = '0.9';
                    $inter = 1;
                }
				elseif ($threshold == 'i3')
				{
                    $threshold1 = '0.7';
                    $threshold2 = '0.8';
                    $inter = 1;
                }
				
                // Calcular os SMILES similares
                if ($inter == 1)
				{
                    $similar_smiles = $this->obabel->calc_similarity($smiles, $threshold1, $threshold2, 1);
                    $this->set_similar_smiles($similar_smiles);
                    $linhas = count($similar_smiles);
                }
				else
				{
                    $similar_smiles = $this->obabel->calc_similarity($smiles, $threshold);
                    $this->set_similar_smiles($similar_smiles);
                    $linhas = count($similar_smiles);
                }
				
                if ($linhas <= 0)
				{
					$result = 3; // Numero de SMILES similares zero
                }
				else
				{
					// Guardar os SMILES similares num tabela temporaria
					$sql = 'DROP TEMPORARY TABLE IF EXISTS sim_sum';
					$this->DB->query($sql) or die("TEMP DB DROP 1 Error");
					$sql = 'CREATE TEMPORARY TABLE sim_sum(mid INT NOT NULL, sim FLOAT(6,3) NOT NULL)';
					$this->DB->query($sql) or die("TEMP DB CREATE Error");
					foreach ($similar_smiles as $ss)
					{
						$sss = explode(' ', $ss);
						$sim = $sss[0];
						$midsmi = $sss[1];
						$sql = "INSERT INTO sim_sum(mid, sim) VALUES ({$midsmi}, {$sim})";
						$this->DB->query($sql) or die("TEMP DB INSERT Error");
					}
					// Construir a query final
					$consulta = $this->DB->query('SELECT mid FROM sim_sum') or die("TEMP DB SELECT Error");
					$query = "SELECT m.mid, ABS(m.mw-{$mw}) AS dif FROM molecule AS m, sim_sum WHERE m.mid IN (";
					
					$i = 0;
					foreach ($consulta->result_array() as $row)
					{
						if ($i == 0) {
							$query .= $row['mid'];
						} else {
							$query .= ', '. $row['mid'];
						}
						++$i;
					}
					$query .= ') AND sim_sum.mid = m.mid HAVING dif <= 1 ORDER BY dif ASC';
					
					if ($limit !== 0) {
						$query .= " LIMIT {$limit}";
					}
					// Interrogar a BD
					$queryResult = $this->DB->query($query);
					if ($queryResult && $queryResult->num_rows() >= 0)
					{
						$this->set_num_results($queryResult->num_rows());
						foreach ($queryResult->result_array() as $row)
						{
							$data = $this->find_by_id($row['mid']);
							if (! empty($data)) {
								array_push($field, $data);
							}
						}
						$result = $field; // Moleculas encontradas
					}
					// Eliminar a tabela temporaria
					$sql = 'DROP TEMPORARY TABLE IF EXISTS sim_sum';
					$this->DB->query($sql) or die("TEMP DB DROP 2 Error");
                }
            }
        }
        return $result;
	}
	
	/**
	 * Pesquisa os compostos associados a um
	 * utilizador
	 *
	 * @param int $user_id ID do utilizador
	 *
	 * @return mixed Array com as moleculas encontradas
	 * ou '0' em caso de falha ou '1' caso o ID do
	 * utilizador estiver em branco
	 *
	 * [Array([int] => object(Molecule_model))]
	 */
	public function find_compounds_from_user($user_id = 0)
	{
		$result = 0;
		if (empty($user_id))
		{
			$result = 1; // ID do utilizador em branco
		}
		else
		{
			$user_id = $this->DB->escape($user_id);
			// Interrogar a BD
			$queryResult = $this->DB->query("SELECT molecule FROM mol_user WHERE user={$user_id}");
			if ($queryResult && $queryResult->num_rows() >= 0)
			{
				$result = array();
				$this->set_num_results($queryResult->num_rows());
				foreach ($queryResult->result_array() as $row)
				{
					$data = $this->find_by_id($row['molecule']);
					if (! empty($data)) {
						array_push($result, $data);
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * Pesquisa pelo sinonimo do composto
	 *
	 * @param string $name Sinonimo
	 * @param int $limit Limite dos resultados (0 - sem limite)
	 *
	 * @return mixed Array com as moleculas encontradas
	 * ou '0' em caso de falha ou '1' caso o sinonimo
	 * estiver em branco
	 *
	 * [Array([int] => object(Molecule_model))]
	 */
	public function find_by_othername($name = '', $limit = 0)
	{
		$result = 0;
		if (empty($name))
		{
			$result = 1; // Sinonimo em branco
		}
		else
		{
			$name = $this->DB->escape_like_str($name);
			// Query
			$queryResult = $this->DB->query("SELECT molecule, INSTR(synonym, '{$name}') AS pos FROM othername WHERE synonym LIKE '%{$name}%' ORDER BY pos");
			if ($limit !== 0) {
				$query .= " LIMIT {$limit}";
			}
			// Interrogar a BD
			if ($queryResult && $queryResult->num_rows() >= 0)
			{
				$result = array();
				$this->set_num_results($queryResult->num_rows());
				foreach ($queryResult->result_array() as $row)
				{
					$data = $this->find_by_id($row['molecule']);
					if (! empty($data)) {
						array_push($result, $data);
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * Pesquisa pelo InChi da molecula
	 *
	 * @param string $inchi InChi
	 * @param string $verify Valida o InChi
	 * @param int $limit Limite dos resultados (0 - sem limite)
	 *
	 * @return mixed Array com as moleculas encontradas
	 * ou '0' em caso de falha ou '1' se o InChi estiver 
	 * em branco ou '2' se o InChi for invalido
	 *
	 * [Array([int] => object(Molecule_model))]
	 */
	public function find_by_inchi($inchi = '', $verify = TRUE, $limit = 0)
	{
		$result = 0;
		if (empty($inchi))
		{
			$result = 1; // InChi em branco
		}
		else
		{
			// Verificacao do formato do InChi
			$verify_inchi = TRUE;
			if ($verify)
			{
				$inchi_valid = $this->obabel->verify_inchi($inchi);
	            if (! $inchi_valid) {
					$verify_inchi = FALSE;
	            }
			}
			
			if (! $verify_inchi)
			{
				$result = 2; // InChi invalido
			}
			else
			{
				$inchi = $this->DB->escape_like_str($inchi);
				// Query
				$query = "SELECT * FROM molecule WHERE inchi LIKE '{$inchi}%' OR s_inchi LIKE '{$inchi}%'";
				if ($limit !== 0) {
					$query .= " LIMIT {$limit}";
				}
				
				// Interrogar a BD
				$queryResult = $this->find_by_sql($query);
				if (is_array($queryResult)) {
					$result = $queryResult; // Moleculas encontradas
				}
			}
		}
		return $result;
	}
	
	/**
     * Pesquisa avancada com varios campos
     *
     * @param string $search_term Nome do composto
     * @param string $state Estado fisico
     * @param string $formula Formula molecular
     * @param string $mwsign Sinal do peso molecular
     * @param int $mw Valor do peso molecular
     * @param string $smiles SMILES do composto
     * @param string $smilesinterv Intervalo para o SMILES
     * @param string $class Classe do composto
     * @param string $subclass Subclasse do composto
     * @param string $family Familia do composto
     * @param array $chars Caracteristicas do composto
	 * @param int $limit Limite dos resultados (0 - sem limite)
     *
     * @return mixed Array com as moleculas encontradas ou '0' em caso de falha
     * ou '1' se os campos estiverem em branco ou '2' se nao existir moleculas 
	 * com as carateristicas enviadas ou '3' se a formula molecular for invalida 
	 * ou '4' se o pesso molecular for invalido ou '5' se o SMILES for invalido 
	 * ou '6' se nao existe SMILES similares 
	 * 
	 * [Array([int] => object(Molecule_model))]
     */
    public function advanced_search($search_term = '', $state = '', $formula = '', $mwsign = '', 
							$mw = '', $smiles = '', $smilesinterv = '', $class = '', 
							$subclass = '', $family = '', $chars = '', $limit = 0)
	{
        $result = 0;
		if (empty($search_term) && empty($state) && empty($formula) && 
			empty($mw) && empty($smiles) && empty($class) && 
			empty($subclass) && empty($family) && empty($chars))
		{
            $result = 1; // Campos em branco
        } 
		else
		{
			$error = 0;
            // ** Inicio da construcao dinamica da query para a pesquisa **
            $query = 'SELECT m.mid, m.therminfo_id, m.casrn, m.name, m.formula, m.state, m.smiles';

            // - Campo nome preenchido -
            if (! empty($search_term))
			{
                $mw_comp1 = $this->obabel->calc_MW($search_term, 1); // Peso molecular

                if ($mw_comp1) {
                    $query .= ", ABS(m.mw-{$mw_comp1}) AS dif"; // Diferenca de peso
				}
            }
            $query .= ' FROM molecule AS m';

            // - Campo familia preenchido -
            if (! empty($family)) {
                $query .= ', family AS f';
			}
            // - Campo classe preenchido -
            if (! empty($class)) {
                $query .= ', class AS c';
			}
            // - Campo subclasse preenchido -
            if (! empty($subclass)) {
				$query .= ', subclass AS s';
			}
            // - Campo nome preenchido -
            if (! empty($search_term))
			{
                $search_term = $this->DB->escape_like_str($search_term);
                $query .= ", ((SELECT mid, name, INSTR(name, '{$search_term}') AS fpos 
				FROM molecule WHERE name LIKE '%{$search_term}%') UNION (SELECT molecule, 
				synonym, INSTR(synonym, '{$search_term}') AS fpos FROM othername WHERE synonym 
				LIKE '%{$search_term}%')) AS a";
            }
            $query .= ' WHERE 1';

            // - Campo caracteristicas preenchido -
            if (! empty($chars) && is_array($chars))
			{
                $i = 0;
                $subquery = 'SELECT molecule FROM mol_char WHERE charact IN(';
				foreach ($chars as $char)
				{
					$consulta = $this->DB->query("SELECT cid FROM characteristic WHERE characteristic.ch_name = '{$char}'");
					if ($consulta)
					{
						$registo = $consulta->row_array();
						if ($i == 0) {
							$subquery .= $registo['cid'];
						} else {
							$subquery .= ", {$registo[cid]}";
						}
					}
					++$i;
				}

				$subquery .= ") GROUP BY molecule HAVING COUNT(*) >= {$i}";
				$consulta = $this->DB->query($subquery);
				if ($consulta)
				{
					$linhas = $consulta->num_rows();
					if ($linhas <= 0)
					{
						$error = 1; // Zero moleculas com as carateristicas seleccionadas
					}
					else
					{
						$query .= ' AND m.mid IN(';
						for ($i = 0; $i < $linhas; ++$i) 
						{
							$registo = $consulta->row_array($i);
							if ($i == 0) {
								$query .= $registo['molecule'];
							} else {
								$query .= ", {$registo[molecule]}";
							}
						}
					}
					$query .= ')';
				}
            }

            // - Campo nome preenchido -
            if (! empty($search_term)) {
                $query .= ' AND a.mid = m.mid';
			}
            // - Campo formula molecular preenchido -
            if (! empty($formula))
			{
                // Verificacao da formula
                $form_original = $formula;
                $res = $this->molecular_formula_parser->parse_mol_f($formula);
                $f = '';
                foreach (array_slice($res, 0, 10) as $key => $value)
				{
                    if ($value != '0')
					{
                        if ($value == '1') {
                            $f .= $key;
                        } else {
                            $f .= $key . $value;
						}
                    }
                }

                $formula = $f;
                $pattern = "?";
                $replacement = "_";
                $formula = str_replace($pattern, $replacement, $formula);
				$formula = $this->DB->escape_like_str($formula);
                if (! empty($formula)) {
                    $query .= " AND m.formula LIKE '%{$formula}%'";
                } else {
                    $error = 2; // Formula molecular invalida
				}
            }

            // - Campo peso molecular preenchido -
            if (! empty($mwsign) && ! empty($mw))
			{
                // Verifica se o peso molecular é numero
                if (! is_numeric($mw))
				{
					$error = 3; // Peso molecular invalido
                } 
				else 
				{
                    $mw = $this->DB->escape($mw);
                    $query .= " AND m.mw {$mwsign} {$mw}";
                }
            }

            // - Campo estado preenchido -
            if (! empty($state)) {
                $query .= " AND m.state = '{$state}'";
			}
            // - Campo classe preenchido -
            if (! empty($class)) {
                $query .= " AND m.class = c.cid AND c.c_name = '{$class}'";
			}
            // - Campo subclasse preenchido -
            if (! empty($subclass)) {
				$query .= " AND m.subclass = s.scid AND s.sc_name = '{$subclass}'";
			}
            // - Campo familia preenchido -
            if (! empty($family)) {
                $query .= " AND m.family = f.fid AND f.f_name = '{$family}'";
			}
            // - Campo SMILES preenchido -
            if (! empty($smiles) && ! empty($smilesinterv))
			{
                $mw_comp2 = $this->obabel->calc_MW($smiles, 2); // Peso molecular
				// Verificar o peso molecular
                if (! $mw_comp2)
				{
					$error = 4; // SMILES invalido
                }
				else 
				{
                    // Verificar o intervalo do threshold
                    $inter = 0;
                    if ($smilesinterv == 'i1')
					{
                        $threshold1 = '0.9';
                        $threshold2 = '0.95';
                        $inter = 1;
                    }
					elseif ($smilesinterv == 'i2')
					{
                        $threshold1 = '0.8';
                        $threshold2 = '0.9';
                        $inter = 1;
                    }
					elseif ($smilesinterv == 'i3')
					{
                        $threshold1 = '0.7';
                        $threshold2 = '0.8';
                        $inter = 1;
                    }

                    // Calcular os SMILES similares
                    if ($inter == 1)
					{
                        $similar_smiles = $this->obabel->calc_similarity($smiles, $threshold1, $threshold2, 1);
                        $this->set_similar_smiles($similar_smiles);
                        $nrsmiles = count($similar_smiles);
                    }
					else
					{
                        $similar_smiles = $this->obabel->calc_similarity($smiles, $smilesinterv);
                        $this->set_similar_smiles($similar_smiles);
                        $nrsmiles = count($similar_smiles);
                    }

                    if ($nrsmiles > 0)
					{
                        $query .= ' AND m.mid IN(';
                        $j = 0;
                        foreach ($similar_smiles as $ss)
						{
                            $sss = explode(' ', $ss);
                            $midsmi = $sss[1];
                            if ($j != 0) {
                                $query .= ",";
							}
                            $query .= $midsmi;
                            ++$j;
                        }
                        $query .=')';
                    }
					else
					{
						$error = 5; // Numero de SMILES similares zero
					}
                }
            }
			$query .= ' AND m.validated = 1 AND m.outdated = 0';

            // - Campo nome preenchido -
            if (! empty($search_term)) 
			{
                $query .= ' GROUP BY a.mid ORDER BY a.fpos'; // Ordenar pela posicao do termo da pesquisa
                if ($mw_comp1) {
                    $query .= ', dif'; // Ordenar pela diferenca de peso
				}
				$query.= ', m.name';
            }
			
			if ($limit !== 0) {
				$query .= " LIMIT {$limit}";
			}
            // ** Fim da construcao da query para pesquisa **
			
            // ** Se nao existir erros **
            if ($error == 0) 
			{
                // ** Efectuar a pesquisa **
                $field = array();
                // Interrogar a BD
				$queryResult = $this->DB->query($query);
				if ($queryResult && $queryResult->num_rows() >= 0) 
				{
					$this->set_num_results($queryResult->num_rows());
					foreach ($queryResult->result_array() as $row) 
					{
						$data = $this->find_by_id($row['mid']);
						if (! empty($data)) {
							array_push($field, $data);
						}
					}
					$result = $field; // Moleculas encontradas
				}
            }
			elseif ($error == 1) 
			{
                $result = 2; // Zero moleculas com as carateristicas seleccionadas
            } 
			elseif ($error == 2) 
			{
                $result = 3; // Formula molecular invalida
            } 
			elseif ($error == 3) 
			{
                $result = 4; // Peso molecular invalido
            } 
			elseif ($error == 4) 
			{
                $result = 5; // SMILES invalido
            }
			elseif ($error == 5) 
			{
                $result = 6; // Numero de SMILES similares zero
            }
		}
		return $result;
	}
	
	/**
     * Pesquisa da subestrutura pelo SMILES/SMARTS
     *
     * @param string $search_term SMILES/SMARTS para pesquisa
	 * @param boolean $is_smarts SMARTS ou nao
     *
     * @return mixed Array com as moleculas encontradas 
	 * e com o respectivo numero de SMARTS ou '0' em caso 
	 * de falha ou '1' se o SMILES/SMARTS estiver em branco
	 * ou '2' se o SMILES/SMARTS for invalido
	 * 
	 * [Array([int] => Array(['smarts'] => [int], 
	 * ['molecule'] => object(Molecule_model)))]
     */
	public function subs_search($search_term = '', $is_smarts = FALSE) 
	{
		$result = 0;
		if (empty($search_term))
		{
			$result = 1; // SMILES em branco
		}
		else
		{
			// Calcular o peso molecular
			$mw = $this->obabel->calc_MW($search_term, 2);
			// Verificar o peso molecular
			if (! $mw && ! $is_smarts)
			{
				$result = 2; // SMILES invalido
			}
			else
			{
				$field = array();
				// Procurar os SMARTS
				$smarts = $this->obabel->calc_smarts($search_term);
				if ($smarts)
				{
					$this->set_num_results(count($smarts));
					foreach ($smarts as $mid => $num)
					{
						$data = $this->find_by_id($mid);
						if (! empty($data)) {
							array_push($field, array('smarts' => $num, 'molecule' => $data));
						}
					}
					$result = $field; // Moleculas encontradas
				}
				else
				{
					$result = 2; // SMILES ou SMARTS invalido
				}
			}
		}
		return $result;
	}
	
	/**
	 * Pesquisa por valores de propriedades
	 *
	 * @param
	 * @param
	 *
	 * @return mixed Array com as moleculas encontradas ou '0' em caso de falha
     * ou '1' se os campos estiverem em branco
	 *
	 * [Array([int] => object(Molecule_model))]
	 */
	public function properties_search()
	{
		$result = 0;
		
		// ** Inicio da construcao dinamica da query para a pesquisa **
        $query = 'SELECT * FROM molecule, molecule_data_ref WHERE mid=molecule';
		
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
	 * @param boolean $escape Remove carateres especiais
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
				case 'mid':
				{
					if (isset($value) && ! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => 'id',
									'error' => 'is not numeric');
					}
				} break;
				// ThermInfo ID
				case 'therminfo_id':
				{
					if (! $this->obabel->verify_thermid($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'invalid');
					}
				} break;
				// CAS RN
				case 'casrn':
				{
					if (! empty($value) && $this->obabel->verify_casrn($value) !== TRUE)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'invalid');
					}
				} break;
				// Nome
				case 'name':
				{
					if (! empty($value) && strlen($value) > 255)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 255');
					}
				} break;
				// Formula molecular
				case 'formula':
				{
					if (! empty($value) && strlen($value) > 255)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 255');
					}
				} break;
				// Peso molecular
				case 'mw':
				{
					if (! empty($value) && ! is_numeric($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'is not numeric');
					}
				} break;
				// State
				case 'state':
				{
					if (! empty($value) && ($value != 's' or $value != 'l' or $value != 'g' or $value != 'cr' or $value != 'c'))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => "is not 's', 'l', 'g', 'cr' or 'c'");
					}
				} break;
				// Phisical form
				case 'phi_form' :
				{
					if (! empty($value) && strlen($value) > 150)
					{
						return array('status' => FALSE, 
									'result' => 'physical form',
									'error' => 'Length greater than 150');
					}
				} break;
				// SMILES
				case 'smiles':
				{
					if (! empty($value) && ! $this->obabel->calc_MW($value, 2))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'invalid');
					}
				} break;
				// InChi
				case 'inchi':
				{
					if (! empty($value) && ! $this->obabel->verify_inchi($value))
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'invalid');
					}
				} break;
				// InChiKey
				case 'inchikey':
				{
					if (! empty($value) && strlen($value) > 27)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'invalid');
					}
				} break;
				// Std. InChi
				case 's_inchi':
				{
					if (! empty($value) && ! $this->obabel->verify_inchi($value))
					{
						return array('status' => FALSE, 
									'result' => 'std. inchi',
									'error' => 'invalid');
					}
				} break;
				// Std. InChiKey
				case 's_inchikey':
				{
					if (! empty($value) && strlen($value) > 27)
						{
						return array('status' => FALSE, 
									'result' => 'std. inchikey',
									'error' => 'invalid');
					}
				} break;
				// Image path
				case 'img_path':
				{
					if (! empty($value) && strlen($value) > 200)
					{
						return array('status' => FALSE, 
									'result' => $key,
									'error' => 'length greater than 200');
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

/* End of file molecule_model.php */
/* Location: ./application/models/molecule_model.php */