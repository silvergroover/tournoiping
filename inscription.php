<HEAD>
<meta charset="UTF-8">

<link rel="stylesheet" href="include/style.css" type="text/css" />

</HEAD>

<html><body>
<?php
include_once ('include/tournoiDAO.php');
$dao = new SmartpingDAO ();
  
$annee = date("Y");

$tableaux = $dao->getTableauxTournoi($annee);

  /*****************************************
  *  Constantes et variables
  *****************************************/
  $message = '';      // Message à afficher à l'utilisateur
 
  /*****************************************
  *  Vérification du formulaire
  *****************************************/
  // Si le tableau $_POST existe alors le formulaire a été envoyé
if(!empty($_POST)) {

	$prenom = $_POST['prenom'];
	$nom = $_POST['nom'];
	$email = $_POST['email'];
	$tel = $_POST['tel'];
	$point = strpos($email,".");
	$aroba = strpos($email,"@");
	$licence = $_POST['licence'];

	// create object session

	$api = new Service('SW021', 'Hd125pYK04');

	if (empty($_SESSION['serial'])) {
		$_SESSION['serial'] = Service::generateSerial();
	}
	$api->setSerial($_SESSION['serial']);

	//initialize connection
	$api->initialization();

	$joueurdetail = $api->getJoueur($licence);

    // Le login est-il rempli ?
    if(empty($_POST['prenom']))
    {
      $message = 'Veuillez indiquer votre prenom svp.';
    }
      // Le nom est-il rempli ?
    elseif(empty($_POST['nom']))
    {
      $message = 'Veuillez indiquer votre nom svp.';
    }
      // L'email est-il rempli ?
      elseif(empty($_POST['email']))
    {
      $message = 'Veuillez indiquer votre email svp.';
    }
	elseif($point=='')
	{
		echo "Votre email doit comporter un <b>point</b>";
	}
	elseif($aroba=='')
	{
		echo "Votre email doit comporter un <b>'@'</b>";
	}
      // Le nom est-il rempli ?
    elseif(empty($_POST['licence']))
    {
      $message = 'Veuillez indiquer votre numero de licence svp.';
    }
      // Le nom est-il correct ?
    elseif(strtolower($_POST['nom']) !== strtolower($joueurdetail['nom']))
    {
      $message = 'Dans la base FFTT, la licence '.$joueurdetail['licence'].' ne correspond pas au nom que vous avez renseigné '.$joueurdetail['nom'];
    }

	if(!empty($message)) 
	{
?>
		<p><?php echo $message; ?></p>
		<form method="post" action="inscription.php">
		<p><center>
		<fieldset>
		<legend>Inscription au tournoi</legend> <!-- Titre du fieldset --> 

		<label for="nom">nom : </label>
		<input type="text" name="nom" id="nom" size="50" value="<?php if(!empty($_POST['nom'])) { echo htmlspecialchars($_POST['nom'], ENT_QUOTES); } ?>"/><br><br>

		<label for="prenom">prénom : </label>
		<input type="text" name="prenom" id="prenom" size="50" value="<?php if(!empty($_POST['prenom'])) { echo htmlspecialchars($_POST['prenom'], ENT_QUOTES); } ?>" /><br><br>

		<label for="prenom">licence : </label>
		<input type="text" name="licence" id="licence" size="50" value="<?php if(!empty($_POST['licence'])) { echo htmlspecialchars($_POST['licence'], ENT_QUOTES); } ?>" /><br><br>

		<label for="email">e-mail : </label>
		<input type="email" name="email" id="email"  size="50" value="<?php if(!empty($_POST['email'])) { echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>"/><br><br>

		<label for="tel">téléphone : </label>
		<input type="tel" name="tel" id="tel" size="50" value="<?php if(!empty($_POST['tel'])) { echo htmlspecialchars($_POST['tel'], ENT_QUOTES); } ?>" /><br><br>
		</fieldset>
		</p>
            toto

		<input type="submit" value="Envoyer">
		</center></form>
	  
<?php
    }
    else {
?> 	  
	<div class="container" id="haut">

	<!-- FICHE DU JOUEUR -->
	<div class="col-md-6">
	<center>
			<div class="thumbnail" style="background-color: #FAFAFA">
				<div class="caption"><fieldset>
					<h3><?php echo "Bienvenue ". $joueurdetail['prenom']. " ". $joueurdetail['nom']?></br>
					</h3>
					<ul class="list-unstyled">
						<li><strong>Licence : </strong> <?php echo $joueurdetail['licence']?></li>
						<li><strong>Catégorie:</strong> <?php echo $joueurdetail['categ']?>  </li>
						<li><strong>Club:</strong> <?php echo $joueurdetail['club']?>  </li>
					</ul>
				</fieldset></div>
			</div><br />
(

			<div class="thumbnail" style="background-color: #FAFAFA">
			<form method="post" action="verif.php">

				<fieldset><p><br />

				Choisissez les tableaux de la première journée auxquels vous souhaitez paticiper (2 maximum) :<br /><br />

                <table>
                <tr><th colspan="2">SIMPLES</th></tr>
				<?php  
				$i=0;
				foreach ( $tableaux as $key => $tab ) {
				$i++;	
//					if($tab['jour'] == 1 ) {
					if($tab['jour'] == 1 && $tab['type'] == "simple") {
                ?>
						<tr><td><input type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php echo $key." (".$tab['clamin']." à ".$tab['clamax'].")  " ?></td><td> <?php echo $tab['description'] ?></label></td></tr>
				<?php  
					}
				}
				?>
				</table><br /><br />


                <table>
                <tr><th colspan="3">DOUBLES</th></tr>
				<?php  
				$i=0;
				foreach ( $tableaux as $key => $tab ) {
                $j2 = $key."j2";
				$i++;	
//					if($tab['jour'] == 1 ) {
					if($tab['jour'] == 1 && $tab['type'] == "double") {
                ?>
				    <tr>
                        <td><input type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php echo $key." (".$tab['clamin']." à ".$tab['clamax'].")  " ?></td>
                        <td><label for="<?php echo "j2" ?>">licence joueur 2 : </label><input type="text" name="<?php echo $j2 ?>" id="<?php echo "j2" ?>" size="25" /></td>
                        <td> <?php echo $tab['description'] ?></label></td>
                    </tr>
				<?php  
					}
				}
				?>
				</table><br />

				</p></fieldset>
			</div>

			<div class="thumbnail" style="background-color: #FAFAFA">
				<fieldset><p><br />
				Choisissez les tableaux de la deuxième journée auxquels vous souhaitez paticiper (2 maximum) :<br /><br />
                <table>
                <tr><th colspan="2">SIMPLES</th></tr>
				<?php  
				$i=0;
				foreach ( $tableaux as $key => $tab ) {
				$i++;	
//					if($tab['jour'] == 1 ) {
					if($tab['jour'] == 2 && $tab['type'] == "simple") {
                ?>
						<tr><td><input type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php echo $key." (".$tab['clamin']." à ".$tab['clamax'].")  " ?></td><td> <?php echo $tab['description'] ?></label></td></tr>
				<?php  
					}
				}
				?>
				</table><br /><br />


                <table>
                <tr><th colspan="3">DOUBLES</th></tr>
				<?php  
				$i=0;
				foreach ( $tableaux as $key => $tab ) {
                $j2 = $key."j2";
				$i++;	
//					if($tab['jour'] == 1 ) {
					if($tab['jour'] == 2 && $tab['type'] == "double") {
                ?>
				    <tr>
                        <td><input type="checkbox" name="<?php echo $key ?>" id="<?php echo $key ?>" /> <label for="<?php echo $key ?>"><?php echo $key." (".$tab['clamin']." à ".$tab['clamax'].")  " ?></td>
                        <td><label for="<?php echo "j2" ?>">licence joueur 2 : </label><input type="text" name="<?php echo $j2 ?>" id="<?php echo "j2" ?>" size="25" /></td>
                        <td> <?php echo $tab['description'] ?></label></td>
                    </tr>
				<?php  
					}
				}
				?>
				</table><br />
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
	else
	{
?> 
	<form method="post" action="inscription.php">
	<p><center>
	<fieldset>
       <legend>Formulaire d'inscription</legend> <!-- Titre du fieldset --> 

       <label for="nom">nom : </label>
       <input type="text" name="nom" id="nom" size="50" /><br><br>

       <label for="prenom">prénom : </label>
       <input type="text" name="prenom" id="prenom" size="50" /><br><br>

       <label for="prenom">licence : </label>
       <input type="text" name="licence" id="licence" size="50" /><br><br>

       <label for="email">e-mail : </label>
       <input type="email" name="email" id="email"  size="50" /><br><br>

       <label for="tel">téléphone : </label>
       <input type="tel" name="tel" id="tel" size="50" /><br><br>Votre numéro de téléphone nous permettra de vous contacter en cas d'annulation d'un tableau.<br>
	</fieldset>
	</p>

	<input type="submit" value="Poursuivre l'inscription">
</center></form>
<?php
  }
?> 
</body></html>
