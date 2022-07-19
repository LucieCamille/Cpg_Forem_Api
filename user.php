<?php
//récupérer la connection
include 'config.php';
//récupérer les headers (qui peut se connecter et comment)
include 'headers.php';
//récupérer la vérification de l'authentification
include "verif_auth.php";

//Pour tout ce qui gère la méthode GET:
if($_SERVER['REQUEST_METHOD'] == 'GET') :
  //-------
  //définir requete pour un ou tous les user
  if(isset($_GET['id_user'])) :
    $sql = sprintf("SELECT user.*, status.* FROM `user` LEFT JOIN status ON user.id_status = status.id_status WHERE id_user = %d", $_GET['id_user']);
    $response['response'] = 'A user with id ' . $_GET['id_user'];

  else :
    //récup tous les post
    $sql = sprintf("SELECT user.*, status.* FROM `user` LEFT JOIN status ON user.id_status = status.id_status");
    $response['response'] = "All users by status";
    
  endif;
  //faire la requete
  $result = $connect->query($sql);
  echo $connect->error;
  //récupérer le résultat (result), qui est un array associatif, dans 'data' d'un array normal(response)
  $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
  //afficher le nombre d'entrées
  $response['nb_hits'] = $result->num_rows;
  
  //--------
  //faire un search de user en fonction du status
  if (isset($_GET['type'])) : 
    $sql = sprintf("SELECT user.*, status.* FROM `user` LEFT JOIN status ON user.id_status = status.id_status WHERE status.state = '%s'",
        $_GET['type']
    );
    //lancer requête
    $result = $connect->query($sql);
    if($result->num_rows > 0) :
      $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
      $response['nb_hits'] = $result->num_rows;
    else :
      $response['nb_hits'] = 0;
      $response['code'] = 404;
    endif;
  endif;

  //--------
  //faire un search de user en fonction du nom/prenom et du type
  if (isset($_GET['search']) AND isset($_GET['type'])) : 
    $sql = sprintf("SELECT user.*, status.* FROM `user` LEFT JOIN status ON user.id_status = status.id_status WHERE (user.name LIKE '%s%%' OR user.firstname LIKE '%s%%') AND status.state = '%s'",
        $_GET['search'],
        $_GET['search'],
        $_GET['type']
    );
    //lancer requête
    $result = $connect->query($sql);
    if($result->num_rows > 0) :
      $response['data'] = $result->fetch_all(MYSQLI_ASSOC);
      $response['nb_hits'] = $result->num_rows;
    else :
      $response['nb_hits'] = 0;
      $response['code'] = 404;
    endif;
  endif;

endif; //fin de la methode GET


//----------------------------------------
// //Pour tout ce qui gère la méthode POST d'un post
if($_SERVER['REQUEST_METHOD'] == 'POST') :
  //récupérer un file json
  $json = file_get_contents('php://input');
  //decoder le file json en php en mode objet
  $objectPost = json_decode($json);
  //définir la requête
  $sql = sprintf("INSERT INTO user SET email='%s', password='%s', name='%s', firstname='%s', id_status=%d",
    strip_tags(addslashes($objectPost->email)),
    strip_tags(addslashes($objectPost->password)),
    strip_tags(addslashes($objectPost->name)),
    strip_tags(addslashes($objectPost->firstname)),
    strip_tags(addslashes($objectPost->id_status))
  );
  //faire la requête
  $connect->query($sql);
  echo $connect->error;
  $response['response'] = "New user with new id";
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

//------------------------------------------
// //Pour tout ce qui gère la méthode DELETE
if($_SERVER['REQUEST_METHOD'] == 'DELETE') :
  //vérifier si l'id existe
  if(isset($_GET['id_user'])) :
     $sql = sprintf("DELETE FROM user WHERE id_user= %d",
        $_GET['id_user']);
     $connect->query($sql);
     echo $connect->error;
     $response['response'] = "Deleted person with id" . $_GET['id_user'];
   else :
     $response['response'] = 'Id of user is missing';
     $response['code'] = 500;
   endif;

endif; //fin de la methode DELETE


$response['code'] = (isset($response['code']) ) ? $response['code'] : 200;
$response['time'] = date('Y-m-d,H:i:s');

//mettre le résultat (peut importe la méthode) en format json
echo json_encode($response);