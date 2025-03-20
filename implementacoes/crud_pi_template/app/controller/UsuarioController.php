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
        $dados['papeis'] = UsuarioPapel::getAllAsArray();

        $this->loadView("usuario/form.php", $dados);
    }

    protected function save() {
        //Capturar os dados do formulário
        $nome = trim($_POST['nome']) != "" ? trim($_POST['nome']) : NULL;
        $login = trim($_POST['login']) != "" ? trim($_POST['login']) : NULL;
        $senha = trim($_POST['senha']) != "" ? trim($_POST['senha']) : NULL;
        $confSenha = trim($_POST['conf_senha']) != "" ? trim($_POST['conf_senha']) : NULL;
        $papel = $_POST['papel'];

        //Criar o objeto Usuario
        $usuario = new Usuario();
        $usuario->setNome($nome);
        $usuario->setLogin($login);
        $usuario->setSenha($senha);
        $usuario->setPapel($papel);

        //Validar os dados (camada service)
        $erros = $this->usuarioService->validarDados($usuario, $confSenha);
        if(! $erros) {
            //Inserir no Base de Dados
            try {
                $this->usuarioDao->insert($usuario);
                
                header("location: " . BASEURL . "/controller/UsuarioController.php?action=list");
                exit;
            } catch(PDOException $e) {
                //Iserir erro no array
                array_push($erros, "Erro ao gravar no banco de dados!");
                //array_push($erros, $e->getMessage());
            }
        } 

        //Mostrar os erros
        $dados['id'] = 0;
        $dados['papeis'] = UsuarioPapel::getAllAsArray();
        $dados["usuario"] = $usuario;
        $dados['confSenha'] = $confSenha;

        $msgErro = implode("<br>", $erros);

        $this->loadView("usuario/form.php", $dados, $msgErro);
    }

    protected function delete() {
        $id = 0;
        if(isset($_GET["id"]))
            $id = $_GET["id"];

        //Busca o usuário na base pelo ID    
        $usuario = $this->usuarioDao->findById($id);
        
        if($usuario) {
            //Excluir
            $this->usuarioDao->deleteById($id);

            header("location: " . BASEURL . "/controller/UsuarioController.php?action=list");
            exit;
        } else {
            $this->list("Usuário não encontrado!");
        }
    }

    

}


#Criar objeto da classe para assim executar o construtor
new UsuarioController();
