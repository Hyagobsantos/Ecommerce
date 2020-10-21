<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() { // o primeiro parametro / indica que nâo foi passado nemhuma rota ele entra e carrega a pagina principal
    
	$page = new Page();

	$page->setTpl("index");

});

$app->run();

 ?>