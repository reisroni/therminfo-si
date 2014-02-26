<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- OBabel
 * Descricao:  Algumas utilidades com o software Open Babel, conversao de formatos 
 * (Nome, SMILES, InCHI, Formula, MOL File), calculadora do peso molecular
 * Criado: 13-09-2011
 * Modificado: 08-02-2014
 * @author Rony Reis, Ana Teixeira
 * @version 0.2
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */

class OBabel {
	
	// Caminho do python e dos scripts python
    private $py_cmd;
	private $py_path;
	
    /**
     * Construtor do modulo
     */
    function __construct()
    {
        $this->py_cmd = 'python';
        //$this->py_cmd = 'C:'. DS .'Applics'. DS .'Jcompiler'. DS .'xampp-portable'. DS .'python'. DS .'python';
        $this->py_path = 'scripts'. DS .'py';
    }
    
	//---------------------------------------------------------------
	// Calculadora do peso molecular
	//---------------------------------------------------------------
    /**
     * Calcula o peso molecular de um composto
     * 
     * @param string $term O composto
     * @param string $type Tipo de identificador do composto 
	 * (1 - Nome, 2 - SMILES, 3 - InChi, 4 - Formula)
     *
     * @return mixed Valor do peso molecular, ou 'FALSE'
     * em caso de falha
     */
    public function calc_MW($term = '', $type = 0) 
	{
        $result = FALSE;
		// Carregar a 'library' CACTUS.
		$CI =& get_instance();
		$CI->load->library('Cactus');
        
        switch ($type)
		{
			// por nome
            case 1 :
			{
				$smi = $CI->cactus->get_smiles($term); // Obter o SMILES do CACTUS
				if ($smi)
				{
					// Execucao do script python
					exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py smi \"{$smi}\" -w", $mw_comp);
					if (! empty($mw_comp)) {
						$result = (double)number_format($mw_comp[0], 3); // Peso molecular
					}
				}
			} break;
			// por SMILES
            case 2 :
			{
				// Execucao do script python
				exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py smi \"{$term}\" -w", $mw_comp);
				if (! empty($mw_comp))
				{
					if ($mw_comp[0] != '0') {
						$result = (double)number_format($mw_comp[0], 3); // Peso molecular
                    }
				}
			} break;
			// por InChi
			case 3 :
			{
				exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py inchi \"{$term}\" -w", $mw_comp); // Execucao do script python
				if (! empty($mw_comp))
				{
					if ($mw_comp[0] != '0') {
						$result = (double)number_format($mw_comp[0], 3); // Peso molecular
                    }
				}
			} break;
			// por formula molecular
			case 4 :
			{
				exec("formol \"{$term}\"", $mw_comp); // Execucao do software 'formol' 
				if (! empty($mw_comp)) {
					$result = (double)number_format($mw_comp[0], 3); // Peso molecular
				}
			} break;
        }
        return $result;
    }
	
	//---------------------------------------------------------------
	// Gerar imagem
	//---------------------------------------------------------------
    /**
     * Gera a imagem de um composto nos formatos png e pdf
     * 
     * @param string $smiles SMILES do composto
     *
     * @return mixed Nome do ficheiro gerado, ou 'FALSE'
     * em caso de falha
     */
	public function get_imgfile($smiles = '')
	{
		$result = FALSE;
		if (!empty($smiles))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."generateimg.py \"{$smiles}\"", $data);
			if ($data[0] != '0')
			{
				$file = $data[0];
				if (file_exists($file.'.png')) {
					$result = $file;
				}
			}
		}
		return $result;
	}
	
	//---------------------------------------------------------------
	// SMILES similirares, SMARTS e Valores de Propriedades
	//---------------------------------------------------------------
    /**
     * Procura SMILES similares
     * 
     * @param string $term SMILES para procurar similares
     * @param float $threshold_1 Valor de percentagem de similaridade
     * @param float $threshold_2 Valor para o intervalo 
	 * de percentagem de similaridade
     * @param int $inter Definir se existe intervalo, 0 - Nao, 1 - Sim
     * 
     * @return array Conjunto de SMILES similares 
	 * (cada linha mostra o 'mid' e o valor da similaridade)
	 * ou 'FALSE' em caso de falha
	 *
	 * [Array([int] => 'sim mid')]
     */
    public function calc_similarity($term = '', $threshold_1 = 0, $threshold_2 = 0, $inter = 0) 
	{
		$result = FALSE;
        switch ($inter)
		{
			// Sem intervalo
            case 0 :
			{
				// Execucao do script python
				exec("{$this->py_cmd} {$this->py_path}". DS ."similarity.py \"{$term}\" {$threshold_1}", $similar_smiles);
				$result = $similar_smiles;
			} break;
			// Com intervalo
            case 1 :
			{
				// Execucao do script python
				exec("{$this->py_cmd} {$this->py_path}". DS ."similarity_intervals.py \"{$term}\" {$threshold_1} {$threshold_2}", $similar_smiles);
				$result = $similar_smiles;
			} break;
        }
        return $result;
    }
	
	/**
     * Procura os SMARTS de um SMILES
     * 
     * @param string $smiles SMILES
     * 
     * @return mixed Array com MID e numero de SMARTS obtido 
	 * ou 'FALSE' em caso de falha ou se o SMILES for invalido
	 * 
	 * [Array([mid] => 'smarts')]
     */
    public function calc_smarts($smiles = '')
	{
		$result = FALSE;
		if (! empty($smiles)) 
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."smarts.py \"{$smiles}\"", $data);
			if (! empty($data))
			{
				$smarts = $data[0];
				if ($smarts != "Invalid SMARTS pattern")
				{
					// Formatar o resultado em um array
					$smarts = str_replace("'", '', $smarts);
					$smarts = str_replace('[', '', $smarts);
					$smarts = str_replace(']', '', $smarts);
					$pares_smarts = explode('), (', $smarts);
					foreach ($pares_smarts as $p) 
					{
						$p = str_replace('(', '',  $p);
						$p = str_replace(')', '', $p);
						
						if ($p != '') 
						{
							list($mid, $nrsmarts) = explode(', ', $p);
							$mid = trim($mid);
							$result[$mid] = $nrsmarts;
						}
					}
				}
			}
		}
        return $result;
    }
	
	/**
	 * Procura os valores de propriedades de um composto ***
	 * 
	 * @param string $smiles SMILES do composto
	 * @param int $bonds Ligacoes duplas
	 * 
	 * @return mixed Array com o resultado ou  'FALSE'
	 * em caso de falha ou se o SMILES for invalido
	 * 
	 * [Array('text','gas','liq','vap','params',
	 * 'gas_nop','liq_nop','vap_nop','gas_pzero',
	 * 'liq_pzero', 'vap_pzero')]
	 */
	public function calc_props($smiles = '', $bonds = 0)
	{
		$result = FALSE;
		// Ficheiro MOL do SMILES
		$mol_file = $this->smiles_to_molfile($smiles, substr(uniqid(), -5));
        
		if ($mol_file)
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."params2.py \"{$smiles}\" {$bonds} \"{$mol_file}\"", $prop_predict);
			if (! empty($prop_predict))
			{
				$text = $prop_predict[0];
				$text = str_replace("'", "", $text);
				$text = str_replace("(", "", $text);
				$text = str_replace(")", "", $text);
				$text = str_replace("]", "", $text);
				
				// list($name_comp, $o_names) = explode(", [", $names);
				if (count($prop_predict) > 1)
				{
					$result = array('text' => $text, 'gas' => $prop_predict[1], 'liq' => $prop_predict[2],
									'vap' => $prop_predict[3], 'params' => $prop_predict[4],
									'gas_nop' => $prop_predict[5], 'liq_nop' => $prop_predict[6],
									'vap_nop' => $prop_predict[7], 'gas_pzero' => $prop_predict[8],
									'liq_pzero' => $prop_predict[9], 'vap_pzero' => $prop_predict[10]
					);
				}
				else
				{
					$result = array('text' => $text);
				}
			}
		}
		return $result;
	}
	
	//---------------------------------------------------------------
	// Formula Molecular
	//---------------------------------------------------------------
	/**
	 * Gerar a formula molecular
	 * 
	 * @param string $mol Composto
	 * @param int $type Tipo de identificador do composto
	 * (1 - SMILES, 2 - InChi)
	 * 
	 * @return mixed A formula molecular ou 'FALSE' em caso
	 * de falha ou se o composto for invalido
	 */
	public function get_formula($mol = '', $type = 0)
	{
		$result = FALSE;
		// Verifica o tipo de identificador do composto
        switch ($type)
		{
			// por SMILES
			case 1 :
			{
				// Execucao do script python
				exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py smi \"{$mol}\" -f", $data);
				if (! empty($data))
				{
					if ($data[0] != '0') {
						$result = $data[0]; // Formula molecular
                    }
				}
			} break;
			// por InChi
			case 2 :
			{
				// Execucao do script python
				exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py inchi \"{$mol}\" -f", $data);
				if (! empty($data))
				{
					if ($data[0] != '0') {
						$result = $data[0]; // Formula molecular
                    }
				}
			}
		}
		return $result;
	}
	
	//---------------------------------------------------------------
	// Conversao de formatos
	//---------------------------------------------------------------
	/**
     * Converter um SMILES para um ficheiro MOL
     * 
     * @param string $smiles SMILES
	 * @param string $name nome para o ficheiro SMILES temporario
     * 
     * @return mixed Ficheiro MOL obtido ou 'FALSE' em caso de falha
     */
	public function smiles_to_molfile($smiles = '', $name = '')
	{
		$result = FALSE;
		// Criar do ficheiro SMILES e MOL
		if (! empty($smiles)) 
		{
			$smif = 'storage'. DS ."smi_{$name}.smi"; // SMILES file
			$fh = fopen($smif, 'w');
			fwrite($fh, $smiles);
			fclose($fh);
			
			$molf = 'storage'. DS ."mol_{$name}.mol"; // MOL file
            exec("babel {$smif} {$molf} --gen3d"); // Execucao do OBabel
			
			$result = $molf;
		}
        return $result;
	}
	
	/**
	 * Converter SMILES para MDL MOL
	 * 
	 * @param string $smiles SMILES a ser convertido
	 * 
	 * @return mixed o MDL MOL correspondente ou 
	 * 'FALSE' em caso de falha ou se o SMILES for invalido
	 */
	public function smiles_to_mol($smiles = '')
	{
		$result = FALSE;
		if (! empty($smiles))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py smi \"{$smiles}\" -cm", $data);
			if (! empty($data))
			{
				if ($data[0] != '0')
				{
					$result = '';
					foreach($data as $row) {
						$result .= $row ."\n"; // MDL MOL
					}
				}
			}
		}
		return $result;
	}
	
	/**
	 * Converter SMILES para InChi
	 * 
	 * @param string $smiles SMILES a ser convertido
	 * 
	 * @return mixed o InChi correspondente ou 'FALSE' 
	 * em caso de falha ou se o SMILES for invalido
	 */
	public function smiles_to_inchi($smiles = '')
	{
		$result = FALSE;
		if (! empty($smiles))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py smi \"{$smiles}\" -ci", $data);
			if (! empty($data))
			{
				if ($data[0] != '0') {
					$result = $data[0]; // InChi
                }
			}
		}
		return $result;
	}
	
	/**
	 * Converter SMILES para InChiKey
	 * 
	 * @param string $smiles SMILES a ser convertido
	 * 
	 * @return mixed o InChiKey correspondente ou 
	 * 'FALSE' em caso de falha ou se o SMILES for invalido
	 */
	public function smiles_to_inchikey($smiles = '')
	{
		$result = FALSE;
		if (! empty($smiles))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py smi \"{$smiles}\" -ck", $data);
			if (! empty($data))
			{
				if ($data[0] != '0') {
					$result = $data[0]; // InChiKey
                }
			}
		}
		return $result;
	}
	
	/**
	 * Converter InChi para SMILES
	 * 
	 * @param string $inchi InChi a ser convertido
	 * 
	 * @return mixed o SMILES correspondente ou 'FALSE' 
	 * em caso de falha ou se o InChi for invalido
	 */
	public function inchi_to_smiles($inchi = '')
	{
		$result = FALSE;
		if (! empty($inchi))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py inchi \"{$inchi}\" -cs", $data);
			if (! empty($data))
			{
				if ($data[0] != '0') {
					$result = $data[0]; // SMILES
                }
			}
		}
		return $result;
	}
	
	/**
	 * Converter InChi para InChiKey 
	 * 
	 * @param string $inchi InChi a ser convertido
	 * 
	 * @return mixed o InChiKey correspondente ou 
	 * 'FALSE' em caso de falha ou se o InChi for invalido
	 */
	public function inchi_to_inchikey($inchi = '')
	{
		$result = FALSE;
		if (! empty($inchi))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS. "check_mol.py inchi \"{$inchi}\" -ck", $data);
			if (! empty($data))
			{
				if ($data[0] != '0') {
					$result = $data[0]; // InChiKey
                }
			}
		}
		return $result;
	}
	
	/**
     * Converter um ficheiro MOL para SMILES
     * 
     * @param string $molfile Ficheiro MOL
	 * @param string $name nome para o ficheiro temporario
     * 
     * @return mixed SMILES obtido ou 'FALSE' em caso de falha
     */
    public function molfile_to_smiles($molfile = '', $name = '')
	{
		$result = FALSE;
		// Criar o ficheiro MOL
		if (! empty($molfile)) 
		{
			$molf = 'storage'. DS ."mol_{$name}.mol"; // MOL file
			$fh = fopen($molf, 'w');
			fwrite($fh, $molfile);
			fclose($fh);
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."molf2smi.py \"{$molf}\"", $molf2smi);
			
			if (! empty($molf2smi)) {
				$result = $molf2smi[0]; // SMILES
            }
		}
        return $result;
    }
	
	/**
	 * Converter InChi para MDL MOL
	 * 
	 * @param string $inchi InChi a ser convertido
	 * 
	 * @return mixed o MDL MOL correspondente ou 
	 * 'FALSE' em caso de falha ou se InChi for invalido
	 */
	public function inchi_to_mol($inchi = '')
	{
		$result = FALSE;
		if (! empty($inchi))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py inchi \"{$inchi}\" -cm", $data);
			if (! empty($data))
			{
				if ($data[0] != '0')
				{
					$result = '';
					foreach($data as $row) {
						$result .= $row ."\n"; // MDL MOL
					}
				}
			}
		}
		return $result;
	}
	
	//---------------------------------------------------------------
	// Verificacao de formatos
	//---------------------------------------------------------------
	/**
	 * Verifica se o InChi e valido
	 * 
	 * @param string $inchi InChi a ser verificado
	 * 
	 * @return boolean 'TRUE' ou 'FALSE'
	 */
	public function verify_inchi($inchi = '')
	{
		$result = FALSE;
		if (! empty($inchi))
		{
			// Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."check_mol.py inchi \"{$inchi}\"", $data);
			if (! empty($data))
			{
				if ($data[0] == '1') {
					$result = TRUE; // InChi valido
                }
			}
		}
		return $result;
	}
	
	/**
	 * Verifica o formato e o digito de controlo
	 * de um CAS RN
	 * 
	 * @param $casrn O CAS RN a verificar
	 * 
	 * @return mixed 'TRUE' se o CAS RN for valido 
	 * ou '1' se o formato for invalido ou '2' 
	 * se o digito de controlo for invalido
	 */
	public function verify_casrn($casrn = '')
	{
		// Verificacao do formato do CASRN
		if (preg_match('/(^[0-9]{1,7})-([0-9]{2})-([0-9]{1}$)/', $casrn, $regs))
		{
			// Verificacao do digito de controle no CASRN 
			$num = $regs[1] . $regs[2];
			$r = $regs[3];
			$n = str_split($num);
			$size = sizeof($n);
			$resultc = 0;
			$j = $size;
            
			for ($i = 0; $i < $size; $i++)
			{
				$j = $j - 1;
				$resultc += $n[$i] * ($j + 1);
			}
			
			$last = substr($resultc, -1);
			if ($last == $r) {
				$result = TRUE; // CASRN valido
			} else {
				$result = 2; // Digito de controle invalido
			}
		}
		else
		{
			$result = 1; // Formato invalido
		}
		return $result;
	}
	
	/**
	 * Verifica o formato do ThermInfo ID
	 * 
	 * @param $thermid O ThermInfo ID a verificar
	 * 
	 * @return mixed 'TRUE' se o ID for valido 
	 * ou 'FALSE' se for invalido
	 */
	public function verify_thermid($thermid = '')
	{
		// Verificacao do formato do ThermInfo ID
		if (! empty($thermid) && preg_match('/^CO([0-9]{7})$/i', $thermid)) {
			$result = TRUE; // Formato valido
		} else {
			$result = FALSE;
		}
		return $result;
	}
    
    //---------------------------------------------------------------
	// Outros
	//---------------------------------------------------------------
    /**
	 * Cria um ficheiro '.pkl' apartir de um ficheiro SMILES
	 * 
	 * @param $smiles_file O nome e localizacao do ficheiro SMILES
	 * 
	 * @return boolean 'TRUE' em caso de sucesso
     * ou 'FALSE' em caso de falha
	 */
	public function create_pkl($smiles_file = '')
	{
        $result = FALSE;
		if (! empty($smiles_file)) {
            // Execucao do script python
			exec("{$this->py_cmd} {$this->py_path}". DS ."similarity.py \"{$smiles_file}\"");
			
			if (is_really_writable('storage'. DS .'dbase.pkl')) {
                $result = TRUE;
            }
		}
        
		return $result;
	}
}

/* End of file OBabel.php */
/* Location: ./application/libraries/OBabel.php */