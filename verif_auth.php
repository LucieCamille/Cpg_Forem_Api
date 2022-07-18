<?php
//démarrer session
session_start();


//Vérification authentification

//vérifier le token car pour post, put et delete, on aura besoin de ce token
if($_SERVER['REQUEST_METHOD'] != 'GET') :
  //heure actuelle
  $now = time();
  //si je n'ai pas de user ou que session expirée, alors access denied
  if(!isset($_SESSION['user']) OR $now > $_SESSION['expiration']) :
    unset($_SESSION['user']);
    unset($_SESSION['token']);
    unset($_SESSION['expiration']);
    $response['response'] = "Expired token";
    $response['code'] = 403;
    echo json_encode($response);
    die();
  else :
    //récupérer un file json (qui contient le token)
    $json = file_get_contents('php://input');
    //decoder le file json en php, en mode array et non en objet
    $arrayJSON = json_decode($json, true);
    //si le token pas correct, alors erreur
    if($arrayJSON['token'] != $_SESSION['token']) :
      $response['response'] = "Token wrong";
      $response['code'] = 401;
      echo json_encode($response);
      die();
    endif;
  endif;
  $_SESSION['expiration'] = time() + 1 * 600;
endif;