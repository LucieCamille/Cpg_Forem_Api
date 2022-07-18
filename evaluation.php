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
  if(isset($_GET['id_user'])) :
    $sql = sprintf("SELECT * FROM evaluation WHERE id_user = %d", $_GET['id_user']);
    $response['response'] = 'Evaluation from user with id ' . $_GET['id_user'];

  else :
    $sql = "SELECT * FROM evaluation ORDER BY id_user ASC";
    $response['response'] = "All evaluation";
    
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
// //Pour tout ce qui gère la méthode POST
if($_SERVER['REQUEST_METHOD'] == 'POST') :
  //récupérer un file json
  $json = file_get_contents('php://input');
  //decoder le file json en php en mode objet
  $arrayJson = json_decode($json, true);
  //définir la requête
  if(isset($_GET['id_training']) AND isset($_GET['id_user'])) :
    $sql = sprintf("INSERT INTO evaluation SET date_evaluation='%s', id_training=%s, id_user=%s, count=%d, analyse=%d, interest=%d, autonomy=%d, criticism=%d, organized=%d, motivation=%d",
      strip_tags(addslashes($arrayJSON['date_evaluation'])),
      $_GET['id_training'],
      $_GET['id_user'],
      strip_tags($arrayJSON['count']),
      strip_tags($arrayJSON['analyse']),
      strip_tags($arrayJSON['interest']),
      strip_tags($arrayJSON['autonomy']),
      strip_tags($arrayJSON['criticism']),
      strip_tags($arrayJSON['organized']),
      strip_tags($arrayJSON['motivation'])
    );
    //faire la requête
    $connect->query($sql);
    echo $connect->error;
    $response['response'] = "New evaluation for user " . $_GET['id_user'] . " for training with id" . $_GET['id_training'];
    $response['new_id'] = $connect->insert_id;
  else : 
    $response['response'] = "id training and/or id user is missing";
    $response['code'] = 500;
  endif;
endif; //fin méthode POST

// //-------------------------------------
// //Pour tout ce qui gère la méthode PUT
// if($_SERVER['REQUEST_METHOD'] == 'PUT') :
//   //récupérer un file json
//   $json = file_get_contents('php://input');
//   //decoder le file json en php, en mode array et non en objet
//   $arrayJSON = json_decode($json, true);

//   //si on a le label:
//   if(isset($arrayJSON['label'])) :
//     $sql = sprintf("UPDATE category SET label='%s' WHERE id_category=%d",
//       addslashes($arrayJSON['label']),
//       $_GET['id_category']
//     );
//     $connect->query($sql);
//     echo $connect->error;
//     $response['new_data'] = $arrayJSON;
//     $response['response'] = "Edit category with id" . $_GET['id_category'];
//   //on n'a pa le label
//   else: 
//     $response['response'] = "Label is missing";
//     $response['code'] = 500;
//   endif;
// endif; //fin méthode PUT

// //----------------------------------------
// //Pour tout ce qui gère la méthode DELETE
// if($_SERVER['REQUEST_METHOD'] == 'DELETE') :
//   //vérifier si l'id existe
//   if(isset($_GET['id_category'])) :
//     //test si il y a des produits. Si oui on ne peut la supprimer
//     $sql = sprintf("SELECT * FROM product WHERE id_category=%d",
//         $_GET['id_category']
//     );
//     $result = $connect->query($sql);
//     //si la catégorie n'a pas de produit
//     if($result->num_rows == 0):
//         $sql = sprintf("DELETE FROM category WHERE id_category=%d",
//             $_GET['id_category']
//         );
//         $connect->query($sql);
//         echo $connect->error;
//         $response['response'] = "Delete category id {$_GET['id_category']}";
//     else :
//     //si la catégorie a des produits
//         $response['response'] = "There are products in this category, you need to delete them first";
//         $response['code'] = 500;
//     endif;
//   else :
//         //si on n'a pas l'id de la catégorie
//         $response['response'] = "Id is missing";
//         $response['code'] = 500;
//   endif;

// endif; //fin de la methode DELETE


$response['code'] = (isset($response['code']) ) ? $response['code'] : 200;
$response['time'] = date('Y-m-d,H:i:s');

//mettre le résultat (peut importe la méthode) en format json
echo json_encode($response);