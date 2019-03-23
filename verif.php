<HEAD>
<meta charset="UTF-8">
<link rel="stylesheet" href="include/style.css" type="text/css" />
<script	src="include/jquery.min.js"></script>

</HEAD>

<body>

<?php
include_once ('include/tournoiDAO.php');
$dao = new SmartpingDAO ();
// create object session
//echo "connect to smartping";

  /*****************************************
  *  Vérification du formulaire
  *****************************************/
  // Si le tableau $_POST existe alors le formulaire a été envoyé
  if(!empty($_POST))
  {

	$api = new Service('SW021', 'Hd125pYK04');

	if (empty($_SESSION['serial'])) {
		$_SESSION['serial'] = Service::generateSerial();
	}
	$api->setSerial($_SESSION['serial']);

	//initialize connection
	$api->initialization();
	//echo "initialization complete";

	$tel = $_POST['tel'];
	$email = $_POST['email'];
	$licence = $_POST['licence'];
	$annee = date("Y");
	$date = date("Y-m-d");
	
	$joueurdetail = $api->getJoueur($licence);
	$tableaux = $dao->getTableauxTournoi($annee);

	$dao->insererJoueurTournoi($joueurdetail['prenom'],$joueurdetail['nom'],$joueurdetail['point'],$joueurdetail['categ'],$joueurdetail['valcla'],$joueurdetail['progmois'],$joueurdetail['progann'],$joueurdetail['licence'],$email,$tel,$joueurdetail['club'],$joueurdetail['clnat'],$annee,$date);
	
	$message = "";
	$jour = array(1 => 0, 2 => 0);
	
	// pour chaque valeur postée
	foreach ( $_POST as $key => $post ) {
		// si c'est une case cochée alors c'est un tableau
		if($post == 'on') {

		// requete nb joueurs inscrits dans ce tableau
			$nb_joueurs = $dao->getNbJoueursTableau($key,$annee);
			if( $nb_joueurs[0]['nb'] >= $tableaux[$key]['joueurs_max'] ) {
					$message = $message."Le tableau ".$key." est complet, merci de modifier vos choix<br>";
			}
			// vérifie les points du joueur vs le tableau
			else {
				if ($joueurdetail['valcla'] < $tableaux[$key]['clamin'] ) {
					$message = $message."Le tableau ".$key." est réservé au joueurs ayant plus de ".$tableaux[$key]['clamin'] ." points<br>";
				}
				elseif ( $joueurdetail['valcla'] > $tableaux[$key]['clamax'] ) {
					$message = $message."Ce tableau ".$key." est réservé au joueurs ayant maximum ".$tableaux[$key]['clamax'] ." points<br>";
				} 
				else {
					// vérifie qu'il ne s'inscrit pas dans plus de 2 tableaux
					$jour[$tableaux[$key]['jour']] = $jour[$tableaux[$key]['jour']] +1;
					if ($jour[$tableaux[$key]['jour']] > 2) {
						$message = $message."Vous ne pouvez choisir que 2 tableaux pour le jour ".$tableaux[$key]['jour']."</br>";
					}
				}
			}
				;
		}
	}
	// si pas de message, on peut valider l'inscription
	if (empty ($message)) { 
		// on réinitialise les tableaux pour ce joueur et cette année
		$dao->supprimerJoueurTableau ($licence,$annee);
		
	// ON insère les nouveaux choix de tableaux
		foreach ( $_POST as $key => $post ) {
			if($post == 'on') {
				$dao->insererJoueurTableau ($licence,$key,$annee);
			}
		}
		$joueurdao = $dao->getJoueurTournoi($joueurdetail['licence'],$annee);

		// on vérifie que le joueur entré en base est bon, sinon on affiche une erreur
		if ( $joueurdao[$licence]['nom'] != $joueurdetail['nom'] ) {
?> 
			<div class="container" id="haut">
			<div class="col-md-6">
			<center>
				<div class="thumbnail" style="background-color: #FE2E2E">
					<div class="caption">
						<fieldset>
							<h3><?php echo "Une erreur s'est produite </br>"?></br></h3>
						</fieldset>
					</div>
				</div><br />
			</center>
			</div>
			</div>
<?php
		}
		else {
	
?> 
			<div class="container" id="haut">
			<div class="col-md-6">
			<center>
				<div class="thumbnail" style="background-color: #FAFAFA">
					<div class="caption">
						<fieldset>
							<h3><?php echo "Votre inscription a été enregistrée avec succès"?></br></h3>
							<ul class="list-unstyled">
								<li>Vous êtes inscrit dans les tableaux <strong> 
								<?php 			$list_tab = $dao->getJoueurTableaux($licence,$annee);
												foreach ( $list_tab as $key => $tab ) {
													echo " ".$key;
												}
								?></strong></li>
								<li><strong>Licence : </strong> <?php echo $licence?></li>
							</ul>
						</fieldset>
					</div>
				</div><br />
			</center>
			</div>
			</div>
    <center><input type=button onClick="location.href='http://aurorevitrett.fr/'" value="retour à l'accueil"><center>
    <center><input type=button onClick="location.href='http://aurorevitrett.fr/fftt/inscription.php'" value='nouvelle inscription'><center>
<?php 
		}
	}
	else {
?> 

		<div class="container" id="haut">

		<!-- FICHE DU JOUEUR -->
		<div class="col-md-6">
		<center>
			<div class="thumbnail" style="background-color: #FAFAFA">
				<div class="caption"><fieldset>
					<h3><?php echo $joueurdetail['prenom']. " ". $joueurdetail['nom']?></br>
					</h3>
					<ul class="list-unstyled">
						<li><strong>Licence : </strong> <?php echo $joueurdetail['licence']?></li>
						<li><strong>Catégorie:</strong> <?php echo $joueurdetail['categ']?>  </li>
						<li><strong>Club:</strong> <?php echo $joueurdetail['club']?>  </li>
					</ul>
				</fieldset></div>
			</div><br />

		<div class="thumbnail" style="background-color: #FE2E2E">
				<div class="caption"><fieldset>
					<h3><?php echo $message?></br>
					</h3>
				</fieldset></div>
			</div><br />

			<div class="thumbnail" style="background-color: #FAFAFA">

						<form method="post" action="verif.php">
				<fieldset><p><br />
				Choisissez les tableaux de la première journée auxquels vous souhaitez paticiper (2 maximum) :<br /><br />
				<?php  
				$i=0;
				foreach ( $tableaux as $key => $tab ) {
				$i++;	
					if($tab['jour'] == 1) {
						?>
						<input type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php echo $key." (".$tab['clamin']." à ".$tab['clamax'].") " ?> </label>
						<?php  
					}
				}
				?>
				<br />
				</p></fieldset>
			</div>

			<div class="thumbnail" style="background-color: #FAFAFA">
				<fieldset><p><br />
				Choisissez les tableaux de la deuxième journée auxquels vous souhaitez paticiper (2 maximum) :<br /><br />
				<?php  
				$i=0;
				foreach ( $tableaux as $key => $tab ) {
				$i++;	
					if($tab['jour'] == 2) {
						?>
						<input type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php echo $key." (".$tab['clamin']." à ".$tab['clamax'].") " ?> </label>
						<?php  
					}
				}
				?>
				<br />
				</p></fieldset>
			</div><br />
				<input type="hidden" name="licence" value="<?php echo $licence?>" />
				<input type="hidden" name="email" value="<?php echo $email?>" />
				<input type="hidden" name="tel" value="<?php echo $tel?>" />
				<input type="submit" value="Envoyer">
			</form>

		</center>
	</div>
</center>
</div>
<?php 
	}
  }
  ?>
</body>
