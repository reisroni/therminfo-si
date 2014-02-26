<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* compound.php
* Controlador da ficha de uma molecula
* Criado: 03-09-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Compound extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('molecule' => NULL,
							'molImage' => NULL,
							'sd_result' => NULL,
							'd_result' => NULL,
							'r_result' => NULL);
		// Carregar os modelos	
		$this->load->model('molecule/Molecule_model');
		$this->load->model('molecule/Othername_model');
		$this->load->model('property/Data_value_model');
		$this->load->model('property/Data_model');
		$this->load->model('property/Reference_model');
		$this->load->model('property/Author_model');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		redirect('/compound/view/');
	}
	
	/**
	 * Vista normal do composto
	 * 
	 * @param string $therm_id Therminfo ID
	 * 
	 * @return void
	 */
	public function view($therm_id = '')
	{
		if (! empty($therm_id))
		{
			$res_array = $this->Molecule_model->find_by_thermid($therm_id, 1);
			
			if (is_array($res_array) && count($res_array) == 1)
			{
				$mol = $res_array[0];
				$mid = $mol->mid; // ID
				$id = $mol->therminfo_id; // ThermInfo ID
				$casrn = is_null($mol->casrn) ? '' : $mol->casrn; // CAS RN
				$name = is_null($mol->name) ? '' : $mol->name; // Nome da molecula
				$form = is_null($mol->formula) ? '' : str_split($mol->formula); // Formula molecular
				$mw = is_null($mol->mw) ? '' : number_format($mol->mw, 2, '.', ''); // Peso molecular
				$state = is_null($mol->state) ? '' : $mol->state; // Estado fisico
				$p_form = is_null($mol->phi_form) ? '' : $mol->phi_form; // Forma fisica
				$smiles = is_null($mol->smiles) ? '' : $mol->smiles; // SMILES
				$usmiles = is_null($mol->usmiles) ? '' : $mol->usmiles; //SMILES unico
				$inchi = is_null($mol->inchi) ? '' : $mol->inchi; // InChI
				$inchikey = is_null($mol->inchikey) ? '' : $mol->inchikey; // InChIKey
				$sinchi = is_null($mol->s_inchi) ? '' : $mol->s_inchi; // Standard InChI
				$sinchikey = is_null($mol->s_inchikey) ? '' : $mol->s_inchikey; // Standard InChIKey
				$mol_file = is_null($mol->mol_file) ? '' : $mol->mol_file; // Mol File
				$img = base_url()."image/compound/{$id}"; // Imagem
				$names = $this->Othername_model->find_by_mol($mid); // Outros nomes
				$props = $this->Data_value_model->find_by_mol($mid); // Propriedades
				
				// Nome do composto
				$this->data['molecule'] = $name;
				// Imagem do composto
				$this->data['molImage'] = "<img id='compoundImg' src={$img} alt='{$name} Image' title='{$name} Image' />";
				// Dados estruturais do composto
				$this->data['sd_result'] = '<table id="compoundTable" class="bodyText"><tbody>';
				$this->data['sd_result'] .= "<tr class='grid'><td class='title'>ThermInfo ID:</td><td>{$id}</td></tr>";
				
				if (! empty($name)) {
					$this->data['sd_result'] .= "<tr><td class='title'>Compound Name:</td><td>{$name}</td></tr>";
				}
				if (! empty($names))
				{
					$i = 0;
					$onames = null;
					
					foreach ($names as $value)
					{
						if ($i == (sizeof($names) - 1))
							$onames .= '<strong>'.($i + 1). '. -> </strong>' . $value->synonym;
						else
							$onames .= '<strong>'.($i + 1). '. -> </strong>' . $value->synonym . '; ';
							
						++$i;
					}
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>Other Names:</td><td>{$onames}</td></tr>";
				}
				
				if (! empty($casrn)) {
					$this->data['sd_result'] .= "<tr><td class='title'>CASRN:</td><td>{$casrn}</td></tr>";
				}
				if (! empty($form))
				{
					$formula = null;
					foreach ($form as $chars)
					{
						if (is_numeric($chars))
						{
							$chars = '<sub>'.$chars.'</sub>';
							$formula .= $chars;
						}
						else
						{
							$formula .= $chars;
						}
					}
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>Molecular Formula:</td><td>{$formula}</td></tr>";
				}
				
				if (! empty($mw)) {
					$this->data['sd_result'] .= "<tr><td class='title'>Molecular Weight:</td><td>{$mw}</td></tr>";
				}
				
				if (! empty($state))
				{
					if ($state == 'l')
						$state = 'Liquid';
					else if ($state == 'g')
						$state = 'Gas';
					else if ($state == 'cr' or $state == 'c')
						$state = 'Crystal';
					else if ($state == 's')
						$state = 'Solid';
					
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>Physical State:</td><td>{$state}</td></tr>";
				}
				
				if (! empty($p_form) && empty($state)) {
					$this->data['sd_result'] .= "<tr><td class='title'>Physical Form:</td><td>{$p_form}</td></tr>";
				}
				if (! empty($smiles)) {
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>SMILES:</td><td>{$smiles}</td></tr>";
				}
				if (! empty($usmiles)) {
					$this->data['sd_result'] .= "<tr><td class='title'>Unique SMILES:</td><td>{$usmiles}</td></tr>";
				}
				if (! empty($inchi)) {
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>InChI:</td><td>{$inchi}</td></tr>";
				}
				if (! empty($inchikey)) {
					$this->data['sd_result'] .= "<tr><td class='title'>InChIKey:</td><td>{$inchikey}</td></tr>";
				}
				if (! empty($sinchi)) {
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>Standard InChI:</td><td>{$sinchi}</td></tr>";
				}
				if (! empty($sinchikey)) {
					$this->data['sd_result'] .= "<tr><td class='title'>Standard InChIKey:</td><td>{$sinchikey}</td></tr>";
				}
				
				if (! empty($mol_file))
				{
					$this->data['sd_result'] .= "<tr class='grid'><td class='title'>Mol File:</td><td>
					<form id='moreInfo' name='moreinfo' action='".base_url('compound/get_mol_file')."' method='post'>
					<input type='submit' class='btTxt clickable' name='submit' value='Download' title='Download Mol File' />
					<input type='hidden' name='mol_file' value='{$mol_file}' /></form></td></tr>";
				}
					
				$this->data['sd_result'] .= '</tbody></table>';
				
				// Propriedades do composto
				if (! empty($props))
				{
					$all_ref = array();
					$this->data['d_result'] = '<table id="compoundTable" class="bodyText"><tbody>';
					
					foreach ($props as $prop)
					{
						$mol_data = $this->Data_model->find_by_id($prop->data);
						$units = empty($mol_data->units) ? '' : "({$mol_data->units})";
                        $data_name = $mol_data->d_full_name ? "{$mol_data->d_full_name} {$units}" : "{$mol_data->d_name} {$units}";
						if (! empty($prop->value))
						{
							$this->data['d_result'] .= "<tr class='grid'><td><span class='orangeText'>{$data_name}</span></td></tr>";
							$prop_values = $this->Data_value_model->find_by_mol_prop($mid, $prop->data);
							
							$this->data['d_result'] .= '<tr><td><table class="prop-table bodyText">';
							$this->data['d_result'] .= '<thead><tr><th>Value</th><th>Uncertainty</th>
							<th>Reference</th><th>Observations</th></tr></thead><tbody>';
							
							foreach ($prop_values as $prop_value)
							{
								if (! empty($prop_value->value))
								{
									$prop_error = empty($prop_value->error) ? 'n.a.' : $prop_value->error;
									$advised = $prop_value->advised == 'yes' ? ' *' : NULL;
									$this->data['d_result'] .= "<tr><td>{$prop_value->value}</td><td>{$prop_error}</td>
									<td>{$prop_value->reference}</td><td>{$prop_value->obs}{$advised}</td></tr>";
									
									array_push($all_ref, $prop_value->reference);
								}
								else
								{
									$this->data['d_result'] .= '<tr><td>N.A.</td><td>N.A.</td><td>N.A.</td><td>N.A.</td><td>N.A.</td></tr>';
								}
							}
							$this->data['d_result'] .= '</tbody></table></td></tr>';
						}
					}
					$this->data['d_result'] .= '</tbody></table>';
				}
				
				// Referencias do composto
				if (isset($all_ref) && ! empty($all_ref))
				{
					$refs = array_unique($all_ref);
					asort($refs);
					$this->data['r_result'] = '<p class="textLeft bodyText">';
					
					foreach ($refs as $ref_id)
					{
						$reference = $this->Reference_model->find_by_id($ref_id);
						
						if ($reference)
						{
							$auth = $this->Author_model->find_by_reference($ref_id);
							
							$authors = '';
							$i = count($auth);
							
							$j = 1;
							foreach ($auth as $row)
							{
								if ($j == $i)
									$authors .= $row->full_name().'. ';
								else
									$authors .= $row->full_name().', ';
								
								++$j;
							}
							
							$this->data['r_result'] .="<strong>({$ref_id}) - </strong>{$authors}{$reference->ref_all}.<br />";
						}
						else
						{
							$this->data['r_result'] .="<strong>({$ref_id}) - </strong>Can't display reference.<br />";
						}
					}
					$this->data['r_result'] .= '</p>';
				}
				
			}
			else
			{
				$this->data['sd_result'] = '<p><strong>NO DATA!</strong></p>';
				$this->data['d_result'] = '<p><strong>NO DATA!</strong></p>';
				$this->data['r_result'] = '<p><strong>NO DATA!</strong></p>';
			}
			
			$this->load->view('content/compound_view', $this->data);
		}
		else
		{
			$this->load->view('content/compound_view', $this->data);
		}
	}
	
	/**
	 * Efectuar o download do ficheiro 'Mol'
	 * 
	 * @return void
	 */
	public function get_mol_file()
	{
		$this->load->helper('download');
		
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			$file_content = $this->input->post('mol_file');
			force_download('mol_file.mol', $file_content);
		}
		else
		{
			force_download('error.txt', 'Error on download');
		}
	}
}

/* End of file compound.php */
/* Location: ./application/controllers/compound.php */