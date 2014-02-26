<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crc extends CI_Controller {

	/**
     * Construtor do controlador.
     */
	function __construct()
    {
        parent::__construct();
		// Alteracoes ao php
		ini_set('memory_limit', -1);
		ini_set ('max_execution_time', '9000');
		// Carregar a BD e os modulos
		$this->load->model('molecule/Molecule_model');
		$this->load->library('PHPExcel');
		$this->load->library('OBabel');
    }
	
	/**
	 * Pagina inicial para este controlador.
	 */
	public function index()
	{
		echo '<h3>Carregar os dados do CRC para o Therminfo v2:</h3>
		<ol>
            <li><a href="'. base_url('db/crc/dump_crc_all_mols') .'">Carregar dados do CRC</a> (necess&aacute;rio \'crc_all_compounds.xls\')</li>
            <li><a href="'. base_url('db/crc/dump_crc_new_names') .'">Carregar os novos nomes (CRC)</a> (necess&aacute;rio \'crc_new_compounds_ids.xls\')</li>
            <li><a href="'. base_url('db/crc/dump_crc_new_smiles') .'">Carregar os novos SMILES (CRC)</a> (necess&aacute;rio \'crc_new_compounds_ids.xls\' e \'crc_new_compounds_smiles.xls\')</li>
            <li><a href="'. base_url('db/crc/dump_crc_new_images') .'">Carregar as novas imagems (CRC)</a> (necess&aacute;rio \'crc_new_compounds_ids.xls\' e \'crc_figs_resize/\')</li>
        </ol>';
	}
	
	/*
	 * Carregar o CRC
	 */
	private function _dump_crc_db()
	{
		// Nome do ficheiro CRC
		$inputFileName_1 = FCPATH . 'public/media/db_data/crc/crc_all_compounds.xls';
		$sheetname = 'CRC Database';
		
		try
		{
			// Criar o leitor do ficheiro .xls
			$objReader = new PHPExcel_Reader_Excel5();
			$objReader->setLoadSheetsOnly($sheetname);
			$objReader->setReadDataOnly(true);
			// Carregar o ficheiro .xls
			$objPHPExcel = $objReader->load($inputFileName_1);
            
			$um = 0;
			$ub = 0;
			$up = 0;
			
			$m = 0;
			$nb = 0;
			$ns = 0;
			$np = 0;
			
			$z = 0;
			
			// Retirar informacao das celulas (10169)
			//$this->Molecule_model->DB->trans_start();
			for ($i = 2; $i <= 65; ++$i)
			{
				$crc        = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue();
				$name       = trim($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue());
				$synon      = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue());
				$cas        = trim($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
				$bel        = trim($objPHPExcel->getActiveSheet()->getCell('E'.$i)->getValue());
				$formula    = trim($objPHPExcel->getActiveSheet()->getCell('F'.$i)->getValue());
				$mw         = trim($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
				$p_form     = trim($objPHPExcel->getActiveSheet()->getCell('H'.$i)->getValue());
				
				$mp         = trim($objPHPExcel->getActiveSheet()->getCell('I'.$i)->getValue());
				$bp         = trim($objPHPExcel->getActiveSheet()->getCell('J'.$i)->getValue());
				$sol        = trim($objPHPExcel->getActiveSheet()->getCell('K'.$i)->getValue());
				
				$hcr        = trim($objPHPExcel->getActiveSheet()->getCell('L'.$i)->getValue());
				$hl         = trim($objPHPExcel->getActiveSheet()->getCell('P'.$i)->getValue());
				$hg         = trim($objPHPExcel->getActiveSheet()->getCell('T'.$i)->getValue());
				
				$gcr        = trim($objPHPExcel->getActiveSheet()->getCell('M'.$i)->getValue());
				$gl         = trim($objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue());
				$gg         = trim($objPHPExcel->getActiveSheet()->getCell('U'.$i)->getValue());
				
				$scr        = trim($objPHPExcel->getActiveSheet()->getCell('N'.$i)->getValue());
				$sl         = trim($objPHPExcel->getActiveSheet()->getCell('R'.$i)->getValue());
				$sg         = trim($objPHPExcel->getActiveSheet()->getCell('V'.$i)->getValue());
				
				$cpcr       = trim($objPHPExcel->getActiveSheet()->getCell('O'.$i)->getValue());
				$cpl        = trim($objPHPExcel->getActiveSheet()->getCell('S'.$i)->getValue());
				$cpg        = trim($objPHPExcel->getActiveSheet()->getCell('W'.$i)->getValue());
				
				$h_fus      = trim($objPHPExcel->getActiveSheet()->getCell('X'.$i)->getValue());
				$h_vap_bp   = trim($objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getValue());
				$h_vap      = trim($objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getValue());
                
				// Interrogar a BD
				/*$query_result = $this->Molecule_model->DB->query("SELECT mid FROM molecule WHERE casrn = '$cas'");
				$rows = $query_result->num_rows();
				
				if ($rows)
				{
					$r = $query_result->row();
					// --
					if ($p_form != 'Null' && ! empty($p_form))
					{
						$p_form = $this->Molecule_model->DB->escape($p_form);
						$ins = $this->Molecule_model->DB->query("UPDATE molecule SET phi_form = $p_form WHERE mid = {$r->mid}");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';FORM<br />';
							++$um;
						} else {
							echo 'Nao;'. $i .';'. $cas .';FORM<br />';
						}
					}
					// --
					if ($bel != 'Null' && ! empty($bel))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO other_db SET molecule = {$r->mid}, beilstein = '$bel'");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';ODB<br />';
							++$ub;
						} else {
							echo 'Nao;'. $i .';'. $cas .';ODB<br />';
						}
					}
					// --
					if ($mp != 'Null' && ! empty($mp))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 1, reference = 3, value = '$mp'");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';1;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';1;0<br />';
						}
					}
					if ($bp != 'Null' && ! empty($bp))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 2, reference = 3, value = '$bp'");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';2;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';2;0<br />';
						}
					}
					if ($sol != 'Null' && ! empty($sol))
					{
						$sol = $this->Molecule_model->DB->escape($sol);
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 5, reference = 3, value = $sol");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';5;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';5;0<br />';
						}
					}
					// --
					if ($hcr != 'Null' && ! empty($hcr))
					{	
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 6, reference = 3, value = $hcr");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';6;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';6;0<br />';
						}
					}
					if ($hl != 'Null' && ! empty($hl))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 7, reference = 3, value = $hl");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';7;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';7;0<br />';
						}
					}
					if ($hg != 'Null' && ! empty($hg))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 8, reference = 3, value = $hg");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';8;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';8;0<br />';
						}
					}
					// --
					if ($gcr != 'Null' && ! empty($gcr))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 9, reference = 3, value = $gcr");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';9;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';9;0<br />';
						}
					}
					if ($gl != 'Null' && ! empty($gl))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 10, reference = 3, value = $gl");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';10;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';10;0<br />';
						}
					}
					if ($gg != 'Null' && ! empty($gg))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 11, reference = 3, value = $gg");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';11;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';11;0<br />';
						}
					}
					// --
					if ($scr != 'Null' && ! empty($scr))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 12, reference = 3, value = $scr");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';12;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';12;0<br />';
						}
					}
					if ($sl != 'Null' && ! empty($sl))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 13, reference = 3, value = $sl");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';13;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';13;0<br />';
						}
					}
					if ($sg != 'Null' && ! empty($sg))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 14, reference = 3, value = $sg");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';14;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';14;0<br />';
						}
					}
					// --
					if ($cpcr != 'Null' && ! empty($cpcr))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 15, reference = 3, value = $cpcr");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';15;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';15;0<br />';
						}
					}
					if ($cpl != 'Null' && ! empty($cpl))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 16, reference = 3, value = $cpl");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';16;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';16;0<br />';
						}
					}
					if ($cpg != 'Null' && ! empty($cpg))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 17, reference = 3, value = $cpg");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';17;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';17;0<br />';
						}
					}
					// --
					if ($h_vap != 'Null' && ! empty($h_vap))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 19, reference = 3, value = $h_vap");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';19;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';19;0<br />';
						}
					}
					if ($h_fus != 'Null' && ! empty($h_fus))
					{
						$h_fus = $this->Molecule_model->DB->escape($h_fus);
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 21, reference = 3, value = $h_fus");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';21;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';21;0<br />';
						}
					}
					if ($h_vap_bp != 'Null' && ! empty($h_vap_bp))
					{
						$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = {$r->mid}, data = 22, reference = 3, value = $h_vap_bp");
						if ($ins) {
							echo 'Sim;'. $i .';'. $cas .';22;0<br />';
							++$up;
						} else {
							echo 'Nao;'. $i .';'. $cas .';22;0<br />';
						}
					}
				}
				else
				{	
					$num = $this->Molecule_model->DB->count_all('molecule');
					$therm_id = sprintf('CO%07s', ($num + 1));
					
					if ($this->obabel->verify_casrn($cas) === TRUE)
					{
						// --
						$query = "INSERT INTO molecule SET therminfo_id = '$therm_id', casrn = '$cas'";
						
						if ($name != 'Null' && ! empty($name))
						{
							$name = $this->Molecule_model->DB->escape($name);
							$query .= ", name = $name";
						}
						if ($formula != 'Null' && ! empty($formula))
						{
							$query .= ", formula = '$formula'";
						}
						if ($mw != 'Null' && ! empty($mw))
						{
							$query .= ", mw = $mw";
						}
						if ($p_form != 'Null' && ! empty($p_form))
						{
							$p_form = $this->Molecule_model->DB->escape($p_form);
							$query .= ", phi_form = $p_form";
						}
						
						$mol = $this->Molecule_model->DB->query($query);
						
						if ($mol)
						{
							$mid = $this->Molecule_model->DB->insert_id();
							echo 'Mol;'. $i .';'. $cas .';'. $mid .'<br />';
							++$m;
							// --
							if ($synon != 'Null' && ! empty($synon))
							{
								$synon = $this->Molecule_model->DB->escape($synon);
								$ins = $this->Molecule_model->DB->query("INSERT INTO othername SET mid = $mid, name = $synon");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';SYNO<br />';
									++$ns;
								} else {
									echo 'Nao;'. $i .';'. $cas .';SYNO<br />';
								}
							}
							// --
							if ($bel != 'Null' && ! empty($bel))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO other_db SET molecule = $mid, beilstein = '$bel'");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';ODB<br />';
									++$nb;
								} else {
									echo 'Nao;'. $i .';'. $cas .';ODB<br />';
								}
							}
							// --
							if ($mp != 'Null' && ! empty($mp))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 1, reference = 3, value = '$mp'");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';1;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';1;1<br />';
								}
							}
							if ($bp != 'Null' && ! empty($bp))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 2, reference = 3, value = '$bp'");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';2;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';2;1<br />';
								}
							}
							if ($sol != 'Null' && ! empty($sol))
							{
								$sol = $this->Molecule_model->DB->escape($sol);
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 5, reference = 3, value = $sol");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';5;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';5;1<br />';
								}
							}
							// --
							if ($hcr != 'Null' && ! empty($hcr))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 6, reference = 3, value = $hcr");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';6;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';6;1<br />';
								}
							}
							if ($hl != 'Null' && ! empty($hl))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 7, reference = 3, value = $hl");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';7;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';7;1<br />';
								}
							}
							if ($hg != 'Null' && ! empty($hg))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 8, reference = 3, value = $hg");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';8;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';8;1<br />';
								}
							}
							// --
							if ($gcr != 'Null' && ! empty($gcr))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 9, reference = 3, value = $gcr");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';9;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';9;1<br />';
								}
							}
							if ($gl != 'Null' && ! empty($gl))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 10, reference = 3, value = $gl");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';10;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';10;1<br />';
								}
							}
							if ($gg != 'Null' && ! empty($gg))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 11, reference = 3, value = $gg");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';11;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';11;1<br />';
								}
							}
							// --
							if ($scr != 'Null' && ! empty($scr))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 12, reference = 3, value = $scr");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';12;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';12;1<br />';
								}
							}
							if ($sl != 'Null' && ! empty($sl))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 13, reference = 3, value = $sl");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';13;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';13;1<br />';
								}
							}
							if ($sg != 'Null' && ! empty($sg))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 14, reference = 3, value = $sg");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';14;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';14;1<br />';
								}
							}
							// --
							if ($cpcr != 'Null' && ! empty($cpcr))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 15, reference = 3, value = $cpcr");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';15;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';15;1<br />';
								}
							}
							if ($cpl != 'Null' && ! empty($cpl))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 16, reference = 3, value = $cpl");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';16;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';16;1<br />';
								}
							}
							if ($cpg != 'Null' && ! empty($cpg))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 17, reference = 3, value = $cpg");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';17;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';17;1<br />';
								}
							}
							// --
							if ($h_vap != 'Null' && ! empty($h_vap))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 19, reference = 3, value = $h_vap");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';19;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';19;1<br />';
								}
							}
							if ($h_fus != 'Null' && ! empty($h_fus))
							{
								$h_fus = $this->Molecule_model->DB->escape($h_fus);
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 21, reference = 3, value = $h_fus");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';21;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';21;1<br />';
								}
							}
							if ($h_vap_bp != 'Null' && ! empty($h_vap_bp))
							{
								$ins = $this->Molecule_model->DB->query("INSERT INTO molecule_data_ref SET molecule = $mid, data = 22, reference = 3, value = $h_vap_bp");
								if ($ins) {
									echo 'Sim;'. $i .';'. $cas .';22;1<br />';
									++$np;
								} else {
									echo 'Nao;'. $i .';'. $cas .';22;1<br />';
								}
							}
						}
						else
						{
							echo 'Mol;'. $i .';'. $cas .',Nao<br />';
						}
						
					}
					else
					{
						echo 'Cas;'. $i .';'. $cas .',Nao<br />';
						++$z;
					}
				}*/
			}
            
			/*$this->Molecule_model->DB->trans_complete();
			$status = $this->Molecule_model->DB->trans_status();
			
            if ($status) {
				echo 'Dados inseridos<br />';
				echo $m. ' Novos compostos inseridos<br />';
				echo $nb. ' Novos Bel inseridos<br />';
				echo $ns. ' Novos sinonimos inseridos<br />';
				echo $np. ' Novas propriedades inseridos<br /><br />';
				echo $um. ' Compostos actualizados<br />';
				echo $ub. ' Bel actualizados<br />';
				echo $up. ' Propriedades actualizados<br />';
				echo $z. ' CAS RN invalidos<br />';
			} else {
				echo 'Dados nao inseridos<br />';
			}*/
		}
		catch (Exception $e)
		{
			die($e->getMessage());
		}
	}
	
    /**
     * Carregar todo o CRC
     */
    public function dump_crc_all_mols()
    {
        // Nome do ficheiro CRC
		$inputFileName_1 = FCPATH . 'public/media/db_data/crc/crc_all_compounds.xls';
		$sheetName_1 = 'CRC Database';
		
		//$this->Molecule_model->setDatabase(HOST, USER, PASS, 'therminfo2_teste');
		//$this->Molecule_model->DB->query('ALTER TABLE molecule AUTO_INCREMENT = 2957');
		//$this->Molecule_model->DB->query('ALTER TABLE other_db AUTO_INCREMENT = 1');
		//$this->Molecule_model->DB->query('ALTER TABLE othername AUTO_INCREMENT = 12599');
		//$this->Molecule_model->DB->query('ALTER TABLE molecule_data_ref AUTO_INCREMENT = 6478');
        try
		{
			// Carregar o ficheiro .xls
			$objPHPExcel = $this->_loadExcel($inputFileName_1, $sheetName_1);
            $result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
			$result .= '<p><a href="'. base_url('db/crc') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>CRC</th><th>NAME</th><th>SYNONYM</th><th>CAS RN</th><th>BEILSTEIN</th><th>FORM</th><th>MW</th>
            <th>PFORM</th><th>MP</th><th>OBS</th><th>BP</th><th>OBS</th><th>SOLUBILITY</th>
            <th>H CRYS</th><th>H LIQ</th><th>H GAS</th>
            <th>G CRYS</th><th>G LIQ</th><th>G GAS</th>
            <th>S CRYS</th><th>S LIQ</th><th>S GAS</th>
            <th>CP CRYS</th><th>CP LIQ</th><th>CP GAS</th>
            <th>H FUS</th><th>H VAP BP</th><th>H VAP</th>
            <th>T</th><th>P</th><th>V</th><th>REF</th></tr>';
			
			// Retirar informacao das celulas (10169)
			//$this->Molecule_model->DB->trans_start();
			for ($i = 2; $i <= 10169; ++$i)
			{
				$crc      = trim($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
				$name     = trim($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue());
				$synon    = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue());
				$cas      = trim($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
				$bel      = trim($objPHPExcel->getActiveSheet()->getCell('E'.$i)->getValue());
				$formula  = trim($objPHPExcel->getActiveSheet()->getCell('F'.$i)->getValue());
				$mw       = trim($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue());
				$p_form   = trim($objPHPExcel->getActiveSheet()->getCell('H'.$i)->getValue());
				
				$mp       = trim($objPHPExcel->getActiveSheet()->getCell('I'.$i)->getValue());
				$bp       = trim($objPHPExcel->getActiveSheet()->getCell('J'.$i)->getValue());
				$sol      = trim($objPHPExcel->getActiveSheet()->getCell('K'.$i)->getValue());
				
				$hcr      = trim($objPHPExcel->getActiveSheet()->getCell('L'.$i)->getValue());
				$hl       = trim($objPHPExcel->getActiveSheet()->getCell('P'.$i)->getValue());
				$hg       = trim($objPHPExcel->getActiveSheet()->getCell('T'.$i)->getValue());
				
				$gcr      = trim($objPHPExcel->getActiveSheet()->getCell('M'.$i)->getValue());
				$gl       = trim($objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue());
				$gg       = trim($objPHPExcel->getActiveSheet()->getCell('U'.$i)->getValue());
				
				$scr      = trim($objPHPExcel->getActiveSheet()->getCell('N'.$i)->getValue());
				$sl       = trim($objPHPExcel->getActiveSheet()->getCell('R'.$i)->getValue());
				$sg       = trim($objPHPExcel->getActiveSheet()->getCell('V'.$i)->getValue());
				
				$cpcr     = trim($objPHPExcel->getActiveSheet()->getCell('O'.$i)->getValue());
				$cpl      = trim($objPHPExcel->getActiveSheet()->getCell('S'.$i)->getValue());
				$cpg      = trim($objPHPExcel->getActiveSheet()->getCell('W'.$i)->getValue());
				
				$h_fus    = trim($objPHPExcel->getActiveSheet()->getCell('X'.$i)->getValue());
				$h_vap_bp = trim($objPHPExcel->getActiveSheet()->getCell('Y'.$i)->getValue());
				$h_vap    = trim($objPHPExcel->getActiveSheet()->getCell('Z'.$i)->getValue());
                
                $t_cri    = trim($objPHPExcel->getActiveSheet()->getCell('AA'.$i)->getValue()); 
                $p_cri    = trim($objPHPExcel->getActiveSheet()->getCell('AB'.$i)->getValue());
                $v_cri    = trim($objPHPExcel->getActiveSheet()->getCell('AC'.$i)->getValue()); 
                $ref_c    = trim($objPHPExcel->getActiveSheet()->getCell('AD'.$i)->getValue());
                
				
                $result .= '<tr>';
                // CRC
                if ($crc) {
                    settype($crc, "integer");
                    $result .= '<td>'. $crc .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CRC</td>';
                }
                // NAME
                if ($this->_verifyEmpty($name)) {
                    $result .= '<td>'. $this->_sql_escape($name) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO NAME</td>';
                }
                // SYNONYM
                if ($this->_verifyEmpty($synon)) {
                    $result .= '<td>'. $this->_sql_escape($synon) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO SYNON</td>';
                }
                //CAS RN
                if ($this->_verifyEmpty($cas)) {
                    if ($this->obabel->verify_casrn($cas) === TRUE) {
                        $result .= '<td>'. $cas .'</td>';
                    } else {
                        $result .= '<td style="background-color:red">CAS NOT VALID</td>';
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO CASRN</td>';
                }
                // BEILSTEIN
                if ($this->_verifyEmpty($bel)) {
                    $result .= '<td>'. $this->_sql_escape($bel) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO BEILSTEIN</td>';
                }
                // FORM
                if ($this->_verifyEmpty($formula)) {
                    $result .= '<td>'. $this->_sql_escape($formula) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO FORM</td>';
                }
                // MW
                if ($this->_verifyEmpty($mw)) {
                    $result .= '<td>'. $this->_sql_escape($mw) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO MW</td>';
                }
                // PFORM
                if ($this->_verifyEmpty($p_form)) {
                    $result .= '<td>'. $this->_sql_escape($p_form) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO PFORM</td>';
                }
                
                //------
                // MP
                if ($this->_verifyEmpty($mp)) {
                    if ($this->_remove_str($mp)) {
                        $result .= '<td>'. $this->_sql_escape($this->_remove_str($mp)) .'</td>';
                    } else {
                        $result .= '<td style="background-color:#C0C0C0">&nbsp;</td>';
                    }
                    
                    if ($this->_remove_num($mp)) {
                        $result .= '<td>';
                        if ($this->_verifyChar('dec', $mp)) {
                            $result .= 'decomposes ';
                        } elseif($this->_verifyChar('tp', $mp)) {
                            $result .= 'triple point ';
                        } elseif($this->_verifyChar('exp', $mp)) {
                            $result .= 'explodes ';
                        } elseif($this->_verifyChar('»', $mp)) {
                            $result .= 'approximately ';
                        } else {
                            $result .= $this->_sql_escape($this->_remove_num($mp));
                        }
                        $result .= '</td>';
                    } else {
                        $result .= '<td>&nbsp;</td>';
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO MP</td>';
                    $result .= '<td style="background-color:yellow">&nbsp;</td>';
                }
                // BP
                if ($this->_verifyEmpty($bp)) {
                    if ($this->_remove_str($bp)) {
                        $result .= '<td>'. $this->_sql_escape($this->_remove_str($bp)) .'</td>';
                    } else {
                        $result .= '<td style="background-color:#C0C0C0">&nbsp;</td>';
                    }
                    
                    if ($this->_remove_num($bp)) {
                        $result .= '<td>';
                        if ($this->_verifyChar('dec', $bp)) {
                            $result .= 'decomposes ';
                        } elseif($this->_verifyChar('sub', $bp)) {
                            $result .= 'sublimes ';
                        } elseif($this->_verifyChar('exp', $bp)) {
                            $result .= 'explodes ';
                        } elseif($this->_verifyChar('sp', $bp)) {
                            $result .= 'sublimation point ';
                        } elseif($this->_verifyChar('»', $bp)) {
                            $result .= 'approximately ';
                        } else {
                            $result .= $this->_sql_escape($this->_remove_num($bp));
                        }
                        $result .= '</td>';
                    } else {
                        $result .= '<td>&nbsp;</td>';
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO BP</td>';
                    $result .= '<td style="background-color:yellow">&nbsp;</td>';
                }
                // SOLUBILITY
                if ($this->_verifyEmpty($sol)) {
                    $result .= '<td>'. $this->_sql_escape($sol) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO SOLUBILITY</td>';
                }
                
                //------
                // H CRY
                if ($this->_verifyNull($hcr)) {
                    $result .= '<td>'. $this->_sql_escape($hcr) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO H CRY</td>';
                }
                // H LIQ
                if ($this->_verifyNull($hl)) {
                    $result .= '<td>'. $this->_sql_escape($hl) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO H LIQ</td>';
                }
                // H GAS
                if ($this->_verifyNull($hg)) {
                    $result .= '<td>'. $this->_sql_escape($hg) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO H GAS</td>';
                }
                
                //------
                // G CRY
                if ($this->_verifyNull($gcr)) {
                    $result .= '<td>'. $this->_sql_escape($gcr) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO G CRY</td>';
                }
                // G LIQ
                if ($this->_verifyNull($gl)) {
                    $result .= '<td>'. $this->_sql_escape($gl) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO G LIQ</td>';
                }
                // G GAS
                if ($this->_verifyNull($gg)) {
                    $result .= '<td>'. $this->_sql_escape($gg) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO G GAS</td>';
                }
                
                //------
                // S CRY
                if ($this->_verifyNull($scr)) {
                    $result .= '<td>'. $this->_sql_escape($scr) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO S CRY</td>';
                }
                // S LIQ
                if ($this->_verifyNull($sl)) {
                    $result .= '<td>'. $this->_sql_escape($sl) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO S LIQ</td>';
                }
                // S GAS
                if ($this->_verifyNull($sg)) {
                    $result .= '<td>'. $this->_sql_escape($sg) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO S GAS</td>';
                }
                
                //------
                // CP CRY
                if ($this->_verifyNull($cpcr)) {
                    $result .= '<td>'. $this->_sql_escape($cpcr) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CP CRY</td>';
                }
                // CP LIQ
                if ($this->_verifyNull($cpl)) {
                    $result .= '<td>'. $this->_sql_escape($cpl) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CP LIQ</td>';
                }
                // CP GAS
                if ($this->_verifyNull($cpg)) {
                    $result .= '<td>'. $this->_sql_escape($cpg) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CP GAS</td>';
                }
                
                //------
                // H FUS
                if ($this->_verifyNull($h_fus)) {
                    $result .= '<td>'. $this->_sql_escape($this->_remove_char('*',$h_fus)) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO H FUS</td>';
                }
                // H VAP BP
                if ($this->_verifyNull($h_vap_bp)) {
                    $result .= '<td>'. $this->_sql_escape($h_vap_bp) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO H VAP BP</td>';
                }
                // H VAP
                if ($this->_verifyNull($h_vap)) {
                    $result .= '<td>'. $this->_sql_escape($h_vap) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO H VAP</td>';
                }
                
                //------
                // T CRI
                if ($this->_verifyNull($t_cri)) {
                    $result .= '<td>'. $this->_sql_escape($this->_remove_char('*',$t_cri)) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO T CRI</td>';
                }
                // P CRI
                if ($this->_verifyNull($p_cri)) {
                    $result .= '<td>'. $this->_sql_escape($this->_remove_char('*',$p_cri)) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO P CRI</td>';
                }
                // V CRI
                if ($this->_verifyNull($v_cri)) {
                    $result .= '<td>'. $this->_sql_escape($this->_remove_char('*',$v_cri)) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO V CRI</td>';
                }
                // REF C
                if ($this->_verifyNull($ref_c)) {
                    $result .= '<td>'. $this->_sql_escape($ref_c) .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO REF C</td>';
                }
                
                $result .= '</tr>';
            }
            /*$this->Molecule_model->DB->trans_complete();
			$status = $this->Molecule_model->DB->trans_status();*/
            
            $result .='</table><p><a href="'. base_url('db/crc') .'">&lt; Back</a></p>';
            echo $result;
        }
        catch (Exception $e)
		{
			die($e->getMessage());
		}
    }
    
    /**
     * Nomes correctos dos compostos que nao existiam na BD 
     * (Falta separar os sinonimos)
     */
    public function dump_crc_new_names()
    {
        // Nome do ficheiro CRC
		$inputFileName_1 = FCPATH . 'public/media/db_data/crc/crc_new_compounds_ids.xls';
		$sheetName_1 = 'crc_new_ids';
		$this->Molecule_model->setDatabase(HOST, USER, PASS, 'therminfo2_teste');
        
        try
		{
			// Carregar o ficheiro .xls
			$objPHPExcel = $this->_loadExcel($inputFileName_1, $sheetName_1);
            $result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
			$result .= '<p><a href="'. base_url('db/crc') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>CRC</th><th>NAME</th><th>SYNONYM 1</th><th>SYNONYM 2</th><th>CAS RN</th><th>MOL ID</th><th>UPDATE N</th><th>UPDATE S1</th><th>UPDATE S2</th></tr>';
			
			// Retirar informacao das celulas (8500)
			//$this->Molecule_model->DB->trans_start();
			for ($i = 2; $i <= 8500; ++$i)
			{
				$crc      = trim($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
				$name     = trim($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue());
				$synon_1  = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue());
                $synon_2  = trim($objPHPExcel->getActiveSheet()->getCell('F'.$i)->getValue());
				$cas      = trim($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue());
				
                $result .= '<tr>';
                // CRC
                if ($crc) {
                    settype($crc, "integer");
                    $result .= '<td>'. $crc .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CRC</td>';
                }
                
                // NAME
                if ($this->_verifyEmpty($name)) {
                    $name = $this->_sql_escape($name);
                    $result .= '<td>'. $name .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO NAME</td>';
                    $name = NULL;
                }
                
                // SYNONYMS
                if ($this->_verifyEmpty($synon_1)) {
                    $synon_1 = $this->_sql_escape($synon_1);
                    $result .= '<td>'. $synon_1 .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO SYNON 1</td>';
                    $synon_1 = NULL;
                }
                
                if ($this->_verifyEmpty($synon_2)) {
                    $synon_2 = $this->_sql_escape($synon_2);
                    $result .= '<td>'. $synon_2 .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO SYNON 2</td>';
                    $synon_2 = NULL;
                }
                
                // CAS RN
                if ($this->_verifyEmpty($cas)) {
                    if ($this->obabel->verify_casrn($cas) === TRUE) {
                        $result .= '<td>'. $cas .'</td>';
                    } else {
                        $result .= '<td style="background-color:red">CAS NOT VALID</td>';
                        $cas = NULL;
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO CASRN</td>';
                    $cas = NULL;
                }
                
                // MOL ID
                // Interrogar a BD
                $this->Molecule_model->DB->where('casrn', $cas);
				$query_result = $this->Molecule_model->DB->get('molecule');
				$db_result = $query_result->num_rows();
                
                if ($db_result)
				{
                    $r = $query_result->row();
                    
                    if ($r->mid)
                    {
                        $result .= '<td>'. $r->mid .'</td>';
                       
                        //$this->Molecule_model->DB->where('mid', $r->mid);
                        //$this->Molecule_model->DB->set('name', $name);
                        //$n_result =  $this->Molecule_model->DB->update('molecule');
                        $n_result = TRUE;
                        $s1_result = TRUE;
                        $s2_result = TRUE;
                        
                        if ($synon_1) {
                            //$this->Molecule_model->DB->where('molecule', $r->mid);
                            //$this->Molecule_model->DB->set('synonym', $synon_1);
                            //$s1_result =  $this->Molecule_model->DB->update('othername');
                        }
                        
                        if ($synon_2) {
                            //$this->Molecule_model->DB->where('molecule', $r->mid);
                            //$this->Molecule_model->DB->set('synonym', $synon_2);
                            //$s2_result =  $this->Molecule_model->DB->update('othername');
                        }
                        
                        if ($n_result) {
                            $result .= '<td style="background-color:green">UPDATE N</td>';
                        } else {
                            $result .= '<td style="background-color:yellow">UPDATE N ERROR</td>';
                        }
                        
                        if ($s1_result) {
                            $result .= '<td style="background-color:green">UPDATE S1</td>';
                        } else {
                            $result .= '<td style="background-color:yellow">UPDATE S1 ERROR</td>';
                        }
                        
                        if ($s2_result) {
                            $result .= '<td style="background-color:green">UPDATE S2</td>';
                        } else {
                            $result .= '<td style="background-color:yellow">UPDATE S2 ERROR</td>';
                        }
                    }
                    else
                    {
                        $result .= '<td style="background-color:yellow">EMPTY MOL ID</td>';
                        $result .= '<td style="background-color:yellow">NO UPDATE N</td>';
                        $result .= '<td style="background-color:yellow">NO UPDATE S1</td>';
                        $result .= '<td style="background-color:yellow">NO UPDATE S2</td>';
                    }
                }
                else
                {
                    $result .= '<td style="background-color:yellow">NO MOL ID</td>';
                    $result .= '<td style="background-color:yellow">NO UPDATE N</td>';
                    $result .= '<td style="background-color:yellow">NO UPDATE S1</td>';
                    $result .= '<td style="background-color:yellow">NO UPDATE S2</td>';
                }
                
                $result .= '</tr>';
            }
            //$this->Molecule_model->DB->trans_complete();
			//$status = $this->Molecule_model->DB->trans_status();
            
            $result .='</table><p><a href="'. base_url('db/crc') .'">&lt; Back</a></p>';
            
            /*if ($status) {
                $result .= '<p><b>DONE</b></p>';
            } else {
                $result .= '<p><b>NOT DONE</b></p>';
            }*/
            
            echo $result;
        }
        catch (Exception $e)
		{
			die($e->getMessage());
		}
    }
    
    /**
     * SMILES dos compostos que nao existiam na BD
     */
    public function dump_crc_new_smiles()
    {
        // Nome do ficheiro CRC
		$inputFileName_1 = FCPATH . 'public/media/db_data/crc/crc_new_compounds_ids.xls';
        $inputFileName_2 = FCPATH . 'public/media/db_data/crc/crc_new_compounds_smiles.xls';
		$sheetName_1 = 'crc_new_ids';
        $sheetName_2 = 'crc_new_smiles';
		$this->Molecule_model->setDatabase(HOST, USER, PASS, 'therminfo2_teste');
        
        try
		{
			// Carregar o ficheiro .xls
			$objPHPExcel_1 = $this->_loadExcel($inputFileName_1, $sheetName_1);
            $objPHPExcel_2 = $this->_loadExcel($inputFileName_2, $sheetName_2);
            $result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
			$result .= '<p><a href="'. base_url('db/crc') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>CRC</th><th>NAME</th><th>CAS RN</th><th>SMILES</th><th>MOL ID</th><th>UPDATE</th></tr>';
			
			// Retirar informacao das celulas (8500)
			//$this->Molecule_model->DB->trans_start();
			for ($i = 2; $i <= 8500; ++$i)
			{
				$crc_1  = trim($objPHPExcel_1->getActiveSheet()->getCell('A'.$i)->getValue());
				$name   = trim($objPHPExcel_1->getActiveSheet()->getCell('B'.$i)->getValue());
				$cas    = trim($objPHPExcel_1->getActiveSheet()->getCell('D'.$i)->getValue());
				$smiles = NULL;
                $smiles_html  = '';
                
                $result .= '<tr>';
                // CRC
                if ($crc_1) {
                    settype($crc_1, "integer");
                    $result .= '<td>'. $crc_1 .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CRC</td>';
                }
                
                // NAME
                if ($this->_verifyEmpty($name)) {
                    $result .= '<td>'. $name .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO NAME</td>';
                }
                
                // CAS RN
                if ($this->_verifyEmpty($cas)) {
                    if ($this->obabel->verify_casrn($cas) === TRUE) {
                        $result .= '<td>'. $cas .'</td>';
                    } else {
                        $result .= '<td style="background-color:red">CAS NOT VALID</td>';
                        $cas = NULL;
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO CASRN</td>';
                    $cas = NULL;
                }
                
                // SMILES
                if ($crc_1)
                {
                    // Retirar smiles das celulas (8500)
                    for ($j = 2; $j <= 8500; ++$j)
                    {
                        $crc_2  = trim($objPHPExcel_2->getActiveSheet()->getCell('A'.$j)->getValue());
                        $smiles = trim($objPHPExcel_2->getActiveSheet()->getCell('B'.$j)->getValue());
                        
                        if ($crc_2 && $crc_2 == $crc_1)
                        {
                            if ($this->_verifyEmpty($smiles)) {
                                $smiles_html = '<td>'. $smiles .'</td>';
                                break;
                            } else {
                                $smiles_html = '<td style="background-color:yellow">NO SMILES</td>';
                                $smiles = NULL;
                            }
                        }
                        else
                        {
                            $smiles_html = '<td style="background-color:yellow">NO SMILES</td>';
                            $smiles = NULL;
                        }
                    }
                }
                else
                {
                    $smiles_html = '<td style="background-color:yellow">NO SMILES</td>';
                }
                
                $result .= $smiles_html;
                
                // MOL ID
                // Interrogar a BD
                $this->Molecule_model->DB->where('casrn', $cas);
				$query_result = $this->Molecule_model->DB->get('molecule');
				$db_result = $query_result->num_rows();
                
                if ($db_result)
				{
                    $r = $query_result->row();
                    
                    if ($r->mid)
                    {
                        $result .= '<td>'. $r->mid .'</td>';
                       
                        //$this->Molecule_model->DB->where('mid', $r->mid);
                        //$this->Molecule_model->DB->set('smiles', $smiles);
                        //$up_result =  $this->Molecule_model->DB->update('molecule');
                        $up_result = TRUE;
                        if ($up_result) {
                            $result .= '<td style="background-color:green">UPDATE</td>';
                        } else {
                            $result .= '<td style="background-color:yellow">UPDATE ERROR</td>';
                        }
                    }
                    else
                    {
                        $result .= '<td style="background-color:yellow">EMPTY MOL ID</td>';
                        $result .= '<td style="background-color:yellow">NO UPDATE</td>';
                    }
                }
                else
                {
                    $result .= '<td style="background-color:yellow">NO MOL ID</td>';
                    $result .= '<td style="background-color:yellow">NO UPDATE</td>';
                }
                
                $result .= '</tr>';
            }
            //$this->Molecule_model->DB->trans_complete();
			//$status = $this->Molecule_model->DB->trans_status();
            
            $result .='</table><p><a href="'. base_url('db/crc') .'">&lt; Back</a></p>';
            
            /*if ($status) {
                $result .= '<p><b>DONE</b></p>';
            } else {
                $result .= '<p><b>NOT DONE</b></p>';
            }*/
            
            echo $result;
        }
        catch (Exception $e)
		{
			die($e->getMessage());
		}
    }
    
    /**
     * Imagens dos compostos que nao existiam na BD
     */
    public function dump_crc_new_images()
    {
    	// Nome do ficheiro CRC
		$inputFileName_1 = FCPATH . 'public/media/db_data/crc/crc_new_compounds_ids.xls';
		$sheetName_1 = 'crc_new_ids';
		$this->Molecule_model->setDatabase(HOST, USER, PASS, 'therminfo2_teste');
        $this->load->helper('file');
        
        try
		{
			// Carregar o ficheiro .xls
			$objPHPExcel_1 = $this->_loadExcel($inputFileName_1, $sheetName_1);
			$img_array = get_filenames('public/media/db_data/crc/crc_figs_resize/');
            $result = '<style>table{font-family:Arial,sans-serif; border:1px solid black; font-size:12px;} th{background-color:#A7C942; color:#ffffff;} td{padding: 2px; white-space:nowrap;}</style>';
			$result .= '<p><a href="'. base_url('db/crc') .'">&lt; Back</a></p><table>';
            $result .= '<tr><th>CRC</th><th>CAS RN</th><th>MOL ID</th><th>IMAGE</th><th>NEW IMAGE</th><th>UPDATE</th></tr>';
			
			// Retirar informacao das celulas (8500)
			//$this->Molecule_model->DB->trans_start();
			for ($i = 2; $i <= 8500; ++$i)
			{
				$crc  = trim($objPHPExcel_1->getActiveSheet()->getCell('A'.$i)->getValue());
				$cas  = trim($objPHPExcel_1->getActiveSheet()->getCell('D'.$i)->getValue());
                
                $result .= '<tr>';
                // CRC
                if ($crc) {
                    settype($crc, "integer");
                    $result .= '<td>'. $crc .'</td>';
                } else {
                    $result .= '<td style="background-color:yellow">NO CRC</td>';
					$crc = NULL;
                }
                
                // CAS RN
                if ($this->_verifyEmpty($cas)) {
                    if ($this->obabel->verify_casrn($cas) === TRUE) {
                        $result .= '<td>'. $cas .'</td>';
                    } else {
                        $result .= '<td style="background-color:red">CAS NOT VALID</td>';
                        $cas = NULL;
                    }
                } else {
                    $result .= '<td style="background-color:yellow">NO CASRN</td>';
                    $cas = NULL;
                }
                
                // MOL ID
                // Interrogar a BD
                $this->Molecule_model->DB->where('casrn', $cas);
				$query_result = $this->Molecule_model->DB->get('molecule');
				$db_result = $query_result->num_rows();
                
                if ($db_result)
				{
                    $r = $query_result->row();
                    
                    if ($r->therminfo_id)
                    {
                        $result .= '<td>'. $r->therminfo_id .'</td>';
                       
					   // IMAGE
					   if ($r->img_path)
					   {
					   		$result .= '<td>'. $r->img_path .'</td>';
							$result .= '<td style="background-color:blue">NO NEW</td>';
							$result .= '<td style="background-color:blue">EXIST IMG</td>';
					   }
					   else
					   {
					   		$key = array_search($crc, $img_array);
							
					   		if ($key !== FALSE)
					   		{
					   			$img_path = $img_array[$key];
								$img = $r->therminfo_id .'.jpg';
					   			$result .= '<td>'. $img_path .'</td>';
								$result .= '<td>'. $img .'</td>';
					   			$img_path = 'public/media/db_data/crc/crc_figs_resize/'. $img_path;
								$new_path = 'public/media/db_data/crc/crc_figs_resize/'. $img;
								//$rn_result = rename($img_path, $new_path);
								$rn_result = TRUE;
								if ($rn_result)
								{
									//$this->Molecule_model->DB->where('therminfo_id', $r->therminfo_id);
			                        //$this->Molecule_model->DB->set('img_path', $img);
			                        //$up_result =  $this->Molecule_model->DB->update('molecule');
			                        $up_result = FALSE;
			                        if ($up_result) {
			                            $result .= '<td style="background-color:green">UPDATE</td>';
			                        } else {
			                            $result .= '<td style="background-color:yellow">UPDATE ERROR</td>';
			                        }
								}
								else
								{
									$result .= '<td style="background-color:yellow">NO RENAME</td>';
								}
							}
							else
							{
								$result .= '<td style="background-color:yellow">NO IMAGE</td>';
								$result .= '<td style="background-color:yellow">NO NEW</td>';
								$result .= '<td style="background-color:yellow">NO UPDATE</td>';
							}
					   }
                        
                    }
                    else
                    {
                        $result .= '<td style="background-color:yellow">EMPTY MOL ID</td>';
						$result .= '<td style="background-color:yellow">NO IMAGE</td>';
						$result .= '<td style="background-color:yellow">NO NEW</td>';
                        $result .= '<td style="background-color:yellow">NO UPDATE</td>';
                    }
                }
                else
                {
                    $result .= '<td style="background-color:yellow">NO MOL ID</td>';
					$result .= '<td style="background-color:yellow">NO IMAGE</td>';
					$result .= '<td style="background-color:yellow">NO NEW</td>';
                    $result .= '<td style="background-color:yellow">NO UPDATE</td>';
                }
                
                $result .= '</tr>';
            }
            //$this->Molecule_model->DB->trans_complete();
			//$status = $this->Molecule_model->DB->trans_status();
            
            $result .='</table><p><a href="'. base_url('db/crc') .'">&lt; Back</a></p>';
            
            /*if ($status) {
                $result .= '<p><b>DONE</b></p>';
            } else {
                $result .= '<p><b>NOT DONE</b></p>';
            }*/
            
            echo $result;
        }
        catch (Exception $e)
		{
			die($e->getMessage());
		}
    }
    
    /*
     * SQL escape
     */
    private function _sql_escape($str)
    {
        //$str = addslashes($str);
        $str = $this->Molecule_model->DB->escape_str($str);
        return $str;
    }
    
    /*
     * Remove numbers from a string
     */
    private function _remove_num($str)
    {
        $result = '';
        $result = preg_replace('/[\d,.,\-,(,),<,>]+/', '', $str);
        
        return trim($result);
    }
    
    /*
     * Remove string from a number
     */
    private function _remove_str($num)
    {
        $result = '';
        $result = preg_replace('/[a-zA-Z,(,),;,»]/', '', $num);
        
        return trim($result);
    }
    
    /*
     * Remove a character from a string
     */
    private function _remove_char($ch, $str)
    {
        $result = '';
        $pattern = '/['. $ch .']+/';
        $result = preg_replace($pattern, '', $str);
        
        return trim($result);
    }
    
    /*
     * Load a Excel file
     */
    private function _loadExcel($file, $sheetName = '')
    {
        // Criar o leitor do ficheiro .xls
        $excelReader = new PHPExcel_Reader_Excel5();
        // Carregar o ficheiro .xls
        if (! empty($sheetName)) {
            $excelReader->setLoadSheetsOnly($sheetName);
        }
        
        $excelReader->setReadDataOnly(true);
        $objPHPExcel = $excelReader->load($file);
        return $objPHPExcel;
    }
    
    /*
     * Verify empty values
     */
    private function _verifyEmpty($str)
    {
        return $str && ! is_null($str) && ! empty($str);
    }
    
    /*
     * Verify null values
     */
    private function _verifyNull($str)
    {
        return $str && ! is_null($str) && ! empty($str) && $str != 'Null';
    }
    
    /*
     * Verify a character in a string
     */
    private function _verifyChar($ch, $str)
    {
        $result = FALSE;
        $pattern = '/'. $ch .'/';
        if (preg_match($pattern, $str) === 1) {
            $result = TRUE;
        }
        
        return $result;
    }
}

/* End of file crc.php */
/* Location: ./application/controllers/crc.php */