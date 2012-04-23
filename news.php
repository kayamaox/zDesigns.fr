<?php
$titre = 'Les News';

$css[] = 'slider';
$js[] = 'slider';
$js[] = 'spotlight';

include('./inc/head.php');
require_once('./inc/functions.php');
require_once('./classes/news.php');
?>


<div id="arianne">
    <a href="<?php echo ROOT; ?>">Les News</a>
</div>

<br/>
<h1>Les 10 dernières nouvelles</h1>
<?php
    $news = new News($BDD, 10);
    while($tab_news = mysql_fetch_array($news->recupere())){
        $tab_news['titre'] = ifdecode($tab_news['titre']);
        $tab_news['html'] = ifdecode($tab_news['html']);
    ?>
        <div class="news">
            <div class="news_gauche">
                <div class="news_droite">
                    <span id="news-<?php echo $tab_news['id']; ?>" class="news_titre"><?php echo $tab_news['titre']; ?></span>

                    <div class="news_texte zcode">
                       <?php echo $tab_news['html'];?>
                    </div>

                    <div class="news_auteur">
                        <?php if(droit_dacces(10)){ ?>
                        <span class="actions_com">
                            <a href="<?php echo ROOT.'admin-news-editer-'.$tab_news['id'].'.html'; ?>">Editer</a> |
                            <a href="<?php echo ROOT.'admin-news-supprimer-'.$tab_news['id'].'.html'; ?>">Supprimer</a> |
                        </span>
                        <?php } ?>
                        Posté <?php echo  parse_date($tab_news['timestamp'], false, 'le');?> par <a href="./membres-<?php echo $tab_news['id_pseudo'];?>.html"><?php echo $tab_news['pseudo'];?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>