<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\pageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // o primeiro parametro / indica que nâo foi passado nemhuma rota ele entra e carrega a pagina principal
    
	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function() { // o primeiro parametro / indica que nâo foi passado nemhuma rota ele entra e carrega a pagina principal
    
	User::verifyLogin();

	$page = new pageAdmin();

	$page->setTpl("index");

});

$app->get('/admin/login' , function() {

	$page = new pageAdmin([

		"header"=>false,
		"footer"=>false

	]);
	$page->setTpl("login");

});

$app->post('/admin/login', function() {  //rota a qual faz todas os passos para dps entrar na parte admin 

		User::login($_POST["login"], $_POST["password"]);

		header("Location: /admin");
		exit;
});

$app->get('/admin/logout', function() {

	User::logout();

	header("Location: /admin/login");
	exit;

});

$app->run();

 ?>