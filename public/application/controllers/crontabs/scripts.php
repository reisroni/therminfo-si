<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* scripts.php
* Scripts para crontabs
*
* Criado: 06-02-2014
* Modificado: 24-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Scripts extends CI_Controller {
    
    // Atributos
	private $path; // Caminho para a pasta 'storage'
    private $path_logs; // Caminho para a pasta 'storage/ip_logs'
    
    /**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
        $this->path = 'storage'. DS;
        $this->path_logs = 'storage'. DS . 'ip_logs'. DS;
		// Carregar os modelos
        $this->load->model('molecule/Molecule_model');
        $this->load->model('user/Mol_user_model');
		$this->load->model('statistics/Contador_model');
        $this->load->model('statistics/Dbevolution_model');
        // Carregar os modulos
        $this->load->library('OBabel');
        $this->load->library('Util');
        $this->load->helper('file');
    }
    
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		$this->load->view('content/forbidden_view');
	}
    
    /**
     * Gerar o ficheiro '.pkl' de toda a BD
     */
    public function create_db_pkl()
    {
    	// Caminho do ficheiro SMILES
    	$smiles_file = $this->path .'therminfo_smiles.smi';
		
        if ($this->_db_file_smiles($smiles_file)) {
        	$pkl_result = $this->obabel->create_pkl($smiles_file);
			
			if ($pkl_result) {
				echo 'PKL file created - '. date('Y/m/d - H:i:s');
			} else {
				echo 'PKL file not created - '. date('Y/m/d - H:i:s');
                log_message('error', '[Crontab] PKL file (.pkl) not created');
			}
        } else {
        	echo 'SMILES file not created - '. date('Y/m/d - H:i:s');
            log_message('error', '[Crontab] SMILES file (.smi) not created on PKL file creation');
        }
    }
    
    /**
     * Evolucao da BD
     */
    public function db_evolution_insert()
    {
        $total_molecules = $this->Molecule_model->count_all();
        $total_user_mols = $this->Mol_user_model->count_all_distinct_columns('molecule');
    
        if ($total_molecules && $total_user_mols)
        {
            $data = array('month' => date("m"), 'year' => date("Y"), 
                          'nrcompounds' => $total_molecules, 'nrcompusers' => $total_user_mols);
            $record = $this->Dbevolution_model->instantiate($data);
            $save_result = $record->save();
            
            if (is_array($save_result)) {
                if ($save_result['result']) {
                    echo 'DB Evolution save ok - '. date('Y/m/d - H:i:s');
                } else {
                    echo 'DB Evolution save error: '. $save_result['error'] .' - '. date('Y/m/d - H:i:s');
                    log_message('error', '[Crontab] DB Evolution save error: '. $save_result['error'] .' - '. $save_result['e_desc']);
                }
            } else {
                echo 'DB Evolution save error - '. date('Y/m/d - H:i:s');
                log_message('error', '[Crontab] DB Evolution save error');
            }
        }
        else
        {
            log_message('error', '[Crontab] DB Evolution save error. No molecules or molecules user');
        }
    }
    
    /**
     * Lista dos IPs da BD
     */
    public function ip_list()
    {
        $file = 'iplog_'. date('Y-m-d_His') .'.txt';
        $file_path = $this->path_logs . $file;
        
        if ($this->_db_file_ip($file_path)) {
            $from = 'noreply@therminfo.com';
            $to = 'therminfo@gmail.com';
            $subject = 'IP Log File: '. date('F - Y');
            $message = "Methods:\n\t1 - Quick Search\n\t2 - Advanced Search\n\t3 - Structural Search\n\t";
            $message .= "4 - Fragment Search\n\t5 - SMARTS Search\n\t6 - Property Search\n\nQuick Search Type:\n\t";
            $message .= "1 - Name\n\t2 - Molecular Formula\n\t3 - ThermInfo ID\n\t4 - CAS RN\n\t5 - SMILES\n\t6 - InChi";
            $send_result = $this->util->send_mail($from, 'ThermInfo Admin', $to, $subject, $message, $file_path);
            
            if ($send_result) {
                echo 'IP log: '. $file .' created and sent - '. date('Y/m/d - H:i:s');
            } else {
                echo 'IP log not sent - '. date('Y/m/d - H:i:s');
                log_message('error', '[Crontab] IP log: '. $file .' - not sent');
            }
        } else {
            echo 'IP log not created - '. date('Y/m/d - H:i:s');
            log_message('error', '[Crontab] IP log not created');
        }
    }
    
    /**
     * Backup da BD
     */
    public function therminfo_backup()
    {
        // Efetua o backup e mostra o resultado
        $bk_result = $this->Dbevolution_model->backup_db('therminfo2');
        
        if ($bk_result) {
            echo 'DB Backup ok - '. date('Y/m/d - H:i:s');
        } else {
            echo 'DB Backup error - '. date('Y/m/d - H:i:s');
            log_message('error', '[Crontab] DB Backup error');
        }
    }
    
    /**
     * SMILES da BD
     */
    public function create_usmiles()
    {
        // Caminho do ficheiro SMILES
    	$smiles_file = $this->path .'db_smiles.smi';
		
        if ($this->_db_file_smiles($smiles_file)) {
        	echo 'SMILES file created - '. date('Y/m/d - H:i:s');
        } else {
        	echo 'SMILES file not created - '. date('Y/m/d - H:i:s');
            log_message('error', '[Crontab] SMILES file (.smi) not created');
        }
    }
    
    /*
     * Cria um ficheiro com os SMILES da BD
	 * 
	 * @param string $file nome e localizacao do ficheiro
	 * 
	 * @return boolean 'TRUE' para sucesso
	 * ou 'FALSE' em caso de falha
     */
    private function _db_file_smiles($file = '')
    {
        $result = FALSE;
		if (! empty($file))
		{
	        $query = "SELECT SUBSTRING_INDEX(smiles, ',', 1) AS smi, mid FROM molecule WHERE smiles IS NOT NULL";
	        $query_result = $this->Dbevolution_model->DB->query($query);
	        
	        if ($query_result)
	        {
	            if ($query_result->num_rows() > 0)
	            {
	                $write_result = write_file($file, "SMILES\tmid\n", FOPEN_WRITE_CREATE_DESTRUCTIVE);
					if ($write_result)
					{
		                foreach($query_result->result() as $row)
		                {
		                	$text = trim($row->smi) ."\t". trim($row->mid) ."\n";
							$write_result = write_file($file, $text, FOPEN_WRITE_CREATE);
							if (! $write_result) {
								return $result;
							}
		                }
						
						$result = TRUE;
					}
	            }
	        }
		}
		
        return $result;
    }
    
    /*
     * Cria um ficheiro com os IPs da BD
	 * 
	 * @param string $file nome e localizacao do ficheiro
     * @param int $month Mes (por omissao mes corrente)
	 * @param int $year Ano (por omissao ano corrente)
     *
	 * @return boolean 'TRUE' para sucesso
	 * ou 'FALSE' em caso de falha
     */
    private function _db_file_ip($file = '', $month = 0, $year = 0)
    {
        $result = FALSE;
		if (! empty($file))
		{
            $month = empty($month) ? date('m') : $month;
            $year  = empty($year) ? date('Y') : $year;
	        $query = "SELECT * FROM contador WHERE month={$month} AND year={$year}";
	        $query_result = $this->Dbevolution_model->DB->query($query);
	        
	        if ($query_result)
	        {
	            if ($query_result->num_rows() > 0)
	            {
	                $write_result = write_file($file, "Day\tMonth\tYear\tHour\tMinute\tSecond\tIP\tSearch\tTerm Type\tCountry\tCity\n", FOPEN_WRITE_CREATE_DESTRUCTIVE);
					if ($write_result)
					{
		                foreach($query_result->result() as $row)
		                {
		                	$text = trim($row->day) ."\t". trim($row->month) ."\t". trim($row->year) .
                                    "\t". trim($row->hour) ."\t". trim($row->minute) ."\t". trim($row->second) .
                                    "\t". trim($row->ip) ."\t". trim($row->method) ."\t". trim($row->method_type) .
                                    "\t". trim($row->country) ."\t". trim($row->city) ."\n";
							$write_result = write_file($file, $text, FOPEN_WRITE_CREATE);
                            
							if (! $write_result) {
								return $result;
							}
		                }
						
						$result = TRUE;
					}
	            }
	        }
		}
		
        return $result;
    }
}

/* End of file scripts.php */
/* Location: ./application/controllers/crontabs/scripts.php */