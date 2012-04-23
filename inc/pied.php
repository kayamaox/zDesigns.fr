<?php
$req_designs = $BDD->query("SELECT id FROM designs WHERE active != '3'");
$nb_designs = mysql_num_rows($req_designs);

$req_valid = $BDD->query("SELECT id FROM designs WHERE active = '1'");
$nb_valid = mysql_num_rows($req_valid);

$req_mbr = $BDD->query("SELECT id FROM membres");
$nb_mbr = mysql_num_rows($req_mbr);
?>
                        </div>
                        <hr class="clear" />
                        <div id="pied">
                            <div id="pied_in">
                                <div id="pied_col1" class="col">
                                    <a href="<?php echo ROOT; ?>livredor.html">Livre d'Or</a>
                                    <a href="http://bugs.zdesigns.fr/">Bug Tracker</a>
                                    <a href="">Nous contacter</a>
                                </div>
                                <div id="pied_col2" class="col">
                                    <span><?php echo $nb_designs; ?> zDesigns en création</span>
                                    <span><?php echo pluralize($nb_valid, "{Aucun|Un|{#}} design{s} en validation"); ?></span>
                                    <span><?php echo $nb_mbr; ?> membres inscrits</span>
                                </div>
                                <div id="pied_col3" class="col">
                                    <?php
                                    require_once('./inc/functions.php');
                                    $top = $BDD->query("SELECT id, titre, complet, note
                                                        FROM designs
                                                        WHERE active = '2' AND complet > ".POURCENT_MIN."
                                                        ORDER BY note_points DESC, note DESC
                                                        LIMIT 0, 3");

                                    while($t = mysql_fetch_assoc($top)){
                                        ?>
                                    <a href="<?php echo url_zdesigns(0, 0, $t['id'], $t['titre']); ?>"><?php echo $t['titre']; ?></a>
                                        <?php
                                        note($t['note']);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
