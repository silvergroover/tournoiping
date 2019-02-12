<?php

include_once ('include/Smartping.inc');

// create object session
$api = new Service('SW021', 'Hd125pYK04');

if (empty($_SESSION['serial'])) {
	$_SESSION['serial'] = Service::generateSerial();
}
$api->setSerial($_SESSION['serial']);

//initialize connection
$api->initialization();

// Info parties du joueur

?>
<h3>infos Parties du joueur</h3>
<?php 
$JoueurParties = $api->getJoueurParties('3532025');
?>
<table>
  <tr>
    <th>date</th>
    <th>adversaire</th>
    <th>Classement</th>
    <th>Résultat</th>
    <th>points</th>
    <th>coeff</th>
  </tr>

<?php 
foreach ($JoueurParties as $cletab=>$valtab) {
?>
  <tr>
    <td><?php echo $valtab['date']?></td>
    <td><?php echo $valtab['advnompre']?></td>
    <td><?php echo $valtab['advclaof']?></td>
    <td><?php echo $valtab['vd']?></td>
    <td><?php echo $valtab['pointres']?></td>
    <td><?php echo $valtab['coefchamp']?></td>
  </tr>
<?php 	
echo "<pre>";
print_r($cletab);
echo "</pre>";
}
?>
</table>
<?php 	

echo "<pre>";
print_r($JoueurParties);
echo "</pre>";
?>

<h3>infos Parties du joueur</h3>
<?php 
$JoueurParties = $api->getJoueurPartiesSpid('3528756');
?>
<table>
  <tr>
    <th>date</th>
    <th>adversaire</th>
    <th>Classement</th>
    <th>Résultat</th>
    <th>points</th>
    <th>coeff</th>
  </tr>

<?php 
foreach ($JoueurParties as $cletab=>$valtab) {
?>
  <tr>
    <td><?php echo $valtab['date']?></td>
    <td><?php echo $valtab['nom']?></td>
    <td><?php echo $valtab['classement']?></td>
    <td><?php echo $valtab['victoire']?></td>
    <td><?php echo $valtab['forfait']?></td>
    <td><?php echo $valtab['epreuve']?></td>
  </tr>
<?php 	
}
?>
</table>
<?php 	

echo "<pre>";
print_r($JoueurParties);
echo "</pre>";
?>
