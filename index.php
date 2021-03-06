<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\pageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;


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

//inicio de rotas da parte do crud

$app->get("/admin/users", function(){

	User::verifyLogin();

	$users = User::listAll();

	$page = new pageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));

});

$app->get("/admin/users/create", function(){

	User::verifyLogin();

	$page = new pageAdmin();

	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;


});

$app->get("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new pageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});

$app->post("/admin/users/create", function() {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

$app->post("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});


$app->get("/admin/forgot", function(){

	$page = new pageAdmin([

		"header"=>false,
		"footer"=>false

	]);
	$page->setTpl("forgot");

});


$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new pageAdmin([

		"header"=>false,
		"footer"=>false

	]);
	$page->setTpl("forgot-sent");

});


$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new pageAdmin([

		"header"=>false,
		"footer"=>false

	]);
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));



});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>10
	]);

	$user->setPassword($password);

	$page = new pageAdmin([

		"header"=>false,
		"footer"=>false

	]);
	$page->setTpl("forgot-reset-success");


});

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new pageAdmin();

	$page->setTpl("categories", [
		'categories'=>$categories
	]);

});


$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new pageAdmin();

	$page->setTpl("categories-create");

});


$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('location: /admin/categories');
	exit;

});

$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new pageAdmin();

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header('location: /admin/categories');
	exit;

});

$app->get("/categories/:idcategory", function($idcategory){

		$category = new Category();

		$category->get((int)$idcategory);

		$page = new Page();

		$page->setTpl("category", [
			'category'=>$category->getValues(),
			'products'=>[]
		]);



});









//fim rotas parte crud

$app->run();

 ?>