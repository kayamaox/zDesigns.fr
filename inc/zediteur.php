<?php
$content = (isset($content)) ? $content : '';
$content = (isset($_POST['texte']) && $content == '') ? $_POST['texte'] : $content;
?>

<!-- ################# BARRE DE BOUTONS ################# -->
<div class="boutons_zform">
        <span class="boutons">
                <img src="zform/img/zcode/zcode_gras.png" alt="Gras" title="Gras" onclick="balise('&lt;gras&gt;','&lt;/gras&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_italique.png" alt="Italique" title="Italique" onclick="balise('&lt;italique&gt;','&lt;/italique&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_souligne.png" alt="Souligné" title="Souligné" onclick="balise('&lt;souligne&gt;','&lt;/souligne&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_barre.png" alt="Barré" title="Barré" onclick="balise('&lt;barre&gt;','&lt;/barre&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_exposant.png" alt="Exposant" title="Exposant" onclick="balise('&lt;exposant&gt;','&lt;/exposant&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_indice.png" alt="Indice" title="Indice" onclick="balise('&lt;indice&gt;','&lt;/indice&gt;'); return false;" class="bouton_cliquable" />
        </span>

        <span class="boutons">
                <img src="zform/img/zcode/zcode_liste.png" alt="Liste à puces" title="Liste à puces" onclick="add_liste(); return false;" class="bouton_cliquable" />

                <img src="zform/img/zcode/zcode_citation.png" alt="Citation" title="Citation" onclick="add_bal2('citation','nom'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_image.png" alt="Image" title="Image" onclick="balise('&lt;image&gt;','&lt;/image&gt;', 'texte'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_lien.png" alt="Lien" title="Lien" onclick="add_bal2('lien','url'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_mail.png" alt="Mail" title="Mail" onclick="add_bal2('email','nom'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_secret.png" alt="Secret" title="Secret" onclick="balise('&lt;secret&gt;','&lt;/secret&gt;'); return false;" class="bouton_cliquable" />
                <!--<img src="zform/img/zcode/zcode_math.png" alt="Math" title="Math" onclick="balise('&lt;math&gt;','&lt;/math&gt;', 'texte'); return false;" class="bouton_cliquable" />-->
        </span>

        <span class="boutons">
                <img src="zform/img/zcode/zcode_titre1.png" alt="Titre 1" title="Titre 1" onclick="balise('&lt;titre1&gt;','&lt;/titre1&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_titre2.png" alt="Titre 2" title="Titre 2" onclick="balise('&lt;titre2&gt;','&lt;/titre2&gt;'); return false;" class="bouton_cliquable" />

        </span>

        <span class="boutons">
                <img src="zform/img/zcode/zcode_info_tn.png" alt="Information" title="Information" onclick="balise('&lt;information&gt;','&lt;/information&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_attention_tn.png" alt="Attention" title="Attention" onclick="balise('&lt;attention&gt;','&lt;/attention&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_erreur_tn.png" alt="Erreur" title="Erreur" onclick="balise('&lt;erreur&gt;','&lt;/erreur&gt;'); return false;" class="bouton_cliquable" />
                <img src="zform/img/zcode/zcode_question_tn.png" alt="Question" title="Question" onclick="balise('&lt;question&gt;','&lt;/question&gt;'); return false;" class="bouton_cliquable" />
        </span>


        <br />
        <span class="clearer">
                <select id="code_texte" onchange="add_bal('code','type','code_texte')" >

                        <option class="opt_titre" selected="selected">Code</option>
                        <optgroup label="Web">
                                <!--<option value="actionscript">ActionScript</option>-->
                                <option value="apache">Apache</option>
                                <option value="css">CSS</option>
                                <option value="html">(x)HTML</option>
                                <option value="javascript">JavaScript</option>
                                <!--<option value="jsp">JSP</option>
                                <option value="perl">Perl</option>-->
                                <option value="php">PHP</option>
                                <!--<option value="smarty">Smarty</option>-->
                                <option value="sql">SQL</option>
                                <!--<option value="xml">XML</option>-->
                        </optgroup>
                        <!--
                        <optgroup label="Prog'">
                                <option value="c">C</option>
                                <option value="cpp">C++</option>
                                <option value="csharp">C#</option>
                                <option value="d">D</option>
                                <option value="delphi">Delphi</option>
                                <option value="java">Java</option>
                                <option value="ocaml">Ocaml</option>
                                <option value="pascal">Pascal</option>
                                <option value="python">Python</option>
                                <option value="ruby">Ruby</option>
                                <option value="tcl">Tcl</option>
                                <option value="vbnet">VB .Net</option>
                        </optgroup>
                        <optgroup label="Autre">
                                <option value="bash">Bash</option>
                                <option value="console">Console</option>
                                <option value="diff">Diff</option>

                                <option value="common-lisp">Lisp</option>
                                <option value="lua">Lua</option>
                                <option value="matlab">MatLab</option>
                                <option value="objective-c">Objc</option>
                                <option value="tex">TeX</option>
                                <option value="zcode">zcode</option>

                                <option value="">( Autre )</option>
                        </optgroup>
                        -->
                </select>

                <select id="position_texte" onchange="add_bal('position','valeur','position_texte')">
                        <option class="opt_titre" selected="selected">Position</option>
                        <option value="justifie">Justifié</option>
                        <option value="gauche">A gauche</option>

                        <option value="centre" class="centre">Centré</option>
                        <option value="droite" class="droite">A droite</option>
                </select>

                <select id="flottant_texte" onchange="add_bal('flottant','valeur','flottant_texte')">
                        <option class="opt_titre" selected="selected">Flottant</option>
                        <option value="gauche">A gauche</option>
                        <option value="droite" class="droite">A droite</option>

                        <option value="aucun">Aucun</option>
                </select>

                <select id="taille_texte" onchange="add_bal('taille','valeur','taille_texte')">
                        <option class="opt_titre" selected="selected">Taille</option>
                        <option value="ttpetit">Très très petit</option>
                        <option value="tpetit">Très petit</option>
                        <option value="petit">Petit</option>

                        <option value="gros">Gros</option>
                        <option value="tgros">Très gros</option>
                        <option value="ttgros">Très très gros</option>
                </select>

                <select id="couleur_texte" onchange="add_bal('couleur','nom','couleur_texte')">
                        <option class="opt_titre" selected="selected">Couleur</option>

                        <option value="rose" class="rose">rose</option>
                        <option value="rouge" class="rouge">rouge</option>
                        <option value="orange" class="orange">orange</option>
                        <option value="jaune" class="jaune">jaune</option>
                        <option value="vertc" class="vertc">vertc</option>
                        <option value="vertf" class="vertf">vertf</option>
                        <option value="olive" class="olive">olive</option>
                        <option value="turquoise" class="turquoise">turquoise</option>
                        <option value="bleugris" class="bleugris">bleugris</option>
                        <option value="bleu" class="bleu">bleu</option>
                        <option value="marine" class="marine">marine</option>
                        <option value="violet" class="violet">violet</option>
                        <option value="marron" class="marron">marron</option>
                        <option value="noir" class="noir">noir</option>
                        <option value="gris" class="gris">gris</option>
                        <option value="argent" class="argent">argent</option>
                        <option value="blanc" class="blanc">blanc</option>
                </select>

                <select id="police_texte" onchange="add_bal('police','nom','police_texte')">
                        <option class="opt_titre" selected="selected">Police</option>
                        <option value="arial" class="arial">arial</option>
                        <option value="times" class="times">times</option>
                        <option value="courrier" class="courrier">courrier</option>
                        <option value="impact" class="impact">impact</option>
                        <option value="geneva" class="geneva">geneva</option>
                        <option value="optima" class="optima">optima</option>
                </select>
        </span>


</div>


<!-- ################# DIV DES SMILIES ################# -->
<div id="panneau_gauche">
    <div id="texte_smilies" class="smilies">
        <a href="#" onclick="toggle_smilies();return false;" id="btn_toggle_smilies_1" >Autres smilies =></a>
        <a href="#" onclick="toggle_smilies();return false;" style="display: none" id="btn_toggle_smilies_2" ><= Autres smilies</a>
        <br />
        <table id="tb_smile1">
            <tr>
                <td><img src="zform/img/smilies/smile.png" class="smiley_cliquable" alt=":)" onclick="balise(' :) ',''); return false;" /></td>
                <td><img src="zform/img/smilies/heureux.png" class="smiley_cliquable" alt=":D" onclick="balise(' :D ',''); return false;" /></td>
                <td><img src="zform/img/smilies/clin.png" class="smiley_cliquable" alt=";)" onclick="balise(' ;) ',''); return false;" /></td>
                <td><img src="zform/img/smilies/langue.png" class="smiley_cliquable" alt=":p" onclick="balise(' :p ',''); return false;" /></td>
            </tr>
            <tr>
                <td><img src="zform/img/smilies/rire.gif" class="smiley_cliquable" alt=":lol:" onclick="balise(' :lol: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/unsure.gif" class="smiley_cliquable" alt=":euh:" onclick="balise(' :euh: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/triste.png" class="smiley_cliquable" alt=":(" onclick="balise(' :( ',''); return false;" /></td>
                <td><img src="zform/img/smilies/huh.png" class="smiley_cliquable" alt=":o" onclick="balise(' :o ',''); return false;" /></td>
            </tr>
            <tr>
                <td><img src="zform/img/smilies/mechant.png" class="smiley_cliquable" alt=":colere2:" onclick="balise(' :colere2: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/blink.gif" class="smiley_cliquable" alt="o_O" onclick="balise(' o_O ',''); return false;" /></td>
                <td><img src="zform/img/smilies/hihi.png" class="smiley_cliquable" alt="^^" onclick="balise(' ^^ ',''); return false;" /></td>
                <td><img src="zform/img/smilies/siffle.png" class="smiley_cliquable" alt=":-°" onclick="balise(' :-° ',''); return false;" /></td>
            </tr>
        </table>

        <table id="tb_smile2" style="display: none">
            <tr>
                <td><img src="zform/img/smilies/ange.png" class="smiley_cliquable" alt=":ange:" onclick="balise(' :ange: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/angry.gif" class="smiley_cliquable" alt=":colere:" onclick="balise(' :colere: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/diable.png" class="smiley_cliquable" alt=":diable:" onclick="balise(' :diable: ',''); return false;" /></td>
            </tr>
            <tr>
                <td><img src="zform/img/smilies/magicien.png" class="smiley_cliquable" alt=":magicien:" onclick="balise(' :magicien: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/ninja.png" class="smiley_cliquable" alt=":ninja:" onclick="balise(' :ninja: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/pinch.png" class="smiley_cliquable" alt="&gt;_&lt;" onclick="balise( '>_< ',''); return false;" /></td>
            </tr>
            <tr>
                <td><img src="zform/img/smilies/pirate.png" class="smiley_cliquable" alt=":pirate:" onclick="balise(' :pirate: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/pleure.png" class="smiley_cliquable" alt=":\'(" onclick="balise(' :\'( ',''); return false;" /></td>
                <td><img src="zform/img/smilies/rouge.png" class="smiley_cliquable" alt=":honte:" onclick="balise(' :honte: ',''); return false;" /></td>
            </tr>
            <tr>
                <td><img src="zform/img/smilies/soleil.png" class="smiley_cliquable" alt=":soleil:" onclick="balise(' :soleil: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/waw.png" class="smiley_cliquable" alt=":waw:" onclick="balise(' :waw: ',''); return false;" /></td>
                <td><img src="zform/img/smilies/zorro.png" class="smiley_cliquable" alt=":zorro:" onclick="balise(' :zorro: ',''); return false;" /></td>
            </tr>
        </table>
    </div>

    <div id="hauteur_textarea">
        <input type="button" value="-" id="txtarea_hauteur_moins" /> Hauteur <input type="button" value="+" id="txtarea_hauteur_plus" />
    </div>
</div>



<!-- ################# DIV DU TEXTAREA ET APERCU ################# -->

<div class="zform">
    <!-- Le fieldset et le div ne servent à rien si ce n'est à lutter contre un bug d'IE sur les largeurs (encore IE...) -->
    <fieldset style="border:0;">
        <div>
            <textarea id="zcodearea" cols="40" rows="15" name="texte" style="width: 100%;"><?php echo $content;?></textarea>
        </div>
    </fieldset>

    <p style="text-align:center; margin:0; padding:0; ">
        <input type="button" value="Aperçu final" id="btn_apercu" />
    </p>

    <div id="apercu" class="zcode"></div>
</div>