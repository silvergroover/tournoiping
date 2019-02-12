<?php
include_once ('Smartping.inc');
#include_once ('JournalException.inc');
class ArticleDAO {
	
	//Méthode de gestion de connexion
	private function connexion () {
		$url = "mysql:host=localhost;dbname=aurorevitett";
		$usr = "aurorevitett";
		$pwd = "DBaurore35";
		
		$db = new PDO($url, $usr, $pwd);
		//par défaut PDO ne renvoi pas d'erreur. (ERRMODE_SILENT). la ligne suivante permet de pouvoir gérer les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		
		return $db;
	}
	
	public function rechercherLesArticles () {
		$db = null;
		try {
			//etablie la connexion
			$db=$this->connexion();
			//ecrire la req SQL à éxecuter
			$sql="SELECT * FROM article ORDER BY date DESC";
			// execute la req/ $resultat sera un tableau à 2 dim: une ligne correspond chaque entrée sera un tableau associatif
			// avec id,titre,etc...
			$resultat=$db->query($sql);
			//nécessite de construire un tableau avec la même première colonne mais des objets article dans la deuxième colonne
			//Tableau d'objets Arcticle
			$tabArticle=array();
			//Parcours le jeu de résultat
			foreach ($resultat as $enreg) {
				//on créé un objet article
				$a = new Article(
						$enreg['id'],
						$enreg['titre'],
						$enreg['date'],
						$enreg['texte'],
						$enreg['auteur']
						);
				// on met l'objet dans le tableau
				$tabArticle[]=$a;
			}
			// on affiche le tableau
			return $tabArticle;
			}			 
			catch (PDOException $e) {
				// on produit une exception orientée application
				throw new JournalException("Erreur de récup de la liste des articles !");
			}
			Finally {
				//fermeture de la connexion
				if(!is_null($resultat))
				$resultat->closeCursor();
				$db=null;
			}
		
	}
	
	public function rechercherArticleParId ($id) {
		$db = null;
		$st = null;
		try {
			//etablie la connexion
			$db=$this->connexion();
			//ecrire la req SQL à éxecuter
			// ne pas mettre la syntaxe ci dessous car pas de concaténation qui pourrait permettre un dropdb de la base via l'url
			// $sql="SELECT * FROM article WHERE id=".$id;
			$sql="SELECT * FROM article WHERE id=?";
			//on prépare la requête qu'on met en cache côté serveur de données (sécu ok et perf améliorée)
			$st = $db->prepare($sql);
			// on remplace la variable de la requête par sa valeur
			// 1 correspond au remplacement du 1er point d'interro dans la req sql
			$st->bindParam(1,$id);
			//execute la req
			$st->execute();
			//traite le résultat, fetch permet de regarder un à un si il y a un enreg. si pas, il arrête
			if($enreg = $st->fetch()) {
				$a = new Article(
						$enreg['id'],
						$enreg['titre'],
						$enreg['date'],
						$enreg['texte'],
						$enreg['auteur']
			);
				return $a;
			}
			else {
				throw new JournalException("Erreur: Aucun article avec cet id !");
				}
		}
		catch (Exception $e) {
			
			throw new JournalException("Erreur: une erreur est survenue mais nous ne l'avions pas prévue !");
		}
		finally {
			//fermeture de la connexion
			if(!is_null($a))
				$a->closeCursor();
			$db=null;
		}
		
	}
	
	public function insererJoueur ($a) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			
			$joueur = $api->getJoueursByClub('07350021');
			foreach ($joueur as $cle=>$val) {
				$joueurdetail = $api->getJoueur($val['licence']);
					
			
			$sql="INSERT INTO article(prenom,nom,point,categ,valinit,progmois,progann) VALUES(?,?,?,?,?,?,?)";
				
			$st = $db->prepare($sql);
			
			$st->bindParam(1,$joueurdetail['prenom']);
			$st->bindParam(2,$joueurdetail['nom']);
			$st->bindParam(3,$joueurdetail['point']);
			$st->bindParam(4,$joueurdetail['categ']);
			$st->bindParam(5,$joueurdetail['valinit']);
			$st->bindParam(6,$joueurdetail['progmois']);
			$st->bindParam(7,$joueurdetail['progann']);
						
			$st->execute();
			}
		} 
		catch (PDOException $e) {
			throw new JournalException("erreur de l'enregistrement de l'article");
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