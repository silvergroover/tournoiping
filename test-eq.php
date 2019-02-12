<?php

//include_once ('include/Smartping.inc');
include_once ('include/SmartpingDAO.php');

$dao = new SmartpingDAO();
// create object session
$api = new Service('SW021', 'Hd125pYK04');

if (empty($_SESSION['serial'])) {
	$_SESSION['serial'] = Service::generateSerial();
}
$api->setSerial($_SESSION['serial']);

//initialize connection
$api->initialization();

$annee = date("Y");
?>

<h3>infos equipes</h3>
<?php 
$equm = $api->getEquipesByClub('07350021','M');
$equj = $api->getEquipesByClub('07350021','J');
	echo "<pre>";
	print_r($equj);
	echo "</pre>";
$equ = array_merge ( $equm, $equj );

	echo "<pre>";
	print_r($equ);
	echo "</pre>";
	
$saison = $dao->getSaison();
echo $saison;
foreach ($equ as $cequ=>$vequ) {
		set_time_limit(60);	
	
	echo "####################################################</br>";
	echo "<pre>";
	print_r($vequ);
	echo "</pre>";

//	echo $vequ['libequipe'];
//	echo $vequ['libdivision'];
//	echo $vequ['liendivision'];
//	echo $vequ['idepr'];
//	echo $vequ['idpoule'];
//	echo $vequ['iddiv'];
//	echo $annee;
	
	$dao->insererEquipe($vequ['libequipe'],$vequ['libdivision'],$vequ['liendivision'],$vequ['idepr'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison);
	
	$pouleclass = $api->getPouleClassement($vequ['iddiv'],$vequ['idpoule']);
//	echo "pouleclass <pre>";
//	print_r($pouleclass);
//	echo "</pre>";

	foreach ($pouleclass as $cclt=>$vclt) {
	
//	$dao->insererPouleClt($vclt['poule'],$vclt['clt'],$vclt['equipe'],$vclt['joue'],$vclt['pts'],$vclt['numero'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison);
	
	}

	$poulerenc = $api->getPouleRencontres($vequ['iddiv'],$vequ['idpoule']);

	echo "poulerenc <pre>";
	print_r($poulerenc);
	echo "</pre>";

	
	foreach ($poulerenc as $crenc=>$vrenc) {
		if ( is_array($vrenc['scorea'])) {
			$scorea = 0;		
		}
		else 
		{ 
			$scorea =$vrenc['scorea'];
		}
		if ( is_array($vrenc['scoreb'])) {
			$scoreb = 0;		
		}
		else {
			$scoreb =$vrenc['scoreb'];
		}
	$detailrenc = $api->getRencontre($vrenc['lien']);

//	echo "rencontre <pre>";
//	print_r($detailrenc);
//	echo "</pre>";

	foreach ($detailrenc as $cdrenc=>$vdrenc) {
		if ( $cdrenc == "joueur") {
			foreach ($vdrenc as $joueur) {
				echo "joueur <pre>";
				print_r($joueur);
				echo "</pre>";
				$dao->insererchpJoueur($vrenc['lien'],str_replace("'","\'",$joueur['xja']),str_replace("'","\'",$joueur['xjb']),$joueur['xca'],$joueur['xcb'],$saison);
			}
		}
		elseif ( $cdrenc == "partie") {
			foreach ($vdrenc as $renc) {
				$dao->insererchpRenc($vrenc['lien'],str_replace("'","\'",$renc['ja']),str_replace("'","\'",$renc['jb']),$renc['scorea'],$renc['scoreb'],$saison);
				echo "detail rencontre <pre>";
				print_r($renc);
				echo "</pre>";
			}
		}
	}
		$dao->insererPouleRenc(str_replace("'","\'",$vrenc['libelle']),str_replace("'","\'",$vrenc['equa']),str_replace("'","\'",$vrenc['equb']),$scorea,$scoreb,$vrenc['lien'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison);
//		$dao->insererchpRenc($vrenc['libelle'],$vrenc['equa'],$vrenc['equb'],$scorea,$scoreb,$vrenc['lien'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison);
		//insererPouleRenc ($libelle,$equa,$equb,$scorea,$scoreb,$lien,$idpoule,$iddiv,$annee)	
	}
}
?>
