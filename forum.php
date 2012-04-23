<?php
session_start();
ob_start(); ////NE PAS SUPPRIMER !
require_once("./inc/core.php");
require_once('./inc/functions.php');
require_once('./includes/fonctions.php');
require_once ("./classes/forum_class.php");
require_once('./zform/class.zcode.php');
require_once('./zform/class.unzcode.php');
require_once('./zform/geshi/geshi.php');


/*A VIRER UN FOIS LE FORUM OUVERT*/
if(!droit_dacces(10))
{
    $_SESSION['message']['erreur'] = 'Commentaire supprimé';
    header('Location: '.ROOT.'erreur.php?e=403');
    exit();
}




$titre = 'Forums';
include('./inc/head.php');
?>

<div id="arianne">
    <?php
    if(!(isset($_GET['action'])))
        echo '<a href="./index.html">zDesigns.fr</a> &gt; Les forums</p>';
    else
    {
        echo '<a href="./index.html">zDesigns.fr</a> &gt; <a href="./forum.html">Les forums</a>';
        switch ($_GET['action'])
        {
                case 1: //Catégrorie d'un forum
                {
                        $nombrecat = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_cat WHERE id = '".$_GET['idf']."'"), 0);
                        if($nombrecat == 1)
                        {
                                $titrecat = mysql_result(mysql_query("SELECT titre FROM forum_cat WHERE id = '".$_GET['idf']."'"), 0);
                                echo ' &gt; '.stripslashes($titrecat);
                        }
                        else
                                echo ' &gt; Aucune catégorie ne correspond à ce numéro';
                }
                break;
                case 2: //Liste des sujets
                {
                        $nombreforum = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_forum WHERE id = '".$_GET['idf']."'"), 0);
                        if($nombreforum == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum, forum_cat.rang_dacces AS acc_cat, forum_forum.id AS id_f, forum_forum.titre AS titre_f, forum_cat.id, forum_cat.titre AS titre_c FROM forum_forum INNER JOIN forum_cat
                                                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                                                WHERE forum_forum.id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                $titreforum = $donnees['titre_f'];
                                $idforum = $donnees['id_f'];
                                echo ' &gt; <a href="./forum-1-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; '.stripslashes($titreforum);
                        }
                        else
                                echo ' &gt; Aucun forum ne correspond à ce numéro';
                }
                break;
                case 3: //Lecture d'un sujet
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.ferme AS verrou, forum_sujet.id_membre AS id_m, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                $auteur = $donnees['id_m'];
                                $idforum = $donnees['id'];
                                $verrou = $donnees['verrou'];
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; '.stripslashes($donnees['titre_s']);
                        }
                }
                break;
                case 4: //Nouveau sujet
                {
                        $nombreforum = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_forum WHERE id = '".$_GET['idf']."'"), 0);
                        if($nombreforum == 1)
                        {
                                $ret = mysql_query("SELECT forum_cat.id, forum_cat.titre AS titre_c, forum_forum.titre AS titre FROM forum_forum
                                                                        INNER JOIN forum_cat
                                                                        ON (forum_forum.id_cat = forum_cat.id)
                                                                        WHERE forum_forum.id = '".$_GET['idf']."'") OR DIE (mysql_error());
                                $don = mysql_fetch_array($ret);
                                $titreforum = $don['titre'];
                                echo ' &gt; <a href="./forum-1-'.$don['id'].'.html">'.stripslashes($don['titre_c']).'</a> &gt; <a href="./forum-2-'.$_GET['idf'].'.html">'.stripslashes($titreforum).'</a>  &gt; Rédiger un nouveau Topic';
                        }
                        else
                                echo ' &gt; Aucun forum ne correspond à ce numéro';
                }
                break;
                case 5: //Rediger nouveau topic
                {
                        $nombreforum = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_forum WHERE id = '".$_GET['idf']."'"), 0);
                        if($nombreforum == 1)
                        {
                                $titreforum = mysql_result(mysql_query("SELECT titre FROM forum_forum WHERE id = '".$_GET['idf']."'"), 0);
                                echo ' &gt; <a href="./forum-2-'.$_GET['idf'].'.html">'.stripslashes($titreforum).'</a>  &gt; Rédiger un nouveau Topic';
                        }
                        else
                                echo ' &gt; Aucun forum ne correspond à ce numéro';
                }
                break;
                case 6: //Répondre à un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum,forum_forum.rang_decriture AS acc_E_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$_GET['idf'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Ajout d\'une réponse au sujet';
                        }
                }
                break;
                case 7: //Répondre à un topic ok
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum,forum_forum.rang_decriture AS acc_E_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$_GET['idf'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Ajout d\'une réponse au sujet';
                        }
                }
                break;
                case 8: //Editer une réponse
                {
                        $nombrereponse = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse WHERE id = '".$_GET['idf']."' "), 0);

                        if($nombrereponse == 1)
                        {
                                $req = mysql_query("SELECT forum_cat.id AS id_c, forum_cat.titre AS titre_c, forum_forum.id AS id_f, forum_forum.titre AS titre_f, forum_sujet.id AS id_s, forum_sujet.titre AS titre_s
                                                                        FROM forum_reponse
                                                                        INNER JOIN forum_sujet
                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                        INNER JOIN forum_forum
                                                                        ON (forum_sujet.id_forum = forum_forum.id)
                                                                        INNER JOIN forum_cat
                                                                        ON (forum_forum.id_cat = forum_cat.id)
                                                                        WHERE forum_reponse.id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                $donnees= mysql_fetch_array($req);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id_f'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$donnees['id_s'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Editer une réponse';
                        }
                }
                break;
                case 9: //Editer une réponse ok
                {
                        $nombrereponse = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse WHERE id = '".$_GET['idf']."' "), 0);

                        if($nombrereponse == 1)
                        {
                                $req = mysql_query("SELECT forum_cat.id AS id_c, forum_cat.titre AS titre_c, forum_forum.id AS id_f, forum_forum.titre AS titre_f, forum_sujet.id AS id_s, forum_sujet.titre AS titre_s
                                                                        FROM forum_reponse
                                                                        INNER JOIN forum_sujet
                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                        INNER JOIN forum_forum
                                                                        ON (forum_sujet.id_forum = forum_forum.id)
                                                                        INNER JOIN forum_cat
                                                                        ON (forum_forum.id_cat = forum_cat.id)
                                                                        WHERE forum_reponse.id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                $donnees= mysql_fetch_array($req);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id_f'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$donnees['id_s'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Editer une réponse';
                        }
                }
                break;
                case 10: //Suivre un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt '.stripslashes($donnees['titre_s']);
                        }
                }
                break;
                case 11: //Ne plus suivre un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt '.stripslashes($donnees['titre_s']);
                        }
                }
                break;
                case 12: //Résoudre un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt '.stripslashes($donnees['titre_s']);
                        }
                }
                break;
                case 13: //Dérésoudre un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt '.stripslashes($donnees['titre_s']);
                        }
                }
                break;
                case 14: //Verrouiller un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum,forum_forum.rang_decriture AS acc_E_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$_GET['idf'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Verrouiller le sujet';
                        }
                }
                break;
                case 15: //Verrouiller un topic ok
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum,forum_forum.rang_decriture AS acc_E_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$_GET['idf'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Verrouiller le sujet';
                        }
                }
                break;
                case 16: //Déverrouiller un topic
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum,forum_forum.rang_decriture AS acc_E_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$_GET['idf'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Déverrouiller le sujet';
                        }
                }
                break;
                case 17: //Déverrouiller un topic ok
                {
                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                        if($nombresujet == 1)
                        {
                                $retour = mysql_query("SELECT forum_forum.rang_dacces AS acc_forum,forum_forum.rang_decriture AS acc_E_forum, forum_forum.id, forum_forum.titre AS titre_f, forum_sujet.sous_titre AS sstitre, forum_sujet.titre AS titre_s, forum_cat.id AS id_c, forum_cat.titre AS titre_c FROM forum_forum
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                INNER JOIN forum_reponse
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN forum_cat
                                                                                ON (forum_forum.id_cat = forum_cat.id)
                                                                                WHERE forum_sujet.id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                $donnees = mysql_fetch_array($retour);
                                echo ' &gt; <a href="./forum-1-'.$donnees['id_c'].'.html">'.stripslashes($donnees['titre_c']).'</a> &gt; <a href="./forum-2-'.$donnees['id'].'.html">'.stripslashes($donnees['titre_f']).'</a> &gt; <a href="./forum-3-'.$_GET['idf'].'.html">'.stripslashes($donnees['titre_s']).'</a> &gt; Déverrouiller le sujet';
                        }
                }
                break;
                default:
                        echo ' &gt; Aucune action ne correspond';
        }
    }
    ?>
</div>


<div id="forum">

                <?php
                if(!(isset($_GET['action']))) // liste des forums
                {
                        $forum = new tableau('Les Forums');

                        $req_cat = mysql_query("SELECT * FROM forum_cat ORDER BY ordre ASC") OR DIE (mysql_error());
                        while($donnees = mysql_fetch_array($req_cat))
                        {
                                if(droit_dacces($donnees['rang_dacces']))
                                {
                                        $nombresujettotal = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet INNER JOIN forum_forum
                                                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                                                WHERE forum_forum.id_cat = '".$donnees['id']."' "), 0);
                                        $nombrereponsetotal = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse INNER JOIN forum_sujet
                                                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                                INNER JOIN forum_forum
                                                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                                                WHERE forum_forum.id_cat = '".$donnees['id']."'
                                                                                                                AND	forum_reponse.initiale = '0' "), 0);
                                        $forum->add_cat($donnees['id'], $donnees['titre'], $nombresujettotal, $nombrereponsetotal);

                                        $req_forum = mysql_query("SELECT * FROM forum_forum WHERE id_cat = '".$donnees['id']."' ORDER BY ordre ASC ") OR DIE(mysql_error());
                                        while($fdonnees = mysql_fetch_array($req_forum))
                                        {
                                                $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id_forum = '".$fdonnees['id']."' "), 0);
                                                $nombrereponse = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse INNER JOIN forum_sujet
                                                                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                                                        WHERE forum_sujet.id_forum = '".$fdonnees['id']."'
                                                                                                                                        AND forum_reponse.initiale = '0' "), 0);
                                                //Dernier message
                                                $data = mysql_query("SELECT membres.id AS id_m, membres.pseudo, forum_reponse.timestamp, forum_reponse.id AS id_r, forum_sujet.titre AS titre_s, forum_sujet.id AS id_s
                                                                                FROM forum_reponse
                                                                                INNER JOIN forum_sujet
                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                INNER JOIN membres
                                                                                ON (forum_reponse.id_pseudo = membres.id)
                                                                                INNER JOIN forum_forum
                                                                                ON (forum_sujet.id_forum = forum_forum.id)
                                                                                WHERE forum_forum.id = '".$fdonnees['id']."'
                                                                                ORDER BY forum_reponse.timestamp DESC
                                                                                LIMIT 1") OR DIE (mysql_error());
                                                $dernier_message = mysql_fetch_array($data);
                                                //echo 'id membre : '.$dernier_message['id_m'].' pseudo : '.$dernier_message['pseudo'].' time : '.$dernier_message['timestamp'].' id réponde : '.$dernier_message['id_r'].' titre sujet : '.$dernier_message['titre_s'].' id sujet : '.$dernier_message['id_s'].'<br />';
                                                $forum->add_forum($fdonnees['id'], $fdonnees['titre'], $fdonnees['description'], $nombresujet, $nombrereponse, $dernier_message['id_m'], $dernier_message['pseudo'], $dernier_message['timestamp'], $dernier_message['id_r'], $dernier_message['titre_s'], $dernier_message['id_s']);
                                        }
                                }

                        }
                        echo $forum;
                }
                else
                {
                        switch ($_GET['action']) // une catégorie particuliere
                        {
                                case 1:
                                {
                                        if((isset($_GET['idf'])) AND (!(empty($_GET['idf']))))
                                        {
                                                $idcat= mysql_real_escape_string($_GET['idf']);

                                                $nombrecat = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_cat WHERE id = '".$idcat."' "), 0);


                                                if($nombrecat > 0)
                                                {
                                                        $forum = new tableau($titrecat);

                                                        $req_cat = mysql_query("SELECT * FROM forum_cat WHERE id='".$idcat."' ");
                                                        $donnees = mysql_fetch_array($req_cat);

                                                        if(droit_dacces($donnees['rang_dacces']))
                                                        {
                                                                $nombresujettotal = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet INNER JOIN forum_forum
                                                                                                                                        ON (forum_sujet.id_forum = forum_forum.id)
                                                                                                                                        WHERE forum_forum.id_cat = '".$donnees['id']."' "), 0);
                                                                $nombrereponsetotal = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse INNER JOIN forum_sujet
                                                                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                                                        INNER JOIN forum_forum
                                                                                                                                        ON (forum_sujet.id_forum = forum_forum.id)
                                                                                                                                        WHERE forum_forum.id_cat = '".$donnees['id']."'
                                                                                                                                        AND	forum_reponse.initiale = '0' "), 0);

                                                                $forum->add_cat($donnees['id'], $donnees['titre'], $nombresujettotal, $nombrereponsetotal);

                                                                $req_forum = mysql_query("SELECT * FROM forum_forum WHERE id_cat = '".$donnees['id']."' ORDER BY ordre ASC ") OR DIE(mysql_error());
                                                                while($fdonnees = mysql_fetch_array($req_forum))
                                                                {
                                                                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id_forum = '".$fdonnees['id']."' "), 0);
                                                                        $nombrereponse = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse INNER JOIN forum_sujet
                                                                                                                                                                ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                                                                                WHERE forum_sujet.id_forum = '".$fdonnees['id']."'
                                                                                                                                                                AND forum_reponse.initiale = '0' "), 0);
                                                                        //Dernier message
                                                                        $data = mysql_query("SELECT membres.id AS id_m, membres.pseudo, forum_reponse.timestamp, forum_reponse.id AS id_r, forum_sujet.titre AS titre_s, forum_sujet.id AS id_s
                                                                                                        FROM forum_reponse
                                                                                                        INNER JOIN forum_sujet
                                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                        INNER JOIN membres
                                                                                                        ON (forum_reponse.id_pseudo = membres.id)
                                                                                                        INNER JOIN forum_forum
                                                                                                        ON (forum_sujet.id_forum = forum_forum.id)
                                                                                                        WHERE forum_forum.id = '".$fdonnees['id']."'
                                                                                                        ORDER BY forum_reponse.timestamp DESC
                                                                                                        LIMIT 1") OR DIE (mysql_error());
                                                                        $dernier_message = mysql_fetch_array($data);

                                                                        $forum->add_forum($fdonnees['id'], $fdonnees['titre'], $fdonnees['description'], $nombresujet, $nombrereponse, $dernier_message['id_m'], $dernier_message['pseudo'], $dernier_message['timestamp'], $dernier_message['id_r'], $dernier_message['titre_s'], $dernier_message['id_s']);
                                                                }

                                                                echo $forum;
                                                        }
                                                        else
                                                                mess(103, './forum.html');

                                                }
                                                else
                                                        mess(102, './forum.html');
                                        }
                                        else
                                                mess(79, './forum.html');
                                }
                                break;
                                case 2: //liste des sujets
                                {
                                        if((isset($_GET['idf'])) AND (!(empty($_GET['idf']))))
                                        {
                                                if($nombreforum > '0')
                                                {
                                                        if(droit_dacces($donnees['acc_cat']))
                                                        {
                                                                if(droit_dacces($donnees['acc_forum']))
                                                                {
                                                                        echo liste_forum($idforum);
                                                                        echo '<p class="boutons_nouveau"><a href="./forum-4-'.$_GET['idf'].'.html"><img src="./images/forum/nouveau.png" alt="Nouveau" /></a></p>';
                                                                        $tableau = new tableau_sujets($titreforum);

                                                                        $nombretopic = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id_forum = '".$_GET['idf']."' AND type='2' "), 0);
                                                                        if($nombretopic > '0')
                                                                        {

                                                                                $ret = mysql_query("SELECT forum_sujet.ferme, forum_sujet.resolu, forum_sujet.id AS idpost, titre, sous_titre, pseudo, membres.id AS idm FROM forum_sujet
                                                                                                                        INNER JOIN membres ON forum_sujet.id_membre = membres.id
                                                                                                                        WHERE forum_sujet.id_forum = '".$_GET['idf']."'
                                                                                                                        AND forum_sujet.type='2'") OR DIE (mysql_error());
                                                                                while($donnees = mysql_fetch_array($ret))
                                                                                {
                                                                                        $nombre_reponses = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse WHERE initiale != '1' AND id_sujet='".$donnees['idpost']."' "), 0);
                                                                                        //Calcule du dernier message posté
                                                                                        $req = mysql_query("SELECT forum_reponse.timestamp, forum_reponse.id AS id_r, membres.pseudo, membres.id AS id_m
                                                                                                                                FROM forum_reponse
                                                                                                                                INNER JOIN membres
                                                                                                                                ON (forum_reponse.id_pseudo = membres.id)
                                                                                                                                WHERE id_sujet = '".$donnees['idpost']."'
                                                                                                                                ORDER BY forum_reponse.id DESC
                                                                                                                                LIMIT 1") OR DIE (mysql_error());
                                                                                        $don = mysql_fetch_array($req);

                                                                                        //Gestion des pages
                                                                                        $nb_mess_par_page = 10;

                                                                                        $req = mysql_query("SELECT COUNT(*) AS nb_messages FROM forum_reponse WHERE id_sujet='".$donnees['idpost']."' ");
                                                                                        $data = mysql_fetch_array($req);

                                                                                        $totalDesMessages = $data['nb_messages'];

                                                                                        $nombreDePages  = ceil($totalDesMessages / $nb_mess_par_page);
                                                                                        //Fin gestion des page

                                                                                        $tableau->add_postit($donnees['idpost'], $donnees['titre'], $donnees['sous_titre'], $donnees['idm'], $donnees['pseudo'], $nombre_reponses, $don['timestamp'], $don['id_r'], $don['pseudo'], $don['id_m'], $nombreDePages, $donnees['resolu'], $donnees['ferme']);
                                                                                }
                                                                        }
                                                                        else
                                                                                $tableau->aucun_postit();

                                                                        $tableau->ligne_vierge();

                                                                        $nombresujet = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_sujet WHERE id_forum = '".$_GET['idf']."' AND type='1' "), 0);
                                                                        if($nombresujet > '0')
                                                                        {
                                                                                $ret2 = mysql_query("SELECT forum_sujet.ferme, forum_sujet.resolu, forum_sujet.id AS idpost, titre, sous_titre, pseudo, membres.id AS idm FROM forum_sujet
                                                                                                                        INNER JOIN membres ON forum_sujet.id_membre = membres.id
                                                                                                                        WHERE forum_sujet.id_forum = '".$_GET['idf']."'
                                                                                                                        AND forum_sujet.type='1'
                                                                                                                        ORDER BY forum_sujet.timestamp_modif DESC ") OR DIE (mysql_error());
                                                                                $paire = true;
                                                                                $test = true;
                                                                                while($donnees2 = mysql_fetch_array($ret2))
                                                                                {
                                                                                        $nombre_reponses = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse WHERE initiale != '1' AND id_sujet='".$donnees2['idpost']."' "), 0);

                                                                                        if($paire AND !$test)
                                                                                        {
                                                                                                $paire = false;
                                                                                                $test = true;
                                                                                        }
                                                                                        if(!$paire AND !$test)
                                                                                        {
                                                                                                $paire = true;
                                                                                        }

                                                                                        $req = mysql_query("SELECT forum_reponse.timestamp, forum_reponse.id AS id_r, membres.pseudo, membres.id AS id_m
                                                                                                                                FROM forum_reponse
                                                                                                                                INNER JOIN membres
                                                                                                                                ON (forum_reponse.id_pseudo = membres.id)
                                                                                                                                WHERE id_sujet = '".$donnees2['idpost']."'
                                                                                                                                ORDER BY forum_reponse.id DESC
                                                                                                                                LIMIT 1") OR DIE (mysql_error());
                                                                                        $don = mysql_fetch_array($req);

                                                                                        //Gestion des pages
                                                                                        $nb_mess_par_page = 10;

                                                                                        $req = mysql_query("SELECT COUNT(*) AS nb_messages FROM forum_reponse WHERE id_sujet='".$donnees2['idpost']."' ");
                                                                                        $data = mysql_fetch_array($req);

                                                                                        $totalDesMessages = $data['nb_messages'];

                                                                                        $nombreDePages  = ceil($totalDesMessages / $nb_mess_par_page);
                                                                                        //Fin gestion des page

                                                                                        $tableau->add_sujet($donnees2['idpost'], $donnees2['titre'], $donnees2['sous_titre'], $donnees2['idm'], $donnees2['pseudo'], $nombre_reponses, $paire, $don['timestamp'], $don['id_r'], $don['pseudo'], $don['id_m'], $nombreDePages, $donnees2['resolu'], $donnees2['ferme']);
                                                                                        $test = false;
                                                                                }
                                                                        }
                                                                        else
                                                                                $tableau->aucun_sujet();

                                                                        echo $tableau;

                                                                        echo '<p class="boutons_nouveau"><a href="./forum-4-'.$_GET['idf'].'.html"><img src="./images/forum/nouveau.png" alt="Nouveau" /></a></p>';
                                                                        echo liste_forum($idforum);
                                                                }
                                                                else
                                                                        mess(111, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(100, './forum.html');
                                        }
                                }
                                break;
                                case 3 :// Lire un Topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                {
                                                                        $ret = mysql_query("SELECT rang_dacces FROM forum_sujet WHERE id='".$donnees['idf']."' ")OR DIE (mysql_error());
                                                                        $don = mysql_fetch_array($ret);

                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                        {
                                                                                $topic = new Topic($donnees['titre_s']);



                                                                                if(!(empty($donnees['sstitre'])))
                                                                                        $topic->sous_titre($donnees['sstitre']);
                                                                                $id_forum = $donnees['id'];
                                                                                $topic->liste($idforum);
                                                                                $topic->boutons($_GET['idf'], $id_forum, $verrou);


                                                                                //Resolution du topic
                                                                                $resolu =  mysql_result(mysql_query("SELECT resolu FROM forum_sujet WHERE id = '".$_GET['idf']."' "), 0);
                                                                                if($resolu)
                                                                                        $topic->bandeau_resolu();
                                                                                //fin resolution topic


                                                                                $topic->debut_tableau();
                                                                                //Gestion des pages

                                                                                        $nb_mess_par_page = 10;

                                                                                        $req = mysql_query("SELECT COUNT(*) AS nb_messages FROM forum_reponse WHERE id_sujet='".$_GET['idf']."' ");
                                                                                        $data = mysql_fetch_array($req);

                                                                                        $totalDesMessages = $data['nb_messages'];

                                                                                        $nombreDePages  = ceil($totalDesMessages / $nb_mess_par_page);



                                                                                        if(isset($_GET['reponse']))
                                                                                                $page = ancre($_GET['idf'], $_GET['reponse'], $nb_mess_par_page, $nombreDePages);
                                                                                        else
                                                                                        {
                                                                                                if(isset($_GET['page']) AND $_GET['page'] > 0 AND $_GET['page'] <= $nombreDePages)
                                                                                                $page = $_GET['page'];
                                                                                                else
                                                                                                $page = 1;
                                                                                        }

                                                                                        $premierMessageAafficher = ($page - 1) * $nb_mess_par_page;


                                                                                //Fin gestion des pages


                                                                                $topic->thead($_GET['idf'], $page, $nombreDePages);
                                                                                $topic->tfoot($_GET['idf'], $page, $nombreDePages);
                                                                                if($page == 1)
                                                                                {
                                                                                        $premierMessageAafficher = ($page - 1) * $nb_mess_par_page;
                                                                                }
                                                                                else
                                                                                {
                                                                                        $premierMessageAafficher = (($page - 1) * $nb_mess_par_page) - 1; //Message précédent
                                                                                        $nb_mess_par_page++; // un message de plus par page
                                                                                }
                                                                                $retour = mysql_query('SELECT membres.pourcentage AS pourcentage, membres.id AS id_m, membres.pseudo, membres.rang, forum_reponse.id_pseudo_modif AS id_p_m, forum_reponse.timestamp_modif AS time_mod, forum_reponse.id AS id_r, forum_reponse.timestamp, forum_reponse.html, forum_reponse.html
                                                                                                                                        FROM forum_sujet
                                                                                                                                        INNER JOIN forum_reponse
                                                                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                                                        INNER JOIN membres
                                                                                                                                        ON (forum_reponse.id_pseudo = membres.id)
                                                                                                                                        WHERE forum_sujet.id = '.$_GET['idf'].'
                                                                                                                                        ORDER BY forum_reponse.timestamp ASC
                                                                                                                                        LIMIT ' . $premierMessageAafficher . ', ' . $nb_mess_par_page )  OR DIE (mysql_error());
                                                                                $i=1;
                                                                                while($donnees = mysql_fetch_array($retour))
                                                                                {

                                                                                        $topic->infos($donnees['id_m'], $donnees['pseudo'], $donnees['timestamp'], $_GET['idf'], $donnees['id_r']);
                                                                                        $topic->corps($donnees['rang'], $donnees['html'], $page, $i, $donnees['id_m'], $_GET['idf'], $donnees['id_r'], $donnees['id_p_m'], $donnees['time_mod'], false, 0, 0, $donnees['pourcentage']);
                                                                                        mysql_query("UPDATE forum_suivre_topic SET envoye = '0' WHERE id_membre = '".$_SESSION['id']."' AND id_topic = '".$_GET['idf']."' AND id_reponse <= '".$donnees['id_r']."' ")OR DIE (mysql_error());
                                                                                        $i++;
                                                                                }

                                                                                $topic->fin_tableau();
                                                                                $topic->boutons($_GET['idf'], $id_forum, $verrou);
                                                                                $topic->liste($idforum);
                                                                                $envoi = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_suivre_topic WHERE id_membre = '".$_SESSION['id']."' AND id_topic = '".$_GET['idf']."' "), 0);
                                                                                $topic->suivre_topic($_GET['idf'], $envoi);

                                                                                if($auteur == $_SESSION['id'] OR droit_dacces(9))
                                                                                        $topic->resoudre_topic($_GET['idf'], $resolu);

                                                                                if(droit_dacces(9))
                                                                                        $topic->verrouiller($_GET['idf'], $verrou);

                                                                                echo $topic;
                                                                        }
                                                                        else
                                                                                mess(112, './forum.html');
                                                                }
                                                                else
                                                                        mess(106, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 4: //Rediger un topic
                                {
                                        if($nombreforum > 0)
                                        {
                                                $retour = mysql_query("SELECT rang_decriture FROM forum_forum WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                $donnees = mysql_fetch_array($retour);
                                                //rajouter sécuriter de la catégorie !
                                                if(droit_dacces($donnees['rang_decriture']))
                                                {
                                                        ?>
                                                        <div class="editeur"> <!-- Début class Editeur -->
                                                                <div class="editeur_titre"><!-- Début class Editeur_titre-->
                                                                        <p class="angle_h_d">
                                                                        </p>
                                                                        <p class="angle_h_g_neutre">
                                                                        </p>

                                                                        <p class="titre_editeur" style="text-align:center">Nouveau Topic</p>
                                                                </div> <!-- Fin div class editeur_titre -->
                                                                <div class="bordure_droite">
                                                                        <div class="bordure_gauche">
                                                                                <form action="./forum-5-<?php echo $_GET['idf'];?>.html" method="post" />
                                                                                <div class="editeur_texte">
                                                                                <fieldset>
                                                                                <br />
                                                                                <br />
                                                                                        Titre : <input type="text" name="titre" size="50" maxlength="80" value="<?php echo $_SESSION['titre'];?>" /><br />
                                                                                        Sous-titre : <input type="text" name="sstitre" size="50" maxlength="50" value="<?php echo $_SESSION['sstitre'];?>" /><br />

                                                                                        <?php
                                                                                        $content = $_SESSION['texte'];
                                                                                        include('./inc/zediteur.php');
                                                                                        $_SESSION['titre'] = '';
                                                                                        $_SESSION['sstitre'] = '';
                                                                                        $_SESSION['texte'] = '';
                                                                                        if(droit_dacces(10))
                                                                                        {
                                                                                                ?>
                                                                                                Type : <input type="radio" name="type" value="1" checked="checked" /> Topic, <input type="radio" name="type" value="2" /> Post-it <br />
                                                                                                Visible par : <select name="visible">
                                                                                                                                <option value="0">Tout le monde</option>
                                                                                                                                <option value="1">Les membres</option>
                                                                                                                                <option value="8">Les développeurs</option>
                                                                                                                                <option value="9">Les modérateurs</option>
                                                                                                                                <option value="10">Les administrateurs</option>
                                                                                                                        </select><br />
                                                                                                Inscriptible par : <select name="inscriptible">
                                                                                                                                <option value="1">Les membres</option>
                                                                                                                                <option value="8">Les développeurs</option>
                                                                                                                                <option value="9">Les modérateurs</option>
                                                                                                                                <option value="10">Les administrateurs</option>
                                                                                                                        </select><br />
                                                                                                <input type="checkbox" name="ferme"> Fermé ?<br />
                                                                                                <input type="checkbox" name="resolu"> Résolus ?<br /><br />
                                                                                                <?php
                                                                                        }

                                                                                        ?>
                                                                                        <input type="submit" value="Ajouter">
                                                                                </fieldset>
                                                                                </form>
                                                                                </div> <!-- Fin div class=editeur_contenu -->
                                                                        </div>
                                                                </div>
                                                                        <div class="editeur_bottom">
                                                                        <p class="angle_b_d">
                                                                        </p>
                                                                        <p class="angle_b_g">
                                                                        </p>
                                                                </div> <!-- Fin div class=editeur_bottom -->
                                                        </div> <!-- Fin div id=editeur-->
                                                        <?php
                                                }
                                                else
                                                        mess(104, './forum-2-'.$_GET['idf'].'.html');
                                        }
                                        else
                                                mess(101, './forum.html');
                                }
                                break;
                                case 5: //Rediger un nouveau topic ok
                                {
                                        $_SESSION['titre'] = $_POST['titre'];
                                        $_SESSION['sstitre'] = $_POST['sstitre'];
                                        $_SESSION['texte'] = $_POST['texte'];
                                        if(antiflood())
                                        {
                                                if($nombreforum > 0)
                                                {
                                                        //Rajouter droit d'acces en leture /ecriture, forum, catégorie !!

                                                        $retour = mysql_query("SELECT rang_decriture FROM forum_forum WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                        $donnees = mysql_fetch_array($retour);

                                                        if(droit_dacces($donnees['rang_decriture']))
                                                        {
                                                                if(!(empty($_POST['titre'])))
                                                                {
                                                                        if(strlen(stripslashes($_POST['titre'])) < 81)
                                                                        {
                                                                                if(strlen(stripslashes($_POST['sstitre'])) < 51)
                                                                                {
                                                                                        if(!(empty($_POST['texte'])))
                                                                                        {
                                                                                                $code = utf8_encode($_POST['texte']);

                                                                                                $zcode = new zcode;
                                                                                                $zcode->load($code);
                                                                                                $zcode->chemin('zform/');
                                                                                                $codeP = utf8_decode($zcode->parse());

                                                                                                /*$unzcode = new unzcode;
                                                                                                $unzcode->load($codeP);
                                                                                                $zcode->chemin('zform/');
                                                                                                $codePU = $unzcode->parse();*/
                                                                                                $titre = htmlspecialchars(stripslashes($_POST['titre']), ENT_QUOTES);
                                                                                                $sstitre = htmlspecialchars(stripslashes($_POST['sstitre']), ENT_QUOTES);
                                                                                                $ferme = 0;
                                                                                                $resolu = 0;
                                                                                                $visible = 0;
                                                                                                $type = 1;
                                                                                                if(droit_dacces(10))
                                                                                                {
                                                                                                        if($_POST['ferme'] == 'on')
                                                                                                                $ferme = 1;
                                                                                                        if($_POST['resolu'] == 'on')
                                                                                                                $resolu = 1;
                                                                                                        $visible = $_POST['visible'];
                                                                                                        $inscriptible = $_POST['inscriptible'];
                                                                                                        $type = $_POST['type'];
                                                                                                }
                                                                                                //echo $titre.'<br />'.$sstitre.'<br />'.$html.'<br />'.$bbcode.'<br />'.$ferme.'<br />'.$resolu.'<br />'.$visible.'<br />'.$type.'<br />';
                                                                                                mysql_query("INSERT INTO forum_sujet VALUES('', '".$titre."', '".$sstitre."', '".$type."', '".$_GET['idf']."', '".$_SESSION['id']."', '".$ferme."', '".$resolu."', '".time()."', '0', '".$visible."', '".$inscriptible."', '".time()."') ") OR DIE (mysql_error());
                                                                                                $idajoute = mysql_insert_id();
                                                                                                mysql_query("INSERT INTO forum_reponse VALUES('', '".$idajoute."', '".$_SESSION['id']."', '".$code."', '".addslashes($codeP)."', '1', '".time()."', '0', '0') ") OR DIE (mysql_error());
                                                                                                ajout_flood();
                                                                                                mess(108, './forum-2-'.$_GET['idf'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(12, './forum-4-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(107, './forum-4-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(106, './forum-4-'.$_GET['idf'].'.html');
                                                                }
                                                                else
                                                                        mess(105, './forum-4-'.$_GET['idf'].'.html');
                                                        }
                                                        else
                                                                mess(104, './forum-2-'.$_GET['idf'].'.html');
                                                }
                                                else
                                                        mess(101, './forum.html');
                                        }
                                        else
                                                mess(125, './forum-4-'.$_GET['idf'].'.html');
                                }
                                break;
                                case 6 :// Répondre à un Topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                $rangEcat = mysql_result(mysql_query("SELECT rang_decriture FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                if(droit_dacces($rangcat)) //Ecriture catégorie
                                                                {
                                                                        if(droit_dacces($donnees['acc_forum'])) //forum
                                                                        {
                                                                                if(droit_dacces($donnees['acc_E_forum'])) //forum
                                                                                {
                                                                                        $ret = mysql_query("SELECT id, rang_dacces, ferme FROM forum_sujet WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                        $don = mysql_fetch_array($ret);
                                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                                        {
                                                                                                if(!$don['ferme'] OR droit_dacces(9))
                                                                                                {
                                                                                                        $request = mysql_query("SELECT id_pseudo, timestamp FROM forum_reponse WHERE id_sujet = '".$_GET['idf']."' ORDER BY id DESC LIMIT 1 ") OR DIE (mysql_error());
                                                                                                        $data= mysql_fetch_array($request);
                                                                                                        if($data['id_pseudo'] == $_SESSION['id'] AND !droit_dacces(9))
                                                                                                        {
                                                                                                                echo 'ici'.time() - $data['timestamp'];
                                                                                                                echo 60*60*24;
                                                                                                                if(time() - $data['timestamp'] < 60*60*24)
                                                                                                                        $poster = false;
                                                                                                                else
                                                                                                                        $poster = true;
                                                                                                        }
                                                                                                        else
                                                                                                                $poster = true;
                                                                                                        if($poster)
                                                                                                        {
                                                                                                                if(isset($_GET['reponse']))
                                                                                                                {
                                                                                                                        $nombre_reponse = @mysql_result(mysql_query("SELECT COUNT(*) FROM forum_reponse WHERE id='".$_GET['reponse']."' AND id_sujet='".$_GET['idf']."' "), 0);
                                                                                                                        if($nombre_reponse > 0)
                                                                                                                        {
                                                                                                                                $retour = mysql_query("SELECT bbcode, pseudo FROM forum_reponse
                                                                                                                                INNER JOIN membres
                                                                                                                                ON (forum_reponse.id_pseudo = membres.id)
                                                                                                                                WHERE forum_reponse.id = '".$_GET['reponse']."' ")  OR DIE (mysql_error());
                                                                                                                                $donnees = mysql_fetch_array($retour);
                                                                                                                                $content = '<citation nom="'.$donnees['pseudo'].'">'.ifdecode($donnees['bbcode']).'</citation>';
                                                                                                                                //$message_bdd = utf8_decode(str_replace('<br />', '', ))
                                                                                                                        }
                                                                                                                }
                                                                                                                ?>
                                                                                                                <div class="editeur"> <!-- Début class Editeur -->
                                                                                                                        <div class="editeur_titre"><!-- Début class Editeur_titre-->
                                                                                                                                <p class="angle_h_d">
                                                                                                                                </p>
                                                                                                                                <p class="angle_h_g_neutre">
                                                                                                                                </p>

                                                                                                                                <p class="titre_editeur" style="text-align:center">Répondre à un Topic</p>
                                                                                                                        </div> <!-- Fin div class editeur_titre -->
                                                                                                                        <div class="bordure_droite">
                                                                                                                                <div class="bordure_gauche">
                                                                                                                                        <form action="./forum-7-<?php echo $_GET['idf'];?>.html" method="post" />
                                                                                                                                        <div class="editeur_texte">
                                                                                                                                        <fieldset>
                                                                                                                                        <br />
                                                                                                                                        <br />
                                                                                                                                                <?php
                                                                                                                                                $content = $_SESSION['texte'];
                                                                                                                                                include('./inc/zediteur.php');
                                                                                                                                                $_SESSION['texte'] = '';
                                                                                                                                                //Fermeture sujet
                                                                                                                                                if(droit_dacces(9))
                                                                                                                                                {
                                                                                                                                                        if(!$don['ferme'])
                                                                                                                                                                echo '<input type="checkbox" name="verrou" /> Fermer ce sujet<br /><br />';
                                                                                                                                                        else
                                                                                                                                                                echo '<input type="checkbox" name="deverrou" /> Ouvrir ce sujet<br /><br />';
                                                                                                                                                }
                                                                                                                                                ?>
                                                                                                                                                <input type="submit" value="Ajouter">
                                                                                                                                        </fieldset>
                                                                                                                                        </form>
                                                                                                                                        </div> <!-- Fin div class=editeur_contenu -->
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                                <div class="editeur_bottom">
                                                                                                                                <p class="angle_b_d">
                                                                                                                                </p>
                                                                                                                                <p class="angle_b_g">
                                                                                                                                </p>
                                                                                                                        </div> <!-- Fin div class=editeur_bottom -->
                                                                                                                </div> <!-- Fin div id=editeur-->
                                                                                                                <?php
                                                                                                                //  15 dernier messages
                                                                                                                $topic = new Topic("15 derniers messages");


                                                                                                                $topic->debut_tableau();

                                                                                                                $retour = mysql_query('SELECT  membres.id AS id_m, membres.pseudo, membres.rang, forum_reponse.id_pseudo_modif AS id_p_m, forum_reponse.timestamp_modif AS time_mod, forum_reponse.id AS id_r, forum_reponse.timestamp, forum_reponse.html, forum_reponse.bbcode
                                                                                                                                                                        FROM forum_sujet
                                                                                                                                                                        INNER JOIN forum_reponse
                                                                                                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                                                                                                        INNER JOIN membres
                                                                                                                                                                        ON (forum_reponse.id_pseudo = membres.id)
                                                                                                                                                                        WHERE forum_sujet.id = '.$_GET['idf'].'
                                                                                                                                                                        ORDER BY forum_reponse.timestamp DESC
                                                                                                                                                                        LIMIT 15' )  OR DIE (mysql_error());
                                                                                                                $i=2;
                                                                                                                while($donnees = mysql_fetch_array($retour))
                                                                                                                {
                                                                                                                        $topic->infos($donnees['id_m'], $donnees['pseudo'], $donnees['timestamp'], $_GET['idf'], $donnees['id_r']);
                                                                                                                        $topic->corps($donnees['rang'], $donnees['html'], $page, $i, $donnees['id_m'], $_GET['idf'], $donnees['id_r'], $donnees['id_p_m'], $donnees['time_mod'], true, $donnees['pseudo'], $donnees['bbcode'], $donnees['pourcentage']);

                                                                                                                }

                                                                                                                echo $topic;

                                                                                                                //a changer
                                                                                                                echo '</table>';
                                                                                                                //


                                                                                                                //fin 15 derniers messages
                                                                                                        }
                                                                                                        else
                                                                                                                mess(123,  './forum-6-'.$_GET['idf'].'.html');
                                                                                                }
                                                                                                else
                                                                                                        mess(114, './forum-3-'.$don['id'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(112, './forum.html');
                                                                                }
                                                                                else
                                                                                        mess(104, './forum.html');
                                                                        }
                                                                        else
                                                                                mess(106, './forum.html');
                                                                }
                                                                else
                                                                        mess(113, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 7 :// Répondre à un Topic ok
                                {
                                        $_SESSION['texte'] = $_POST['texte'];
                                        if(antiflood())
                                        {
                                                if(isset($_GET['idf']))
                                                {
                                                        if($nombresujet == 1)
                                                        {
                                                                $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                if(droit_dacces($rangcat)) //catégorie
                                                                {
                                                                        $rangEcat = mysql_result(mysql_query("SELECT rang_decriture FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                        if(droit_dacces($rangcat)) //Ecriture catégorie
                                                                        {
                                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                                {
                                                                                        if(droit_dacces($donnees['acc_E_forum'])) //forum
                                                                                        {
                                                                                                $ret = mysql_query("SELECT id, rang_dacces, ferme FROM forum_sujet WHERE id='".$_GET['idf']."' ")OR DIE (mysql_error());
                                                                                                $don = mysql_fetch_array($ret);
                                                                                                if(droit_dacces($don['rang_dacces'])) //sujet
                                                                                                {
                                                                                                        if(!$don['ferme'] OR droit_dacces(9))
                                                                                                        {
                                                                                                                $request = mysql_query("SELECT id_pseudo, timestamp FROM forum_reponse WHERE id_sujet = '".$_GET['idf']."' ORDER BY id DESC LIMIT 1 ") OR DIE (mysql_error());
                                                                                                                $data= mysql_fetch_array($request);
                                                                                                                if($data['id_pseudo'] == $_SESSION['id'] AND !droit_dacces(9))
                                                                                                                {
                                                                                                                        if(time() - $data['timestamp'] < 60*60*24)
                                                                                                                                $poster = false;
                                                                                                                        else
                                                                                                                                $poster = true;
                                                                                                                }
                                                                                                                else
                                                                                                                        $poster = true;
                                                                                                                if($poster)
                                                                                                                {
                                                                                                                        if(!(empty($_POST['texte'])))
                                                                                                                        {
                                                                                                                                $code = utf8_encode($_POST['texte']);

                                                                                                                                $zcode = new zcode;
                                                                                                                                $zcode->load($code);
                                                                                                                                $zcode->chemin('zform/');
                                                                                                                                $codeP = utf8_decode($zcode->parse());

                                                                                                                                /*$unzcode = new unzcode;
                                                                                                                                $unzcode->load($codeP);
                                                                                                                                $zcode->chemin('zform/');
                                                                                                                                $codePU = $unzcode->parse();*/
                                                                                                                                mysql_query("UPDATE forum_sujet SET timestamp_modif='".time()."' WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                                                                mysql_query("INSERT INTO forum_reponse VALUES('', '".$_GET['idf']."', '".$_SESSION['id']."', '".$code."', '".addslashes($codeP)."', '0', '".time()."', '0', '0') ") OR DIE (mysql_error());
                                                                                                                                $id = mysql_insert_id();
                                                                                                                                $request = mysql_query("SELECT id_membre FROM forum_suivre_topic WHERE id_topic = '".$_GET['idf']."' AND envoye = '0' ") OR DIE (mysql_error());
                                                                                                                                while($donnees2 = mysql_fetch_array($request))
                                                                                                                                {
                                                                                                                                        $ret = mysql_query("SELECT id, pseudo, mail FROM membres WHERE id = '".$donnees2['id_membre']."' ") OR DIE (mysql_error());
                                                                                                                                        $don = mysql_fetch_array($ret);
                                                                                                                                        if($don['id'] != $_SESSION['id'])
                                                                                                                                                notif_topic($_SESSION['id'], $_SESSION['pseudo'], $don['pseudo'], $donnees['titre_s'], 'http://www.zdesigns.fr/forum-3-'.$_GET['idf'].'-'.$id.'.html#r'.$id, $don['mail']);

                                                                                                                                }

                                                                                                                                //Fermeture/Ouverture sujet
                                                                                                                                if(droit_dacces(9))
                                                                                                                                {
                                                                                                                                        if($don['ferme'])
                                                                                                                                        {
                                                                                                                                                if($_POST['deverrou'] == 'on')
                                                                                                                                                        mysql_query("UPDATE forum_sujet SET ferme='0' WHERE id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                                                                        }
                                                                                                                                        else
                                                                                                                                        {
                                                                                                                                                if($_POST['verrou'] == 'on')
                                                                                                                                                        mysql_query("UPDATE forum_sujet SET ferme='1' WHERE id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                                                                        }
                                                                                                                                }

                                                                                                                                mysql_query("UPDATE forum_suivre_topic SET envoye='1' WHERE id_topic = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                                                                mysql_query("UPDATE forum_suivre_topic SET envoye='0' WHERE id_topic = '".$_GET['idf']."' AND id_membre = '".$_SESSION['id']."' ") OR DIE (mysql_error());
                                                                                                                                ajout_flood();

                                                                                                                                mess(115, './forum-3-'.$_GET['idf'].'-'.$id.'.html#r'.$id);
                                                                                                                        }
                                                                                                                        else
                                                                                                                                mess(12, './forum-6-'.$_GET['idf'].'.html');
                                                                                                                }
                                                                                                                else
                                                                                                                        mess(123,  './forum-6-'.$_GET['idf'].'.html');

                                                                                                        }
                                                                                                        else
                                                                                                                mess(114, './forum-3-'.$don['id'].'.html');
                                                                                                }
                                                                                                else
                                                                                                        mess(112, './forum.html');
                                                                                        }
                                                                                        else
                                                                                                mess(104, './forum.html');
                                                                                }
                                                                                else
                                                                                        mess(106, './forum.html');
                                                                        }
                                                                        else
                                                                                mess(113, './forum.html');
                                                                }
                                                                else
                                                                        mess(103, './forum.html');
                                                        }
                                                        else
                                                                mess(110, './forum.html');
                                                }
                                                else
                                                        mess(109, './forum.html');
                                        }
                                        else
                                                mess(125, './forum-6-'.$_GET['idf'].'.html');
                                }
                                break;
                                case 8: // Editer une réponse
                                {
                                        if(isset($_GET['idf']))
                                        {

                                                $ret = mysql_query("SELECT ferme, rang_dacces, id_sujet FROM forum_sujet INNER JOIN forum_reponse
                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                        WHERE forum_reponse.id='".$_GET['idf']."' ")OR DIE (mysql_error());
                                                $don = mysql_fetch_array($ret);
                                                if(droit_dacces($don['rang_dacces'])) //sujet
                                                {

                                                        if(!$don['ferme'] OR droit_dacces(9))
                                                        {
                                                                if($nombrereponse == 1)
                                                                {

                                                                                $retour = mysql_query("SELECT id_pseudo, bbcode FROM forum_reponse WHERE id = '".$_GET['idf']."' ")  OR DIE (mysql_error());
                                                                                $donnees = mysql_fetch_array($retour);

                                                                        if(($donnees['id_pseudo'] == $_SESSION['id'] AND $donnees['id_pseudo'] != 0) OR droit_dacces(9))
                                                                        {
                                                                                ?>
                                                                                <div class="news"> <!-- Début class News -->
                                                                                        <div class="news_titre"><!-- Début class news_titre-->
                                                                                                <p class="angle_h_d">
                                                                                                </p>
                                                                                                <p class="angle_h_g">
                                                                                                </p>

                                                                                                <p class="titre_news" style="text-align:center">Editer un réponse</p>
                                                                                        </div> <!-- Fin div class news_titre -->
                                                                                        <div class="bordure_droite">
                                                                                                <div class="bordure_gauche">
                                                                                                        <form action="./forum-9-<?php echo $_GET['idf'];?>.html" method="post" />
                                                                                                        <div class="news_texte">
                                                                                                        <fieldset>
                                                                                                        <br />
                                                                                                        <br />
                                                                                                                <?php
                                                                                                                if(empty($_SESSION['texte']))
                                                                                                                    $content = ifdecode($donnees['bbcode']);
                                                                                                                else
                                                                                                                    $content = $_SESSION['texte'];
                                                                                                                include('./inc/zediteur.php');
                                                                                                                $_SESSION['texte'] = '';
                                                                                                                ?>

                                                                                                                <input type="submit" value="Modifier">
                                                                                                                </fieldset>
                                                                                                        </form>
                                                                                                        </div> <!-- Fin div class=news_contenu -->
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="news_bottom">
                                                                                                <p class="angle_b_d">
                                                                                                </p>
                                                                                                <p class="angle_b_g">
                                                                                                </p>
                                                                                        </div> <!-- Fin div class=news_bottom -->
                                                                                </div> <!-- Fin div id=news -->
                                                                                <?php
                                                                        }
                                                                        else
                                                                                mess(117, './forum-3-'.$_GET['idf'].'.html');
                                                                }
                                                                else
                                                                        mess(116, './forum-3-'.$_GET['idf'].'.html');


                                                        }
                                                        else
                                                                mess(114, './forum-3-'.$don['id_sujet'].'-'.$_GET['idf'].'.html#r'.$_GET['idf']);
                                                }
                                                else
                                                        mess(112, './forum.html');

                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 9: //Editer une réponse ok
                                {
                                        $_SESSION['texte'] = $_POST['texte'];
                                        if(isset($_GET['idf']))
                                        {

                                                $ret = mysql_query("SELECT ferme, rang_dacces, id_sujet FROM forum_sujet INNER JOIN forum_reponse
                                                                                        ON (forum_reponse.id_sujet = forum_sujet.id)
                                                                                        WHERE forum_reponse.id='".$_GET['idf']."' ")OR DIE (mysql_error());
                                                $don = mysql_fetch_array($ret);
                                                if(droit_dacces($don['rang_dacces'])) //sujet
                                                {

                                                        if(!$don['ferme'] OR droit_dacces(9))
                                                        {
                                                                if($nombrereponse == 1)
                                                                {
                                                                                $retour = mysql_query("SELECT id_pseudo, id_sujet, bbcode FROM forum_reponse WHERE id = '".$_GET['idf']."' ")  OR DIE (mysql_error());
                                                                                $donnees = mysql_fetch_array($retour);
                                                                        if(($donnees['id_pseudo'] == $_SESSION['id'] AND $donnees['id_pseudo'] != 0) OR droit_dacces(9))
                                                                        {
                                                                                if(!(empty($_POST['texte'])))
                                                                                {
                                                                                        $code = utf8_encode($_POST['texte']);

                                                                                        $zcode = new zcode;
                                                                                        $zcode->load($code);
                                                                                        $zcode->chemin('zform/');
                                                                                        $codeP = utf8_decode($zcode->parse());

                                                                                        /*$unzcode = new unzcode;
                                                                                        $unzcode->load($codeP);
                                                                                        $zcode->chemin('zform/');
                                                                                        $codePU = $unzcode->parse();*/
                                                                                        mysql_query("UPDATE forum_reponse SET bbcode='".htmlentities($code, ENT_QUOTES)."', html='".addslashes($codeP)."', id_pseudo_modif='".$_SESSION['id']."', timestamp_modif='".time()."' WHERE id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                        mess(118, './forum-3-'.$donnees['id_sujet'].'-'.$_GET['idf'].'.html#r'.$_GET['idf']);
                                                                                }
                                                                                else
                                                                                        mess(12, './forum-6-'.$_GET['idf'].'.html');

                                                                        }
                                                                        else
                                                                                mess(117, './forum-3-'.$_GET['idf'].'.html');
                                                                }
                                                                else
                                                                        mess(116, './forum-3-'.$_GET['idf'].'.html');


                                                        }
                                                        else
                                                                mess(114, './forum-3-'.$don['id_sujet'].'-'.$_GET['idf'].'.html#r'.$_GET['idf']);
                                                }
                                                else
                                                        mess(112, './forum.html');

                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 10 ://Suivre un topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                {
                                                                        $ret = mysql_query("SELECT rang_dacces FROM forum_sujet WHERE id='".$donnees['idf']."' ")OR DIE (mysql_error());
                                                                        $don = mysql_fetch_array($ret);

                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                        {
                                                                                $envoi = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_suivre_topic WHERE id_membre = '".$_SESSION['id']."' AND id_topic = '".$_GET['idf']."' "), 0);
                                                                                if(!$envoi)
                                                                                {
                                                                                        $request = mysql_query("SELECT id FROM forum_reponse WHERE id_sujet = '".$_GET['idf']."' ORDER BY id DESC LIMIT 1 ") OR DIE (mysql_error());
                                                                                        $don = mysql_fetch_array($request);

                                                                                        mysql_query("INSERT INTO forum_suivre_topic VALUES('".$_GET['idf']."', '".$_SESSION['id']."', '0', '".$don['id']."') ") OR DIE  (mysql_error());
                                                                                        mess(119, './forum-3-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(120, './forum-3-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(112, './forum.html');
                                                                }
                                                                else
                                                                        mess(106, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 11 :// Ne plus suivre un topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                {
                                                                        $ret = mysql_query("SELECT rang_dacces FROM forum_sujet WHERE id='".$donnees['idf']."' ")OR DIE (mysql_error());
                                                                        $don = mysql_fetch_array($ret);

                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                        {
                                                                                $envoi = mysql_result(mysql_query("SELECT COUNT(*) FROM forum_suivre_topic WHERE id_membre = '".$_SESSION['id']."' AND id_topic = '".$_GET['idf']."' "), 0);
                                                                                if($envoi)
                                                                                {
                                                                                        mysql_query("DELETE FROM forum_suivre_topic WHERE id_membre = '".$_SESSION['id']."' AND id_topic = '".$_GET['idf']."' ") OR DIE  (mysql_error());
                                                                                        mess(121, './forum-3-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(122, './forum-3-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(112, './forum.html');
                                                                }
                                                                else
                                                                        mess(106, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 12 ://Résoudre un topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                {
                                                                        $ret = mysql_query("SELECT rang_dacces FROM forum_sujet WHERE id='".$donnees['idf']."' ")OR DIE (mysql_error());
                                                                        $don = mysql_fetch_array($ret);

                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                        {
                                                                                $ret =  mysql_query("SELECT resolu, id_membre FROM forum_sujet WHERE id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                $data = mysql_fetch_array($ret);

                                                                                if($data['id_membre'] == $_SESSION['id'] OR droit_dacces(9)) //À condition que le membre soit bien l'auteur du sujet ou que ce soit un admin qui demande la résolution
                                                                                {
                                                                                        if(!$data['resolu']) // À condition que le sujet ne soit pas déjà résolu !
                                                                                        {
                                                                                                mysql_query("UPDATE forum_sujet SET resolu='1' WHERE id = '".$_GET['idf']."' ") OR DIE  (mysql_error());
                                                                                                mess(126, './forum-3-'.$_GET['idf'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(129, './forum-3-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(128, './forum-3-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(112, './forum.html');
                                                                }
                                                                else
                                                                        mess(106, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;
                                case 13 ://Dérésoudre un topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                {
                                                                        $ret = mysql_query("SELECT rang_dacces FROM forum_sujet WHERE id='".$donnees['idf']."' ")OR DIE (mysql_error());
                                                                        $don = mysql_fetch_array($ret);

                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                        {
                                                                                $ret =  mysql_query("SELECT resolu, id_membre FROM forum_sujet WHERE id = '".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                $data = mysql_fetch_array($ret);

                                                                                if($data['id_membre'] == $_SESSION['id'] OR droit_dacces(9)) //À condition que le membre soit bien l'auteur du sujet ou que ce soit un admin qui demande la résolution
                                                                                {
                                                                                        if($data['resolu']) // À condition que le sujet soit résolu !
                                                                                        {

                                                                                                mysql_query("UPDATE forum_sujet SET resolu='0' WHERE id = '".$_GET['idf']."' ") OR DIE  (mysql_error());
                                                                                                mess(127, './forum-3-'.$_GET['idf'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(130, './forum-3-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(128, './forum-3-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(112, './forum.html');
                                                                }
                                                                else
                                                                        mess(106, './forum.html');
                                                        }
                                                        else
                                                                mess(103, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;

                                case 14 :// Verrouiller un Topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                $rangEcat = mysql_result(mysql_query("SELECT rang_decriture FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                if(droit_dacces($rangcat)) //Ecriture catégorie
                                                                {
                                                                        if(droit_dacces($donnees['acc_forum'])) //forum
                                                                        {
                                                                                if(droit_dacces($donnees['acc_E_forum'])) //forum
                                                                                {
                                                                                        $ret = mysql_query("SELECT id_membre, titre, rang_dacces, ferme FROM forum_sujet WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                        $don = mysql_fetch_array($ret);
                                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                                        {
                                                                                                if(!$don['ferme'])
                                                                                                {
                                                                                                        echo '<h2>Verrouiller un sujet</h2>';
                                                                                                        echo 'Êtes vous sûre de vouloir verrouiller le sujet "'.stripslashes($don['titre']).'" de '.membre($don['id_membre'], true, true).' ?';
                                                                                                        echo '<div class="ouinon"><form action="./forum-15-'.$_GET['idf'].'.html" method="post" >
                                                                                                                        <input type="submit" value="Oui" name="Verrou" />
                                                                                                                </form>
                                                                                                                <form action="./forum-3-'.$_GET['idf'].'.html" method="post" >
                                                                                                                        <input type="submit" value="Non" />
                                                                                                                </form></div>';
                                                                                                }
                                                                                                else
                                                                                                        mess(135, './forum-2-'.$donnees['id'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(134, './forum.html');
                                                                                }
                                                                                else
                                                                                        mess(134, './forum.html');
                                                                        }
                                                                        else
                                                                                mess(134, './forum.html');
                                                                }
                                                                else
                                                                        mess(134, './forum.html');
                                                        }
                                                        else
                                                                mess(134, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;

                                case 15 :// Verrouiller un Topic ok
                                {
                                        if(isset($_POST['Verrou']))
                                        {
                                                if(isset($_GET['idf']))
                                                {
                                                        if($nombresujet == 1)
                                                        {
                                                                $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                if(droit_dacces($rangcat)) //catégorie
                                                                {
                                                                        $rangEcat = mysql_result(mysql_query("SELECT rang_decriture FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                        if(droit_dacces($rangcat)) //Ecriture catégorie
                                                                        {
                                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                                {
                                                                                        if(droit_dacces($donnees['acc_E_forum'])) //forum
                                                                                        {
                                                                                                $ret = mysql_query("SELECT id_membre, titre, rang_dacces, ferme FROM forum_sujet WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                                $don = mysql_fetch_array($ret);
                                                                                                if(droit_dacces($don['rang_dacces'])) //sujet
                                                                                                {
                                                                                                        if(!$don['ferme'])
                                                                                                        {
                                                                                                                mysql_query("UPDATE forum_sujet SET ferme='1' WHERE id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                                                                                                mess(136, './forum-2-'.$donnees['id'].'.html');
                                                                                                        }
                                                                                                        else
                                                                                                                mess(135, './forum-2-'.$donnees['id'].'.html');
                                                                                                }
                                                                                                else
                                                                                                        mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                }
                                                                else
                                                                        mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                        }
                                                        else
                                                                mess(110, './forum.html');
                                                }
                                                else
                                                        mess(109, './forum.html');
                                        }
                                        else
                                                mess(12, 'forum-3-'.$_GET['idf'].'.html');
                                }
                                break;

                                case 16 :// Déverrouiller un Topic
                                {
                                        if(isset($_GET['idf']))
                                        {
                                                if($nombresujet == 1)
                                                {
                                                        $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                        if(droit_dacces($rangcat)) //catégorie
                                                        {
                                                                $rangEcat = mysql_result(mysql_query("SELECT rang_decriture FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                if(droit_dacces($rangcat)) //Ecriture catégorie
                                                                {
                                                                        if(droit_dacces($donnees['acc_forum'])) //forum
                                                                        {
                                                                                if(droit_dacces($donnees['acc_E_forum'])) //forum
                                                                                {
                                                                                        $ret = mysql_query("SELECT id_membre, titre, rang_dacces, ferme FROM forum_sujet WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                        $don = mysql_fetch_array($ret);
                                                                                        if(droit_dacces($don['rang_dacces'])) //sujet
                                                                                        {
                                                                                                if($don['ferme'])
                                                                                                {
                                                                                                        echo '<h2>Verrouiller un sujet</h2>';
                                                                                                        echo 'Êtes vous sûre de vouloir déverrouiller le sujet "'.stripslashes($don['titre']).'" de '.membre($don['id_membre'], true, true).' ?';
                                                                                                        echo '<form action="./forum-17-'.$_GET['idf'].'.html" method="post" >
                                                                                                                        <input type="submit" value="Oui" name="Deverrou" />
                                                                                                                </form>
                                                                                                                <form action="./forum-3-'.$_GET['idf'].'.html" method="post" >
                                                                                                                        <input type="submit" value="Non" />
                                                                                                                </form>';
                                                                                                }
                                                                                                else
                                                                                                        mess(137, './forum-2-'.$donnees['id'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(134, './forum.html');
                                                                                }
                                                                                else
                                                                                        mess(134, './forum.html');
                                                                        }
                                                                        else
                                                                                mess(134, './forum.html');
                                                                }
                                                                else
                                                                        mess(134, './forum.html');
                                                        }
                                                        else
                                                                mess(134, './forum.html');
                                                }
                                                else
                                                        mess(110, './forum.html');
                                        }
                                        else
                                                mess(109, './forum.html');
                                }
                                break;

                                case 17 :// Déverrouiller un Topic ok
                                {
                                        if(isset($_POST['Deverrou']))
                                        {
                                                if(isset($_GET['idf']))
                                                {
                                                        if($nombresujet == 1)
                                                        {
                                                                $rangcat = mysql_result(mysql_query("SELECT rang_dacces FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                if(droit_dacces($rangcat)) //catégorie
                                                                {
                                                                        $rangEcat = mysql_result(mysql_query("SELECT rang_decriture FROM forum_cat WHERE id='".$donnees['id_c']."' "), 0);
                                                                        if(droit_dacces($rangcat)) //Ecriture catégorie
                                                                        {
                                                                                if(droit_dacces($donnees['acc_forum'])) //forum
                                                                                {
                                                                                        if(droit_dacces($donnees['acc_E_forum'])) //forum
                                                                                        {
                                                                                                $ret = mysql_query("SELECT id_membre, titre, rang_dacces, ferme FROM forum_sujet WHERE id='".$_GET['idf']."' ") OR DIE (mysql_error());
                                                                                                $don = mysql_fetch_array($ret);
                                                                                                if(droit_dacces($don['rang_dacces'])) //sujet
                                                                                                {
                                                                                                        if($don['ferme'])
                                                                                                        {
                                                                                                                mysql_query("UPDATE forum_sujet SET ferme='0' WHERE id = '".$_GET['idf']."' ") OR DIE(mysql_error());
                                                                                                                mess(138, './forum-2-'.$donnees['id'].'.html');
                                                                                                        }
                                                                                                        else
                                                                                                                mess(137, './forum-2-'.$donnees['id'].'.html');
                                                                                                }
                                                                                                else
                                                                                                        mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                                        }
                                                                                        else
                                                                                                mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                                }
                                                                                else
                                                                                        mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                        }
                                                                        else
                                                                                mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                                }
                                                                else
                                                                        mess(134, './forum-3-'.$_GET['idf'].'.html');
                                                        }
                                                        else
                                                                mess(110, './forum.html');
                                                }
                                                else
                                                        mess(109, './forum.html');
                                        }
                                        else
                                                mess(12, 'forum-3-'.$_GET['idf'].'.html');
                                }
                                break;
                                default:
                                        mess(5, './forum.html');
                        }
                }
                ?>
        </div>	<!-- div id=forum-->

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
ob_end_flush();// NE PAS SUPPRIMER !!
?>