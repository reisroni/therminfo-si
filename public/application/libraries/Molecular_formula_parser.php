<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Molecular_formula_parser
 * Descricao:  Parser de formula molecular
 * Criado: 13-10-2011
 * Modificado: 13-10-2011
 * @author Rony Reis, Ana Teixeira
 * @version 0.1
 * @package Therminfo
 * @copyright Copyright (c) 2011, ThermInfo
 */

class Molecular_formula_parser {

	public function parse_mol_f($mol_form) 
	{
		// Receives a string with the molecular formula - empirical or structural - entered by the user
		$mol_form = trim($mol_form);
		
		// Converts the string to upper case - uniformity
		$molf_form = strtoupper($mol_form);

		// Splits the string to an array. each character to a position of the array
		$chars = str_split($molf_form);

		// Associative array where we will count the number of atoms of each type. 
		// In case of organic compounds we have: C;H;Br;Cl;F;I;P;N;O;S and our database is ordered this way.  
		$organic_atoms = array('C' => 0, 'H' => 0, 'Br' => 0, 'Cl' => 0 , 'F' => 0 , 'I' => 0 , 'P' => 0 , 'N' => 0 , 'O' => 0 , 'S' => 0 );

		// The cycle will loop the array
		$i = 0;

		while ($i < sizeof($chars)) {
			if (!is_numeric($chars[$i]) and !preg_match('/C|H|B|F|I|P|N|O|S/', $chars[$i]) and $chars[$i] != '?') {
				$type = $chars[$i];
				$error .= $type ."; ";
				$type = '';
			}
  
			// We will verify if the char is one of the atoms we aim 
			// (excluding "Cl" and "Br" that are special cases, because the atom type is composed by 2 characters)
			if (preg_match('/C|H|B|F|I|P|N|O|S/', $chars[$i]) and $chars[$i] != '?') {
				$o_a = 0;
				
				if ($chars[$i] == 'C' and $chars[$i+1] == 'L') {
					$type = 'Cl';
					$i = $i + 2;
					$aux = $i - 1;
					$o_a = 0;

					if (!is_numeric($chars[$i]) and $chars[$i] != '?') {
						$o_a += 1;
					}
					
					while ($chars[$i] == '?'){
						if ($o_a == 0) {
							$o_a = '?';
						//$organic_atoms[$type] = (int)$chars[$i+1];
						} else {
							$o_a .= '?';
						//$organic_atoms[$type] .= (int)$chars[$i+1];
						}
						$i++;
					}

					while (is_numeric($chars[$i])) {
						if ($o_a == 0) {
							$o_a = (int)$chars[$i];
							//$organic_atoms[$type] = (int)$chars[$i+1];
						} else {
							$o_a .= (int)$chars[$i];
							//$organic_atoms[$type] .= (int)$chars[$i+1];
						}
						
						$i++;
					}
					
					if ($aux == $i) {
						$o_a += 1;
						$i++;
						//$organic_atoms[$type] += 1;
					}

				} else if ($chars[$i] == 'B' and $chars[$i+1] == 'R') {
					$type = 'Br';
					$i = $i + 2;
					$aux = $i - 1;
					$o_a = 0;
					
					if (!is_numeric($chars[$i]) and $chars[$i] != '?') {
						$o_a += 1;
					}

					while ($chars[$i] == '?') {
						if ($o_a == 0) {
							$o_a = '?';
							//$organic_atoms[$type] = (int)$chars[$i+1];
						} else {
							$o_a .= '?';
							//$organic_atoms[$type] .= (int)$chars[$i+1];
						}
						
						$i++;
					}

					while (is_numeric($chars[$i])) {
						if ($o_a == 0) {
							$o_a = (int)$chars[$i];
							//$organic_atoms[$type] = (int)$chars[$i+1];
						} else {
							$o_a .= (int)$chars[$i];
							//$organic_atoms[$type] .= (int)$chars[$i+1];
						}
						
						$i++;
					}
					
					if ($aux == $i) {
						$o_a += 1;
						$i++;
						//$organic_atoms[$type] += 1;
					}
				} else {
					$type = $chars[$i];
					
					if (!is_numeric($chars[$i+1]) and $chars[$i+1] != '?') {
						$o_a += 1;
					}
					
					$i++;
				}
			} else {
				//number or other letters (to ignore)
				$aux = $i;
				$o_a = 0;

				while ($chars[$i] == '?') {
					if ($o_a == 0) {
						$o_a = '?';
						//$organic_atoms[$type] = (int)$chars[$i+1];
					} else {
						$o_a .= '?';
						//$organic_atoms[$type] .= (int)$chars[$i+1];
					}
					
					$i++;
				}

				while (is_numeric($chars[$i])) {
					if ($o_a == 0){
						$o_a = (int)$chars[$i];
						//$organic_atoms[$type] = (int)$chars[$i];
					} else {
						$o_a .= (int)$chars[$i];
						//$organic_atoms[$type] .= (int)$chars[$i];
					}
					
					$i++;
				}
			
				if ($aux == $i) {
					$o_a += 1;
					$i++;
					//$organic_atoms[$type] += 1;
				}
			}
			
			if (strpos($o_a, '?') === false) {
				$organic_atoms[$type] += $o_a;
			} else {
				if ($organic_atoms[$type] == 0) {
					$organic_atoms[$type] = $o_a;
				} else {
					$organic_atoms[$type] .= $o_a;
				}
			}
		}

		/*foreach ($organic_atoms as $key => $value){
		   echo $key.'=>'.$value.'<br />';
		}*/

		return $organic_atoms;
	}
}

/* End of file Molecular_formula_parser.php */
/* Location: ./application/libraries/Molecular_formula_parser.php */