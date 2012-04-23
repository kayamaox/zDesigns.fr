<?php
$req_valid = $BDD->query("SELECT id FROM designs WHERE active = '1'");
$req_todo = $BDD->query("SELECT id FROM todolist WHERE statut != '3' AND membre_concerne = '".$_SESSION['id']."'");

$nb_valid = mysql_num_rows($req_valid);
$nb_todo = mysql_num_rows($req_todo);
?>
<div id="sidebar">
    <div class="bloc_sidebar">
        <h3>Les zDesigns</h3>
        <ul>
            <li><a href="<?php echo ROOT; ?>admin-zdesigns.html">Voir tous les zDesigns</a></li>
            <li><a href="<?php echo ROOT; ?>admin-zdesigns-f2-en-validation.html">En validation (<?php echo $nb_valid; ?>)</a></li>
            <li><a href="<?php echo ROOT; ?>admin-zdesigns-cat.html">Les catégories</a></li>
        </ul>
    </div>
    <div class="bloc_sidebar">
        <h3>Les News</h3>
        <ul>
            <li><a href="<?php echo ROOT; ?>admin-news.html">Toutes les news</a></li>
            <li><a href="<?php echo ROOT; ?>admin-news-poster.html">Poster une news</a></li>
        </ul>
    </div>
    <div class="bloc_sidebar">
        <h3>Gestion</h3>
        <ul>
            <li><a href="<?php echo ROOT; ?>admin-todo.html">Todo-List (<?php echo $nb_todo; ?>)</a></li>
            <li><a href="http://zdesigns.fr:2082">cPanel</a></li>
            <li><a href="<?php echo ROOT; ?>">Mail</a></li>
        </ul>
    </div>
	<div class="bloc_sidebar">
        <h3>Le Site</h3>
        <ul>
            <li><a href="<?php echo ROOT; ?>zexploreur_zds.html">Parcourir zDs</a></li>
        </ul>
    </div>
</div>