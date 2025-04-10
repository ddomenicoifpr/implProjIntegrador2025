<?php
#Classe controller padrão

require_once(__DIR__ . "/../util/config.php");

class Controller {

    //Método que efetua a chamada do ação conforme parâmetro GET recebido pela requisição
    protected function handleAction() {
        //Captura a ação do parâmetro GET
        $action = NULL;
        if(isset($_GET['action']))
            $action = $_GET['action'];
        
        //Chama a ação
        $this->callAction($action);
    }

    protected function callAction($methodName) {
        //Verifica se o método da action recebido por parâmetro existe na classe
        //Se sim, chama-o
        if($methodName && method_exists($this, $methodName))
            $this->$methodName();
        
        else {
            echo "Ação não encontrada no controller.<br>";
            echo "Verifique com o administrador do sistema.";
        }

    }

    protected function loadView(string $path, array $dados, string $msgErro = "", string $msgSucesso = "") {
        
        //Verificar os dados que estão sendo recebidos na função
        //echo "<pre>" . print_r($dados, true) . "</pre>";
        //exit;

        $caminho = __DIR__ . "/../view/" . $path;
        //echo $caminho;
        if(file_exists($caminho)) {
            //Inclui e exibe a view a partir do controller
            require $caminho;

        } else {
            echo "Erro ao carrega a view solicitada<br>";
            echo "Caminho: " . $caminho;
        }
    }

    protected function usuarioEstaLogado() {
        session_start();

        if(! isset($_SESSION[SESSAO_USUARIO_ID])) {
            header("location: " . LOGIN_PAGE);
            return false;
        }

        return true;
    }

    protected function getIdUsuarioLogado() {
        if(session_status() != PHP_SESSION_ACTIVE)
            session_start();
        
        if(isset($_SESSION[SESSAO_USUARIO_ID]))
            return $_SESSION[SESSAO_USUARIO_ID];

        return 0;
    }

    protected function usuarioLogadoPapelAdmin() {
        if(session_status() != PHP_SESSION_ACTIVE)
            session_start();
        
        if(isset($_SESSION[SESSAO_USUARIO_PAPEL]))
            return $_SESSION[SESSAO_USUARIO_PAPEL] == UsuarioPapel::ADMINISTRADOR;

        return false;
    }


}