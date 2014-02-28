<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin_insert_data.php
* Controlador da administracao (Insert New data)
* Criado: 20-01-2014
* Modificado: 28-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin_insert_data extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('user_name' => NULL,
							'user_type' => NULL,
							'user_inst' => NULL,
							'user_email' => NULL);
        // Carregar os modelos
        $this->load->model('reference/Reference_model');
		$this->load->model('reference/Author_model');
		$this->load->model('reference/Author_ref_model');
		$this->load->model('property/Data_value_model');
		$this->load->model('property/Data_model');
		$this->load->model('other/Serialize_values_model');
        $this->load->model('other/Session_model');
		// Carregar os modulos necessarios
        $this->load->library('Util');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// ** Verifica se ja esta logado **
		if ($this->Session_model->is_logged_in())
		{
			// Verifica o tipo de utilizador
			if ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin')
			{
				// Dados do utilizador
				$this->data['user_name'] = $_SESSION['name'];
				$this->data['user_type'] = $_SESSION['type'];
				$this->data['user_inst'] = $_SESSION['user_inst'];
				$this->data['user_email'] = $_SESSION['user_email'];

				$this->load->view('content/admin/admin_insert_data_view', $this->data);
			}
			else
			{
				// Area proibida
				set_status_header(401, 'Forbidden Area');
				$this->load->view('content/forbidden_view');
			}
		}
		else
		{ 
			// Volta a pagina de login
			redirect('/login/redirect/administration/admin_insert_data');
		}
	}
		
	//---------------------------------------------------------------
	// Separador 'Insert New Data'
	//---------------------------------------------------------------
	//
	// ----- Inserir novos dados
	/**
	 * Adiciona uma nova referencia a base de dados
	 * 
	 * @return void
	 */
	public function insert_new_reference()
	{
		if (! isset($_POST['submit'])) 
		{
			// Volta a pagina de admin
			redirect('/administration/admin_insert_data');
		}
		else
		{
			$authors = $this->input->post('a-ref-author-select') ? $this->input->post('a-ref-author-select') : array();
			$title = $this->input->post('a-ref-title') ? $this->input->post('a-ref-title') : '';
			$type = $this->input->post('a-ref-type') ? $this->input->post('a-ref-type') : '';
			$volume = $this->input->post('a-ref-volume') ? $this->input->post('a-ref-volume') : '';
			$issue = $this->input->post('a-ref-issue')? $this->input->post('a-ref-issue') : '';
			$year = $this->input->post('a-ref-year') ? $this->input->post('a-ref-year') : '';
			$bpage = $this->input->post('a-ref-bpage') ? $this->input->post('a-ref-bpage') : '';
			$epage = $this->input->post('a-ref-epage') ? $this->input->post('a-ref-epage') : '';
			
			switch ($type)
			{
				// Livro
				case 'book' :
				{
					$book = $this->input->post('a-ref-book');
					$editor = $this->input->post('a-ref-editor');
					$publisher = $this->input->post('a-ref-publisher');
					
					$ins_status = FALSE; //$this->Admin_model->add_reference('Book', $authors, $title, $year, '', $book, $volume, $issue, $bpage, $epage, $editor, $publisher);
					
					if ($ins_status) {
						$result = '<p class="msgPane">Added a new reference!</p>';
					} else {
						$result = '<p class="errorPane">No reference added!</p>';
					}
				} break;
				// Artigo
				case 'paper' :
				{
					$journal = $this->input->post('a-ref-journal');
					
					$ins_status = FALSE; //$this->Admin_model->add_reference('Paper', $authors, $title, $year, $journal, '', $volume, $issue, $bpage, $epage);
					
					if ($ins_status) {
						$result = '<p class="msgPane"> Added a new reference!</p>';
					} else {
						$result = '<p class="errorPane">No reference added!</p>';
					}
				} break;
				default : $result = '<p class="errorPane">Failure!</p>';
			}
			
			$this->output->set_output($result);
		}
	}
	
	/**
	 * Adiciona um novo autor a base de dados
	 * 
	 * @return void
	 */
	public function insert_new_author()
	{
		if (! isset($_POST['submit'])) 
		{
			// Volta a pagina de admin
			redirect('/administration/admin_insert_data');
		}
		else
		{
			$new_auth = $this->input->post('a-ref-author');
			$ins_status = FALSE; //$this->Admin_model->add_author($new_auth);
			
			if ($ins_status) {
				$result = 1;
			} else {
				$result = 0;
			}
			
			$this->output->set_output($result);
		}
	}
	
	/**
	 * Adiciona novos valores de propriedades a base de dados
	 * 
	 * @return void
	 */
	public function insert_new_data_values()
	{
		if (! isset($_POST['submit'])) 
		{
			// ** Verifica se o utilizador e administrador **
			if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
			{
				$this->load->view('content/admin/admin_insert_data_view');
			}
			else
			{
				set_status_header(401, 'Forbidden Area');
				$this->load->view('content/forbidden_view');
			}
		}
		else
		{
			if (isset($_SESSION['validate']) && $_SESSION['validate'])
			{
				if (isset($_SESSION['data_values']))
				{
					$data = FALSE; //$this->Admin_model->get_serialize_values($_SESSION['data_values']);
					
					if (! $data) {
						$data = array();
					}
					
					$ins_result = FALSE; //$this->Admin_model->add_batch($data);
					
					if (is_array($ins_result))
					{
						if ($ins_result['done'] === FALSE)
						{
							$result = '<div class="errorPane"><img src="public/media/images/error.png" alt="Error" title="Error" />'.
							'<p class="textCenter">A problem occurred. No records inserted<br>';
							$lines = $ins_result['lines'];
							$i = 1;
							
							foreach($lines as $r)
							{
								if ($r['result'] === FALSE) {
									$result .= "<strong>- Line {$i}</strong> is cause a problem<br>";
								} else {
									$result .= "<strong>- Line {$i}</strong> is ok<br>";
								}
								
								++$i;
							}
							
							$result .= '</p></div>';
						}
						else
						{
							$result = '<p><strong>Records inserted:</strong></p>';
							$result .= '<table class="a-msg-table bodyText"><thead><tr>'.
							'<th>Line</th><th>ThermInfo ID</th><th>Record</th></tr></thead><tbody>';
							$lines = $ins_result['lines'];
							$i = 1;
							
							foreach($lines as $r)
							{
								$mol = FALSE; //$this->Admin_model->searchByMID($r['id']);
								$thermID = 0; //$mol->therminfo_id;
								$result .= "<tr><td>$i</td><td>{$thermID}</td><td><a href='compound/view/{$thermID}' ".
								"title='{$thermID} Record' target='_blank'>". site_url("/compound/view/{$thermID}") .'</a></td></tr>';
								++$i;
							}
							
							$result .='</tbody></table>';
						}
					}
					else if ($ins_result === FALSE)
					{
						$result = '<div class="a-loading errorPane"><img src="public/media/images/error.png" 
						alt="Error" title="Error" /> Failure</div>';
					}
					else
					{
						$result = '<div class="a-loading errorPane"><img src="public/media/images/error.png" 
						alt="Error" title="Error" /> Database Failure</div>';
					}
					// Apaga os dados
					//$this->Admin_model->remove_serialize_values($_SESSION['data_values']);
					unset($_SESSION['validate']);
					unset($_SESSION['data_values']);
				}
				else
				{
					$result = '<div class="a-loading errorPane"><img src="public/media/images/error.png" 
					alt="Error" title="Error" /> Failure</div>';
				}
			}
			else
			{
				$result = '<div class="a-loading errorPane"><img src="public/media/images/error.png" 
				alt="Error" title="Error" /> Data not yet validated</div>';
			}
			
			$this->output->set_output($result);
		}
	}
	
	/**
	 * Valida os valores
	 * 
	 * @return void
	 */
	public function validate_new_data_values()
	{
		if (! isset($_POST['submit'])) 
		{
			// Volta a pagina de admin
			redirect('/administration/admin_insert_data');
		}
		else
		{
			// Valida as linhas
			$_SESSION['validate'] = FALSE;
			$prop_ref = $this->input->post('ref-id');
			$mol_type = $this->input->post('mol-id');
			$prop_id = $this->input->post('prop-id');
			$prop_is_numeric = $this->input->post('prop-num') == 'false' ? FALSE : TRUE;
			
			if ($this->input->post('input-type') == 'box')
			{
				$mols = rtrim($this->input->post('mols'));
			}
			else if ($this->input->post('input-type') == 'file')
			{
				$file_path = base64_decode($this->input->post('file'));
				
				if (file_exists($file_path)) {
					$file_content = @file_get_contents($file_path);
					$mols = rtrim($file_content);
				} else {
					$mols = FALSE;
				}
			}
			else
			{
				$mols = FALSE;
			}
			
			$result = '<table class="a-msg-table bodyText"><thead><tr><th>Line</th>
			<th>Compound</th><th>Property Value</th><th>Value Error</th><th>OBS</th>
			<th>Message</th><th></th></tr></thead><tbody>';
			$lines = explode("\n", $mols);
			$len = count($lines); // Linhas
			
			if ($len < 0)
			{
				$result = '<table><tbody><tr><td>No Lines added</td><td><img src="public/media/images/error.png"'.
				' alt="Error" title="Error" /></td></tr>';
			}
			else
			{
				$data_values = array();
				$errors = 0;
				
				for ($i = 0; $i < $len; ++$i)
				{
					$line = explode('|', $lines[$i]);
					$x = count($line); // Colunas
					$img = '<img src="public/media/images/no.png" alt="Error" title="Error" />';
					
					if ($x > 3)
					{
						$tmp_1 = str_replace(' ', '', $line[0]);
						$tmp_2 = str_replace(' ', '', $line[1]);
						$tmp_3 = str_replace(' ', '', $line[2]);
						$tmp_4 = str_replace(' ', '', $line[3]);
						$mol_id = empty($tmp_1) ? 'None' : trim($line[0]);
						$prop_value = empty($tmp_2) ? 'None' : trim($line[1]);
						$prop_value = str_replace(',', '.', $prop_value);
						$prop_error = empty($tmp_3) ? 'n.a.' : trim($line[2]);
						$prop_error = str_replace(',', '.', $prop_error);
						$prop_obs = empty($tmp_4) ? NULL : $line[3];
						
						if (($x - 4) > 0)
						{
							for ($j = 4; $j < $x; ++$j)
							{
								$tmp_5 = str_replace(' ', '', $line[$j]);
								$prop_obs .= empty($tmp_5) ? NULL : " {$line[$j]}";
							}
						}
							
						$val_status = 0; //$this->Admin_model->validate_data($mol_type, $mol_id, $prop_ref, $prop_id, $prop_value, $prop_error, $prop_is_numeric);
						
						switch ($val_status)
						{
							case 11 : { $msg = 'Invalid InChi format'; }; break;
							case 12 : { $msg = 'There is already a value'; }; break;
							case 21 : { $msg = 'Invalid CAS Registry Number format'; }; break;
							case 22 : { $msg = 'Invalid CAS Registry Number'; }; break;
							case 23 : { $msg = 'There is already a value'; }; break;
							case 31 : { $msg = 'Invalid SMILES'; }; break;
							case 32 : { $msg = 'There is already a value'; }; break;
							case 41 : { $msg = 'Value is not numeric'; }; break;
							case 42 : { $msg = 'Error value is not numeric'; }; break;
							case 1 : { $msg = 'Ok'; $img = '<img src="public/media/images/ok.png" alt="Ok" title="Ok" />'; }; break;
							case 0 : { $msg = 'An error occurred in the database'; }; break;
							case 6 : { $msg = 'Compound ID not correct'; }; break;
						}
						
						array_push($data_values, array('type' => $mol_type, 'mol_id' => $mol_id, 'prop' => $prop_id, 'value' => $prop_value, 
						'error' => $prop_error, 'obs' => $prop_obs, 'ref' => $prop_ref, 'valid' => 1, 'user' => $_SESSION['user_email']));
						
						$result .= '<tr><td>'.($i + 1)."</td><td>{$mol_id}</td>
						<td>{$prop_value}</td><td>{$prop_error}</td><td>{$prop_obs}</td>
						<td>{$msg}</td><td>{$img}</td></tr>";
						
						if ($val_status !== 1) {
							++$errors;
						}
					}
					else if ($x == 3)
					{
						$tmp_1 = str_replace(' ', '', $line[0]);
						$tmp_2 = str_replace(' ', '', $line[1]);
						$tmp_3 = str_replace(' ', '', $line[2]);
						$mol_id = empty($tmp_1) ? 'None' : trim($line[0]);
						$prop_value = empty($tmp_2) ? 'None' : trim($line[1]);
						$prop_value = str_replace(',', '.', $prop_value);
						$prop_error = empty($tmp_3) ? 'n.a.' : trim($line[2]);
						$prop_error = str_replace(',', '.', $prop_error);
						$prop_obs = NULL;
							
						$val_status = 0; //$this->Admin_model->validate_data($mol_type, $mol_id, $prop_ref, $prop_id, $prop_value, $prop_error, $prop_is_numeric);
						
						switch ($val_status)
						{
							case 11 : { $msg = 'Invalid InChi format'; }; break;
							case 12 : { $msg = 'There is already a value'; }; break;
							case 21 : { $msg = 'Invalid CAS Registry Number format'; }; break;
							case 22 : { $msg = 'Invalid CAS Registry Number'; }; break;
							case 23 : { $msg = 'There is already a value'; }; break;
							case 31 : { $msg = 'Invalid SMILES'; }; break;
							case 32 : { $msg = 'There is already a value'; }; break;
							case 41 : { $msg = 'Value is not numeric'; }; break;
							case 42 : { $msg = 'Error value is not numeric'; }; break;
							case 1 : { $msg = 'Ok'; $img = '<img src="public/media/images/ok.png" alt="Ok" title="Ok" />'; }; break;
							case 0 : { $msg = 'An error occurred in the database'; }; break;
							case 6 : { $msg = 'Compound ID not correct'; }; break;
						}
						
						array_push($data_values, array('type' => $mol_type, 'mol_id' => $mol_id, 'prop' => $prop_id, 'value' => $prop_value, 
						'error' => $prop_error, 'obs' => $prop_obs, 'ref' => $prop_ref, 'valid' => 1, 'user' => $_SESSION['user_email']));
						
						$result .= '<tr><td>'.($i + 1)."</td><td>{$mol_id}</td>
						<td>{$prop_value}</td><td>{$prop_error}</td><td>{$prop_obs}</td>
						<td>{$msg}</td><td>{$img}</td></tr>";
						
						if ($val_status !== 1) {
							++$errors;
						}
					}
					else if ($x == 2)
					{
						$tmp_1 = str_replace(' ', '', $line[0]);
						$tmp_2 = str_replace(' ', '', $line[1]);
						$mol_id = empty($tmp_1) ? 'None' : trim($line[0]);
						$prop_value = empty($tmp_2) ? 'None' : trim($line[1]);
						$prop_value = str_replace(',', '.', $prop_value);
						$prop_error = 'n.a.';
						$prop_obs = NULL;
							
						$val_status = 0; //$this->Admin_model->validate_data($mol_type, $mol_id, $prop_ref, $prop_id, $prop_value, $prop_error, $prop_is_numeric);
						
						switch ($val_status)
						{
							case 11 : { $msg = 'Invalid InChi format'; }; break;
							case 12 : { $msg = 'There is already a value'; }; break;
							case 21 : { $msg = 'Invalid CAS Registry Number format'; }; break;
							case 22 : { $msg = 'Invalid CAS Registry Number'; }; break;
							case 23 : { $msg = 'There is already a value'; }; break;
							case 31 : { $msg = 'Invalid SMILES'; }; break;
							case 32 : { $msg = 'There is already a value'; }; break;
							case 41 : { $msg = 'Value is not numeric'; }; break;
							case 42 : { $msg = 'Error value is not numeric'; }; break;
							case 1 : { $msg = 'Ok'; $img = '<img src="public/media/images/ok.png" alt="Ok" title="Ok" />'; }; break;
							case 0 : { $msg = 'An error occurred in the database'; }; break;
							case 6 : { $msg = 'Compound ID not correct'; }; break;
						}
						
						array_push($data_values, array('type' => $mol_type, 'mol_id' => $mol_id, 'prop' => $prop_id, 'value' => $prop_value, 
						'error' => $prop_error, 'obs' => $prop_obs, 'ref' => $prop_ref, 'valid' => 1, 'user' => $_SESSION['user_email']));
						
						$result .= '<tr><td>'.($i + 1)."</td><td>{$mol_id}</td>
						<td>{$prop_value}</td><td>{$prop_error}</td><td>{$prop_obs}</td>
						<td>{$msg}</td><td>{$img}</td></tr>";
						
						if ($val_status !== 1) {
							++$errors;
						}
					}
					else
					{
						$result .= '<tr><td>'.($i + 1)."</td><td colspan='5'>Need at least 2 values</td><td>{$img}</td></tr>";
						++$errors;
					}
				}
			}
			
			$result .= '</tbody></table>';
			
			// Dados validados
			if (isset($errors) && $errors === 0)
			{
				$_SESSION['validate'] = TRUE;
				
				if (isset($data_values))
				{
					$data_id = FALSE; //$this->Admin_model->add_serialize_values($data_values);
					if ($data_id) {
						$_SESSION['data_values'] = $data_id;
					} else {
						$_SESSION['validate'] = FALSE;
					}
				}
			}
			
			$this->output->set_output($result);
		}
	}
	
	//---------------------------------------------------------------
	// Outros
	//---------------------------------------------------------------
	//
	/**
	 * Preenchimento das listas de referencias e propriedades
	 * 
	 * @return void
	 */
	public function get_lists()
	{
		$list = $this->input->post('list');
		
		if (! $list)
		{
			// Area proibida
			set_status_header(401, 'Forbidden Area');
			$this->load->view('content/forbidden_view');
		}
		else
		{
			switch ($list)
			{
				case 'refs' :
				{
					$refs = $this->Reference_model->find_all();
					$result = '<option value="none">Select a reference</option>';
				
					foreach ($refs as $ref) 
					{
						$auths = $this->Author_model->find_by_reference($ref->id);
						$auths_html = '';
						$i = count($auths);
						$j = 1;
						
						foreach ($auths as $auth)
						{
							if ($j == $i) {
								$auths_html .= $auth->full_name();
							} else {
								$auths_html .= $auth->full_name() .', ';
							}
							
							++$j;
						}
						
						$result .= "<option value='{$ref->id}' 
						title='{$auths_html} - {$ref->ref_all}'>{$ref->reference_code}</option>";
					}
				}; break;
				
				case 'props' :
				{
					$props = $this->Data_model->find_all_distinct('d_name');
					$result = '<option value="none">Select a property</option>';
				
					foreach ($props as $prop) 
					{
						$units = is_null($prop->units) ? '' : " - {$prop->units}";
						$result .= "<option value='{$prop->id}'>{$prop->d_name}{$units}</option>";
					}
				}; break;
				
				case 'auth' :
				{
					$auths = $this->Author_model->find_all_distinct('a_first_name, a_last_name');
					$result = '';
				
					foreach ($auths as $auth) {
						$result .= "<option value='{$auth->id}'>{$auth->full_name()}</option>";
					}
				}; break;
				
				default : $result = 'Bad list input';
			}
			
			$this->output->set_output($result);
		}
	}
	
	/**
	 * Carrega um ficheiro para o servidor
	 * 
	 * @return void
	 */
	public function upload_file()
	{
		if ($this->input->post('upload'))
		{
			$file_element_name = 'input-file';
			
			$config['upload_path'] = './storage/uploads/';
			$config['allowed_types'] = 'doc|txt';
			$config['max_size'] = 1024 * 10;
			$config['overwrite'] = TRUE;
			$config['encrypt_name'] = TRUE;
			
			$this->load->library('upload', $config);
			
			if (! $this->upload->do_upload($file_element_name))
			{
				$status = 'error';
				$msg = strip_tags($this->upload->display_errors());
				$file = false;
			}
			else
			{
				$data = $this->upload->data();
				$status = 'ok';
				$msg = 'File: '. $this->util->remover_acentos($data['file_name']) .' - Size: '. $data['file_size'];
				$file = base64_encode($data['full_path']);
				// Apagar o ficheiro temporario
				@unlink($_FILES[$file_element_name]['tmp_name']);
			}
			
			if ($this->input->post('type') && $this->input->post('type') == 'json') {
				$this->output->set_output(json_encode(array('status' => $status, 'msg' => $msg, 'file' => $file)));
			} else {
				$this->output->set_output("status: {$status}; {$msg}; file: {$file}");
			}
		}
		else
		{
			if ($this->input->post('type') && $this->input->post('type') == 'json') {
				$this->output->set_output(json_encode(array('status' => 'error', 'msg' => 'No data send', 'file' => false)));
			} else {
				$this->output->set_output("status: error; No data send; file: null");
			}
		}
	}
	
	/**
	 * Elimina um ficheiro carregado no servidor
	 * 
	 * @param string $file_path Caminho do ficheiro 
	 * 
	 * @return mixed 'TRUE' em caso de sucesso ou 
	 * 'FALSE' em caso de falha
	 */
	public function delete_file($file_path = '')
	{
		if (! empty($file_path))
		{
			return @unlink($file_path);
		}
		else if ($this->input->post('delete'))
		{
			$path = $this->input->post('path');
			
			if ($path) {
				if(@unlink(base64_decode($path))) {
					$status = 'ok';
				} else {
					$status = 'error';
				}
			} else {
				$status = 'error';
			}
			// Mostra o objecto JSON no output
			$this->output->set_output(json_encode(array('status' => $status)));
		}
		else
		{
			return FALSE;
		}
	}
}

/* End of file admin_insert_data.php */
/* Location: ./application/controllers/administration/admin_insert_data.php */