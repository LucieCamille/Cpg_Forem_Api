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
  if(isset($_GET['id_job'])) :
    $sql = sprintf("SELECT * FROM `job` WHERE id_job = %d", $_GET['id_job']);
    $response['response'] = 'A job with id ' . $_GET['id_job'];

  else :
    //récup tous les post
    $sql = sprintf("SELECT * FROM `job`  ORDER BY title ASC");
    $response['response'] = "All job";
    
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
  $sql = sprintf("INSERT INTO job SET title='%s'",
    strip_tags(addslashes($objectPost->title))
  );
  //faire la requête
  $connect->query($sql);
  echo $connect->error;
  $response['response'] = "New job with new id";
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
  if(isset($arrayJSON['title'])) :
    $sql = sprintf("UPDATE job SET title='%s' WHERE id_job=%d",
      addslashes($arrayJSON['title']),
      $_GET['id_job']
    );
    $connect->query($sql);
    echo $connect->error;
    $response['new_data'] = $arrayJSON;
    $response['response'] = "Edit job title with id" . $_GET['id_job'];
  //on n'a pa le label
  else: 
    $response['response'] = "Job title is missing";
    $response['code'] = 500;
  endif;
endif; //fin méthode PUT

//------------------------------------------
// //Pour tout ce qui gère la méthode DELETE
if($_SERVER['REQUEST_METHOD'] == 'DELETE') :
  //vérifier si l'id existe
  if(isset($_GET['id_job'])) :
     $sql = sprintf("DELETE FROM job WHERE id_job= %d",
        $_GET['id_job']);
     $connect->query($sql);
     echo $connect->error;
     $response['response'] = "Deleted job with id" . $_GET['id_job'];
   else :
     $response['response'] = 'id job is missing';
     $response['code'] = 500;
   endif;

endif; //fin de la methode DELETE


$response['code'] = (isset($response['code']) ) ? $response['code'] : 200;
$response['time'] = date('Y-m-d,H:i:s');

//mettre le résultat (peut importe la méthode) en format json
echo json_encode($response);