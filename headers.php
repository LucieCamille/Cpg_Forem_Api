<?php
//test allowheaders
header("Access-Control-Allow-Headers: Date,Server,X-Powered-By,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Set-Cookie,Expires,Cache-Control,Pragma,Content-Length,Keep-Alive,Connection,Content-Type");
//n'import quel script peut interoger mon api
header("Access-Control-Allow-Origin:*");
//quelles sont les methodes que j'accepte - mais parfois fonctionne sans ce header dans certains navigateurs
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//les retour du server seront en json (sinon js va recuperer des datas en format texte) - idem
header("Content-Type:application/json");