<?php 
session_start();
$page_title = "abu carrinho";
require_once __DIR__ . '/app/config/config.php';

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/navbar.php';

if(!isset($_SESSION['carrinho'])){
		$_SESSION['carrinho']=array();
		
		}
	if(isset($_GET['acao'])){
		
		// ADICIONAR AO CARRINHO 
		
		if($_GET['acao'] == 'add' ){
			
			$id= intval($_GET['id']);
			
			if(!isset($_SESSION['carrinho'][$id])) {
						
			$_SESSION['carrinho'][$id] = 1;

		}else{
			
		$_SESSION['carrinho'][$id] += 1;
			
			}
		
		}
		
		
		// REMOVER do CARRINHO
		
		if($_GET ['acao'] == 'del'){
			$id= intval ($_GET['id']);
			if(isset($_SESSION['carrinho'][$id])){
				unset($_SESSION['carrinho'][$id]);
			
		}
		
		}
		
		// ALTERAR QUANTIDADE DIRETO NA PÁGINA DE CARRINHO
		
		if($_GET['acao'] == 'up') {
			if(is_array($_POST['prod'])){
				foreach($_POST['prod'] as $id => $qtd){
					$id= intval($id);
					$qtd= intval($qtd);
					if(!empty($qtd) || $qtd <> 0){
							$_SESSION['carrinho'][$id] = $qtd;
							
					}else{
						unset($_SESSION['carrinho'][$id]);
						
					}
				}
				}
			
		}
			
	}


require_once BASE_PATH . '/components/carrinho/carrinho-hero.php';
require_once BASE_PATH . '/components/carrinho/carrinho-section.php';


require_once BASE_PATH . '/components/layout/footer.php';
?>