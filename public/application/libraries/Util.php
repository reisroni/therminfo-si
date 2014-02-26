<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * --- Util
 * Descricao:  Algumas utilidades
 * Criado: 13-09-2011
 * Modificado: 20-02-2014
 * @author Rony Reis, Ana Teixeira
 * @version 0.2
 * @package Therminfo
 * @copyright (c) 2014, ThermInfo
 */

class Util {
	
	/**
     * Retorna o conteudo do resultado de um URL
     * 
     * @param string $url URL a procurar
	 * @param int $timeout Tempo de espera (por omissao 5)
     *
     * @return mixed Conteudo do URL ou 'FALSE' 
	 * em caso de falha
     */
	public function get_url_contents($url = '', $timeout = 0) 
	{
		if (function_exists('curl_init')) 
		{
			$conn = curl_init();
			
			if ($conn) 
			{
				$log_path = APPPATH.'logs/curl_log_'.date('Y-m-d').'.txt';
				if (empty($timeout)) {
					$timeout = 5;
				}
				
				curl_setopt($conn, CURLOPT_URL, $url);
				curl_setopt($conn, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
				curl_setopt($conn, CURLOPT_FOLLOWLOCATION, TRUE);
				curl_setopt($conn, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($conn, CURLOPT_VERBOSE, TRUE);
				curl_setopt($conn, CURLOPT_STDERR, $f = fopen($log_path, FOPEN_READ_WRITE_CREATE));
				curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, $timeout);
				curl_setopt($conn, CURLOPT_TIMEOUT, $timeout);
				
				$content = curl_exec($conn);
				$error_code = curl_errno($conn); // CURL error code
				$error_message = curl_error($conn); // CURL error msg
				$http_code = curl_getinfo($conn, CURLINFO_HTTP_CODE); // HTTP code
				
				if ($http_code == 200) {
					if ($error_code == 0) {
						$result = $content;
					} else {
						log_message('error', 'CURL error: '. $error_message);
						$result = FALSE;
					}
				} else {
					$result = FALSE;
				}
				
				fclose($f);
				
				$CI =& get_instance();
				$CI->load->helper('file');
				write_file($log_path, "\n --------------- END --------------- \n", FOPEN_WRITE_CREATE);
				curl_close($conn);
			}
			else
			{
				$result = FALSE;
			}
		}
		else
		{
            $result = FALSE;
        }
		
        return $result;
    }

    /**
	 * Substitui numa string alguns caracteres especiais, 
	 * para enviar para a linha de comandos ou URL
	 * 
	 * @param string $term String para substituir 
	 * os caracteres
	 * @param int $type Tipo de subtituicao
	 * 1 - Linha de comandos, 2 - URL, 3 - '#' (por omissao linha de cmd)
	 *
	 * @return string String com os carateres substituidos
	 */
    public function replace_char($term = '', $type = 0) 
	{
        switch ($type) 
		{
			// linha de comando
            case 1 :
			{
				$result = escapeshellcmd($term);
			} break;
			// URL
            case 2 :
			{
				$result = rawurlencode($term);
			} break;
			// '#' para '%23'
			case 3 :
			{
				$result = str_replace('#', '%23', $term);
			} break;
			// linha de comando por defeito
            default:
                $result = escapeshellcmd($term);
        }
        
        return $result;
    }
	
	/**
	 * Leitura do ficheiro com a descricao
	 * dos parametros ELBA
	 * 
	 * @return mixed Array com a descricao
	 * ou 'FALSE' em caso de falha
	 * 
	 * [Array([parametro] => [descricao])]
	 */
	public function read_params()
	{
		$result = FALSE;
		$file_contents = file_get_contents('scripts/py/params/parameters_description.txt');
		
		if ($file_contents)
		{
			$p_descriptions = explode(';', $file_contents);
			$result = array();
			
			foreach ($p_descriptions as $p_description)
			{
				list($key, $value) = explode('* ', $p_description);
				$key = trim($key);
				$result[$key] = $value;
			}
		}
		
		return $result;
	}
	
	/**
	 * Gerar uma palavra-passe
	 * 
	 * @param int $length Tamanho da palavra-passe (por omissao 9)
	 * @param int $strength Forca da palavra-passe (0, 1, 2, 3, 4)
	 * 
	 * @return string A palavra-passe gerada
	 */
	public function generate_password($length = 9, $strength = 0)
	{
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		
		if ($strength == 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		
		if ($strength == 2) {
			$vowels .= 'AEUY';
		}
		
		if ($strength == 3) {
			$consonants .= '23456789';
		}
		
		if ($strength == 4) {
			$consonants .= '@#$%';
		}
		 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; ++$i)
		{
			if ($alt == 1)
			{
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			}
			else
			{
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		
		return $password;
	}
	
	/**
	 * Enviar um e-mail
	 * 
	 * @param string $from O remetente
	 * @param string $name O nome do remetente
	 * @param string $to O destinatario
	 * @param string $subject O assunto
	 * @param string $message A mensagem
     * @param string $attach_path O anexo
	 * 
	 * @return boolean 'TRUE' em caso de sucesso
	 * ou 'FALSE' em caso de falha
	 */
	public function send_mail($from = '', $name = '', $to = '', $subject = '', $message = '', $attach_path = '')
	{
		$CI =& get_instance();
		$CI->load->library('email');
		$CI->load->helper('email');
		$result = FALSE;
		
		if (valid_email($to))
		{
			$CI->email->from($from, $name);
			$CI->email->to($to);
			$CI->email->subject($subject);
			$CI->email->message($message);
            
            if (! empty($attach_path) && is_file($attach_path)) {
                $CI->email->attach($attach_path);
            }
            
			$result = $CI->email->send();
		}
		
		return $result;
	}
	
	/**
	 * Remove os acentos e caracteres especiais 
	 * de um texto
	 * 
	 * @param string $text Texto a remover
	 * 
	 * @return string Texto sem acentos
	 */
	public function remover_acentos($text = '')
	{
		return preg_replace("/[^a-zA-Z0-9_.]/i", "", strtr($text, "�������������������������� ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
	}
	
	/**
	 * Verifica se o browser e IE 
     * e retorna indicacoes de configuracao
     *
     * @return string HTML
	 */
	public function verify_ie_browser()
	{
		$CI =& get_instance();
		$CI->load->library('user_agent');
		$CI->load->helper('html');
		$html = '';
		
		if ($CI->agent->is_browser('Internet Explorer'))
		{
			$steps = array(
            "Click on the 'Tools' menu.", 
            "Click on the 'Internet Options'.", 
            "Click on the 'Privacy' tab",
            "Click on the 'Sites' button",
			"Enter the <strong>fc.ul.pt</strong> domain in the text field and then click 'Allow'.",
			"Save changes by clicking 'Ok'.",
			"Refresh the Page (F5).");
			$html = '<p>You are using <u>Internet Explorer</u>, so please be sure you have cookies enabled for the following domain: <strong>fc.ul.pt</strong>.';
			$html .= ol($steps);
			$html .= '</p>';
		}
		
		return $html;
	}
	
	/**
	 * Retorna a localizacao geografica 
	 * pelo endereco IP
	 *
	 * @param string $ip Endereco IP
	 * 
	 * @return mixed Array com os dados 
	 * da localizacao ou 'False' em caso
	 * de falha
	 * 
	 * [Array('ip','country_code','country_name',
	 * 'region_name','city_name','zip_code',
	 * 'latitude','longitude','timezone')]
	 */
	public function get_geolocation($ip = 0)
	{
		if (empty($ip))
		{
			$result = FALSE; // IP empty
		}
		else
		{
			$CI =& get_instance();
			$CI->load->library('input');
			
			if (! $CI->valid_ip($ip))
			{
				$result = FALSE; // IP not valid
	        }
			else
			{
				$key = ''; // API key
				$url = "http://api.ipinfodb.com/v3/ip-city/?key={$key}&ip={$ip}&format=xml"; // 'IPInfoDB' url
				
				$xml_content = $this->get_url_contents($url);
				
		        if (! $xml_content) {
		            $result = FALSE; // Failed to open connection
		        } else {
		            $xml = new SimpleXMLElement($xml_content);
					// Return the data as an array
					$result = array('ip' => $ip, 'country_code' => $xml->countryCode, 'country_name' => $xml->countryName, 
					'region_name' => $xml->regionName, 'city_name' => $xml->cityName, 'zip_code' => $xml->zipCode, 
					'latitude' => $xml->latitude, 'longitude' => $xml->longitude, 'timezone' => $xml->timeZone);
		        }
			}
		}
		
		return $result;
	}
}

/* End of file Util.php */
/* Location: ./application/libraries/Util.php */