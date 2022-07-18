<?php
include 'config.php';
include 'headers.php';
session_start();

$response['script'] = 'auth_api.php';

//déconnection si utilisateur clique sur se déconnecter
if(isset($_GET['delog'])) :
  //supprimer la variable user et token dans la session
  unset($_SESSION['user']);
  unset($_SESSION['token']);
  unset($_SESSION['expiration']);
  $response['response'] = "deconnected";
  $response['code'] = 200;
  $response['time'] = date('Y-m-d,H:i:s');
  //mettre le résultat (peut importe la méthode) en format json
  echo json_encode($response);
  exit;
endif;

//connection
//récupérer un file json
$json = file_get_contents('php://input');
//decoder le file json en php, en mode array et non en objet
$arrayPOST = json_decode($json, true);

if ( !isset($arrayPOST['email']) OR !isset($arrayPOST['password'])) :
  $response['message'] = "Email or password is missing";
  $response['code'] = 500;   

else:
  //définir requete
  $sql = sprintf("SELECT * FROM user WHERE email = '%s' AND password = '%s'",
    $arrayPOST['email'],
    $arrayPOST['password']
  );
  //lancer requete
  $result = $connect->query($sql);
  echo $connect->error;
  $nb_rows =  $result->num_rows;
  //contrôler si champs remplis correspondent à DB:
  if($nb_rows == 0) :
    $response['message'] = 'email or password error';
    $response['code'] = 403;
  else :
    $row = $result->fetch_all(MYSQLI_ASSOC);
    $row = $row[0];
    //on garde les datas pendant toute la session jusqu'au moment où user ferme navigateur ou si script tue session:
    $_SESSION['user'] = $row['id_user'];
    //utiliser un token pour crypter le login du user via chaine md5 qu'on peut complexifier en ajoutant l'id car inconnudu user + time car ça le change à chaque connection
    $_SESSION['token'] = md5($row['email'].time());
    //définir durée expiration token (1minute)
    $_SESSION['expiration'] = time() + 1 * 600;
    $response['response'] = "Connected";
    $response['token'] = $_SESSION['token'];
    $response['id_user'] = $_SESSION['user'];
    
  endif;
endif;

$response['code'] = ( isset($response['code']) ) ? $response['code'] : 200;

echo json_encode($response);
exit;

?>