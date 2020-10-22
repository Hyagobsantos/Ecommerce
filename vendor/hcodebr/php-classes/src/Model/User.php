<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

    const SESSION = "User";

    public static function login($login, $password)
    {

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN" , array(

           ":LOGIN"=>$login

        ));

        if (count($results) === 0)  //verificando se retornou algum login 
        {
            throw new \Exception("Usuario Inexistente ou Senha inválida.");
            
        }

        $data = $results[0]; //dps que passou pela validação eu atribui a variavel $data o resultado do primeiro indice encontrado 

        if (password_verify($password, $data["despassword"]) === true) //verificando a senha hash do banco pra saber se é a mesma senha 
        {

            $user = new User();  //a partir desse ponto dps de verificado eu preciso dos dados do usuario, para isso é preciso dos gets e sets desse usuario
                                //ao inves de fazer isso vou fazer o import dinamicamente e não estaticamente criando a partir daqui um atributo private e conseguentimente um get e set para cada um 

            $user->setData($data); //vem da classe model que sabe criar meus sets e gets do usuario retornado

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;



        } else {
            throw new \Exception("Usuario Inexistente ou Senha inválida.");
        }




    } 

    public static function verifyLogin($inadmin = true)
    {

        if (
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
        ) {
            header("Location: /admin/login");
            exit;
        }

    }

    public static function logout()
    {
        $_SESSION[User::SESSION] = NULL;
    }


}


?>