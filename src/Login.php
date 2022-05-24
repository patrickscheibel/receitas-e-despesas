<?php
class Login {
    private $atributos;
 
    public function __construct() { }
 
    public function __set(string $atributo, $valor) {
        $this->atributos[$atributo] = $valor;
        return $this;
    }
 
    public function __get(string $atributo) {
        return $this->atributos[$atributo];
    }
 
    public function __isset($atributo) {
        return isset($this->atributos[$atributo]);
    }
 
    /**
     * Salvar o categoria
     * @return boolean
     */
    public function save() {
        $colunas = $this->preparar($this->atributos);
        if (!isset($this->id)) {
            $query = "INSERT INTO login (".
                implode(', ', array_keys($colunas)).
                ") VALUES (".
                implode(', ', array_values($colunas)).");";
        } else {
            foreach ($colunas as $key => $value) {
                if ($key !== 'id') {
                    $definir[] = "{$key}={$value}";
                }
            }
            $query = "UPDATE login SET ".implode(', ', $definir)." WHERE id='{$this->id}';";
        }
        if ($conexao = Conexao::getInstance()) {
            $stmt = $conexao->prepare($query);
            if ($stmt->execute()) {
                return $stmt->rowCount();
            }
        }
        return false;
    }
 
    /**
     * Tornar valores aceitos para sintaxe SQL
     * @param type $dados
     * @return string
     */
    private function escapar($dados) {
        if (is_string($dados) & !empty($dados)) {
            return "'".addslashes($dados)."'";
        } elseif (is_bool($dados)) {
            return $dados ? 'TRUE' : 'FALSE';
        } elseif ($dados !== '') {
            return $dados;
        } else {
            return 'NULL';
        }
    }
 
    /**
     * Verifica se dados são próprios para ser salvos
     * @param array $dados
     * @return array
     */
    private function preparar($dados) {
        $resultado = array();
        foreach ($dados as $k => $v) {
            if (is_scalar($v)) {
                $resultado[$k] = $this->escapar($v);
            }
        }
        return $resultado;
    }
 
    /**
     * Retorna uma lista de categoria
     * @return array/boolean
     */
    public static function all() {
        $conexao = Conexao::getInstance();
        $stmt    = $conexao->prepare("SELECT * FROM login;");
        $result  = array();
        if ($stmt->execute()) {
            while ($rs = $stmt->fetchObject(Login::class)) {
                $result[] = $rs;
            }
        }
        if (count($result) > 0) {
            return $result;
        }
        return false;
    }
 
    /**
     * Retornar o número de registros
     * @return int/boolean
     */
    public static function count() {
        $conexao = Conexao::getInstance();
        $count   = $conexao->exec("SELECT count(*) FROM login;");
        if ($count) {
            return (int) $count;
        }
        return false;
    }
 
    /**
     * Encontra um recurso pelo id
     * @param type $id
     * @return type
     */
    public static function find($id) {
        $conexao = Conexao::getInstance();
        $stmt    = $conexao->prepare("SELECT * FROM login WHERE id='{$id}';");
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $resultado = $stmt->fetchObject('Login');
                if ($resultado) {
                    return $resultado;
                }
            }
        }
        return false;
    }

        /**
     * Encontra um recurso pelo name
     * @param type $name
     * @return type
     */
    public static function testLogin($username, $password) {
        $conexao = Conexao::getInstance();
        $stmt    = $conexao->prepare("SELECT * FROM login WHERE username='{$username}' and password='{$password}';");
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $resultado = $stmt->fetchObject('Login');
                if ($resultado) {
                    return $resultado;
                }
            }
        }
        return false;
    }

    /**
     * Encontra o id do ultimo registro
     * @return type
     */
    public static function findLastById() {
        $conexao = Conexao::getInstance();
        $stmt    = $conexao->prepare("SELECT * FROM login WHERE id=(SELECT max(id) FROM login);");
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $resultado = $stmt->fetchObject('Login');
                if ($resultado) {
                    return $resultado;
                }
            }
        }
        return false;
    }
 
    /**
     * Destruir um recurso
     * @param type $id
     * @return boolean
     */
    public static function destroy($id) {
        $conexao = Conexao::getInstance();
        if ($conexao->exec("DELETE FROM login WHERE id='{$id}';")) {
            return true;
        }
        return false;
    }
}

?>