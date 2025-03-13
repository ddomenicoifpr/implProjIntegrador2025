<?php
#Classe controller para Usuário
require_once(__DIR__ . "/Controller.php");
require_once(__DIR__ . "/../dao/UsuarioDAO.php");
require_once(__DIR__ . "/../service/UsuarioService.php");
require_once(__DIR__ . "/../model/Usuario.php");
require_once(__DIR__ . "/../model/enum/UsuarioPapel.php");

class UsuarioController extends Controller {

    private UsuarioDAO $usuarioDao;
    private UsuarioService $usuarioService;

    //Método construtor do controller - será executado a cada requisição a está classe
    public function __construct() {
        if(! $this->usuarioEstaLogado())
            return;

        $this->usuarioDao = new UsuarioDAO();
        $this->usuarioService = new UsuarioService();

        $this->handleAction();
    }

    protected function list(string $msgErro = "", string $msgSucesso = "") {
        $dados["lista"] = $this->usuarioDao->list();

        $this->loadView("usuario/list.php", $dados,  $msgErro, $msgSucesso);
    }

    protected function create() {
        $dados['id'] = 0;

        $this->loadView("usuario/form.php", $dados);
    }

    protected function save() {
        echo "Chamou save";
    }

    

}


#Criar objeto da classe para assim executar o construtor
new UsuarioController();
