<HEAD>
<meta charset="UTF-8">
</HEAD>

<body>
<?php

include_once ('include/SmartpingDAO.php');
$dao = new SmartpingDAO ();
  
$annee = date("Y");
$tableaux = $dao->getJoueursTournoi($annee);
    
foreach ( $tableaux as $tab ) {
    echo $tab['licence'].";".$tab['nom'].";".$tab['prenom'].";".$tab['email'].";".$tab['tel'].";".$tab['date_inscription'].";".$tab['club'].";".$tab['tableau'].";</br>";
}
?>
</body>
