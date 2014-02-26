<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* admin.php
* Controlador da administracao
* Criado: 03-10-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class Admin extends CI_Controller {
	
	// Atributos
	private $data; // Dados para as vistas
	
	/**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('name' => NULL,
							'user_type' => NULL,
							'user_inst' => NULL,
							'user_email' => NULL);
        // Carregar os modelos
        $this->load->model('user/User_model');
        $this->load->model('other/Session_model');
        // Carregar os modulos
        $this->load->library('grocery_CRUD');
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
				$this->data['name'] = $_SESSION['name'];
				$this->data['user_type'] = $_SESSION['type'];
				$this->data['user_inst'] = $_SESSION['user_inst'];
				$this->data['user_email'] = $_SESSION['user_email'];

				$this->load->view('content/admin/admin_view', $this->data);
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
			redirect('/login/redirect/administration/admin_user');
		}
	}
		
	//---------------------------------------------------------------
	// Separador 'Users'
	//---------------------------------------------------------------
	//
	// ----- Utilizadores
	/**
	 * Gestao de utilizadores (grocery CRUD)
	 * 
	 * @return void
	 */
	public function users_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && $_SESSION['type'] == 'superadmin')
		{
			$this->User_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
            
			// Tabela 'User'
            $crud->set_table('user');
			$crud->where('validated', 1);
			$crud->where('outdated', 0);
			$crud->set_subject('User');
			$crud->columns('u_first_name', 'u_last_name', 'email', 'institution', 'type');
			$crud->display_as('u_first_name', 'First Name');
            $crud->display_as('u_last_name', 'Last Name');
			
			$crud->required_fields('u_first_name', 'u_last_name', 'email', 'password', 'type', 'validated');
			$crud->change_field_type('password', 'password');
			$crud->change_field_type('validated', 'enum', array('',0,1));
			$crud->unset_fields('outdated');
			// Regras de validacao
			$crud->set_rules('email', 'E-mail', 'valid_email');
			
			// Callback functions
			// Encriptar a palava-passe
			$crud->callback_before_insert(array($this, 'user_pass_encrypt_callback'));
			$crud->callback_before_update(array($this, 'user_pass_encrypt_callback'));
			// Apagar
			$crud->callback_delete(array($this, 'user_delete_callback'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add, Delete and Edit users';
			$this->load->view('content/admin/admin_tabs_view', $output);
		}
		else
		{
			// Area proibida
			set_status_header(401, 'Forbidden Area');
			$this->output->set_output('<h1>Forbidden Area</h1>');
		}
	}
	
	/*
	 * Encripta a palavra-passe para inserir
	 * na BD (grocery CRUD callback)
	 */
	public function user_pass_encrypt_callback($post_array = array())
	{
		$this->load->helper('security');
		
		if(empty($post_array['password']))
		{
			unset($post_array['password']);
		}
		else
		{
			// Verifica se a palavra-passe ja esta encripado (MD5, tamanho 32)
			if (strlen($post_array['password']) != 32) {
				$post_array['password'] = do_hash($post_array['password'], 'md5');
			}
		}
		return $post_array;
	}
	
	/*
	 * Elimina um utilizador (grocery CRUD callback)
	 */
	public function user_delete_callback($primary_key = 0)
	{
        $result = FALSE;
        $user = $this->User_model->find_by_id($primary_key);
        
		if ($user)
		{
            $del = $user->outdated();
            if (is_array($del)) {
                if ($del['result'] == TRUE) {
                    $result = TRUE;
                }
            } elseif($del == TRUE) {
                $result = TRUE;
            }
		}
		
		return $result;
	}
	
	// ----- Novos utilizadores
	/**
	 * Gestao de pedidos de novos utilizadores
	 * 
	 * @return void
	 */
	public function new_users_management()
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			$this->User_model->setDatabase(HOST, USER, PASS, DB, FALSE);
			$crud = new grocery_CRUD();
			
			// Tabela 'User'
            $crud->set_table('user');
			$crud->where('validated', 0);
			$crud->where('outdated', 0);
			$crud->set_subject('User');
			$crud->columns('u_first_name', 'u_last_name', 'email', 'institution', 'type');
			$crud->display_as('u_first_name', 'First Name');
            $crud->display_as('u_last_name', 'Last Name');
			$crud->unset_add();
			
			$crud->edit_fields('u_first_name', 'u_last_name', 'email', 'institution', 'type');
			$crud->change_field_type('u_first_name', 'readonly');
            $crud->change_field_type('u_last_name', 'readonly');
			$crud->change_field_type('email', 'readonly');
			$crud->change_field_type('institution', 'readonly');
			$crud->change_field_type('type', 'enum', array('guest','admin'));
			
			// Callback functions
			// Validar o utilizador
			$img_url = base_url('assets/grocery_crud/themes/flexigrid/css/images/add.png');
			$crud->add_action('Validate', $img_url, 'admin/new_user_insert_callback');
			// Apagar
			$crud->callback_delete(array($this, 'new_user_remove_callback'));
			
			// Vista
			$output = $crud->render();
			$output->title = 'Add, and Reject new users';
			$this->load->view('content/admin/admin_tabs_view', $output);
		}
		else
		{
			// Area proibida
			set_status_header(401, 'Forbidden Area');
			$this->output->set_output('<h1>Forbidden Area</h1>');
		}
	}
	
	/*
	 * Valida um novo utilizador na BD (grocery CRUD callback)
	 */
	public function new_user_insert_callback($user_id = 0)
	{
		// ** Verifica se o utilizador e administrador **
		if (isset($_SESSION['type']) && ($_SESSION['type'] == 'admin' or $_SESSION['type'] == 'superadmin'))
		{
			if (empty($user_id))
			{
                $html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
				border-color: #D893A1;">Lack user ID - <a href="'. base_url() .'admin/new_users_management">Back to List</a></p>';
			}
			else
			{
				// Novo utilizador
                $user = $this->User_model->find_by_id($user_id);
                
                if (! $user)
                {
                    $html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
					border-color: #D893A1;">User doesn\'t exist - <a href="'. base_url() .'admin/new_users_management">Back to List</a></p>';
                }
                else
                {
                    // Valida o novo utilizador
                    $user_pass = $this->util->generate_password(); // Gera uma palavra-passe
                    $user->password = do_hash($user_pass, 'md5'); // Encripta a palavra-passe
                    $user->validated = 1;
                    $user->outdated = 0;
                    $save_status = $user->save();
                    
                    if (! is_array($save_status))
                    {
                        $html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
                        border-color: #D893A1;">Failure - <a href="'. base_url() .'admin/new_users_management">Back to List</a></p>';
                    }
                    else
                    {
                        if ($save_status['result'] == FALSE)
                        {
                            $html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #FBE6F2;
                            border-color: #D893A1;">Could not add the user. Error: '. $save['e_desc'] .' - <a href="'. base_url() .
                            'admin/new_users_management">Back to List</a></p>';
                        }
                        else
                        {
                            // Envio do e-mail
                            $today_date = date('l, F j, Y, g:i a');
                            $user_email = $user->email;
                            $email_subject = 'ThermInfo Login Information';
                            $email_msg = "{$today_date}\n\nDear user,\nWelcome to ThermInfo! You may now login at www.therminfo.com and add new compounds to the Database.\n\n";
                            $email_msg .= "Login details:\nE-mail address = {$user_email}\n Password = {$user_pass}\n\n(The password is case sensitive)\n\n";
                            $email_msg .= 'If you need assistance, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
                            $email_msg .= "\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
                            
                            $send_status = $this->util->send_mail('ThermInfo@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
                            
                            if ($send_status) {
                                $email_status = "An email was sent to {$user_email} with the new password";
                            } else {
                                $email_status = 'The email was not sent ('. $user_email .'). Contact de user and sent the new password: '. $user_pass;
                            }
                            
                            $html = '<p style="padding: 0.7em;color: #000000;border: 1px solid;background-color: #DFF2BF;
                            border-color: #008000;">User added with success - <a href="'. base_url() .'admin/new_users_management">Back to List</a><br />
                            <span>'. $email_status .'</span></p>';
                        }
                    }
                }
			}
			$this->output->set_output($html);
		}
		else
		{
			// Area proibida
			set_status_header(401, 'Forbidden Area');
			$this->output->set_output('<h1>Forbidden Area</h1>');
		}
	}
	
	/*
	 * Remove um utilizador nao validado (grocery CRUD callback)
	 */
	public function new_user_remove_callback($user_id = 0)
	{
        // Procura o utilizador
		$user = $this->User_model->find_by_id($user_id);
        if (! $user)
        {
            $result = FALSE;
        }
        else
        {
            // Remove o novo utilizador nao validado
            $del_status = $user->delete();
            if (! $del_status)
            {
                $result = FALSE;
            }
            else
            {
                // Envio do e-mail
                $today_date = date('l, F j, Y, g:i a');
                $user_email = $user->email;
                $email_subject = 'Account Not Approved';
                $email_msg = "{$today_date}\n\nDear user,\nYour ThermInfo account has not been approved.\n\n";
                $email_msg .= 'If you need more informations, please contact us at http://therminfo.lasige.di.fc.ul.pt/contact';
                $email_msg .= "\n\nRegards,\nThe ThermInfo Team.\n\n\n\nNote: Please do not reply this email.";
                
                $this->util->send_mail('ThermInfo@therminfo.com', 'ThermInfo', $user_email, $email_subject, $email_msg);
                $result = TRUE;
            }
		}
		return $result;
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */