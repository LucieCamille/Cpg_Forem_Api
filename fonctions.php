<?php 
//cette fonction de print_r (print text, non json) sera bien utile en mode dev. Dès qu'on passe en prod, comme la fnct est variabilisée, elle ne s'appliquera plus nul part (et évite d'oublier un print_r qq part)
function myPrint_r($value) {
  if(MODE == 'dev') :
  echo '<pre>';
    print_r($value);
  echo '</pre>';
  endif;
};
?>