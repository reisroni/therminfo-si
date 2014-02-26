<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* psearch.php
* Controlador da pagina 'Property Search'
* Criado: 12-07-2012
* Modificado: 20-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Psearch extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('sel_props' => NULL,
                            'result' => NULL,
                            'count' => NULL);
                            
        // Carregar os modelos	
		$this->load->model('property/Data_model');
		$this->load->model('property/Data_value_model');
        $this->load->model('other/Session_model');
        $this->load->model('statistics/Contador_model');
        // Carregar os modulos
		$this->load->library('Util');
        // Estatistica do metodo
        $this->data['count'] = $this->Contador_model->count_search(6);
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
        // * Preenchimento das listas de propriedades *
        $x = $this->Data_model->find_numeric_props();
        $this->data['sel_props'] = '<option value="none">None</option>';
        
        foreach ($x as $p) {
            $units = $p->units ? " [{$p->units}]" : '';
            $f_name = $p->d_full_name ? $p->d_full_name.$units : $p->d_name.$units;
			$this->data['sel_props'] .= "<option value='{$p->did}' title='{$f_name}'>{$p->d_name}{$units}</option>";
		}
        
        
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			// ** Verifica o codigo de seguranca
			if ($this->Session_model->capcha_code() == $_POST['vercode'] && $this->Session_model->check_capcha())
			{	
                // Todos os campos
                $fields = array('prop_1' => $this->input->post('prop_1'),
                                'prop_2' => $this->input->post('prop_2'),
                                'prop_3' => $this->input->post('prop_3'),
                                'prop_1_value_1' => $this->input->post('prop_1_value_1') ? trim($this->input->post('prop_1_value_1')) : '',
                                'prop_1_value_2' => $this->input->post('prop_1_value_2') ? trim($this->input->post('prop_1_value_2')) : '',
                                'prop_2_value_1' => $this->input->post('prop_2_value_1') ? trim($this->input->post('prop_2_value_1')) : '',
                                'prop_2_value_2' => $this->input->post('prop_2_value_2') ? trim($this->input->post('prop_2_value_2')) : '',
                                'prop_3_value_1' => $this->input->post('prop_3_value_1') ? trim($this->input->post('prop_3_value_1')) : '',
                                'prop_3_value_2' => $this->input->post('prop_3_value_2') ? trim($this->input->post('prop_3_value_2')) : '',
                                'option_1' => $this->input->post('option_1'),
                                'option_2' => $this->input->post('option_2'));
                
				$this->data['result'] = $this->_psearch($fields);
                
				if (isset($_POST['ajax']))
				{
					$this->output->set_output($this->data['result']);
				}
				else
				{
					$this->load->view('content/psearch_view', $this->data);
				}
			}
			else
			{
				// Codigo de seguranca invalido
				$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
				Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span> 
				numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
				if (! $this->Session_model->check_capcha()) {
                    // Verifica se o utilizador esta a utilizar o browser IE
                    $this->data['result'] .= $this->util->verify_ie_browser();
                }
                
				if (isset($_POST['ajax']))
				{
					$this->output->set_output($this->data['result']);
				}
				else
				{
					$this->load->view('content/psearch_view', $this->data);
				}
			}
		}
		else
		{
			// Pesquisa nao submetida (pagina 'Psearch')
			$this->load->view('content/psearch_view', $this->data);
		}
	}
    
    /**
     * Pesquisa por propriedades
     *
     * @param array $fields Os campos para pesquisa
     *
     * @return string Resultado da pesquisa
     */
    private function _psearch($fields = array())
    {
        $html_result;
        // Verifica se existe campos
        if (empty($fields))
        {
            $html_result = '<p class="errorPane"><strong>You have not entered 
			search details. Please go back and try again!</strong></p>';
        }
        else
        {
            if ($fields['prop_1'] == 'none' && $fields['prop_2'] == 'none' && $fields['prop_2'] == 'none')
            {
                $html_result = '<p class="errorPane"><strong>You have not selected 
                a property. Please go back and try again!</strong></p>';
            }
            elseif ($fields['prop_1'] == 'none')
            {
                $html_result = '<p class="errorPane"><strong>You have to select 
                at least the first property. Please go back and try again!</strong></p>';
            }
            else
            {
                $html_result = '<p><strong>';
                $html_result .= $fields['prop_1'];
                $html_result .= $fields['prop_2'];
                $html_result .= $fields['prop_3'];
                $html_result .= '</strong></p>';
            }
        }
        return $html_result;
    }
    
    /*
     * Incrementa o contador da procura
     *
     * @param int $search_type Metodo de pesquisa
     *
     * @return void
     */
    private function _search_increment($search_type)
    {
        $fields = array('method' => $search_type);
        
        $record = $this->Contador_model->instantiate($fields);
        $result = $record->save();
        
        if (is_array($result) && ! empty($result))
        {
            if (! $result['result'])
            {
                $error_date = date('Y-m-d');
                $error_code = 'Increment ' .$result['error'];
                log_message('error', "[PSearch] {$error_date} - [{$error_code}]: {$result['e_desc']}.");
            }
        }
        else
        {
            $error_date = date('Y-m-d');
            $error_code = 'Increment error';
			log_message('error', "[PSearch] {$error_date} - [{$error_code}]: An error occurred.");
        }
    }
}

/* End of file psearch.php */
/* Location: ./application/controllers/psearch.php */