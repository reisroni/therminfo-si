<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * compound.php
 * Compound Service API
 * Criado: 01-08-2012
 * Modificado: 01-08-2012
 * Copyright (c) 2012, ThermInfo
 */
require APPPATH.'/libraries/REST_Controller.php';

class Compound extends REST_Controller
{
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->load->model('Search_model');
		$this->Search_model->setDatabase(HOST, USER, PASS, DB);
    }
	
	/**
	 * Ficha de um composto pelo mid
	 */
	public function id_get()
	{
		if(! $this->get('query'))
        {
			// Query em branco
        	$data = array('status' => 'error', 'message' => 'you have not entered a compound');
			$http_code = 400;
        }
		else
		{
			$search_query = $this->get('query');
			
			// * Efectua a pesquisa
			$mol = $this->Search_model->searchByMID($search_query);
			if (empty($mol))
			{
				// * Sem resultado
				$data = array('query' => $search_query, 
							'status' => 'no results', 
							'message' => 'your query returned zero results');
				$http_code = 404;
			}
			else
			{
				// * Resultado
				$id = $mol->therminfo_id; // ThermInfo ID
				$name = is_null($mol->name) ? '' : $mol->name; // Nome da molecula
				$names = $this->Search_model->searchNames($search_query); // Outros nomes
				$casrn = is_null($mol->casrn) ? '' : $mol->casrn; // CAS RN
				$img = base_url()."image/compound/$search_query"; // Imagem
				$form = is_null($mol->formula) ? '' : $mol->formula; // Formula molecular
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
				$props = $this->Search_model->searchProp($search_query); // Propriedades
				
				// Informacao geral
				$result['compound']['general']['therminfo_id'] = $id;
				if (! empty($name))
					$result['compound']['general']['name'] = $name;
				if (! empty($names))
					$result['compound']['general']['other_names'] = $names;
				if (! empty($casrn))
					$result['compound']['general']['casrn'] = $casrn;
				$result['compound']['general']['image_path'] = $img;
				
				// Dados estruturais
				if (! empty($form))
					$result['compound']['structural']['formula'] = $form;
				if (! empty($mw))
					$result['compound']['structural']['molecular_weight'] = $mw;
				if (! empty($state))
				{
					if ($state == 'l')
						$state = 'liquid';
					else if ($state == 'g')
						$state = 'gas';
					else if ($state == 'cr' or $state == 'c')
						$state = 'crystal';
					else if ($state == 's')
						$state = 'solid';
					
					$result['compound']['structural']['physical_state'] = $state;
				}
				if (! empty($p_form) && empty($state))
					$result['compound']['structural']['physical_form'] = $p_form;
				if (! empty($smiles))
					$result['compound']['structural']['smiles'] = $smiles;
				if (! empty($usmiles))
					$result['compound']['structural']['unique_smiles'] = $usmiles;
				if (! empty($inchi))
					$result['compound']['structural']['inchi'] = $inchi;
				if (! empty($inchikey))
					$result['compound']['structural']['inchi_key'] = $inchikey;
				if (! empty($sinchi))
					$result['compound']['structural']['std_inchi'] = $sinchi;
				if (! empty($sinchikey))
					$result['compound']['structural']['std_inchi_key'] = $sinchikey;
				if (! empty($mol_file))
					$result['compound']['structural']['mol_file'] = $mol_file;
				
				// Propriedades e referencias
				if (! empty($props))
				{
					$props_refs = array(); // Array para formatar tudo
					// propriedades
					foreach ($props as $prop)
					{
						if (! empty($prop['value']))
						{
							// Valores
							$prop_values = $this->Search_model->get_prop_val($search_query, $prop['id']);
							$vals_refs = array(); // Array para formatar os valores e referencias
							foreach ($prop_values as $prop_value)
							{
								if (! empty($prop_value['value']) && $prop_value['advised'] == 'yes')
								{
									// Referencia
									$refs = $this->Search_model->get_ref($prop_value['reference']);
									if (is_array($refs) && ! empty($refs))
									{
										// Autores da referencia
										$auth = $this->Search_model->get_ref_authors($prop_value['reference']);
										$ref = $refs[0];
										$authors = '';
										$i = count($auth);
										$j = 1;
										foreach ($auth as $row)
										{
											if ($j == $i)
												$authors .= $row['name'];
											else
												$authors .= $row['name'].', ';
											++$j;
										}
										$references = "{$authors}. {$ref['ref_all']}.";
									}
									else
									{
										$references = 'can\'t display reference.';
									}
									$prop_error = empty($prop_value['error']) ? 'n.a.' : $prop_value['error'];
									array_push($vals_refs, array('data' => $prop_value['value'], 'uncertainty' => $prop_error, 
												'observations' => $prop_value['obs'], 'reference' => $references));
								}
							}
							array_push($props_refs, array('name' => $prop['data'], 'units' => $prop['units'], 
										'values' => $vals_refs));
						}
					}
					if (! empty($props_refs))
						$result['compound']['properties'] = $props_refs;
				}
				
				// * Formata o resultado
				$data = array('query' => $search_query, 'status' => 'ok', 
						'result' => $result);
				$http_code = 200;
			}
		}
		// * Mostra o resultado
		$this->response($data, $http_code);
	}
}

/* End of file compound.php */
/* Location: ./application/controllers/api/compound.php */