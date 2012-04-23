            <h3>Rapport <?php if($nb_tot > 0){ ?><a href="#all_maj" class="fr">Tout mettre à jour</a><?php } ?></h3>
            <ul>
                <li>
                    <span class="ico plus2"></span>
                    <span class="elem_name"><?php echo pluralize($nb_dossiers, '{Aucun|Un|{#}} dossier{s} manquant{s}'); ?></span>
                    <?php if($nb_dossiers > 0){ ?>
                    <a href="#mass_dir_maj" class="elem_opt"><?php echo pluralize($nb_dossiers, 'Récupérer {ce|tous ces} dossier{s}'); ?></a>
                    <ul>
                        <?php
                        foreach($rapport->getDossiers() as $f){
                            echo '<li>
                                     <span class="ico plus"></span>
                                     <a href="'.$f['url_from'].'" rel="'.$f['url_to'].'" class="elem_name" onclick="return false;">'.$f['nom'].'</a>
                                     <a href="#dir_maj" class="elem_opt">Récupérer ce dossier</a>
                                  </li>';
                        }
                        ?>
                    </ul>
                    <?php } ?>
                </li>
                <li>
                    <span class="ico plus2"></span>
                    <span class="elem_name"><?php echo pluralize($nb_files, '{Aucun|Un|{#}} fichier{s} manquant{s}'); ?></span>
                    <?php if($nb_files > 0){ ?>
                    <a href="#all_maj" class="elem_opt"><?php echo pluralize($nb_files, 'Récupérer {ce|tous ces} fichier{s}'); ?></a>
                    <ul>
                        <?php
                        foreach($rapport->getFichiers() as $f){
                            echo '<li>
                                     <span class="ico plus"></span>
                                     <a href="'.$f['url_from'].'" rel="'.$f['url_to'].'" class="zoombox zgallery_rapport elem_name">'.$f['nom'].'</a>
                                     <a href="#file_maj" class="elem_opt">Récupérer ce fichier</a>
                                  </li>';
                        }
                        ?>
                    </ul>
                    <?php } ?>
                </li>
            </ul>