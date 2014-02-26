<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* login.php
* Controlador da pagina 'login'
* Criado: 21-09-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Login extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('email' => NULL,
							'url' => NULL,
							'info' => NULL,
							'result' => NULL);
		// Carregar os modelos
		$this->load->model('user/User_model');
		$this->load->model('other/Session_model');
		// Carregar os modulos
        $this->load->library('Util');
		$this->load->helper('email');
		
    }
	
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
		redirect('/login/redirect/');
	}
	
	/**
	 * Efectuar login em diferentes paginas
	 * 
	 * @param string $main_url Pagina/pasta a redireccionar
     * @param string $sub_url Pagina de uma pasta a redireccionar
	 * 
	 * @return void
	 */
	public function redirect($main_url = 'main', $sub_url = '')
	{
        $url = empty($sub_url) ? $main_url : $main_url .'/'. $sub_url;
		$this->data['url'] = $url;
        
		// ** Verifica se ja esta logado
		if ($this->Session_model->is_logged_in()) 
		{
			redirect($url);
		}
		else
		{
			// ** Verifica se foi submetido o formulario
			if (isset($_POST['submit']))
			{
				// ** Verifica o codigo de seguranca
				if ($this->Session_model->capcha_code() == $_POST['vercode'] && $this->Session_model->check_capcha())
				{
					// ** Verifica se foi preenchido os dados
					if (! empty($_POST['email']) && ! empty($_POST['password']))
					{
						$email = $this->input->post('email');
						$pass = $this->input->post('password');
						
						if (valid_email($email)) 
						{
							$user = $this->User_model->authenticate($email, $pass);
							// ** Verifica se login ok
							if ($user)
							{
								$this->Session_model->login($user);
								$_SESSION['name'] = $user->full_name();
								$_SESSION['type'] = $user->type;
								$_SESSION['user_inst'] = $user->institution;
								$_SESSION['user_email'] = $email;
								
								redirect($url);
							}
							else 
							{
								// Utilizador nao existe
								$this->data['email'] = $email;
								$this->data['info'] = '<p class="errorPane">Password or e-mail incorrect. Please try again!</p>';
								$this->load->view('content/login_view', $this->data);
							}
						}
						else 
						{
							// Formato do e-mail invalido
							$this->data['info'] = '<p class="errorPane"><strong><span class="underlineText">
							Invalid E-mail:</span></strong> Make sure your e-mail adress has the format - xxx@(at)xxx.(dot)xxx. 
							Please go back and try again.</p>';
							$this->load->view('content/login_view', $this->data);
						}
						
					}
					else
					{
						// E-mail ou password nao inserido
						$this->data['info'] = '<p class="errorPane"><strong>No Username or Password inserted. Please try again!</strong></p>';
						$this->load->view('content/login_view', $this->data);
					}
				}
				else
				{
					// Codigo de seguranca invalido
					$this->data['email'] = $this->input->post('email');
					$this->data['info'] = '<p class="errorPane"><strong><span class="underlineText">
					Invalid Security Code:</span></strong> Make sure you typed <span class="underlineText">only</span>
					numerical characters. Please try again. [<a href="help.php#seccode" title="Security Code Help" target="_blank"><strong>Help</strong></a>]</p>';
					if (! $this->Session_model->check_capcha()) {
                        // Verifica se o utilizador esta a utilizar o browser IE
                        $this->data['info'] .= $this->util->verify_ie_browser();
                    }
                    
                    $this->load->view('content/login_view', $this->data);
				}
			}
			else
			{
				// Formulario nao submetido (pagina 'Login')
				$this->load->view('content/login_view', $this->data);
			}
		}
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */