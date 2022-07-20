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
    //récupérer une formation par l'id du job
    $sql = sprintf("SELECT training.*, job.*, center.id_center, center.organisation, user.id_user, user.id_status, user.name, user.firstname FROM `training` JOIN job ON training.id_job = job.id_job JOIN center ON training.id_center = center.id_center JOIN user ON training.id_instructor = user.id_user WHERE training.id_job = %d", $_GET['id_job']);
    $response['response'] = 'A training infos with job id ' . $_GET['id_job'];
  
  elseif(isset($_GET['id_training'])) :
    //récupérer une formation par l'id du training
    $sql = sprintf("SELECT training.*, job.*, center.id_center, center.organisation, user.id_user, user.id_status, user.name, user.firstname FROM `training` JOIN job ON training.id_job = job.id_job JOIN center ON training.id_center = center.id_center JOIN user ON training.id_instructor = user.id_user WHERE training.id_training = %d", $_GET['id_training']);
    $response['response'] = 'A training infos with job id ' . $_GET['id_training'];

  else :
    //récup toutes les formations
    $sql = sprintf("SELECT training.*, job.*, center.id_center, center.organisation, user.id_user, user.id_status, user.name, user.firstname FROM `training` JOIN job ON training.id_job = job.id_job JOIN center ON training.id_center = center.id_center JOIN user ON training.id_instructor = user.id_user ORDER BY job.title ASC");
    $response['response'] = "All training with there related job";
    
  endif;
  //faire la requete
  $result = $connect->query($sql);
  echo $connect->error;

  //récupérer le résultat (result), qui est un array associatif, dans 'data' d'un array normal(response)
  $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
  //afficher le nombre d'entrées
  $response['nb_hits'] = $result->num_rows;
endif; //fin de la methode GET

//------------------------------------------
// //Pour tout ce qui gère la méthode POST d'un comment
if($_SERVER['REQUEST_METHOD'] == 'POST') :
  //récupérer un file json
  $json = file_get_contents('php://input');
  //decoder le file json en php en mode objet
  $objectPost = json_decode($json);
  //requete
  $sql = sprintf("INSERT INTO training SET id_job=%d, label='%s', start_date='%s', id_center=%s, id_instructor=%s",
    strip_tags(addslashes($objectPost->id_job)),
    strip_tags(addslashes($objectPost->label)),
    strip_tags(addslashes($objectPost->start_date)),
    strip_tags(addslashes($objectPost->id_center)),
    strip_tags(addslashes($objectPost->id_instructor))
  );
  //faire la requête
  $connect->query($sql);
  echo $connect->error;
  $response['response'] = "New training";
  $response['new_id'] = $connect->insert_id;

endif; //fin méthode POST

// //----------------------------------------
// // //Pour tout ce qui gère la méthode PUT
// if($_SERVER['REQUEST_METHOD'] == 'PUT') :
//   //récupérer un file json
//   $json = file_get_contents('php://input');
//   //decoder le file json en php, en mode array et non en objet
//   $arrayJSON = json_decode($json, true);
//   //si on a l'id du produit, le 'label' et le 'prix'
//   if( isset($arrayJSON['name']) AND isset($arrayJSON['price']) AND $_GET['id_product']) :
//     $sql = sprintf("UPDATE product SET name='%s', price=%d, id_category=%s WHERE id_product=%d",
//       strip_tags(addslashes($arrayJSON['name'])),
//       strip_tags(addslashes($arrayJSON['price'])),
//       isset($arrayJSON['id_category']) ? strip_tags($arrayJSON['id_category']) : 'NULL',
//       $_GET['id_product']
//     );
//     $result = $connect->query($sql);
//     echo $connect->error;
//     $response['new_data'] = $arrayJSON;
//     $response['response'] = "Edit product and id_category with id_product" . $_GET['id_product'];
//   else:
//     $response['response'] = "Id or product is missing";
//     $response['code'] = 500;
//   endif;
// endif; //fin méthode PUT

// //------------------------------------------
// // //Pour tout ce qui gère la méthode DELETE
// if($_SERVER['REQUEST_METHOD'] == 'DELETE') :
//   //vérifier si l'id existe
//   if(isset($_GET['id_product'])) :
//      $sql = sprintf("DELETE FROM product WHERE id_product= %d",
//         $_GET['id_product']);
//      $connect->query($sql);
//      echo $connect->error;
//      $response['response'] = "Deleted person with id" . $_GET['id_product'];
//    else :
//      $response['response'] = 'Id is missing';
//      $response['code'] = 500;
//    endif;

// endif; //fin de la methode DELETE


$response['code'] = (isset($response['code']) ) ? $response['code'] : 200;
$response['time'] = date('Y-m-d,H:i:s');

//mettre le résultat (peut importe la méthode) en format json
echo json_encode($response);