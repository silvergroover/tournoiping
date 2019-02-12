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
$equm = $api->getEquipesByClub('03350021','M');
$equj = $api->getEquipesByClub('03350021','J');
$equ = array_merge ( $equm, $equj );

	echo "Liste des equipes du club : <br/>";
	echo "<pre>";
	print_r($equ);
	echo "</pre><br/>";
	
$saison = $dao->getSaison();
echo $saison;
foreach ($equ as $cequ=>$vequ) {
		set_time_limit(60);	
	
	echo "####################################################</br>";
	echo "valeur equipe : <pre>";
	print_r($vequ);
	echo "</pre><br/>";

//	echo $vequ['libequipe'];
//	echo $vequ['libdivision'];
//	echo $vequ['liendivision'];
//	echo $vequ['idepr'];
//	echo $vequ['idpoule'];
//	echo $vequ['iddiv'];
	echo $vequ['type'];
//	echo $annee;
	
	if (preg_match("/- phase 1/i", $vequ['libequipe'])) { $phase = 1;} 
	elseif (preg_match("/- phase 2/i", $vequ['libequipe'])) { $phase = 2;} 
	else { $phase = 0;}
	
	$dao->insererEquipe($vequ['libequipe'],$vequ['libdivision'],$vequ['liendivision'],$vequ['idepr'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison,$phase,$vequ['type']);
	
	$pouleclass = $api->getPouleClassement($vequ['iddiv'],$vequ['idpoule']);
	echo "poule classement : <pre>";
	print_r($pouleclass);
	echo "</pre><br>";

	foreach ($pouleclass as $cclt=>$vclt) {
	
	$dao->insererPouleClt($vclt['poule'],$vclt['clt'],str_replace("'","\'",$vclt['equipe']),$vclt['joue'],$vclt['pts'],$vclt['numero'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison,$vclt['totvic'],$vclt['totdef'],$vclt['idequipe'],$vclt['idclub'],$vclt['vic'],$vclt['def'],$vclt['nul'],$vclt['pf'],$vclt['pg'],$vclt['pp']);
	
	}

	$poulerenc = $api->getPouleRencontres($vequ['iddiv'],$vequ['idpoule']);

	echo "<br/> poulerenc <pre>";
	print_r($poulerenc);
	echo "</pre><br/>";

	
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
		$lien = explode("&",$vrenc['lien']);
		echo "<br/> explode lien <pre>";
		print_r($lien);
		echo "</pre><br/>";
		foreach ($lien as $value) {
			print_r($value);
			$liendetail = explode("=",$value);
			if ($liendetail[0] == "renc_id") {
				$renc_id = $liendetail[1];
				echo "renc_id = $renc_id <br/>";
			}
			
			echo "<br/> explode liendetail <pre>";
			print_r($liendetail);
			echo "</pre><br/>";
		}
		
	$detailrenc = $api->getRencontre($vrenc['lien']);
	
	echo "rencontre <pre>";
	print_r($detailrenc);
	echo "</pre>";

	foreach ($detailrenc as $cdrenc=>$vdrenc) {
		set_time_limit(10);
		$i = 0;
		$clejoueur = 0;
		$clepartie = 0;
		if ( $cdrenc == "joueur") {
			foreach ($vdrenc as $cle=>$joueur) {
				echo "joueur <pre>";
				print_r($joueur);
				echo "</pre>";
				echo "cle : ".$clejoueur++."<br/>";
				if ( $joueur['xja'] == "Absent" ) {
					$joueur['xca'] = "";
				}
				if ( $joueur['xjb'] == "Absent" ) {
					$joueur['xcb'] = "";
				}
			
				$dao->insererchpJoueur($vrenc['lien'],str_replace("'","\'",$joueur['xja']),str_replace("'","\'",$joueur['xjb']),$joueur['xca'],$joueur['xcb'],$saison);
			}
		}
		elseif ( $cdrenc == "partie") {
			
			foreach ($vdrenc as $renc) {
				echo "cle : ".$clepartie++."<br/>";
				if ( $renc['ja'] == "Double" ) {
					$renc['ja'] = "Double". ++$i;
					$renc['jb'] = "Double". $i;
				}
 $dao->insererchpRenc($vrenc['lien'],str_replace("'","\'",$renc['ja']),str_replace("'","\'",$renc['jb']),$renc['scorea'],$renc['scoreb'],$saison,$clepartie,$renc_id);
				echo "detail rencontre <pre>";
				print_r($renc);
				echo "</pre>";
			}
		}
	}
		$dao->insererPouleRenc(str_replace("'","\'",$vrenc['libelle']),str_replace("'","\'",$vrenc['equa']),str_replace("'","\'",$vrenc['equb']),$scorea,$scoreb,$vrenc['lien'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison,$clepartie);
//		$dao->insererchpRenc($vrenc['libelle'],$vrenc['equa'],$vrenc['equb'],$scorea,$scoreb,$vrenc['lien'],$vequ['idpoule'],$vequ['iddiv'],$annee,$saison);
		//insererPouleRenc ($libelle,$equa,$equb,$scorea,$scoreb,$lien,$idpoule,$iddiv,$annee)	
	}
}
?>
