<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Cactus
 * Descricao:  Algumas utilidades do servico CACTUS
 * Criado: 13-09-2011
 * Modificado: 02-02-2014
 * @author Rony Reis, Ana Teixeira
 * @version 0.2
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */

class Cactus {
	
	// URL do CACTUS (Chemical Identifier Resolver)
	private $cactus_url = 'http://cactus.nci.nih.gov/chemical/structure';
	// Var para carregar o CI core
	private $CI;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
		// Carregar a 'library' Util
		$this->CI =& get_instance();
		$this->CI->load->library('Util');
    }

    /**
	 * Retorna o SMILES de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, CAS RN, InChi, InChiKey)
	 * @param string $type Tipo do composto (nome, casrn, inchi, inchikey)
	 *
	 * @return mixed SMILES correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
    public function get_smiles($term = '', $type = '') 
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		}
		
        $url = "{$this->cactus_url}/{$term}/smiles";
        $generated_smiles = $this->CI->util->get_url_contents($url);
        
        if ($generated_smiles)
		{
            $smiles = explode("\n", $generated_smiles, 1);
            if (strlen($smiles[0]) == 0 or empty($smiles[0])) {
                $smiles[0] = FALSE;
            }

            $result = $smiles[0];
        }
		else
		{
            $result = FALSE;
        }
		
        return $result;
    }
	
	/**
	 * Retorna o nome de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (SMILES, CAS RN, InChi, InChiKey)
	 * @param string $type Tipo do composto (smiles, casrn, inchi, inchikey)
	 *
	 * @return mixed Nome correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
    public function get_name($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/names";
        $generated_name = $this->CI->util->get_url_contents($url);
        
        if ($generated_name)
		{
            $name = explode("\n", $generated_name, 2);
            if (strlen($name[0]) == 0 or empty($name[0])) {
                $name[0] = FALSE;
            }

            $result = $name[0];
        }
		else
		{
            $result = FALSE;
        }
		
        return $result;
    }
	
	/**
	 * Retorna os nomes de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (SMILES, CAS RN, InChi, InChiKey)
	 * @param string $type Tipo do composto (smiles, casrn, inchi, inchikey)
	 *
	 * @return mixed Array com os nomes ou 'FALSE' 
	 * em caso de falha ou se nao existir nomes
	 */
    public function get_names($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/names";
        $generated_names = $this->CI->util->get_url_contents($url);
        
        if ($generated_names)
		{
            $result = explode("\n", $generated_names);
            if (count($result) == 0 or empty($result)) {
                $result = FALSE;
            }
        }
		else
		{
            $result = FALSE;
        }
		
        return $result;
    }
	
	/**
	 * Retorna o CAS RN de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, SMILES, InChi, InChiKey)
	 * @param string $type Tipo do composto (nome, smiles, inchi, inchikey)
	 * 
	 * @return mixed O CAS RN correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
	public function get_casrn($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/cas";
        $generated_casrn = $this->CI->util->get_url_contents($url);
        
        if ($generated_casrn)
		{
            $casrn = explode("\n", $generated_casrn, 2);
            if (strlen($casrn[0]) == 0 or empty($casrn[0])) {
                $casrn[0] = FALSE;
            }

            $result = $casrn[0];
        }
		else
		{
            $result = FALSE;
        }
		
        return $result;
	}
	
	/**
	 * Retorna o Std. InChi de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, SMILES, CAS RN, InChiKey)
	 * @param string $type Tipo do composto (nome, smiles, casrn, inchikey)
	 * 
	 * @return mixed O std. InChi correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
	public function get_sinchi($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/stdinchi";
        $generated_sinchi = $this->CI->util->get_url_contents($url);
        
        if ($generated_sinchi)
		{
            $sinchi = explode("\n", $generated_sinchi, 2);
            if (strlen($sinchi[0]) == 0 or empty($sinchi[0])) {
                $sinchi[0] = FALSE;
            }

            $result = $sinchi[0];
        }
		else
		{
            $result = FALSE;
        }
		
        return $result;
	}
	
	/**
	 * Retorna o Std. InChiKey de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, SMILES, CAS RN, InChi)
	 * @param string $type Tipo do composto (nome, smiles, casrn, inchi)
	 * 
	 * @return mixed O std. InChiKey correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
	public function get_sinchikey($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/stdinchikey";
        $generated_sinchikey = $this->CI->util->get_url_contents($url);
        
        if ($generated_sinchikey)
		{
            $sinchikey = explode("\n", $generated_sinchikey, 2);
            if (strlen($sinchikey[0]) == 0 or empty($sinchikey[0])) {
                $sinchikey[0] = FALSE;
            }

            $result = $sinchikey[0];
        }
		else
		{
            $result = FALSE;
        }
		
        return $result;
	}
	
	/**
	 * Retorna o InChi de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, SMILES, CAS RN, InChiKey)
	 * @param string $type Tipo do composto (nome, smiles, casrn, inchikey)
	 * 
	 * @return mixed O InChi correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
	public function get_inchi($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/inchi";
        $generated_inchi = $this->CI->util->get_url_contents($url);
		
        if ($generated_inchi) 
		{
			$inchi = explode("\n", $generated_inchi, 2);
			if (strlen($inchi[0]) == 0 or empty($inchi[0])) {
				$inchi[0] = FALSE;
			}
			$result = $inchi[0];
		}
		else
		{
			$result = FALSE;
		}
		
		return $result;
	}
	
	/**
	 * Retorna o InChiKey de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, SMILES, CAS RN, InChi)
	 * @param string $type Tipo do composto (nome, smiles, casrn, inchi)
	 * 
	 * @return mixed O InChiKey correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
	public function get_inchikey($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/inchikey";
        $generated_inchikey = $this->CI->util->get_url_contents($url);
		
        if ($generated_inchikey)
		{
			$inchikey = explode("\n", $generated_inchikey, 2);
			if (strlen($inchikey[0]) == 0 or empty($inchikey[0])) {
				$inchikey[0] = FALSE;
			}
			$result = $inchikey[0];
		}
		else
		{
			$result = FALSE;
		}
		
		return $result;
	}
    
    /**
	 * Retorna a formula de um composto atraves do CACTUS
	 * 
	 * @param string $term O composto a procurar
	 * (Nome, SMILES, CAS RN, InChi)
	 * @param string $type Tipo do composto (nome, smiles, casrn, inchi)
	 * 
	 * @return mixed A formula correspondente ou 'FALSE' 
	 * em caso de falha ou se nao existir o composto
	 */
	public function get_formula($term = '', $type = '')
	{
		if ($type == 'name') {
			$term = $this->CI->util->replace_char($term, 2);
		} else if ($type == 'smiles') {
			$term = $this->CI->util->replace_char($term, 3);
		}
		
        $url = "{$this->cactus_url}/{$term}/formula";
        $generated_formula = $this->CI->util->get_url_contents($url);
		
        if ($generated_formula)
		{
			$formula = explode("\n", $generated_formula, 2);
			if (strlen($formula[0]) == 0 or empty($formula[0])) {
				$formula[0] = FALSE;
			}
			$result = $formula[0];
		}
		else
		{
			$result = FALSE;
		}
		
		return $result;
	}
}

/* End of file Cactus.php */
/* Location: ./application/libraries/Cactus.php */