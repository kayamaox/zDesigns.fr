<?php
require_once('./inc/core.php');
require_once('./inc/functions.php');

if(!isset($_SESSION['pseudo'])){
   $_SESSION['message']['alert'] = "Vous devez être connecté";
   header('Location: '.ROOT);
   exit();
}

$js[] = 'tchat';
include('./inc/head.php');
?>

<div id="arianne">
    <a href="<?php echo ROOT; ?>tchat.html">Tchat</a>
</div>
<br />
<h1>Tchat Admin</h1>

<?php
if(droit_dacces(10)){
    echo '<div id="tchats">';
    $ids = array();
    
    $req = $BDD->query("SELECT tchat.id AS id, tchat.id_membre AS idm, tchat.id_auteur AS ida,
                               tchat.message AS message, tchat.timestamp AS time, tchat.lu AS lu, tchat.ignorer AS ignorer,
                               membres.id, membres.pseudo AS pseudo, membres.rang AS rang
                        FROM tchat
                        INNER JOIN membres
                           ON tchat.id_auteur = membres.id
                        ORDER BY tchat.timestamp DESC");
    while($m = mysql_fetch_assoc($req)){
        if(!in_array($m['ida'], $ids)){
            $ids[] = $m['ida'];
            
            echo '<a class="mbr_tchat" href="./tchat-'.$m['ida'].'.html">';
            $url_avatar = './images/avatars/'.$m['ida'].'.png';
            $url_avatar = (is_file($url_avatar)) ? $url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
            echo '<img src="'.$url_avatar.'" alt="Avatar de '.$m['pseudo'].'" height="50" />
                  <b>'.$m['pseudo'].'<br />
                      <span class="l1">Dernier message</span>
                      <span class="l2">'. date('d/m/y H\hi', $m['time']).'</span>
                  </b><br />';
            echo '</a>';
        }
    }
    echo '</div>';
}
?>

<div id="tchat">
<?php

if(droit_dacces(10) && isset($_GET['idm'])){
    $idm = (int) $_GET['idm'];
} else {
    $idm = $_SESSION['id'];
}

echo '<div id="id_mbr" class="dn">'.$idm.'</div>';

ob_start();

echo '<div id="scroll_mess">';
echo '<div id="all_mess">';
$req = $BDD->query("SELECT tchat.id AS id, tchat.id_membre AS idm, tchat.id_auteur AS ida,
                           tchat.message AS message, tchat.timestamp AS time, tchat.lu AS lu, tchat.ignorer AS ignorer,
                           membres.id, membres.pseudo AS pseudo, membres.rang AS rang
                    FROM tchat
                    INNER JOIN membres
                       ON tchat.id_auteur = membres.id
                    WHERE tchat.id_membre = '".$idm."'
                    ORDER BY tchat.timestamp ASC");

$participants = array();
$last_id = 0;
$date_mess = '';
$timestamp_mess = 0;
$last_mess = '';
$to_refresh = '';
$last_time = 0;

$req_mbr = $BDD->query("SELECT id, pseudo, rang
                        FROM membres
                        WHERE id = '".$idm."'");
$mbr = mysql_fetch_assoc($req_mbr);
$url_avatar = 'images/avatars/'.$idm.'.png';
$url_avatar = (is_file('./'.$url_avatar)) ? ROOT.$url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
$participants[$idm] = array(
    'id' => $idm,
    'pseudo' => $mbr['pseudo'],
    'avatar' => $url_avatar,
    'rang' => $mbr['rang'],
    'last_mess' => 0
);

while($m = mysql_fetch_assoc($req)){
    if($last_id != (int) $m['ida'] || ($last_id == (int) $m['ida'] && abs($last_time-$m['time']) > 3600)){
        if($last_id != 0){
            echo '<div class="date_mess '.$to_refresh.' t_'.$timestamp_mess.'">'.$date_mess.'</div>';
            echo '</div>';
            $last_mess = '';
        }
        $url_avatar = 'images/avatars/'.$m['ida'].'.png';
        $url_avatar = (is_file('./'.$url_avatar)) ? ROOT.$url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
        $date_mess = parse_date($m['time'], true);
        $timestamp_mess = (int) $m['time'];
        if((time()-$timestamp_mess) < 48*3600){
            $to_refresh = 'to_refresh';
        } else {
            $to_refresh = '';
        }
        
        echo '<div class="grp_mess">';
        echo '<div class="pseudo_mess">
                  <b>'.$m['pseudo'].'</b>
                  <img src="'.$url_avatar.'" alt="Avatar de '.$m['pseudo'].'" height="35" />
              </div>';
    }
    if($last_mess != $m['message']){
        echo '<div class="mess">';
            echo '<div class="text_mess"><b>•</b> '.stripslashes(ifdecode($m['message'])).'</div>';
            $last_id = $m['ida'];
            $last_time = $m['time'];
        echo '</div>';
    }
    $last_mess = $m['message'];
    
    $participants[$m['ida']] = array(
        'id' => $m['ida'],
        'pseudo' => $m['pseudo'],
        'avatar' => $url_avatar,
        'rang' => $m['rang'],
        'last_mess' => $m['time']
    );
}
$_SESSION['lastTchatTime'] = $last_time;
$_SESSION['lastTchatID'] = $last_id;
$_SESSION['lastTchatMess'] = $last_mess;
$_SESSION['lastNbParticipants'] = count($participants);

if($date_mess != ''){
    echo '<div class="date_mess '.$to_refresh.' t_'.$timestamp_mess.'">'.$date_mess.'</div>';
    echo '</div>';
}
echo '</div>';
echo '</div>';

$content = ob_get_clean();
$i = 0;
$last = '';
echo '<div id="participants"><div id="participants_in">';
echo '<div class="participant_titre_out"><div class="participant_titre">'.pluralize(count($participants), "<b>{#}</b> participant{s}").'</div></div>';
foreach($participants as $mbr){
    $i++;
    if($i == count($participants)){
        $last = ' last';
    }
    
    if(droit_dacces(10)){
        $designs = $BDD->query("SELECT id, id_membre, titre, active, date
                                FROM designs
                                WHERE id_membre = '".$mbr['id']."'
                                ORDER BY active DESC, date DESC");
    }
    
    $d = ($mbr['last_mess'] != 0) ? date('d/m/y H\hi', $mbr['last_mess']) : 'Jamais posté';
    
    echo '<div class="participant'.$last.'">
              <img src="'.$mbr['avatar'].'" alt="Avatar de '.$mbr['pseudo'].'" height="50" />
              <b>'.$mbr['pseudo'].'<br /><span class="l1">Dernier message</span><br /><span class="l2">'.$d.'</span></b><br />
              <div class="content_plus">
                  <i class="rang">'.$rangs[$mbr['rang']].'</i>';
                  if(droit_dacces(10)){
                      echo '<hr />
                      <u>Ses zDesigns ('.mysql_num_rows($designs).') : </u><br />';
                      
                      while($d = mysql_fetch_assoc($designs)){
                          echo '<a href="./mes_zdesigns-'.$d['id'].'.html" style="color: '.$etats[$d['active']]['couleur'].';" title="Statut : '.$etats[$d['active']]['etat'].'" rel="infobulle">
                                    <i class="status_design">•</i> '.$d['titre'].'
                                </a><br />';
                      }
                  }
                  echo '
              </div>
          </div>';
}
$_SESSION['participants'] = $participants;
echo '</div>';
echo '</div>';
echo $content;
?>
<form action="#" method="post">
    <label for="message">Message : </label>
    <input type="text" name="message" id="message" />
    <input type="hidden" name="idm" value="<?php echo $idm; ?>" />
    <input type="submit" value="Envoyer" />
</form>
</div>

<?php
include('./inc/pied.php');
include('./inc/barre_bas.php');
include('./inc/end.php');
?>