<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**********************************
* news.php
* Controlador da pagina 'news e contributions'
* Criado: 19-08-2011
* Modificado: 02-02-2014
* Copyright (c) 2014, ThermInfo 
***********************************/

class News extends CI_Controller {

    // Atributos
	private $data; // Dados para as vistas
    
    /**
     * Construtor do controlador
     */
	function __construct()
    {
        parent::__construct();
		
		$this->data = array('news' => NULL,
                            'contributions' => NULL);
		// Carregar os modelos
        $this->load->model('other/News_model');
        $this->load->model('user/User_model');
        $this->load->model('user/Mol_user_model');
    }
    
	/**
	 * Pagina inicial para este controlador
	 */
	public function index()
	{
        // Procura as noticias
        $result = $this->News_model->find_news();
        
        // Verifica se retorna noticias
        if ($result && ! empty($result)) {
            $this->data['news'] = $result;
        } else {
            $this->data['news'] = FALSE;
        }
        
        // Contribuicoes top
        $this->data['contributions'] = $this->_top_contributions();
        
        // Mostra a pagina com os dados
		$this->load->view('content/news_view', $this->data);
	}
    
    /*
     * Procura e formata em HTML 
     * as contribuicoes top
     *
     * @return string Resultado em HTML
     */
    private function _top_contributions()
    {
        $html_result = '';
        // Procura os utilizadores top
        $top_users = $this->Mol_user_model->get_top_users();
        
        if (is_array($top_users))
        {
            if (empty($top_users))
            {
                // Sem utilizadores
                $html_result = '<tr><td><strong>At the moment, there are no contributions to our database</strong></td></tr>';
            }
            else
            {
                $i = 1;
                foreach ($top_users as $top_user)
                {
                    $html_result .= '<tr><td><strong><span class="orangeText">'. $i .'.</span> ';
                    // Detalhe do utilizador
                    $user = $this->User_model->find_by_id($top_user['user']);
                    if ($user) {
                        $html_result .= $user->full_name(). ' --</strong> ' .$user->institution;
                    } else {
                        $html_result .= 'No name --</strong> No institution';
                    }
                    
                    $html_result .= '</td><td>';
                    
                    // Estrelas
                    if ($i == 1)
                    {
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                    }
                    else if ($i == 2)
                    {
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                    }
                    else if ($i == 3)
                    {
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                    }
                    else if ($i == 4)
                    {
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                    }
                    else if ($i == 5)
                    {
                        $html_result .= '<img width="20" height="20" src="public/media/images/starx.gif" alt="star image" />';
                    }
                    // Total moleculas
                    $html_result .= '<br><strong>'. $top_user['top'] .'</strong> compounds added & validated</td></tr>';
                    $i++;
                }
            }
        }
        else
        {
            // Erro
            $html_result = '<tr><td><strong>Error showing contibutions</strong></td></tr>';
        }
        return $html_result;
    }
}

/* End of file news.php */
/* Location: ./application/controllers/news.php */