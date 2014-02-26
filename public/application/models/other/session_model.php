<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Session
 * Descricao: Modelo para sessoes
 * Criado: 09-05-2013
 * Modificado: 26-01-2014
 * @author Roni Reis
 * @version 0.1
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */

class Session_model extends CI_Model
{
	// Atributos
	private $logged_in = FALSE;
	private $user_id;
	private $message;
	private $capcha_code;
	
	/**
     * Construtor da classe
     */
    function __construct()
    {
        parent::__construct();
		// Iniciar a sessao
		session_start();
		// Verifcar a msg o login e o codigo
		$this->_check_message();
		$this->_check_login();
		$this->_check_code();
    }
	
	//---------------------------------------------------------------
	// Metodos da classe
	//---------------------------------------------------------------
	/**
     * Verifca se esta login
	 *
	 * @return boolean 'TRUE' ou 'FALSE'
     */
	public function is_logged_in()
	{
		return $this->logged_in;
	}
	
	/**
     * Efectua o login
	 *
	 * @param $user Registo do utilizador
	 *
	 * @return void
     */
	public function login($user)
	{
		if ($user)
		{
			$this->user_id = $_SESSION['user_id'] = $user->id;
			$this->logged_in = TRUE;
		}
	}
	
	/**
     * Efectua o logout
	 *
	 * @return void
     */
	public function logout()
	{
		unset($_SESSION['user_id']);
		unset($this->user_id);
		$this->logged_in = FALSE;
	}
	
	/**
     * Guarda ou retorna uma mensagem
	 * 
	 * @param string $msg Mensagem para guardar
	 *
	 * @return mixed A mensagem guardada
	 * ou nada (void)
     */
	public function message($msg = '')
	{
		if (! empty($msg))
		{
			// "set message"
			$_SESSION['message'] = $msg;
		}
		else
		{
			// "get message"
			return $this->message;
		}
	}
	
	/**
	 * Guarda ou retorna o codigo capcha
	 * 
	 * @param string $code Codigo para guardar
	 *
	 * @return mixed O codigo guardado
	 * ou nada (void)
	 * 
	 */
	public function capcha_code($code = '')
	{
		if (! empty($code))
		{
			// "set code"
			$_SESSION['vercode'] = $code;
		}
		else
		{
			// "get code"
			return $this->capcha_code;
		}
	}
    
    /**
     * Verifica se o capcha esta vazio
     *
     * @return boolean TRUE se nao esta vazio
     * ou FALSE se esta vazio
     */
    public function check_capcha()
    {
        return (isset($this->capcha_code) && !empty($this->capcha_code)) ? TRUE : FALSE;
    }
	
	//---------------------------------------------------------------
	// Metodos auxiliares
	//---------------------------------------------------------------
	/*
     * Verifica o login
	 * 
	 * @return void
     */
	private function _check_login()
	{
		if (isset($_SESSION['user_id']))
		{
			$this->user_id = $_SESSION['user_id'];
			$this->logged_in = TRUE;
		}
		else
		{
			unset($this->user_id);
			$this->logged_in = FALSE;
		}
	}
	
	/*
     * Verifica a mensagem
	 * 
	 * @return void 
     */
	private function _check_message()
	{
		if (isset($_SESSION['message']))
		{
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		else
		{
			$this->message = '';
		}
	}
	
	/*
     * Verifica o codigo
	 * 
	 * @return void 
     */
	private function _check_code()
	{
		if (isset($_SESSION['vercode']))
		{
			$this->capcha_code = $_SESSION['vercode'];
			unset($_SESSION['vercode']);
		}
		else
		{
			$this->capcha_code = '';
		}
	}
}

/* End of file session_model.php */
/* Location: ./application/models/session_model.php */