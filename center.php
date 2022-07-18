<?php
//récupérer la connection
include 'config.php';
//récupérer les headers (qui peut se connecter et comment)
include 'headers.php';
//récupérer la vérification de l'authentification
include "verif_auth.php";

//Pour tout ce qui gère la méthode GET:
if($_SERVER['REQUEST_METHOD'] == 'GET') :
  //définir ma requete
  if(isset($_GET['id_center'])) :
    $sql = sprintf("SELECT * FROM `center` WHERE id_center = %d", $_GET['id_center']);
    $response['response'] = 'A center with id ' . $_GET['id_center'];

  else :
    //récup tous les post
    $sql = sprintf("SELECT * FROM `center`  ORDER BY organisation ASC");
    $response['response'] = "All center";
    
  endif;
  //faire la requete
  $result = $connect->query($sql);
  echo $connect->error;

  //récupérer le résultat (result), qui est un array associatif, dans 'data' d'un array normal(response)
  $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
  //afficher le nombre d'entrées
  $response['nb_hits'] = $result->num_rows;
endif; //fin de la methode GET


//----------------------------------------
// //Pour tout ce qui gère la méthode POST d'un post
if($_SERVER['REQUEST_METHOD'] == 'POST') :
  //récupérer un file json
  $json = file_get_contents('php://input');
  //decoder le file json en php en mode objet
  $objectPost = json_decode($json);
  //définir la requête
  $sql = sprintf("INSERT INTO center SET organisation='%s', location='%s'",
    strip_tags(addslashes($objectPost->organisation)),
    isset($objectPost->location) ? strip_tags(addslashes($objectPost->location)) : 'NULL'
  );
  //faire la requête
  $connect->query($sql);
  echo $connect->error;
  $response['response'] = "New center with new id";
  $response['new_id'] = $connect->insert_id;
endif; //fin méthode POST

//-------------------------------------
//Pour tout ce qui gère la méthode PUT
if($_SERVER['REQUEST_METHOD'] == 'PUT') :
  //récupérer un file json
  $json = file_get_contents('php://input');
  //decoder le file json en php, en mode array et non en objet
  $arrayJSON = json_decode($json, true);

  //si on a le label:
  if(isset($arrayJSON['organisation'])) :
    $sql = sprintf("UPDATE center SET organisation='%s', location='%s' WHERE id_center=%d",
      addslashes($arrayJSON['organisation']),
      isset($objectPost->location) ? strip_tags(addslashes($objectPost->location)) : 'NULL',
      $_GET['id_center']
    );
    $connect->query($sql);
    echo $connect->error;
    $response['new_data'] = $arrayJSON;
    $response['response'] = "Edit center name and/or location with id" . $_GET['id_job'];
  //on n'a pa le label
  else: 
    $response['response'] = "Center is missing";
    $response['code'] = 500;
  endif;
endif; //fin méthode PUT

//------------------------------------------
// //Pour tout ce qui gère la méthode DELETE
if($_SERVER['REQUEST_METHOD'] == 'DELETE') :
  //vérifier si l'id existe
  if(isset($_GET['id_center'])) :
     $sql = sprintf("DELETE FROM center WHERE id_center= %d",
        $_GET['id_center']);
     $connect->query($sql);
     echo $connect->error;
     $response['response'] = "Deleted center with id" . $_GET['id_center'];
   else :
     $response['response'] = 'id center is missing';
     $response['code'] = 500;
   endif;

endif; //fin de la methode DELETE


$response['code'] = (isset($response['code']) ) ? $response['code'] : 200;
$response['time'] = date('Y-m-d,H:i:s');

//mettre le résultat (peut importe la méthode) en format json
echo json_encode($response);