<?php
class tableau
{

	private $html = '';
	
	public function __construct($titre)
	{
		$this->html .= '<h1>'.stripslashes($titre).'</h1>
		<table class="forum_index">
			<thead>
				<tr class="trcategorie_index">
				<th colspan="2" class="case_categories">Catégories</th>
				<th class="case_sujets">Sujets</th>
				<th class="case_reponses">Réponses</th>
				<th class="case_dernier">Dernier message</th>
				</tr>
			</thead>';
	}
	
	public function add_cat($id, $titre, $sujets, $reponses)
	{
		$this->html .= '
				<tr class="sous_categorie">
					<td colspan="2" class="titre_categorie"><strong><a href="./forum-1-'.$id.'.html">'.stripslashes($titre).'</a></strong></td>
					<td class="nombre_sujets"><strong>'.$sujets.'</strong></td>
					<td class="nombre_reponses"><strong>'.$reponses.'</strong></td>
					<td class="last_message">&nbsp;</td>
				</tr>';
	}
	
	public function add_forum($id, $titre, $description, $sujets, $reponses, $id_m, $pseudo, $time, $id_r, $titre_s, $id_s)
	{
		$this->html .= '
				<tr class="categorie">
					<td class="image">
						
					</td>
					<td class="categories">
						<a href="./forum-2-'.$id.'.html"><strong>'.stripslashes($titre).'</strong></a><br />
							'.stripslashes($description).'
					</td>
					<td class="les_sujets">
						'.$sujets.'
					</td>
					<td class="les_reponses">
						'.$reponses.'
					</td>
					<td class="message_dernier">';
		if($id_s > 0)
			$this->html .= parse_date($time).'<br />
						Par <a href="./membres-'.$id_m.'.html">'.stripslashes($pseudo).'</a><br />
						Dans <a href="./forum-3-'.$id_s.'-'.$id_r.'.html#r'.$id_r.'">'.stripslashes($titre_s).'</a>';
		else
			$this->html .= 'Pas de message';
			
		$this->html .= '</td>
				</tr>';
	}
	
	public function __toString()
	{
		return $this->html.'
		</table>';
	}
}

class tableau_sujets
{
	private $html ='';
	
	public function __construct($titre)
	{
		$this->html .= '<h1>'.stripslashes($titre).'</h1>
					<table class="table_sous_forum">
						<thead>
							<tr>
							<th colspan="2" class="case_resolut">&nbsp;</th>
							<th class="case_categories">Titre du Sujet</th>
							<th class="case_page">Pages</th>
							<th class="case_sujets">Auteur</th>
							<th class="case_reponses">Réponses</th>
							<th class="case_dernier">Dernier message</th>
							</tr>
						</thead>';
	}
	
	public function add_postit($idp, $titre, $soustitre, $idm, $pseudo, $reponses, $last_time, $last_rep, $last_pseudo, $last_idm, $nb_pages, $resolu, $ferme)
	{
		$this->html .= '
						<tr class="tr_postit">
							<td class="postit_image" align="center"><img src="./images/forum/dossier_paire.png" alt="Nouvelles Réponses" /></td>
							<td class="postit_logo" align="center"><img src="./images/forum/dossier_postit.png" alt="Post-it" />';
		if($resolu)
			$this->html .= '<img src="./images/resolu.png" width="25" height="25" alt="Ce post-it à été résolu" />';
		if($ferme)
			$this->html .= '<img src="./images/verrouiller.png" width="25" height="25" alt="Ce post-it à été fermé" />';
			
		$this->html .= '</td>
							<td class="postit_cat"><a href="./forum-3-'.$idp.'.html">'.stripslashes($titre).'</a><br /><span class="sous_titre">'.stripslashes($soustitre).'</span></td>
							<td class="postit_page">';
		if($nb_pages > 1)
			$this->html .= parse_pages(0, $nb_page, './forum-3-'.$idp.'-p', '.html', false);
		$this->html .= '</td>
							<td class="postit_auteur"><a href="./membres-'.$idm.'.html">'.stripslashes($pseudo).'</a></td>
							<td class="postit_reponse">'.$reponses.'</td>
							<td class="postit_lastmessage"><a href="./forum-3-'.$idp.'-'.$last_rep.'.html#r'.$last_rep.'">'.parse_date($last_time).'</a><br /><a href="./membres-'.$last_idm.'.html">'.$last_pseudo.'</a></td>
						</tr>';
	 
	}
	
	public function aucun_postit()
	{
		$this->html .= '<tr><td colspan="7">Aucun post-it</td></tr>';
	}
	
	public function add_sujet($ids, $titre, $soustitre, $idm, $pseudo, $reponses, $paire, $last_time, $last_rep, $last_pseudo, $last_idm, $nb_pages, $resolu, $ferme)
	{
		
		if($paire)
			$this->html .= '
						<tr class="sujet_paire">';
						
		else
			$this->html .= '
						<tr class="sujet_impaire">';
		$this->html .= '	<td class="icone_image" align="center"><img src="./images/forum/dossier_impaire.png" alt="Nouvelles Réponses" /></td>
							<td class="sujet">';
		if($resolu)
			$this->html .= '<img src="./images/resolu.png"  width="25" height="25" alt="Ce sujet a été résolu" />';
		if($ferme)
			$this->html .= '<img src="./images/verrouiller.png"  width="25" height="25" alt="Ce sujet a été fermé" />';
			
		$this->html .= '</td>
							<td class="message_forum"><a href="./forum-3-'.$ids.'.html">'.stripslashes($titre).'</a><br /><span class="sous_titre">'.stripslashes($soustitre).'</span></td>
							<td class="page">';
		
		if($nb_pages > 1)
			$this->html .= parse_pages(0, $nb_pages, './forum-3-'.$ids.'-p', '.html', false);
		$this->html .= '</td>					
							<td class="auteur"><a href="./membres-'.$idm.'.html">'.stripslashes($pseudo).'</a></td>
							<td class="reponses">'.$reponses.'</td>
							<td class="dernier_message"><a href="./forum-3-'.$ids.'-'.$last_rep.'.html#r'.$last_rep.'">'.parse_date($last_time).'</a><br /><a href="./membres-'.$last_idm.'.html">'.$last_pseudo.'</a></td>
						</tr>';
	}
	
	public function aucun_sujet()
	{
		$html .= '<tr><td colspan="7">Aucun sujet</td></tr>';
	}
	
	public function ligne_vierge()
	{
		$this->html .= '
						<tr class="sujet_paire">
							<td colspan="7">&nbsp;</td>
						</tr>';
	}
	
	public function __toString()
	{
		return $this->html.'
		</table>';
	}

}

class Topic
{
	private $html = '';
	
	
	public function __construct($titre)
	{
		$this->html .= '<h1 class="titre_forum">'.stripslashes($titre).'</h1>
		';
	}
	
	public function sous_titre($titre)
	{
		if(!(empty($titre)))
			$this->html .= '<h2 class="soustitre_forum">'.stripslashes($titre).'</h2>
		';
	}
	
	public function liste($courant)
	{
		$this->html .= liste_forum($courant);
	}
	
	public function boutons($id_rep, $id_forum, $verrou)
	{
		$this->html .= '<p class="boutons_repondre_nouveau"> <!-- Boutons répondre ou fermé et Nouveau -->
				';
		if(!$verrou OR droit_dacces(9))
			$this->html .= '<a href="./forum-6-'.$id_rep.'.html"><img src="http://zdesigns.fr/images/forum/repondre.png" alt="Répondre au Sujet" /></a>';
		else
			$this->html .= '<img src="http://zdesigns.fr/images/forum/ferme.png" alt="Sujet fermé" /></a>';
		
		$this->html .= '&nbsp;<a href="./forum-4-'.$id_forum.'.html"><img src="http://zdesigns.fr/images/forum/nouveau.png" alt="Nouveau Sujet" /></a>
				</p>
				';
	}
	
	public function bandeau_resolu()
	{
		$this->html .= '<div class="sujet_resolu">Le problème de ce sujet a été résolu</div>';
	}
	
	public function debut_tableau()
	{
		$this->html .= '<table class="liste_messages zcode"> <!-- Début du tableau liste_messages -->
		';
	}
	
	public function thead($idf, $page_courante, $nb_page)
	{
		$this->html .= '<thead>
					<tr class="trpages_topic">
						<td class="pages_topic" colspan="2"> <!-- Pages du topic (en haut de page) -->
							Page :';

		
		$this->html .= parse_pages($page_courante, $nb_page, './forum-3-'.$idf.'-p', '.html');
	
		$this->html .= '</td>
					</tr>
					<tr class="trhead_topic">
						<th class="thauteur">
							Auteur
						</th>
						<th class="thmessage">
							Message
						</th>
					</tr>
				</thead>
				';
	}
	
	public function infos($id, $pseudo, $time, $id_f, $id_r)
	{
		$this->html .= '
				<tbody>
					<tr class="trinfos">
						<td class="nom_membre">
							<a href="./membres-'.$id.'.html">'.stripslashes($pseudo).'</a>
						</td>
						<td class="infos_date">
							<span id="r'.$id_r.'">
								&nbsp;<a href="./forum-3-'.$id_f.'-'.$id_r.'.html#r'.$id_r.'">#</a>&nbsp;Posté '.parse_date($time).'
							</span>
						</td>
					</tr>
					';
	}
	
	public function corps($rang, $contenu, $page_courante, $i, $id_m, $id_f, $id_r, $id_pseudo_modif, $time_modif, $quinze_dernier_mess, $pseudo, $bbcode, $pourcentage)
	{
		//echo 'ici'.$bbcode;
		$this->html .= '<tr class="trcorps_message">
						<td class="infos_membre">
							<img src="/images/avatars/'.$id_m.'.png" alt="Avatar Membre" />
						<br />
							Groupe : ';
		if($rang == 1)
			$this->html .= 'Membres';
		if($rang == 8)
		{
			$this->html .= 'Développeur';
			$this->html .= '<img src="./images/8.png" alt="Développeur" />';
		}
		if($rang == 10)
		{
			$this->html .= 'Administrateurs';
			$this->html .= '<img src="./images/10.png" alt="Administrateur" />';
		}
			
		$this->html .= '<br /><br />';
		if($quinze_dernier_mess)
		{
			$this-> html .= "<a href=\"#\" onclick=\"balise('<citation nom=&quot;".$pseudo."&quot;>".htmlspecialchars(utf8_decode($bbcode))."</citation>', '', 'texte');parse('texte','prev_texte'); return false;\"><img src=\"./images/forum/citer.png\" alt=\"Citer\" 
=\"Citer\" /></a>";
		}
		else
		{
			$this->html .= '<a href="./forum-6-'.$id_f.'-'.$id_r.'.html"><img src="./images/forum/citer.png" alt="Citer" /></a>';
			
			if($id_m == $_SESSION['id'] OR droit_dacces(9))
				$this->html .= '&nbsp;<a href="./forum-8-'.$id_r.'.html"><img src="./images/forum/editer.png" alt="Editer" /></a>&nbsp;';
			if(droit_dacces(9))	
				$this->html .= '<img src="./images/forum/supprimer.png" alt="Supprimer" />';
				
			//POURCENTAGE
			$this->html .= '<br />';
			$this->html .= '<br />';
			$this->html .= '<form action="" method="post">
				<input type="submit" value="-" />
				<strong>'.$pourcentage.'%</trong>
				<input type="submit" value="+" />
			<form>';
			$this->html .= '<br />';
			$this->html .= '<br />';
		}
		$this->html .= '</td>
						<td class="post_membre">
							<div class="bloc_message_membre">
							<div class="message_membre">';
		if($page_courante!=1 AND $i==1)
		$this->html .= '<div class="reprise_dernier_message"><strong>Reprise du dernier message de la page précédente :</strong></div>
		';
		$this->html .= $contenu;
		if($id_pseudo_modif > 0)
		{
			$pseudo_modif = mysql_result(mysql_query("SELECT pseudo FROM membres WHERE id = '".$id_pseudo_modif."' "), 0) OR DIE (mysql_error());
			$this->html .= '<div class="message_edite">';
			if($id_pseudo_modif == $id_m)
				$this->html .= 'Edité '.parse_date($time_modif).' par <a href="./membres-'.$id_pseudo_modif.'.html">'.stripslashes($pseudo_modif).'</a>';
			else
				$this->html .= 'Edité '.parse_date($time_modif).' par <a href="./membres-'.$id_pseudo_modif.'.html"><span style="color:red">'.stripslashes($pseudo_modif).'</span></a>';
			$this->html .= '</div>';
		}
		$this->html .= '<!--<div class="signature_membre">
							
							</div>-->
							
							</div>
							</div>
						</td>
					</tr>
				</tbody>
					';
	}
	
	public function tfoot($idf, $page_courante, $nb_page)
	{
		$this->html .= '<tfoot>
					<tr class="trpages_topic">
						<td class="pages_topic" colspan="2"> <!-- Pages du topic (en haut de page) -->
							Page :';
							
		$this->html .= parse_pages($page_courante, $nb_page, './forum-3-'.$idf.'-p', '.html');
	
		$this->html .= '</td>
					</tr>
				</tfoot>';
			
	}
	
	function fin_tableau()
	{
		$this->html .= '</table> <!-- Fin du tableau liste_messages -->
			<br />
			';
	}
	
	public function suivre_topic($id_f, $envoye)
	{
		if($envoye)
			$this->html .= '<div class="option_post zcode"><a href="./forum-11-'.$id_f.'.html"><img src="./images/mail.png" alt="mail" /><span> Ne plus être averti par mail en cas de réponses à ce topic.</span></a></div>';
		else
			$this->html .= '<div class="option_post zcode"><a href="./forum-10-'.$id_f.'.html"><img src="./images/mail.png" alt="mail" /><span> être averti par mail en cas de réponses à ce topic.</span></a></div>';

	}
	
	public function resoudre_topic($id_f, $resolu)
	{
		if($resolu)
			$this->html .= '<div class="option_post zcode"><a href="./forum-13-'.$id_f.'.html"><img src="./images/resolu.png" alt="Résolu" /><span> Indiquer que ce problè�me n\'est plus résolu.</span></a></div>';
		else
			$this->html .= '<div class="option_post zcode"><a href="./forum-12-'.$id_f.'.html"><img src="./images/resolu.png" alt="Résolu" /><span> Indiquer que ce problème est résolu.</span></a></div>';

	}
	
	public function verrouiller($id_f, $verrou)
	{
		if($verrou)
			$this->html .= '<div class="option_post zcode"><a href="./forum-16-'.$id_f.'.html"><img src="./images/deverrouiller.png" alt="Résolu" /><span> Déverrouiller ce sujet.</span></a></div>';
		else
			$this->html .= '<div class="option_post zcode"><a href="./forum-14-'.$id_f.'.html"><img src="./images/verrouiller.png" alt="Vérouiller" /><span> Verrouiller ce sujet.</span></a></div>';
	}
	
	public function __toString()
	{
		return $this->html;
	}
}
?>