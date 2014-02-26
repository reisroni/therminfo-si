<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* change_pass.php
* Controlador da pagina 'Change Password'
* Criado: 20-05-2012
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Change_pass extends CI_Controller {

	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
							'ch_email' => NULL);
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
				$user_email = $this->input->post('change_email') ? $this->input->post('change_email') : '';
				$user_pass = $this->input->post('change_old_pass') ? $this->input->post('change_old_pass') : '';
				$user_new_pass1 = $this->input->post('change_new_pass_1') ? $this->input->post('change_new_pass_1') : '';
				$user_new_pass2 = $this->input->post('change_new_pass_2') ? $this->input->post('change_new_pass_2') : '';
				
				if (empty($user_email) or empty($user_pass) or empty($user_new_pass1) or empty($user_new_pass2))
				{
					// Campos em falta
					$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
					Fill in all fields:</span></strong> Make sure to fill in all required fields. 
					Please go back and try again.</p>';
				}
				else
				{
					if ($user_new_pass1 != $user_new_pass2)
					{
						// Nova palavra-passe diferente
						$this->data['ch_email'] = $user_email;
						$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
						New Password:</span></strong> New Passwords do not match. Please go back and try again.</p>';
					}
					else
					{
						if (! valid_email($user_email))
						{
							// Formato do e-mail invalido
							$this->data['ch_email'] = $user_email;
							$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
							Invalid E-mail:</span></strong> Make sure your e-mail adress has the format - xxx@(at)xxx.(dot)xxx. 
							Please go back and try again.</p>';
						}
						else
						{
							// Verificar se o utilizador existe na BD
							$user = $this->User_model->authenticate($user_email, $user_pass);
							
							if (! $user)
							{
								// Utilizador nao existe
								$this->data['ch_email'] = $user_email;
								$this->data['result'] = '<p class="errorPane">Password or e-mail incorrect, user doesn\'t exist. Please try again!</p>';
							}
                            else if ($user->validated == 0 or $user->outdated == 1)
                            {
                                // Utilizador nao validado ou apagado
								$this->data['ch_email'] = $user_email;
								$this->data['result'] = '<p class="errorPane">Password or e-mail incorrect, user doesn\'t exist. Please try again!</p>';
                            }
							else
							{
								// Actualizar a palavra-passe
								$user->password = $user_new_pass2;
								$result = $user->save();
								
								if (! is_array($result))
								{
									// Erro
                                    $this->data['ch_email'] = $user_email;
									$this->data['result'] = '<p class="errorPane">An error occurred and your password has not been changed. 
                                    If persists send us an email</p>';
									$error_date = date('Y-m-d');
									log_message('error', "[Change_pass] {$error_date} - [Change pass]: An error occurred.");
								}
								else if ($result['result'] == FALSE)
								{
									// Nao foi actualizado
									$this->data['ch_email'] = $user_email;
									$this->data['result'] = '<p class="errorPane">An error occurred and your password has not been changed.
									If persists send us an email</p>';
									$error_date = date('Y-m-d');
									$error_code = 'Change pass';
									$error_desc = 'Error';
									if (! empty($result['error'])) {
										$error_code = $result['error'];
										$error_desc = $result['e_desc'];
									}
									log_message('error', "[Change_pass] {$error_date} - [{$error_code}]: {$error_desc}.");
								}
								else
								{
									// Enviar o e-mail
									$today_date = date('l, F j, Y, g:i a');
									$email_subject = 'ThermInfo Login Information';
									$email_msg = "{$today_date}\n\nDear user,\nYou have successfully updated the password of your account.\n\n";
									$email_msg .= "Login details:\n E-mail address = {$user_email}\n Password = {$user_new_pass1}\n\n(The password is case sensitive)\n\n";
									$email_msg .= 'If you did not authorize this change or if you need assistance, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
									$email_msg .= "\n\nThank you for using ThermInfo!\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
								
									$this->util->send_mail('noreply@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
									
									// Actualizado com sucesso
									$this->data['result'] = '<p class="msgPane"><strong>Your password has been successfully updated!</strong></p>';
								}
							}
						}
					}
				}
				$this->load->view('content/change_pass_view', $this->data);
			}
			else
			{
				// Codigo de seguranca invalido
				$this->data['ch_email'] = $this->input->post('change_email');
				$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
				Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span> 
				numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
				if (! $this->Session_model->check_capcha()) {
                    // Verifica se o utilizador esta a utilizar o browser IE
                    $this->data['result'] .= $this->util->verify_ie_browser();
                }
                
				$this->load->view('content/change_pass_view', $this->data);
			}
		}
		else
		{
			// Formulario nao submetido (pagina 'Change Password')
			$this->load->view('content/change_pass_view', $this->data);
		}
	}
}

/* End of file change_pass.php */
/* Location: ./application/controllers/change_pass.php */