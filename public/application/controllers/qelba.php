<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* qelba.php
* Controlador da pagina 'quick elba'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Qelba extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
							'info' => NULL);
		// Carregar os modelos
		$this->load->model('molecule/Molecule_model');
        $this->load->model('property/Data_value_model');
        // Carregar os modulos
		$this->load->library('OBabel');
		$this->load->library('Util');
		$this->load->library('Cactus');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			$predict_type = $this->input->post('qelba_type_select'); // Tipo de previsao
			$term = $this->input->post('qelba_term_input'); // Termo para previsao
			$ebonds = $this->input->post('ebonds_select'); // Ligacoes duplas (trans)
			$term = trim($term);
			
			// ** Previsao de acordo com o tipo selecionado
			switch ($predict_type)
			{
				// ********************************
				// Previsao pelo nome
				// ********************************
				case 'name':
				{
					// * Efectua e formata a previsao
					$this->data['result'] = $this->_qelba_name($term, $ebonds);
				} break;
				
				// ********************************
				// Previsao pelo SMILES
				// ********************************
				case 'smiles':
				{
					// * Efectua e formata a previsao
					$this->data['result'] = $this->_qelba_smiles($term, $ebonds);
				} break;
			}
            $this->load->view('content/qelba_view', $this->data);
		}
		else
		{
			// Formulario nao submetido (pagina 'Quick ELBA')
			$this->load->view('content/qelba_view', $this->data);
		}
	}
	
	/**
	 * Pagina do resultado da previsao
	 * usando a base de dados
	 */
	public function pred_db()
	{
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			$mid = $this->input->post('qelba_mid'); // ID do composto
			$r_smiles = $this->input->post('qelba_smiles'); // SMILES
			$bonds = $this->input->post('qelba_bonds'); // Ligacoes duplas
			$name = 'n.a.'; // Nome
			$compound = $this->Molecule_model->find_by_id($mid); // Composto da BD
			$img = 'public/media/images/chemstruct.jpg'; // Imagem do composto por defeito
			$therminfo_id = '';
			
			if ($compound)
			{
				$smi_tmp = $compound->smiles ? explode(',', $compound->smiles) : '';
				$smi = is_array($smi_tmp) ? $smi_tmp[0] : $smi_tmp;
				$uni_smi_tmp = $compound->usmiles ? explode(',', $compound->usmiles) : '';
				$uni_smiles = is_array($uni_smi_tmp) ? $uni_smi_tmp[0] : $uni_smi_tmp;
				$name = $compound->name ? $compound->name : 'n.a.';
				$smiles = $compound->smiles ? $compound->smiles : 'n.a.';
				$img = $compound->get_image() ? 'public/media/images/molecules/'. $compound->get_image() : $img;
                $therminfo_id = $compound->therminfo_id;
				
				// Se for um composto com esterioisomeria - @@ usamos o unique SMILES, caso contrario utilizamos o SMILES
				if (preg_match('/@/', $uni_smiles)) {
					$smiles_bd = $uni_smiles;
				} else {
					$smiles_bd = $smi;
                }
			}
			else
			{
				$smiles_bd = $r_smiles;
				$smiles = $r_smiles;
				$name = '';
				$names = $this->cactus->get_names($r_smiles, 'smiles'); // Nomes (CACTUS)
				if ($names)
                {
					foreach ($names as $n) {
						$name .= "'{$n}' ";
                    }
				}
			}
			
			// Calcula as propriedades
			$props = $this->obabel->calc_props($smiles_bd, $bonds);
			
			if ($props)
			{
				// Informacao do composto
				$this->data['result'] = '<table class="qelba-resultTable-1 center"><tbody>';
				$this->data['result'] .= '<tr><th>Compound Name(s):</th><td>';
				$this->data['result'] .= " <span class='orangeText'>'{$name}'</span></td></tr>";
				$this->data['result'] .= "<tr><th>SMILES:</th><td><span class='orangeText'>{$smiles}</span></td></tr>";
				$this->data['result'] .= '<tr><th>More Info:</th><td><form action="'. base_url("compound/view/{$therminfo_id}"). '" method="post" target="_blank">
				<input type="submit" class="btTxt clickable" value="View" title="View more" /></form></td></tr>';
				$this->data['result'] .= '</tbody></table>';
				$this->data['result'] .= "<p><img id='compoundImg' alt='Chemical Structure Image' src='{$img}' title='Compound Image' /></p>";
				
				if (count($props) > 1)
				{
					// Propriedades da BD					
					if (isset($mid) && $mid)
					{
						$liq_exp = 'n.a.';
						$gas_exp = 'n.a.';
						$vap_exp = 'n.a.';
						$query_result = $this->Data_value_model->find_ent_values($mid); // Valores de entalpias
						
						if (is_array($query_result) && ! empty($query_result))
						{
							foreach($query_result as $r)
							{
								if ($r->data == 7 && ! is_null($r->value)) {
									$liq_exp = number_format($r->value, 1, '.', '');
                                }
								if ($r->data == 8 && ! is_null($r->value)) {
									$gas_exp = number_format($r->value, 1, '.', '');
                                }
								if ($r->data == 19 && ! is_null($r->value)) {
									$vap_exp = number_format($r->value, 1, '.', '');
                                }
							}
						}
						
						$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
						$this->data['result'] .= "<tr><td colspan='3'><strong><span class='underlineText'>Experimental</span> Standard Molar Enthalpy of Formation/Phase Change at 298.15 K 
						for <span class='orangeText'>'{$smiles}'</span> [kJ/mol]:</strong></td></tr>";
						$this->data['result'] .=  '<tr><td><strong>Gas-phase</strong></td><td><strong>Liquid-phase</strong></td><td><strong>Vaporization<br />(Liquid-Gas)</strong></td></tr>';
						$this->data['result'] .=  "<tr><td>{$gas_exp}</td><td>{$liq_exp}</td><td>{$vap_exp}</td></tr>";
						$this->data['result'] .= '</tbody></table>';
					}
					else
					{
						$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
						$this->data['result'] .= "<tr><td colspan='3'><strong><span class='underlineText'>Experimental</span> Standard Molar Enthalpy of Formation/Phase Change at 298.15 K 
						for <span class='orangeText'>'{$smiles}'</span> [kJ/mol]:</strong></td></tr>";
						$this->data['result'] .=  '<tr><td><strong>The compound you entered doesn\'t exist in our database. You can insert it <a href="insert" title="Insert new compound">
						here</a>.</strong></td></tr>';
					}
					
					// Propriedades calculadas
					$gas_ent = strlen($props['gas']) == 0 ? 'n.a.' : number_format($props['gas'], 1, '.', '');
					$liq_ent = strlen($props['liq']) == 0 ? 'n.a.' : number_format($props['liq'], 1, '.', '');
					$vap_ent = strlen($props['vap']) == 0 ? 'n.a.' : number_format($props['vap'], 1, '.', '');
					$gas_nop = $props['gas_nop'] != 0 ? '&#8224;' : '';
					$liq_nop = $props['liq_nop'] != 0 ? '&#8225;' : '';
					$vap_nop = $props['vap_nop'] != 0 ? '&#8226;' : '';
					$gas_pzero = $props['gas_pzero'];
					$liq_pzero = $props['liq_pzero'];
					$vap_pzero = $props['vap_pzero'];
			
					$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
					$this->data['result'] .= "<tr><td colspan='3'><strong><span class='underlineText'>Estimated</span> Standard Molar Enthalpy of Formation/Phase Change at 298.15 K using 
					<span class='orangeText'>ELBA</span> for <span class='orangeText'>'{$smiles}'</span> [kJ/mol]:</strong></td></tr>";
					$this->data['result'] .=  '<tr><td><strong>Gas-phase</strong></td><td><strong>Liquid-phase</strong></td><td><strong>Vaporization<br />(Liquid-Gas)</strong></td></tr>';
					$this->data['result'] .=  "<tr><td>{$gas_ent} <span class='orangeText'>{$gas_nop}</span></td><td>{$liq_ent} <span class='orangeText'>{$liq_nop}</span></td><td>{$vap_ent} 
					<span class='orangeText'>{$vap_nop}</span></td></tr>";
					$this->data['result'] .= '</tbody></table>';
					
					if (! empty($gas_nop))
					{
						$nop_text = "<p><span class='orangeText'>{$gas_nop}</span> - Due to the lack of experimental data to calculate the value of the following parameter";
						if ($props['gas_nop'] > 1) {
							$nop_text .= 's';
                        }
						$nop_text .= ':';
						
						$gas_pzero = str_replace('[', '', $gas_pzero);
						$gas_pzero = str_replace(']', '', $gas_pzero);
						$gas_pzero = str_replace("1'\"", '1*', $gas_pzero);
						$gas_pzero = str_replace("1'1", '1*1', $gas_pzero);
						$gas_pzero = str_replace("1'4", '1*4', $gas_pzero);
						$gas_pzero = str_replace("4'\"", '4*', $gas_pzero);
						$gas_pzero = str_replace("4'1", '4*1', $gas_pzero);
						$gas_pzero = str_replace("4'4", '4*4', $gas_pzero);
						$gas_pzero = str_replace("'", '', $gas_pzero);
						$gas_pzero = str_replace('*', "'", $gas_pzero);
						$gas_pzero = str_replace('"', '', $gas_pzero);
						
						$nop_text .= " <strong>{$gas_pzero}</strong>, ";
						if ($props['gas_nop'] > 1) {
							$nop_text .= 'they were';
						} else {
							$nop_text .= 'it was';
                        }
						$nop_text .= ' not used to estimate this value.</p>';
						
						$this->data['result'] .= $nop_text;
					}
                    
					if (! empty($liq_nop))
					{
						$nop_text = "<p><span class='orangeText'>{$liq_nop}</span> - Due to the lack of experimental data to calculate the value of the following parameter";
						if ($props['liq_nop'] > 1) {
							$nop_text .= 's';
                        }
						$nop_text .= ':';
						
						$liq_pzero = str_replace('[', '', $liq_pzero);
						$liq_pzero = str_replace(']', '', $liq_pzero);
						$liq_pzero = str_replace("1'\"", '1*', $liq_pzero);
						$liq_pzero = str_replace("1'1", '1*1', $liq_pzero);
						$liq_pzero = str_replace("1'4", '1*4', $liq_pzero);
						$liq_pzero = str_replace("4'\"", '4*', $liq_pzero);
						$liq_pzero = str_replace("4'1", '4*1', $liq_pzero);
						$liq_pzero = str_replace("4'4", '4*4', $liq_pzero);
						$liq_pzero = str_replace("'", '', $liq_pzero);
						$liq_pzero = str_replace('*', "'", $liq_pzero);
						$liq_pzero = str_replace('"', '', $liq_pzero);
						
						$nop_text .= " <strong>{$liq_pzero}</strong>, ";
						if ($props['liq_nop'] > 1) {
							$nop_text .= 'they were';
						} else {
							$nop_text .= 'it was';
                        }
						$nop_text .= ' not used to estimate this value.</p>';
						
						$this->data['result'] .= $nop_text;
					}
                    
					if (! empty($vap_nop))
					{
						$nop_text = "<p><span class='orangeText'>{$vap_nop}</span> - Due to the lack of experimental data to calculate the value of the following parameter";
						if ($props['vap_nop'] > 1) {
							$nop_text .= 's';
                        }
						$nop_text .= ':';
						
						$vap_pzero = str_replace('[', '', $vap_pzero);
						$vap_pzero = str_replace(']', '', $vap_pzero);
						$vap_pzero = str_replace("1'\"", '1*', $vap_pzero);
						$vap_pzero = str_replace("1'1", '1*1', $vap_pzero);
						$vap_pzero = str_replace("1'4", '1*4', $vap_pzero);
						$vap_pzero = str_replace("4'\"", '4*', $vap_pzero);
						$vap_pzero = str_replace("4'1", '4*', $vap_pzero);
						$vap_pzero = str_replace("4'4", '4*4', $vap_pzero);
						$vap_pzero = str_replace("'", '', $vap_pzero);
						$vap_pzero = str_replace('*', "'", $vap_pzero);
						$vap_pzero = str_replace('"', '', $vap_pzero);
						
						$nop_text .= " <strong>{$vap_pzero}</strong>, ";
						if ($props['vap_nop'] > 1) {
							$nop_text .= 'they were';
						} else {
							$nop_text .= 'it was';
                        }
						$nop_text .= ' not used to estimate this value.</p>';
						
						$this->data['result'] .= $nop_text;
					}
					
					// Parametros usados
					$props['params'] = str_replace("1'\"", '1*', $props['params']);
					$props['params'] = str_replace("1'1", '1*1', $props['params']);
					$props['params'] = str_replace("1'4", '1*4', $props['params']);
					$props['params'] = str_replace("4'\"", '4*', $props['params']);
					$props['params'] = str_replace("4'1", '4*1', $props['params']);
					$props['params'] = str_replace("4'4", '4*4', $props['params']);
					$props['params'] = str_replace("'", '', $props['params']);
					$props['params'] = str_replace('*', "'", $props['params']);
					$props['params'] = str_replace('{', '', $props['params']);
					$props['params'] = str_replace('}', '', $props['params']);
					$props['params'] = str_replace('"', '', $props['params']);
					
					$params = explode(', ', $props['params']);
					$p_descrip = $this->util->read_params();
					$is_trans = 0;
					
					foreach ($params as $param)
					{
						$x = explode(':', $param);
						$p = $x[0];
						if ($p == 'Z8uE' or $p == 'Z8uEE' or $p == 'Z8uZE' or $p == 'Z12u159E' or $p == 'Z11' or $p == 'Z14') {
							$is_trans = 1;
                        }
					}
					
					if ($bonds != 0 && $is_trans == 1) {
						$this->data['result'] .= "<p><strong><span class='orangeText'>*</span></strong> <span class='underlineText'>Remark</span>: 
						The properties were calculated considering that the compound has {$bonds} double bond(s) in the <strong><span class='orangeText'>(E)</span></strong> conformation.</p>";
					}
					
					$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
					$this->data['result'] .= "<tr><td colspan='3'><strong>Set of ELBA parameters used to predict properties of <span class='orangeText'>'{$smiles}'</span>:</strong></td></tr>";
					$this->data['result'] .=  '<tr><td><strong>ELBA parameter</strong></td><td><strong>Used Frequency</strong></td><td><strong>Short Description<br />
					<a href="public/media/docs/rsantos_et_al_2010.pdf" title="ELBA Method, R. Santos et al" target="_blank">[More Info (PDF/137 KB)]</a></strong></td></tr>';
					foreach ($params as $param)
					{
						list($parameter, $freq) = explode(':', $param);
						$des = $p_descrip[$parameter];
						
						$this->data['result'] .=  "<tr><td><strong><span class='orangeText'>{$parameter}</span></strong></td><td>{$freq}</td><td class='tjustify'>{$des}</td></tr>";
					}
					$this->data['result'] .= '</tbody></table>';
				}
				else if ($props['text'] == 'Error1')
				{
					$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
					$this->data['result'] .= "<tr><td colspan='3'><strong><span class='underlineText'>Estimated</span> Standard Molar Enthalpy of Formation/Phase Change at 298.15 K using 
					<span class='orangeText'>ELBA</span> for <span class='orangeText'>{$smiles}</span> [kJ/mol]:</strong></td></tr>";
					$this->data['result'] .=  '<tr><td><strong>Gas-phase</strong></td><td><strong>Liquid-phase</strong></td><td><strong>Vaporization<br />(Liquid-Gas)</strong></td></tr>';
					$this->data['result'] .=  '<tr><td colspan="3">The method is only available for predicting properties of hydrocarbons!</td></tr>';
					$this->data['result'] .= '</tbody></table>';
				}
			}
			else
			{
				// Sem resultado
				$this->data['result'] = '<p class="errorPane"><strong>Without prediction of property values</strong></p>';
			}
			
			// Mostra o resultado
			$this->load->view('content/elba_result_view', $this->data);
		}
		else
		{
			// Redirecionar para a pagina 'Quick ELBA'
			redirect('qelba');
		}
	}
	
	/**
	 * Pagina do resultado da previsao
	 * sem a base de dados
	 */
	public function pred_nodb()
	{
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			$smiles = $this->input->post('qelba_smiles'); // SMILES
			$bonds = $this->input->post('qelba_bonds'); // Ligacoes duplas
			$smile_img = $this->util->replace_char($smiles, 3); // SMILES para imagem
			// Calcula as propriedades
			$props = $this->obabel->calc_props($smiles, $bonds);
			
			if ($props)
			{
				// Informacao do composto (CACTUS)
				$this->data['result'] = '<table class="qelba-resultTable-1 center"><tbody>';
				// Verifica se o CACTUS retorna algum resultado (imagem)
				$cactus = $this->util->get_url_contents("http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image");
				
				if ($cactus)
				{
					$names = $this->cactus->get_names($smiles, 'smiles'); // Nomes (CACTUS)
					if ($names)
					{
						$this->data['result'] .= '<tr><th>Compound Name(s):</th><td>';
						foreach ($names as $name) {
							$this->data['result'] .= " <span class='orangeText'>'{$name}'</span>";
						}
						$this->data['result'] .= '</td></tr>';
					}
				}
                
				$this->data['result'] .= '<tr><th>SMILES:</th><td><span class="orangeText">'. $smiles .'</span></td></tr>';
				$this->data['result'] .= '</tbody></table>';
				
				if ($cactus)
				{
					$this->data['result'] .= "<p><img id='cactusImg' class='ImgBorder_2' alt='Chemical Structure Image (By CACTUS)' 
					src='http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image' width='250' height='250' title='Compound Image (By CACTUS)' /></p>";
					$this->data['result'] .= '<p>* Compound Name(s) and Structure provided by the <a href="http://cactus.nci.nih.gov/chemical/structure"
					target="_blank" title="CACTUS Link"><strong>Chemical Identifier Resolver</strong></a>.</p>';
				}
				
				if (count($props) > 1)
				{
					// Propriedades calculadas
					$gas_ent = strlen($props['gas']) == 0 ? 'n.a.' : number_format($props['gas'], 1, '.', '');
					$liq_ent = strlen($props['liq']) == 0 ? 'n.a.' : number_format($props['liq'], 1, '.', '');
					$vap_ent = strlen($props['vap']) == 0 ? 'n.a.' : number_format($props['vap'], 1, '.', '');
					$gas_nop = $props['gas_nop'] != 0 ? '&#8224;' : '';
					$liq_nop = $props['liq_nop'] != 0 ? '&#8225;' : '';
					$vap_nop = $props['vap_nop'] != 0 ? '&#8226;' : '';
					$gas_pzero = $props['gas_pzero'];
					$liq_pzero = $props['liq_pzero'];
					$vap_pzero = $props['vap_pzero'];
			
					$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
					$this->data['result'] .= "<tr><td colspan='3'><strong><span class='underlineText'>Estimated</span> Standard Molar Enthalpy of Formation/Phase Change at 298.15 K using 
					<span class='orangeText'>ELBA</span> for <span class='orangeText'>'{$smiles}'</span> [kJ/mol]:</strong></td></tr>";
					$this->data['result'] .=  '<tr><td><strong>Gas-phase</strong></td><td><strong>Liquid-phase</strong></td><td><strong>Vaporization<br />(Liquid-Gas)</strong></td></tr>';
					$this->data['result'] .=  "<tr><td>{$gas_ent} <span class='orangeText'>{$gas_nop}</span></td><td>{$liq_ent} <span class='orangeText'>{$liq_nop}</span></td><td>{$vap_ent} 
					<span class='orangeText'>{$vap_nop}</span></td></tr>";
					$this->data['result'] .= '</tbody></table>';
					
					if (! empty($gas_nop))
					{
						$nop_text = "<p><span class='orangeText'>{$gas_nop}</span> - Due to the lack of experimental data to calculate the value of the following parameter";
						if ($props['gas_nop'] > 1) {
							$nop_text .= 's';
                        }
						$nop_text .= ':';
						
						$gas_pzero = str_replace('[', '', $gas_pzero);
						$gas_pzero = str_replace(']', '', $gas_pzero);
						$gas_pzero = str_replace("1'\"", '1*', $gas_pzero);
						$gas_pzero = str_replace("1'1", '1*1', $gas_pzero);
						$gas_pzero = str_replace("1'4", '1*4', $gas_pzero);
						$gas_pzero = str_replace("4'\"", '4*', $gas_pzero);
						$gas_pzero = str_replace("4'1", '4*1', $gas_pzero);
						$gas_pzero = str_replace("4'4", '4*4', $gas_pzero);
						$gas_pzero = str_replace("'", '', $gas_pzero);
						$gas_pzero = str_replace('*', "'", $gas_pzero);
						$gas_pzero = str_replace('"', '', $gas_pzero);
						
						$nop_text .= " <strong>{$gas_pzero}</strong>, ";
						if ($props['gas_nop'] > 1) {
							$nop_text .= 'they were';
						} else {
							$nop_text .= 'it was';
                        }
						$nop_text .= ' not used to estimate this value.</p>';
						
						$this->data['result'] .= $nop_text;
					}
                    
					if (! empty($liq_nop))
					{
						$nop_text = "<p><span class='orangeText'>{$liq_nop}</span> - Due to the lack of experimental data to calculate the value of the following parameter";
						if ($props['liq_nop'] > 1) {
							$nop_text .= 's';
                        }
						$nop_text .= ':';
						
						$liq_pzero = str_replace('[', '', $liq_pzero);
						$liq_pzero = str_replace(']', '', $liq_pzero);
						$liq_pzero = str_replace("1'\"", '1*', $liq_pzero);
						$liq_pzero = str_replace("1'1", '1*1', $liq_pzero);
						$liq_pzero = str_replace("1'4", '1*4', $liq_pzero);
						$liq_pzero = str_replace("4'\"", '4*', $liq_pzero);
						$liq_pzero = str_replace("4'1", '4*1', $liq_pzero);
						$liq_pzero = str_replace("4'4", '4*4', $liq_pzero);
						$liq_pzero = str_replace("'", '', $liq_pzero);
						$liq_pzero = str_replace('*', "'", $liq_pzero);
						$liq_pzero = str_replace('"', '', $liq_pzero);
						
						$nop_text .= " <strong>{$liq_pzero}</strong>, ";
						if ($props['liq_nop'] > 1) {
							$nop_text .= 'they were';
						} else {
							$nop_text .= 'it was';
                        }
						$nop_text .= ' not used to estimate this value.</p>';
						
						$this->data['result'] .= $nop_text;
					}
                    
					if (! empty($vap_nop))
					{
						$nop_text = "<p><span class='orangeText'>{$vap_nop}</span> - Due to the lack of experimental data to calculate the value of the following parameter";
						if ($props['vap_nop'] > 1) {
							$nop_text .= 's';
                        }
						$nop_text .= ':';
						
						$vap_pzero = str_replace('[', '', $vap_pzero);
						$vap_pzero = str_replace(']', '', $vap_pzero);
						$vap_pzero = str_replace("1'\"", '1*', $vap_pzero);
						$vap_pzero = str_replace("1'1", '1*1', $vap_pzero);
						$vap_pzero = str_replace("1'4", '1*4', $vap_pzero);
						$vap_pzero = str_replace("4'\"", '4*', $vap_pzero);
						$vap_pzero = str_replace("4'1", '4*', $vap_pzero);
						$vap_pzero = str_replace("4'4", '4*4', $vap_pzero);
						$vap_pzero = str_replace("'", '', $vap_pzero);
						$vap_pzero = str_replace('*', "'", $vap_pzero);
						$vap_pzero = str_replace('"', '', $vap_pzero);
						
						$nop_text .= " <strong>{$vap_pzero}</strong>, ";
						if ($props['vap_nop'] > 1) {
							$nop_text .= 'they were';
						} else {
							$nop_text .= 'it was';
                        }
						$nop_text .= ' not used to estimate this value.</p>';
						
						$this->data['result'] .= $nop_text;
					}
					
					// Parametros usados
					$props['params'] = str_replace("1'\"", '1*', $props['params']);
					$props['params'] = str_replace("1'1", '1*1', $props['params']);
					$props['params'] = str_replace("1'4", '1*4', $props['params']);
					$props['params'] = str_replace("4'\"", '4*', $props['params']);
					$props['params'] = str_replace("4'1", '4*1', $props['params']);
					$props['params'] = str_replace("4'4", '4*4', $props['params']);
					$props['params'] = str_replace("'", '', $props['params']);
					$props['params'] = str_replace('*', "'", $props['params']);
					$props['params'] = str_replace('{', '', $props['params']);
					$props['params'] = str_replace('}', '', $props['params']);
					$props['params'] = str_replace('"', '', $props['params']);
					
					$params = explode(', ', $props['params']);
					$p_descrip = $this->util->read_params();
					$is_trans = 0;
					
					foreach ($params as $param)
					{
						$x = explode(':', $param);
						$p = $x[0];
						if ($p == 'Z8uE' or $p == 'Z8uEE' or $p == 'Z8uZE' or $p == 'Z12u159E' or $p == 'Z11' or $p == 'Z14') {
							$is_trans = 1;
                        }
					}
					
					if ($bonds != 0 && $is_trans == 1)
					{
						$this->data['result'] .= "<p><strong><span class='orangeText'>*</span></strong> <span class='underlineText'>Remark</span>: 
						The properties were calculated considering that the compound has {$bonds} double bond(s) in the <strong><span class='orangeText'>(E)</span></strong> conformation.</p>";
					}
					
					$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
					$this->data['result'] .= "<tr><td colspan='3'><strong>Set of ELBA parameters used to predict properties of <span class='orangeText'>'{$smiles}'</span>:</strong></td></tr>";
					$this->data['result'] .=  '<tr><td><strong>ELBA parameter</strong></td><td><strong>Used Frequency</strong></td><td><strong>Short Description<br />
					<a href="public/media/docs/rsantos_et_al_2010.pdf" title="ELBA Method, R. Santos et al" target="_blank">[More Info (PDF/137 KB)]</a></strong></td></tr>';
					foreach ($params as $param)
					{
						list($parameter, $freq) = explode(':', $param);
						$des = $p_descrip[$parameter];
						
						$this->data['result'] .=  "<tr><td><strong><span class='orangeText'>{$parameter}</span></strong></td><td>{$freq}</td><td class='tjustify'>{$des}</td></tr>";
					}
					$this->data['result'] .= '</tbody></table>';
				}
				else if ($props['text'] == 'Error1')
				{
					$this->data['result'] .= '<table class="qelba-resultTable-2 center"><tbody>';
					$this->data['result'] .= "<tr><td colspan='3'><strong><span class='underlineText'>Estimated</span> Standard Molar Enthalpy of Formation/Phase Change at 298.15 K using 
					<span class='orangeText'>ELBA</span> for <span class='orangeText'>{$smiles}</span> [kJ/mol]:</strong></td></tr>";
					$this->data['result'] .=  '<tr><td><strong>Gas-phase</strong></td><td><strong>Liquid-phase</strong></td><td><strong>Vaporization<br />(Liquid-Gas)</strong></td></tr>';
					$this->data['result'] .=  '<tr><td colspan="3">The method is only available for predicting properties of hydrocarbons!</td></tr>';
					$this->data['result'] .= '</tbody></table>';
				}
			}
			else
			{
				// Sem resultado
				$this->data['result'] = '<p class="errorPane"><strong>Without prediction of property values</strong></p>';
			}
			
			// Mostra o resultado
			$this->load->view('content/elba_result_view', $this->data);
		}
		else
		{
			// Redireciona para a pagina 'Quick ELBA'
			redirect('qelba');
		}
	}
	
	/*
	 * Previsao rapida pelo nome do composto
	 * 
	 * @param string $name Nome do composto
	 * @param int $bonds Ligacoes duplas
	 * 
	 * @return Resultado da pesquisa
	 */
	private function _qelba_name($name, $bonds)
	{
        $html_result;
		// Verifica se o nome esta vazio
		if (empty($name))
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered molecule details. Please try again!</strong></p>';
		}
		else
		{
			$get_smiles = $this->cactus->get_smiles($name, 'name'); // SMILES (CACTUS)
			
			if (! $get_smiles)
			{
				// SMILES nao encontrado
				$html_result = '<p class="errorPane"><strong>We couldn\'t find the molecule. Please go back and try again!</strong></p>';
			}
			else
			{
				// Pesquisa na DB
				$db_result = $this->Molecule_model->find_by_smiles_pred($get_smiles, 1);
				
				if ($db_result === 0)
				{
					// Erro na BD
					$error_code = 'TI_501'; // QElba: 5, qelba_name: 0, DB Error: 1
					$error_date = date('Y-m-d');
					log_message('error', "[QElba] {$error_date} - [{$error_code}]: An error occurred in the database.");
					$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
					'. If persists send us an email</strong></p>';
				}
				else if ($db_result === 1)
				{
					// Campo em branco
					$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
				}
				else if ($db_result === 2)
				{
					// SMILES invalido
					$html_result = '<p class="errorPane"><strong>The molecule you entered is not valid. Please go back and try again!</strong></p>';
				}
				else
				{
					$smile_img = $this->util->replace_char($get_smiles, 3); // SMILES para imagem
					$mw = $this->obabel->calc_MW($get_smiles, 2); // Peso molecular
					// Informacao do composto (CACTUS)
					$this->data['info'] = '<h2>You are predicting properties for:</h2>';
					$this->data['info'] .= '<p><strong>Name:</strong> <span class="orangeText">'. html_escape($name) .'</span><br />';
					$this->data['info'] .= '<strong>SMILES:</strong> <span class="orangeText">'. html_escape($get_smiles) .'</span><br />';
					$this->data['info'] .= '<strong>Molecular Weight:</strong> <span class="orangeText">'. html_escape($mw) .'</span></p>';
					// Verifica se o CACTUS retorna algum resultado (imagem)
					$cactus = $this->util->get_url_contents("http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image");
					
					if ($cactus) {
						$this->data['info'] .= "<p><img id='cactusImg' class='ImgBorder_2' alt='Chemical Structure Image (By CACTUS)' 
						src='http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image' width='200' height='200' title='{$name} Image (By CACTUS)' /></p>";
					}
                    
					if ($bonds != 0) {
						$this->data['info'] .= '<p>- Cyclic compound having '. $bonds .' double bonds in the <span class="orangeText">(E)</span> conformation.</p>';
                    }
                    
					if ($cactus) {
						$this->data['info'] .= '<p>* Compound SMILES and Structure provided by the <a href="http://cactus.nci.nih.gov/chemical/structure"
						target="_blank" title="CACTUS Link"><strong>Chemical Identifier Resolver</strong></a>.</p>';
					}
					
					// Botao para previsao
					$this->data['info'] .= "<p><form action='". base_url('qelba/pred_nodb') ."' method='post' target='_blank'>
					<input type='hidden' name='qelba_smiles' value='{$get_smiles}'><input type='hidden' name='qelba_bonds' value='{$bonds}'>
					<input type='submit' name='submit' class='btTxt clickable' value='Just Predict Properties' title='Predict Properties'></form></p>";
					
					if (is_array($db_result))
					{
						$linhas = count($db_result);
						if ($linhas == 0)
						{
							// Sem resultado da BD
							$html_result = '<p><strong>.::. Number of compounds found on the database: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
						}
						else
						{
							// Formata o resultado
							$html_result = '<p><strong>.::. Number of compounds found on the database: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
							$html_result .= '<p>- Experimental and Estimated Properties for similar compounds found on the database:</p>';
							$html_result .= $this->_html_result($db_result, $bonds, $get_smiles);
						}
					}
					else if ($db_result === 3)
					{
						// Nao existe SMILES similares
						$html_result = '<p><strong>No compounds in the database with similar SMILES: 
						<span class="errorText">'. html_escape($get_smiles) .'</span></strong></p>';
					}
					else
					{
						// Outra situacao
						$error_code = 'TI_502'; // QElba: 5, qelba_name: 0, Other Error: 2
                        $error_date = date('Y-m-d');
                        log_message('error', "[QElba] {$error_date} - [{$error_code}]: An error occurred.");
                        $html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
                        If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
					}
				}
			}
		}
        return $html_result;
	}
	
	/*
	 * Previsao rapida pelo SMILES do composto
	 * 
	 * @param string $smiles SMILES do composto
	 * @param int $bonds Ligacoes duplas
	 * 
	 * @return Resultado da pesquisa
	 */
	private function _qelba_smiles($smiles, $bonds)
	{
        $html_result;
		// Verifica se o SMILES esta vazio
		if (empty($smiles))
		{
			// Campo em branco
			$html_result = '<p class="errorPane"><strong>You have not entered molecule details. Please try again!</strong></p>';
		}
		else
		{
			// Pesquisa na DB
			$db_result = $this->Molecule_model->find_by_smiles_pred($smiles, 1);
			
			if ($db_result === 0)
			{
				// Erro na BD
				$error_code = 'TI_511'; // QElba: 5, qelba_smiles: 1, DB Error: 1
				$error_date = date('Y-m-d');
				log_message('error', "[QElba] {$error_date} - [{$error_code}]: An error occurred in the database.");
				$html_result = '<p class="errorPane"><strong>An error occurred: '. $error_date .', '. $error_code .
				'. If persists send us an email</strong></p>';
			}
			else if ($db_result === 1)
			{
				// Campo em branco
				$html_result = '<p class="errorPane"><strong>You have not entered search details. Please try again!</strong></p>';
			}
			else if ($db_result === 2)
			{
				// SMILES invalido
				$html_result = '<p class="errorPane"><strong>The molecule you entered is not valid. Please go back and try again!</strong></p>';
			}
			else
			{
				$smile_img = $this->util->replace_char($smiles, 3); // SMILES para imagem
				$get_name = $this->cactus->get_name($smiles, 'smiles'); // Nome (CACTUS)
				$mw = $this->obabel->calc_MW($smiles, 2); // Peso molecular
				// Informacao do composto (CACTUS)
				$this->data['info'] = '<h2>You are predicting properties for:</h2>';
				
				if ($get_name) {
					$this->data['info'] .= '<p><strong>Name:</strong> <span class="orangeText">'. html_escape($get_name) .'</span><br />';
				}
				
				$this->data['info'] .= '<strong>SMILES:</strong> <span class="orangeText">'. html_escape($smiles) .'</span><br />';
				$this->data['info'] .= '<strong>Molecular Weight:</strong> <span class="orangeText">'. html_escape($mw) .'</span></p>';
				
				if ($get_name) {
					$img_title = $get_name .' Image (By CACTUS)';
				} else {
					$img_title = 'Compound Image (By CACTUS)';
                }
                
				// Verifica se o CACTUS retorna algum resultado (imagem)
				$cactus = $this->util->get_url_contents("http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image");
				
				if ($cactus) {
					$this->data['info'] .= "<p><img id='cactusImg' class='ImgBorder_2' alt='Chemical Structure Image (By CACTUS)' 
					src='http://cactus.nci.nih.gov/chemical/structure/{$smile_img}/image' width='200' height='200' title='{$img_title}' /></p>";
				}
                
				if ($bonds != 0) {
					$this->data['info'] .= '<p>- Cyclic compound having '. $bonds .' double bonds in the <span class="orangeText">(E)</span> conformation.</p>';
				}
                
                if ($cactus or $get_name) {
					$this->data['info'] .= '<p>* Compound SMILES and Structure provided by the <a href="http://cactus.nci.nih.gov/chemical/structure"
					target="_blank" title="CACTUS Link"><strong>Chemical Identifier Resolver</strong></a>.</p>';
				}
				
				// Botao para previsao
				$this->data['info'] .= "<p><form action='". base_url('qelba/pred_nodb') ."' method='post' target='_blank'>
				<input type='hidden' name='qelba_smiles' value='{$smiles}'><input type='hidden' name='qelba_bonds' value='{$bonds}'>
				<input type='submit' name='submit' class='btTxt clickable' value='Just Predict Properties' title='Predict Properties'></form></p>";
				
				if (is_array($db_result))
				{
					$linhas = count($db_result);
					if ($linhas == 0)
					{
						// Sem resultado da BD
						$html_result = '<p><strong>.::. Number of compounds found on the database: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
					}
					else
					{
						// Formata o resultado
						$html_result = '<p><strong>.::. Number of compounds found on the database: <span class="orangeText">'. $linhas .'</span>  .::.</strong></p>';
						$html_result .= '<p>- Experimental and Estimated Properties for similar compounds found on the database:</p>';
						$html_result .= $this->_html_result($db_result, $bonds, $smiles);
					}
				}
				else if ($db_result === 3)
				{
					// Nao existe SMILES similares
					$html_result = '<p><strong>No compounds in the database with similar SMILES: 
					<span class="errorText">'. html_escape($smiles) .'</span></strong></p>';
				}
				else
				{
					// Outra situacao
					$error_code = 'TI_512'; // QElba: 5, qelba_smiles: 1, Other Error: 2
                    $error_date = date('Y-m-d');
                    log_message('error', "[QElba] {$error_date} - [{$error_code}]: An error occurred.");
                    $html_result = '<p class="errorPane"><strong>It is currently not possible to fulfill the task. 
                    If persists send us an email. ('. $error_date .', '. $error_code .')</strong></p>';
				}
			}
		}
		
        return $html_result;
	}
	
	/*
	 * Formatar o resultado da previsao em HTML
	 * 
	 * @param array $data Resultado da pesquisa
	 * @param int $bonds Ligacoes duplas
	 * @param string $smiles SMILES da pesquisa
	 * 
	 * @return string Resultado em HTML
	 */ 
	private function _html_result($data, $bonds, $smiles) 
	{
		// * Mostrar os campos do resultado
		$limit = count($data);
		$html = '<table id="resultTable"><tbody>';
		for ($i = 0; $i < $limit; ++$i)
		{
			$mid = $data[$i]->mid;
			$name = $data[$i]->name;
			$num = $i + 1;
			
			// Numero da linha
			$html .= '<tr class="alt"><td class="index" colspan="3">'. $num .'.</td></tr>';
			
			// ThermInfo ID
			$html .= '<tr><th>ThermInfo ID:</th>';
			$html .= '<td>'. $data[$i]->therminfo_id .'</td>';
			
			// Imagem
			$html .= "<td rowspan='7' class='img'><img class='ImgBorder_1' alt='Chemical 
			Structure Image' title='{$data[$i]->therminfo_id} Image' src='image/compound/{$data[$i]->therminfo_id}' /></td></tr>";
			
			// Name
			$html .= '<tr><th>Name:</th>';
			$html .= '<td>'. $name .'</td></tr>';
			
			// Formula
			$tmp = str_split(stripslashes($data[$i]->formula));
			$formula = '';
			foreach ($tmp as $chars)
			{
				if (is_numeric($chars))
				{
					$chars = '<sub>'. $chars .'</sub>';
					$formula .= $chars;
				}
				else
				{
					$formula .= $chars;
				}
			}
			
			$html .= '<tr><th>Formula:</th>';
			$html .= '<td>'. $formula .'</td></tr>';
			
			// Peso Molecular
			$html .= '<tr><th>Mol. Weight:</th>';
			$html .= '<td>'. number_format($data[$i]->mw, 2) .'</td></tr>';
			
			// SMILES
			$smi = $data[$i]->smiles;
			$html .= '<tr><th>SMILES:</th>';
			$html .= "<td><div class='smiles'><a class='lnk' name='{$smi}' title='{$smi}'>{$smi}</a></div></td></tr>";
			
			// Previsao das propriedades
			$html .= '<tr><th class="orangeText">Properties Prediction:</th>';
			$html .= "<td><form action='". base_url('qelba/pred_db') ."' method='post' target='_blank'>
			<input type='hidden' name='qelba_mid' value='{$mid}'><input type='hidden' name='qelba_bonds' value='{$bonds}'>
			<input type='hidden' name='qelba_smiles' value='{$smiles}'><input type='submit' class='btTxt clickable' 
			name='submit' value='View' title='View Prediction' /></form></td></tr>";
		}
		$html .= '</tbody></table>';
		
		return $html;
	}
}

/* End of file qelba.php */
/* Location: ./application/controllers/qelba.php */