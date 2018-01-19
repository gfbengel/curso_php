<?php

    class Usuario{

        private $id;
        private $login;
        private $pass;
        private $dataCadastro;

        public function getId(){
            return $this->id;
        }
        public function setId($value){
            $this->id = $value;
        }

        public function getLogin(){
            return $this->login;
        }
        public function setLogin($value){
            $this->login = $value;
        }

        public function getPass(){
            return $this->pass;
        }
        public function setPass($value){
            $this->pass = $value;
        }

        public function getDataCadastro(){
            return $this->dataCadastro;
        }
        public function setDataCadastro($value){
            $this->dataCadastro = $value;
        }




        public function setDados($dados){
            $this->setId($dados['id']);
            $this->setLogin($dados['login']);
            $this->setPass($dados['pass']);
            $this->setDataCadastro(new DateTime($dados['dataCadastro']));
        }




        public function login($login, $pass){
            
            $sql = new Sql();
            $results = $sql->select("SELECT * FROM users WHERE login = :LOGIN AND pass = :PASS;", array(
                ":LOGIN" => $login,
                ":PASS" => $pass
            ));

            if (count($results) > 0){

                $this->setDados($results[0]);

            } else{
                throw new Exception("Login e/ou senha incorretos.");
            }
            
        }

        public function insert(){

            $sql = new Sql();

            $results = $sql->select("CALL sp_users_insert(:LOGIN, :PASS)", array(
                ':LOGIN' => $this->getLogin(),
                ':PASS' => $this->getPass()
            ));
            if (count($results) > 0){

                $this->setDados($results[0]);

            } 


        }

        public function update($login, $pass){

            $this->setLogin($login);
            $this->setPass($pass);

            $sql = new Sql();

            $sql->query("UPDATE users SET login = :LOGIN, pass = :PASS WHERE id = :ID;", array(
                ':LOGIN' => $this->getLogin(),
                ':PASS' => $this->getPass(),
                ':ID' => $this->getId()
            ));

        }

        public function delete(){
            $sql = new Sql();

            $sql->query("DELETE FROM users WHERE id = :ID;",array(
                ':ID' => $this->getId()
            ));

             $this->setDados(array(
                'id' => 0,
                'login' => '',
                'pass' => '',
                'dataCadastro' => ''
             ));
        }

        public function loadById($id){

            $sql = new Sql();
            $results = $sql->select("SELECT * FROM users WHERE id = :ID;", array(":ID"=>$id));

            if (count($results) > 0){

                $this->setDados($results[0]);

            }

        }




        public static function getList(){

            $sql = new Sql();

            return $sql->select("SELECT * FROM users ORDER BY login;");

        }

        public static function search($login){

            $sql = new Sql();

            return $sql->select("SELECT * FROM users WHERE login LIKE :LOGIN ORDER BY login;", array(":LOGIN" => "%".$login."%"));

        }




        public function __construct($login = "", $pass = ""){
            $this->setLogin($login);
            $this->setPass($pass);
        }

        public function __tostring(){

            return json_encode(array(
                "id" => $this->getId(),
                "login" => $this->getLogin(),
                "pass" => $this->getPass(),
                "dataCadastro" => $this->getDataCadastro()->format('d/m/Y H:i:s')
            ));
        }

    }