<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data extends CI_Controller {

	/**
     * Construtor do controlador.
     */
	function __construct()
    {
        parent::__construct();
		
		// Alteracoes ao php
		ini_set('memory_limit', -1);
		ini_set ('max_execution_time', '9000');
		// Carregar os modulos
        $this->load->library('OBabel');
		$this->load->library('Util');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		echo '<h3>Passar os dados do Therminfo v1 para v2:</h3>
        <ol>
            <li><a href="'. base_url('db/data/therminfoDB') .'">Carregar mol&eacute;culas do ThermInfo</a></li>
            <li><a href="'. base_url('db/data/therminfoDB_class') .'">Preencher a classe</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_family') .'">Preencher a familia</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_subclass') .'">Preencher a subclasse</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_update_thermid') .'">Actualizar o ThermInfo ID - COXXXXXXX</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_chars') .'">Preencher as caracter&iacute;sticas</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_moltype') .'">Preencher o tipo de mol&eacute;cula</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_values') .'">Preencher os valores de propriedades</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_images') .'">Preencher a imagem</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_generate_inchi') .'">Gerar o InChi</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_generate_inchikey') .'">Gerar o InChiKey</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_generate_sinchikey') .'">Gerar o Std. InChi</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_generate_sinchikey') .'">Gerar o Std. InChiKey</a> (necess&aacute;rio as mol&eacute;culas)</li>
            <li><a href="'. base_url('db/data/therminfoDB_generate_molfile') .'">Gerar o Molfile</a> (necess&aacute;rio as mol&eacute;culas)</li>
        </ol>';
	}
    
    /**
     * Moleculas
     */
    public function therminfoDB()
    {
        $DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo', TRUE);
		$DB2 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
            $result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>ROW</th><th>MID</th><th>THERM ID</th><th>CAS RN</th><th>NAME</th><th>FORMULA</th>
            <th>MW</th><th>STATE</th><th>SMILES</th><th>USMILES</th><th>INSERT</th></tr>';
            
            $i = 1;
			//$DB2->trans_start();
            foreach ($query_result->result() as $row)
			{
                $mid = trim($row->mid);
                $therm_id = trim($row->mol_id);
                $cas = trim($row->casrn);
                $name = trim($row->name);
                $formula = trim($row->formula);
                $mw = trim($row->mw);
                $state = trim($row->state);
                $smiles = trim($row->smile);
                $usmiles = trim($row->usmile);
                
                $result .= '<tr>';
                
                $result .= '<td>'. $i .'</td>';
                 
                if ($this->_verifyNull($mid)) {
                    $result .= '<td>'. $mid .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO THERM ID</td>';
					$mid = NULL;
                }
                
                if ($this->_verifyNull($therm_id)) {
                    $result .= '<td>'. $therm_id .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO MID</td>';
					$therm_id = NULL;
                }
                
                
                if ($this->_verifyNull($cas)) {
                    if ($this->obabel->verify_casrn($cas) === TRUE) {
                        $result .= '<td>'. $cas .'</td>';
                    } else {
                        $result .= '<td style="background-color:red">CAS NOT VALID</td>';
						$cas = NULL;
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO CAS RN</td>';
					$cas = NULL;
                }
                
                if ($this->_verifyNull($name)) {
                    $result .= '<td>'. $name .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO NAME</td>';
					$name = NULL;
                }
                
                if ($this->_verifyNull($formula)) {
                    $result .= '<td>'. $formula .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO FORMULA</td>';
					$formula = NULL;
                }
                
                if ($this->_verifyNull($mw)) {
                	$mw = (double)$mw;
                    $result .= '<td>'. $mw .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO MW</td>';
					$mw = NULL;
                }
                
                if ($this->_verifyNull($state)) {
                    $result .= '<td>'. $state .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO STATE</td>';
					$state = NULL;
                }
                
                if ($this->_verifyNull($smiles)) {
                    $result .= '<td>'. $smiles .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO SMILES</td>';
					$smiles = NULL;
                }
                
                if ($this->_verifyNull($usmiles)) {
                    $result .= '<td>'. $usmiles .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO USMILES</td>';
					$usmiles = NULL;
                }
                
				//$DB2->set('therminfo_id', $therm_id);
				//$DB2->set('casrn', $cas);
				//$DB2->set('name', $name);
				//$DB2->set('formula', $formula);
				//$DB2->set('mw', $mw);
				//$DB2->set('state', $state);
				//$DB2->set('smiles', $smiles);
				//$DB2->set('usmiles', $usmiles);
				//$up = $DB2->insert('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">INSERT</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO INSERT</td>';
				}
				
                $result .= '</tr>';
                $i++;
            }
            
            $result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
        }
        else
        {
            echo 'Sem resultados - therminfoDB<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
        }
    }

	/**
	 * Classe
	 */
	public function therminfoDB_class()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo', TRUE);
		$DB2 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
		
		$DB1->select('mid, class, class.name');
		$DB1->join('class', 'cid = class');
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>CLASS</th><th>CLASS ID</th><th>UPDATE</th></tr>';
			
			//$DB2->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = trim($row->mid);
				$c_name = trim($row->name);
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($c_name)) {
					$result .= '<td>'. $c_name .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO CLASS</td>';
					$c_name = NULL;
				}
				
				$DB2->where('c_name', $c_name);
				$query_result_2 = $DB2->get('class');
				
				if ($query_result_2->num_rows() > 0) {
					$row = $query_result_2->row();
					$class = trim($row->cid);
					$result .= '<td>'. $class .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO CLASS ID</td>';
					$class = NULL;
				}
				
				//$DB2->where('mid', $mid);
				//$DB2->set('class', $class);
				//$up = $DB2->update('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_class<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}

	/**
	 * Familia
	 */
	public function therminfoDB_family()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo', TRUE);
		$DB2 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
		
		$DB1->select('mid, family, family.name');
		$DB1->join('family', 'fid = family');
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>FAMILY</th><th>FAMILY ID</th><th>UPDATE</th></tr>';
			
			//$DB2->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = trim($row->mid);
				$f_name = trim($row->name);
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($f_name)) {
					$result .= '<td>'. $f_name .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO FAMILY</td>';
					$f_name = NULL;
				}
				
				$DB2->where('f_name', $f_name);
				$query_result_2 = $DB2->get('family');
				
				if ($query_result_2->num_rows() > 0) {
					$row = $query_result_2->row();
					$family = trim($row->fid);
					$result .= '<td>'. $family .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO FAMILY ID</td>';
					$family = NULL;
				}
				
				//$DB2->where('mid', $mid);
				//$DB2->set('family', $family);
				//$up = $DB2->update('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_family<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}

	/**
	 * Subclasse
	 */
	public function therminfoDB_subclass()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo', TRUE);
		$DB2 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
		
		$DB1->select('mid, molecule.class, subclass.name');
		$DB1->join('subclass', 'subclass.class = molecule.class');
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>SUBCLASS</th><th>SUBCLASS ID</th><th>UPDATE</th></tr>';
			
			//$DB2->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = trim($row->mid);
				$sc_name = trim($row->name);
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($sc_name)) {
					$result .= '<td>'. $sc_name .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO SUBCLASS</td>';
					$sc_name = NULL;
				}
				
				$DB2->where('sc_name', $sc_name);
				$query_result_2 = $DB2->get('subclass');
				
				if ($query_result_2->num_rows() > 0) {
					$row = $query_result_2->row();
					$subclass = trim($row->scid);
					$result .= '<td>'. $subclass .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO SUBCLASS ID</td>';
					$subclass = NULL;
				}
				
				//$DB2->where('mid', $mid);
				//$DB2->set('subclass', $subclass);
				//$up = $DB2->update('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_subclass<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
	
	/**
	 * Therminfo ID
	 */
	public function therminfoDB_update_thermid()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>THERM ID 1</th><th>THERM ID 2</th><th>UPDATE</th></tr>';
			
			//$DB1->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->mid;
				$thermid_1 = $row->therminfo_id;
				$thermid_2 = trim($this->_generate_therminfoID(7, $mid));
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($thermid_1)) {
					$result .= '<td>'. $thermid_1 .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO THERM ID 1</td>';
					$thermid_1 = NULL;
				}
				
				if ($this->_verifyNull($thermid_2)) {
					$result .= '<td>'. $thermid_2 .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO THERM ID 2</td>';
					$thermid_2 = NULL;
				}
				
				//$DB1->where('mid', $mid);
				//$DB1->set('therminfo_id', $thermid_2);
				//$up = $DB1->update('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_update_thermid<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}

	/**
	 * Caracteristicas
	 */
	public function therminfoDB_chars()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo', TRUE);
		$DB2 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
		
        $query_result = $DB1->get('mol_char');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>CHAR</th><th>INSERT</th></tr>';
			
			//$D2->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->molecule;
				$char = $row->charact;
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($char)) {
					$result .= '<td>'. $char .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO CHAR</td>';
					$char = NULL;
				}
				
				//$DB2->set('molecule', $mid);
				//$DB2->set('charact', $char);
				//$up = $DB2->insert('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">INSERT</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO INSERT</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_chars<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}

	/**
	 * Tipo
	 */
	public function therminfoDB_moltype()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>MOL TYPE</th><th>UPDATE</th></tr>';
			
			//$DB1->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->mid;
				$moltype = 1;
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
					$result .= '<td>'. $moltype .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$result .= '<td style="background-color:yellow">NO MOL TYPE</td>';
					$mid = NULL;
					$moltype = NULL;
				}
				
				//$DB1->where('mid', $mid);
				//$DB1->set('mol_type', $moltype);
				//$up = $DB1->update('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_moltype<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
	
	/**
	 * Valor propriedades - falta
	 */
	public function therminfoDB_values()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo', TRUE);
		$DB2 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
		
		$query_result = $DB1->get('data');
		
		if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>REF</th><th>DATA</th><th>VALUE</th><th>ERROR</th><th>OBS</th></tr>';
			
			//$D2->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->idmol;
				$cry = $row->crys;
				$liq = $row->liq;
				$gas = $row->gas;
				$cerror = $row->cerror;
				$lerror = $row->lerror;
				$gerror = $row->gerror;
				$phasecl = $row->phasecl;
				$phaselg = $row->phaselg;
				$phasecg = $row->phasecg;
				$pclerror = $row->pclerror;
				$plgerror = $row->plgerror;
				$pcgerror = $row->pcgerror;
				$obs = $row->obs;
				
				if ($this->_verifyNull($mid)) {
					$DB1->where('idmol', $mid);
					$query_result2 = $DB1->get('data_ref');
					
					if ($query_result2->num_rows() > 0)
					{
						$ref = $query_result2->row();
						if ($this->_verifyNull($ref->refid)) {
							if ($this->_verifyNull($cry)) {
								$result .= '<tr>';
								$result .= '<td>'. $mid .'</td>';
								$result .= '<td>'. $ref->refid .'</td>';
								$result .= '<td>6</td>';
								$result .= '<td>'. $cry .'</td>';
								
								if ($this->_verifyNull($cerror)) {
									$result .= '<td>'. $cerror .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO ERROR</td>';
								}
								
								if ($this->_verifyNull($obs)) {
									$result .= '<td>'. $obs .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO OBS</td>';
								}
								
								$result .= '</tr>';
							}
							
							if ($this->_verifyNull($liq)) {
								$result .= '<tr>';
								$result .= '<td>'. $mid .'</td>';
								$result .= '<td>'. $ref->refid .'</td>';
								$result .= '<td>7</td>';
								$result .= '<td>'. $liq .'</td>';
								
								if ($this->_verifyNull($lerror)) {
									$result .= '<td>'. $lerror .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO ERROR</td>';
								}
								
								if ($this->_verifyNull($obs)) {
									$result .= '<td>'. $obs .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO OBS</td>';
								}
								
								$result .= '</tr>';
							}
							
							if ($this->_verifyNull($gas)) {
								$result .= '<tr>';
								$result .= '<td>'. $mid .'</td>';
								$result .= '<td>'. $ref->refid .'</td>';
								$result .= '<td>8</td>';
								$result .= '<td>'. $gas .'</td>';
								
								if ($this->_verifyNull($gerror)) {
									$result .= '<td>'. $gerror .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO ERROR</td>';
								}
								
								if ($this->_verifyNull($obs)) {
									$result .= '<td>'. $obs .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO OBS</td>';
								}
								
								$result .= '</tr>';
							}
							
							if ($this->_verifyNull($phasecl)) {
								$result .= '<tr>';
								$result .= '<td>'. $mid .'</td>';
								$result .= '<td>'. $ref->refid .'</td>';
								$result .= '<td>18</td>';
								$result .= '<td>'. $phasecl .'</td>';
								
								if ($this->_verifyNull($pclerror)) {
									$result .= '<td>'. $pclerror .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO ERROR</td>';
								}
								
								if ($this->_verifyNull($obs)) {
									$result .= '<td>'. $obs .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO OBS</td>';
								}

								$result .= '</tr>';
							}
							
							if ($this->_verifyNull($phaselg)) {
								$result .= '<tr>';
								$result .= '<td>'. $mid .'</td>';
								$result .= '<td>'. $ref->refid .'</td>';
								$result .= '<td>19</td>';
								$result .= '<td>'. $phaselg .'</td>';
								
								if ($this->_verifyNull($plgerror)) {
									$result .= '<td>'. $plgerror .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO ERROR</td>';
								}
								
								if ($this->_verifyNull($obs)) {
									$result .= '<td>'. $obs .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO OBS</td>';
								}

								$result .= '</tr>';
							}
							
							if ($this->_verifyNull($phasecg)) {
								$result .= '<tr>';
								$result .= '<td>'. $mid .'</td>';
								$result .= '<td>'. $ref->refid .'</td>';
								$result .= '<td>20</td>';
								$result .= '<td>'. $phasecg .'</td>';
								
								if ($this->_verifyNull($pcgerror)) {
									$result .= '<td>'. $pcgerror .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO ERROR</td>';
								}
								
								if ($this->_verifyNull($obs)) {
									$result .= '<td>'. $obs .'</td>';
								} else {
									$result .= '<td style="background-color:yellow">NO OBS</td>';
								}

								$result .= '</tr>';
							}
						} else {
							$result .= '<tr>';
							$result .= '<td> </td>';
							$result .= '<td> </td>';
							$result .= '<td style="background-color:yellow">NO DATA</td>';
							$result .= '<td style="background-color:yellow">NO VALUE</td>';
							$result .= '<td style="background-color:yellow">NO ERROR</td>';
							$result .= '<td style="background-color:yellow">NO OBS</td>';
							$result .= '</tr>';
						}
					} else {
						$result .= '<tr>';
						$result .= '<td> </td>';
						$result .= '<td style="background-color:yellow">NO REF</td>';
						$result .= '<td style="background-color:yellow">NO DATA</td>';
						$result .= '<td style="background-color:yellow">NO VALUE</td>';
						$result .= '<td style="background-color:yellow">NO ERROR</td>';
						$result .= '<td style="background-color:yellow">NO OBS</td>';
						$result .= '</tr>';
					}
				} else {
					$result .= '<tr>';
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$result .= '<td style="background-color:yellow">NO REF</td>';
					$result .= '<td style="background-color:yellow">NO DATA</td>';
					$result .= '<td style="background-color:yellow">NO VALUE</td>';
					$result .= '<td style="background-color:yellow">NO ERROR</td>';
					$result .= '<td style="background-color:yellow">NO OBS</td>';
					$result .= '</tr>';
					$mid = NULL;
				}
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_values<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
	
	/**
	 * Imagens
	 */
	public function therminfoDB_images()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>IMAGE</th><th>UPDATE</th></tr>';
			
			//$DB1->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->mid;
				$img = trim($this->_generate_therminfoID(7, $mid));
				$img .= '.jpg';
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
					$result .= '<td>'. $img .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$result .= '<td style="background-color:yellow">NO IMAGE</td>';
					$mid = NULL;
					$img = NULL;
				}
				
				//$DB1->where('mid', $mid);
				//$DB1->set('img_path', $img);
				//$up = $DB1->update('molecule');
				$up = TRUE;
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_images<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
	
	/**
	 * Novos campos (inchi) - Falta
	 */
	public function therminfoDB_generate_inchi()
	{
		echo 'Not implemented yet<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
	}

	/**
	 * Novos campos (inchikey) - Falta
	 */
	public function therminfoDB_generate_inchikey()
	{
		echo 'Not implemented yet<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
	}
	
	/**
	 * Novos campos (std. inchi)
	 */
	public function therminfoDB_generate_sinchi()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>SMILES</th><th>INCHI</th><th>UPDATE</th></tr>';
			
			//$DB1->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->mid;
				$smiles = explode(',', $row->smiles);
				$smi = $smiles[0]; 
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($smi)) {
					$result .= '<td>'. $smi .'</td>';
					
					if ($this->_verifyNull($row->s_inchi)) {
						$s_inchi = $row->s_inchi;
						$result .= '<td>'. $s_inchi .'</td>';
					} else {
						$s_inchi = $this->obabel->smiles_to_inchi($smi);
						
						if ($this->_verifyNull($s_inchi)) {
							$result .= '<td>'. $s_inchi .'</td>';
						} else {
							$result .= '<td style="background-color:yellow">NO GEN INCHI</td>';
							$s_inchi = NULL;
						}
					}
				} else {
					$result .= '<td style="background-color:yellow">NO SMILES</td>';
					$result .= '<td style="background-color:yellow">NO GEN INCHI</td>';
					$smi = NULL;
					$s_inchi = NULL;
				}
				
				//$DB1->where('mid', $mid);
				//$DB1->set('s_inchi', $s_inchi);
				//$up = $DB1->update('molecule');
				$up = TRUE;
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_generate_sinchi<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
	
	/**
	 * Novos campos (std. inchikey)
	 */
	public function therminfoDB_generate_sinchikey()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo2_teste', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>INCHI</th><th>INCHIKEY</th><th>UPDATE</th></tr>';
			
			//$DB1->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->mid;
				$s_inchi = $row->s_inchi;
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($s_inchi)) {
					$result .= '<td>'. $s_inchi .'</td>';
					
					if ($this->_verifyNull($row->s_inchikey)) {
						$s_inchikey = $row->s_inchikey;
						$result .= '<td>'. $s_inchikey .'</td>';
					} else {
						$s_inchikey = $this->obabel->inchi_to_inchikey($s_inchi);
						
						if ($this->_verifyNull($s_inchikey)) {
							$result .= '<td>'. $s_inchikey .'</td>';
						} else {
							$result .= '<td style="background-color:yellow">NO GEN INCHIKEY</td>';
							$s_inchikey = NULL;
						}
					}
				} else {
					$result .= '<td style="background-color:yellow">NO INCHI</td>';
					$result .= '<td style="background-color:yellow">NO GEN INCHIKEY</td>';
					$s_inchi = NULL;
					$s_inchikey = NULL;
				}
				
				//$DB1->where('mid', $mid);
				//$DB1->set('s_inchikey', $s_inchikey);
				//$up = $DB1->update('molecule');
				$up = TRUE;
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			/*$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}*/
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_generate_sinchikey<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
	
	/**
	 * Novos campos (molfile)
	 */
	public function therminfoDB_generate_molfile()
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, 'therminfo2', TRUE);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
            $result .= '<p><a href="'. base_url('db/data') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>MID</th><th>SMILES</th><th>MOLFILE</th><th>UPDATE</th></tr>';
			
			$DB1->trans_start();
			foreach ($query_result->result() as $row)
			{
				$mid = $row->mid;
				$smiles = $row->smiles;
				
				if ($this->_verifyNull($mid)) {
					$result .= '<td>'. $mid .'</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO MID</td>';
					$mid = NULL;
				}
				
				if ($this->_verifyNull($smiles)) {
					$result .= '<td>'. $smiles .'</td>';
					
					if ($this->_verifyNull($row->mol_file)) {
						$molfile = $row->mol_file;
						$result .= '<td>'. $molfile .'</td>';
					} else {
						$molfile = $this->obabel->smiles_to_mol($smiles);
						
						if ($this->_verifyNull($molfile)) {
							$result .= '<td>'. $molfile .'</td>';
						} else {
							$result .= '<td style="background-color:yellow">NO GEN MOLFILE</td>';
							$molfile = NULL;
						}
					}
				} else {
					$result .= '<td style="background-color:yellow">NO SMILES</td>';
					$result .= '<td style="background-color:yellow">NO GEN MOLFILE</td>';
					$smiles = NULL;
					$molfile = NULL;
				}
				
				$DB1->where('mid', $mid);
				$DB1->set('mol_file', $molfile);
				$up = $DB1->update('molecule');
				
				if ($up) {
					$result .= '<td style="background-color:green">UPDATE</td>';
				} else {
					$result .= '<td style="background-color:yellow">NO UPDATE</td>';
				}
				
				$result .= '</tr>';
			}
			
			$result .= '</table><p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';
			$DB1->trans_complete();
			
			if($DB1->trans_status()) {
				$result .= '<p><b>DONE</b></p>';
			} else {
				$result .= '<p><b>NOT DONE</b></p>';
			}
			
			echo $result;
		}
		else
		{
			echo 'Sem resultados - therminfoDB_generate_molfile<p><a href="'. base_url('db/data') .'">&lt; Back</a></p>';	
		}
	}
    
	/**
	 * Separar os SMILES
	 */
	private function _get_smiles($mid = 0, $db_name = 'therminfo2_teste')
	{
		$DB1 = $this->_set_database(HOST, USER, PASS, $db_name, TRUE);
		$DB1->where('mid', $mid);
        $DB1->limit(1);
        $query_result = $DB1->get('molecule');
        
        if ($query_result->num_rows() > 0)
		{
			$row = $query_result->row();
            $smiles = explode(',', $row->smile);
            
            if (count($smiles) > 1) {
                $smi_1 = trim($smiles[0]);
                $smi_2 = trim($smiles[1]);
            } else {
                $smi_1 = trim($smiles[0]);
                $smi_2 = NULL;
            }
            
            if (! $this->_verifyNull($smi_1)) {
                $smi_1 = NULL;
            }

            if (! $this->_verifyNull($smi_2)) {
                $smi_2 = NULL;
            }
			
			return $smi_1;
		}
		else
		{
			return FALSE;
		}
	}
	
    /*
     * Set database
     */
	private function _set_database($host = '', $user = '', $pass = '', $db = '', $multiple = FALSE)
	{
		$config['hostname'] = $host;
		$config['username'] = $user;
		$config['password'] = $pass;
		$config['database'] = $db;
		$config['dbdriver'] = 'mysqli';
		
		// Carrega a configuracao da base de dados
		if ($multiple) {
			return $this->load->database($config, TRUE);
		} else {
			$this->load->database($config);
		}
	}
    
    /*
     * Verify null values
     */
    private function _verifyNull($str)
    {
        return $str && ! is_null($str) && ! empty($str) && $str != 'Null' && $str != 'NULL';
    }
    
    /*
     * Generate therminfoID
     */
    private function _generate_therminfoID($num1, $num2)
	{
		return sprintf("CO%0{$num1}s", $num2);
	}
}

/* End of file data.php */
/* Location: ./application/controllers/db/data.php */