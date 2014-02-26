<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* contact.php
* Controlador da pagina 'contact us'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Contact extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('result' => NULL,
							'c_name' => NULL,
							'c_email' => NULL,
							'c_msg' => NULL,
							'ip' => $this->input->ip_address(),
							'agent' => $this->input->user_agent());
        // Carregar os modelos
        $this->load->model('other/Session_model');
		// Carregar os modulos
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
				$name = $this->input->post('contact_name') ? $this->input->post('contact_name') : '';
				$email = $this->input->post('contact_email') ? $this->input->post('contact_email') : '';
				$subject = ($this->input->post('contact_subject') && $this->input->post('contact_subject') != 'none') ? 
							$this->input->post('contact_subject') : '';
				$msg = $this->input->post('contact_msg') ? $this->input->post('contact_msg') : '';
				$ip = $this->input->post('contact_ip') ? $this->input->post('contact_ip') : '';
				$agent = $this->input->post('contact_agent') ? $this->input->post('contact_agent') : '';
				
				if (empty($name) or empty($email) or empty($subject) or empty($msg))
				{
					// Campos em falta
					$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
					Fill in all fields:</span></strong> Make sure to fill in all required fields. 
					Please go back and try again.</p>';
				}
				else
				{
					// Validar o e-mail
					$this->load->helper('email');
					
					if (! valid_email($email))
					{
						// Formato do e-mail invalido
						$this->data['c_name'] = $name;
						$this->data['c_msg'] = $msg;
						$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
						Invalid E-mail:</span></strong> Make sure your e-mail adress has the format - xxx@(at)xxx.(dot)xxx. 
						Please go back and try again.</p>';
					}
					else
					{
						// Enviar o e-mail
						$today_date = date('l, F j, Y, g:i a');
						$msg = strip_tags($msg);
						$msg = stripcslashes($msg);
						$email_msg = "{$today_date} [EST]\n\nSubject: {$subject}\nFrom: {$name} ({$email})\nMessage: {$msg}";
						$email_msg .= "\n\nAdditional Info:\nIP = {$ip}\nBrowser = {$agent}\n";
						
						$result = $this->util->send_mail($email, $name, 'reisrony@gmail.com', $subject, $email_msg);
						
						if ($result)
						{
							// Enviado e-mail com sucesso
							$this->data['result'] = '<p class="msgPane"><strong>Your Message has been sent!</strong>
							<br /><strong>Date:</strong> '. $today_date .'<br /><strong>Subject:</strong> '. html_escape($subject) .'<br /><strong>Message:</strong> '.
							html_escape($msg) .'<br /><br /><strong>Thank You '. html_escape($name) .' ('. html_escape($email). ')</strong></p>';
						}
						else
						{
							// E-mail nao enviado
							$this->data['c_name'] = $name;
							$this->data['c_email'] = $email;
							$this->data['c_msg'] = $msg;
							$this->data['result'] = '<p class="errorPane">There is a problem and the message wasn\'t sent. 
                            Alternatively, you can send an email to therminfo@gmail.com</p>';
						}
					}
				}
				$this->load->view('content/contact_view', $this->data);
			}
			else
			{
				// Codigo de seguranca invalido
				$this->data['c_name'] = $this->input->post('contact_name');
				$this->data['c_email'] = $this->input->post('contact_email');
				$this->data['c_msg'] = $this->input->post('contact_msg');
				$this->data['result'] = '<p class="errorPane"><strong><span class="underlineText">
				Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span> 
				numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
				if (! $this->Session_model->check_capcha()) {
                    // Verifica se o utilizador esta a utilizar o browser IE
                    $this->data['result'] .= $this->util->verify_ie_browser();
                }
                
				$this->load->view('content/contact_view', $this->data);
			}
		}
		else
		{
			// Formulario nao submetido (pagina 'Contact us')
			$this->load->view('content/contact_view', $this->data);
		}
	}
}

/* End of file contact.php */
/* Location: ./application/controllers/contact.php */