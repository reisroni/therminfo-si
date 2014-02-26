<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* register_user.php
* Controlador da pagina 'Register'
* Criado: 20-05-2012
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Register_user extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
							'r_fname' => NULL,
                            'r_lname' => NULL,
							'r_email' => NULL,
							'r_inst' => NULL,
							'ip' => $this->input->ip_address(),
							'agent' => $this->input->user_agent());
		// Carregar os modelos
        $this->load->model('user/User_model');
        $this->load->model('other/Session_model');
		// Carregar os modulos
		$this->load->helper('email');
		$this->load->library('Util');
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		// ** Verifica se foi submetido o formulario
		if (isset($_POST['submit']))
		{
			// ** Verifica o codigo de seguranca
			if ($this->Session_model->capcha_code() == $_POST['vercode'] && $this->Session_model->check_capcha())
			{
                $user_f_name = $this->input->post('register_f_name') ? $this->input->post('register_f_name') : '';
				$user_l_name = $this->input->post('register_l_name') ? $this->input->post('register_l_name') : '';
				$user_email = $this->input->post('register_email') ? $this->input->post('register_email') : '';
				$user_inst = $this->input->post('register_institution') ? $this->input->post('register_institution') : '';
				$user_ip = $this->input->post('register_ip') ? $this->input->post('register_ip') : '';
				$user_agent = $this->input->post('register_agent') ? $this->input->post('register_agent') : '';
				
				if (empty($user_f_name) or empty($user_l_name) or empty($user_email) or empty($user_inst))
				{
					// Campos em falta
					$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
					Fill in all fields:</span></strong> Make sure to fill in all required fields. 
					Please go back and try again.</p>';
				}
				else
				{
					// Gerar a password
					$user_pass = $this->util->generate_password();
					// Criar o novo utilizador
                    $new_user_data = array('u_first_name' => $user_f_name, 'u_last_name' => $user_l_name,
                                        'email' => $user_email, 'institution' => $user_inst,
                                        'password' => $user_pass, 'type' => 'guest',
                                        'validated' => 0, 'outdated' => 0);
					$new_user = $this->User_model->instantiate($new_user_data);
                    if (! $new_user)
                    {
                        // Erro
                        $this->data['r_fname'] = $user_f_name;
                        $this->data['r_lname'] = $user_l_name;
                        $this->data['r_email'] = $user_email;
                        $this->data['r_inst'] = $user_inst;
                        $this->data['result'] = '<p class="errorPane">There is a problem and your account has not been registered. 
                        If persists send us an email</p>';
                    }
                    else
                    {
                        // Adicionar o novo utilizador na BD
                        $result = $new_user->save();
                        
                        if (! is_array($result))
                        {
                            // Erro
                            $this->data['r_fname'] = $user_f_name;
                            $this->data['r_lname'] = $user_l_name;
                            $this->data['r_email'] = $user_email;
                            $this->data['r_inst'] = $user_inst;
                            $this->data['result'] = '<p class="errorPane">There is a problem and your account has not been registered. 
                            If persists send us an email</p>';
                            $error_date = date('Y-m-d');
                            log_message('error', "[Register_user] {$error_date} - [Register user]: An error occurred.");
                        }
                        else if ($result['result'] == FALSE)
                        {
                            // Nao inserido novo utilizador
                            $this->data['r_fname'] = $user_f_name;
                            $this->data['r_lname'] = $user_l_name;
                            $this->data['r_email'] = $user_email;
                            $this->data['r_inst'] = $user_inst;
                            $this->data['result'] = '<p class="errorPane">There is a problem and your account has not been registered. 
                            If persists send us an email</p>';
                            $error_date = date('Y-m-d');
                            $error_code = 'Register user';
                            $error_desc = 'Error';
                            if (! empty($result['error'])) {
                                $error_code = $result['error'];
                                $error_desc = $result['e_desc'];
                            }
                            log_message('error', "[Register_user] {$error_date} - [{$error_code}]: {$error_desc}.");
                        }
                        else
                        {
                            // Enviar o e-mail
                            $today_date = date('l, F j, Y, g:i a');
                            $email_subject = 'New Account Registered';
                            $email_msg = "{$today_date} [EST]\n\nSubject: New Account Registered\nFrom: {$user_name} ({$user_email})";
                            $email_msg .= "\n\nAdditional Info:\nIP = {$user_ip}\nBrowser = {$user_agent}\n";
                            
                            $this->util->send_mail($user_email, $new_user->full_name(), 'reisrony@gmail.com', $email_subject, $email_msg);
                            
                            // Utilizador inserido com sucesso
                            $this->data['result'] = '<p class="msgPane"><strong>Your Account has been Registered! 
                            Now you have to wait for approval by an Admnistrator</strong></p>';
                        }
                    }
				}
				$this->load->view('content/register_user_view', $this->data);
			}
			else
			{
				// Codigo de seguranca invalido
				$this->data['r_name'] = $this->input->post('register_name');
				$this->data['r_email'] = $this->input->post('register_email');
				$this->data['r_inst'] = $this->input->post('register_institution');
				$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
				Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span> 
				numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
				if (! $this->Session_model->check_capcha()) {
                    // Verifica se o utilizador esta a utilizar o browser IE
                    $this->data['result'] .= $this->util->verify_ie_browser();
                }
                
				$this->load->view('content/register_user_view', $this->data);
			}
		}
		else
		{
			// Formulario nao submetido (pagina 'Register User')
			$this->load->view('content/register_user_view', $this->data);
		}
	}
}

/* End of file register_user.php */
/* Location: ./application/controllers/register_user.php */