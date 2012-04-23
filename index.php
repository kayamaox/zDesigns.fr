<?php
@session_start();

$titre = 'Accueil';

$css[] = 'slider';
$js[] = 'slider';

if(isset($_GET['e']) && isset($_SESSION['message']['_error'])){
    $messageError = $_SESSION['message']['_error'];
    unset($_SESSION['message']['_error']);
}

include('./inc/head.php');
require_once('./inc/functions.php');
require_once('./classes/news.php');

$req_slide_nouv = $BDD->query("SELECT membres.id AS idm, designs.id AS idd, designs.titre AS titre, membres.pseudo AS pseudo, imprim, description, vu, complet, note
                               FROM designs
                               INNER JOIN membres ON membres.id = designs.id_membre
                               WHERE active='2' AND complet > ".POURCENT_MIN."
                               ORDER BY date DESC
                               LIMIT 0, 1");
$req_slide_hasa = $BDD->query("SELECT membres.id AS idm, designs.id AS idd, designs.titre AS titre, membres.pseudo AS pseudo, imprim, description, vu, complet, note
                               FROM designs
                               INNER JOIN membres ON membres.id = designs.id_membre
                               WHERE active='2' AND complet > ".POURCENT_MIN."
                               ORDER BY RAND()
                               LIMIT 0, 1");
$req_slide_plus = $BDD->query("SELECT membres.id AS idm, designs.id AS idd, designs.titre AS titre, membres.pseudo AS pseudo, imprim, description, vu, complet, note
                               FROM designs
                               INNER JOIN membres ON membres.id = designs.id_membre
                               WHERE active='2' AND complet > ".POURCENT_MIN."
                               ORDER BY vu DESC
                               LIMIT 0, 1");

$slide_nouv = mysql_fetch_assoc($req_slide_nouv);
$slide_hasa = mysql_fetch_assoc($req_slide_hasa);
$slide_plus = mysql_fetch_assoc($req_slide_plus);
?>


<div id="arianne">
    <a href="<?php echo ROOT; ?>">Accueil</a>
</div>

<div id="slider_conteneur">
    <div id="garnitures" class="dn">
        <div id="garnituresIn">
            <div id="garnituresIn2">
                <div id="garnitureGauche" class="garniture fl"></div>
                <div id="garnitureCentre" class="fl"></div>
                <div id="garnitureDroite" class="garniture fl"></div>
            </div>
        </div>
    </div>
    <div id="slider">
        <div id="sliderIn">
            <div id="activeSlide">
                <div id="apercuSlider" class="fl">
                    <div id="apercuSliderOmbre"></div>
                    <div id="apercuSliderIn">
                        <div id="apercuSlideVert" class="transition"></div>
                        <div id="apercuSlideHoriz" class="transition"></div>
                        <img src="<?php echo ROOT; ?><?php echo str_replace('./', '', $slide_hasa['imprim']); ?>" alt="Aperçu de <?php echo $slide_hasa['titre']; ?>" id="apercuFond" width="100%" />
                        <script type="text/javascript">$('#apercuFond').hide();</script>
                    </div>
                </div>
                <div id="volet_droit_slider" class="fl">
                    <h3 class="nomDesign"><?php echo $slide_hasa['titre']; ?></h3>
                    <span class="description">
                        <?php echo $slide_hasa['description']; ?>
                    </span>
                    <a href="<?php echo url_zdesigns(0, 0, $slide_hasa['idd'], $slide_hasa['titre']); ?>" class="details">Voir la fiche du design</a>
                    <?php note($slide_hasa['note']); ?>
                    <div class="infos">
                        Auteur : <span class="auteur"><?php echo $slide_hasa['pseudo']; ?></span><br />
                        Déjà essayé <b class="nbEssais"><?php echo $slide_hasa['vu']; ?></b> fois<br />
                        Complet à <span class="complet"><?php echo $slide_hasa['complet']; ?>%</span>
                    </div>
                    <a href="./redirection-<?php echo $slide_hasa['idd']?>.html" class="essayer">
                        <img src="<?php echo DESIGN_DIR; ?>images/essayer.png" alt="Essayer ce zDesign sur le Site du Zéro" width="158" height="55" />
                    </a>
                </div>
            </div>
            <div id="slides">
                <div id="Slide1" class="slide dn">
                    <img src="<?php echo ROOT; ?><?php echo str_replace('./', '', $slide_nouv['imprim']); ?>" alt="" width="100%" />
                    <span class="nomDesign"><?php echo $slide_nouv['titre']; ?></span>
                    <span class="description">
                        <?php echo $slide_nouv['description']; ?>
                    </span>
                    <?php note($slide_nouv['note']); ?>
                    <a href="<?php echo url_zdesigns(0, 0, $slide_nouv['idd'], $slide_nouv['titre']); ?>" class="details"></a>
                    <span class="auteur"><?php echo $slide_nouv['pseudo']; ?></span>
                    <span class="nbEssais"><?php echo $slide_nouv['vu']; ?></span>
                    <span class="complet"><?php echo $slide_nouv['complet']; ?>%</span>
                    <a href="./redirection-<?php echo $slide_nouv['idd']?>.html" class="essayer"></a>
                </div>
                <div id="Slide2" class="slide dn">
                    <img src="<?php echo ROOT; ?><?php echo str_replace('./', '', $slide_hasa['imprim']); ?>" alt="" width="100%" />
                    <span class="nomDesign"><?php echo $slide_hasa['titre']; ?></span>
                    <span class="description">
                        <?php echo $slide_hasa['description']; ?>
                    </span>
                    <?php note($slide_hasa['note']); ?>
                    <a href="<?php echo url_zdesigns(0, 0, $slide_hasa['idd'], $slide_hasa['titre']); ?>" class="details"></a>
                    <span class="auteur"><?php echo $slide_hasa['pseudo']; ?></span>
                    <span class="nbEssais"><?php echo $slide_hasa['vu']; ?></span>
                    <span class="complet"><?php echo $slide_hasa['complet']; ?>%</span>
                    <a href="./redirection-<?php echo $slide_hasa['idd']?>.html" class="essayer"></a>
                </div>
                <div id="Slide3" class="slide dn">
                    <img src="<?php echo ROOT; ?><?php echo str_replace('./', '', $slide_plus['imprim']); ?>" alt="" width="100%" />
                    <span class="nomDesign"><?php echo $slide_plus['titre']; ?></span>
                    <span class="description">
                        <?php echo $slide_plus['description']; ?>
                    </span>
                    <?php note($slide_plus['note']); ?>
                    <a href="<?php echo url_zdesigns(0, 0, $slide_plus['idd'], $slide_plus['titre']); ?>" class="details"></a>
                    <span class="auteur"><?php echo $slide_plus['pseudo']; ?></span>
                    <span class="nbEssais"><?php echo $slide_plus['vu']; ?></span>
                    <span class="complet"><?php echo $slide_plus['complet']; ?>%</span>
                    <a href="./redirection-<?php echo $slide_plus['idd']?>.html" class="essayer"></a>
                </div>
            </div>
            <img src="<?php echo DESIGN_DIR; ?>images/loading.gif" alt="Chargement" class="dn loadingSlider" width="42" height="42" />
        </div>
        <script type="text/javascript">
            $('#sliderIn #activeSlide').hide();
            $('#sliderIn img.loadingSlider').show();
        </script>

        <div id="navbar">
            <div id="navbarIn">
                <div class="fl"><a href="" id="btnSlide1" class="active noborder" rel="1">Le p'tit nouveau</a></div>
                <div class="fl"><a href="" id="btnSlide2" class="noborder" rel="2">Tiré au hasard</a></div>
                <div class="fl"><a href="" id="btnSlide3" class="noborder" rel="3">Le plus essayé</a></div>
            </div>
            <div id="bg_navbar">
                <div id="activeNavbar">Actif</div>
            </div>
        </div>
        <script type="text/javascript">
            $('#navbar').hide();
        </script>
    </div>
</div>


<h1>Qu'est-ce que zDesigns.fr ?</h1>
<div class="bloc_texte">
    <b>zDesigns.fr</b> est un site dont l'intérêt est lié au célèbre <span class="souligne">Site du Zéro</span>. Il est possible de créer des <strong>designs distants</strong> pour ce site,
    c'est à dire que n'importe qui peux créer un design qui s'adapte à la structure du <span class="souligne">Site du Zéro</span> et le partager avec le reste de la communauté.
    <ul>
        <li>Ces designs distants sont appellés plus communément <strong>zDesigns</strong>, d'où le nom du site. Celui-ci <b>regroupe la plupart des zDesigns</b> permettant
            ainsi une vision globale, pour les membres du SdZ, des designs qu'ils peuvent essayer et adopter d'un simple clic.</li>
        <li>D'autre part, il est fournit à qui le souhaite un <b>espace d'hébergement de 1,5Mo / design</b>, ainsi qu'un outil développé, appellé <strong>zExplorer</strong>,
            pour <b>accompagner le designer dans la création puis la mise à jour</b> du design au fil des évolutions du Site du Zéro.</li>
    </ul>
    zDesigns.fr est l'initative de Cyril6789 (developpeur) &amp; Kenny61 (designer), suivis pour la V2 par Alex-D (designer &amp; developpeur). Ce site est totalement indépendant du SdZ.
</div>


<?php
    $news = new News($BDD, 1);
    $tab_news = mysql_fetch_array($news->recupere());
?>
<br/>
<h1>La dernière nouvelle</h1>
<div class="news">
    <div class="news_gauche">
        <div class="news_droite">
            <span id="news-32" class="news_titre"><?php echo $tab_news['titre'];?></span>

            <div class="news_texte zcode">
               <?php echo $tab_news['html'];?>
            </div>

            <div class="news_auteur">
                Posté <?php echo parse_date($tab_news['timestamp'], false, 'le');?> par <a href="./membres-<?php echo $tab_news['id_pseudo'];?>.html"><?php echo $tab_news['pseudo'];?></a>
            </div>
        </div>
    </div>
</div>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>