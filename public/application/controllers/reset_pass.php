<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* reset_pass.php
* Controlador da pagina 'Reset Password'
* Criado: 21-05-2012
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Reset_pass extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
							'rs_email' => NULL);
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
				$user_email = $this->input->post('reset_email') ? $this->input->post('reset_email') : '';
				
				if (empty($user_email))
				{
					// Campo em falta
					$this->data['result'] = '<p class="errorPane">Make sure to fill in your e-mail address. Please go back and try again.</p>';
				}
				else
				{
                    if (! valid_email($user_email))
                    {
                        // Formato do e-mail invalido
                        $this->data['rs_email'] = $user_email;
                        $this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
                        Invalid E-mail:</span></strong> Make sure your e-mail adress has the format - xxx@(at)xxx.(dot)xxx. 
                        Please go back and try again.</p>';
                    }
                    else
                    {
                        // Verificar se o utilizador existe na BD
						$user = $this->User_model->find_by_email($user_email);
                        
						if (! $user)
						{
                            // Utilizador nao existe
							$this->data['rs_email'] = $user_email;
							$this->data['result'] = '<p class="errorPane">It\'s not possible to fulfill the task. User doesn\'t exist. Please try again!</p>';
						}
						else if ($user->validated == 0 or $user->outdated == 1)
						{
                            // Utilizador nao validado ou apagado
							$this->data['rs_email'] = $user_email;
							$this->data['result'] = '<p class="errorPane">It\'s not possible to fulfill the task. User doesn\'t exist. Please try again!</p>';
                        }
                        else
                        {
							// Gerar a nova palavra-passe
							$user_new_pass = $this->util->generate_password();
							// Actualiar a palavra-passe
                            $user->password = $user_new_pass;
							$result = $user->save();
                            
                            if (! is_array($result))
                            {
                                // Erro
                                $this->data['rs_email'] = $user_email;
                                $this->data['result'] = '<p class="errorPane">An error occurred and your password has not been updated. 
                                If persists send us an email</p>';
                                $error_date = date('Y-m-d');
                                log_message('error', "[Reset_pass] {$error_date} - [Reset pass]: An error occurred.");
                            }
                            else if ($result['result'] == FALSE)
                            {
                                // Nao foi actualizado
                                $this->data['rs_email'] = $user_email;
                                $this->data['result'] = '<p class="errorPane">An error occurred and your password has not been updated.
                                If persists send us an email</p>';
                                $error_date = date('Y-m-d');
                                $error_code = 'Reset pass';
                                $error_desc = 'Error';
                                if (! empty($result['error'])) {
                                    $error_code = $result['error'];
                                    $error_desc = $result['e_desc'];
                                }
                                log_message('error', "[Reset_pass] {$error_date} - [{$error_code}]: {$error_desc}.");
                            }
                            else
                            {
                                // Enviar o e-mail
								$today_date = date('l, F j, Y, g:i a');
								$email_subject = 'ThermInfo Login Information';
								$email_msg = "{$today_date}\n\nDear user,\nThe following login details were requested:\n\n";
								$email_msg .= " E-mail address = {$user_email}\n Password = {$user_new_pass}\n\n(The password is case sensitive)\n\n";
								$email_msg .= 'If you did not authorize this change or if you need assistance, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
								$email_msg .= "\n\nThank you for using ThermInfo!\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
							
								$send = $this->util->send_mail('notreply@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
								
								// Actualizado com sucesso
								$this->data['result'] = '<p class="msgPane"><strong>Your password has been successfully reseted!<br />';
								if ($send) {
									$this->data['result'] .= 'Please check your e-mail inbox. New password sent to '. $user_email;
								} else {
									$this->data['result'] .= 'Could not send the new password. Please <a href="contact" title="Go to contact page">contact us</a>.';
								}
								$this->data['result'] .= '</strong></p>';
                            }
						}
					}
				}
				$this->load->view('content/reset_pass_view', $this->data);
			}
			else
			{
				// Codigo de seguranca invalido
				$this->data['rs_email'] = $this->input->post('reset_email');
				$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
				Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span> 
				numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
				if (! $this->Session_model->check_capcha()) {
                    // Verifica se o utilizador esta a utilizar o browser IE
                    $this->data['result'] .= $this->util->verify_ie_browser();
                }
                
				$this->load->view('content/reset_pass_view', $this->data);
			}
		}
		else
		{
			// Formulario nao submetido (pagina 'Reset Password')
			$this->load->view('content/reset_pass_view', $this->data);
		}
	}
}

/* End of file reset_pass.php */
/* Location: ./application/controllers/reset_pass.php */