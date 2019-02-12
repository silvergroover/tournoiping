<?php

echo "read SmartpingDAO.php";

include_once ('Smartping.php');
include_once ('JournalException.php');
class SmartpingDAO {

	//M�thode de gestion de connexion
	private function connexion () {
	//	$url = "mysql:host=localhost;dbname=aurorevitett";
		$url = "mysql:host=aurorevitett.mysql.db;dbname=aurorevitett";
		$usr = "aurorevitett";
		$pwd = "DBaurore35";
	
		$db = new PDO($url, $usr, $pwd);
		//par d�faut PDO ne renvoi pas d'erreur. (ERRMODE_SILENT). la ligne suivante permet de pouvoir g�rer les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
		return $db;
	}
	
	public function insererJoueur ($prenom,$nom,$point,$categ,$valinit,$progmois,$progann,$licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_joueur(prenom,nom,point,categ,valinit,progmois,progann,licence) VALUES('$prenom','$nom','$point','$categ','$valinit','$progmois','$progann','$licence')";
			
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
			$db=null;
		}
	}

	public function insererPointsMens ($licence,$annee,$mois,$point,$categ,$valinit,$progmois,$progann) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="INSERT INTO fftt_clt_mens(licence,annee,mois,points,categ,valinit,progmois,progann) VALUES('$licence','$annee','$mois','$point','$categ','$valinit','$progmois','$progann')";
				echo $sql;
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function updatePointsMens ($licence,$annee,$mois,$point,$progmois,$progann) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="UPDATE fftt_clt_mens SET points = '$point', progmois = '$progmois', progann = '$progann' WHERE licence = '$licence' AND annee = '$annee' AND mois = '$mois'";
				echo $sql;
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getCleProgMens($mois,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
		
			$sql="SELECT licence FROM fftt_clt_mens WHERE annee = '$annee' AND mois = '$mois'";
			echo $sql;
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getProgMens($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
		
			$sql="SELECT annee,mois,points FROM fftt_clt_mens WHERE licence = '$licence'";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function insererPartieSpid ($licence,$adversaire,$date,$classement,$epreuve,$victoire,$forfait) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_parties_spid(licence,adversaire,date,classement,epreuve,victoire,forfait) VALUES('$licence','$adversaire',STR_TO_DATE('$date','%d/%m/%Y'),'$classement','$epreuve','$victoire','$forfait')";
				
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			return $sql;
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function insererPartie ($licence,$advlic,$vd,$numjourn,$codechamp,$date,$advnompre,$pointres,$coefchamp,$advclaof) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_parties(licence,advlic,vd,numjourn,codechamp,date,advnompre,pointres,coefchamp,advclaof) 
				VALUES('$licence','$advlic','$vd','$numjourn','$codechamp',STR_TO_DATE('$date','%d/%m/%Y'),'$advnompre','$pointres','$coefchamp','$advclaof')";
			
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			return $sql;
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getListLicence () {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			//			$joueur = $api->getJoueursByClub('07350021');
			//			foreach ($joueur as $cle=>$val) {
			//			$joueurdetail = $api->getJoueur($prenom,$nom,$point,$categ,$valinit,$progmois,$progann,$licence);
				
			$sql="SELECT licence from fftt_joueur";
				
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getJoueurs() {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * from fftt_joueur order by progmois desc";
	
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getJoueurDetails($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * from fftt_joueur where licence = $licence";
	
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getDateDernierePartie($table,$licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$last_date = $db->query("SELECT date FROM $table WHERE licence = $licence ORDER BY date DESC LIMIT 1");
	
			$date = '';
			foreach ($last_date as $enreg) {
				//on cr�� un objet article
				$date = $enreg['date'];
			}
		
			return $date;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getPartieParJoueurSpid($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			//$sql = "SELECT * FROM fftt_parties_spid WHERE licence = $licence";
			$sql = "SELECT adversaire, date,epreuve,classement,victoire, 
						(select Distinct pointres from fftt_parties 
        					where fftt_parties.licence = fftt_parties_spid.licence 
           					and fftt_parties.advnompre = fftt_parties_spid.adversaire
           					and fftt_parties.date = fftt_parties_spid.date
                         and fftt_parties.vd = fftt_parties_spid.victoire
           						) as pointres
		           FROM `fftt_parties_spid` WHERE licence = $licence order by date desc";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
			}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getTopVictoiresSpid($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties_spid where licence = $licence and victoire = 'V' order by classement desc LIMIT 5 ";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
			}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getTopVictoires($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties where licence = $licence and vd = 'V' order by advclaof desc, pointres desc LIMIT 5 ";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getAvgClassement($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT AVG(classement) FROM fftt_parties_spid where licence = $licence";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	
	public function getPartieParJoueur($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties,fftt_epreuve WHERE fftt_parties.codechamp = fftt_epreuve.code and licence = $licence order by date desc";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function insererhisto($licence,$saison,$phase,$point) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="INSERT INTO fftt_saisons(licence,saison,phase,points) VALUES('$licence','$saison','$phase','$point')";
	
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			return $sql;
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getClassementAdversaires($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT classement,count(*) FROM fftt_parties_spid WHERE licence = $licence group by victoire";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getHistorique($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT saison,phase,points FROM fftt_saisons WHERE licence = $licence order by saison,phase";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
}	
?>