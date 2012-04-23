<?php
$urlLog = '../inc/log.php';
$urlFileLog = '../log.txt';
require_once('../classes/rapport.php');
require_once('../classes/bdd.php');
$BDD = new BDD();
require_once('../inc/core.php');
require_once('../inc/functions.php');


$action = (isset($_POST['a']))?$_POST['a']:'';

switch($action){
    case 'addMessage':
        if(isset($_SESSION['id'])){
            extract($_POST);
            $message = mysql_real_escape_string(htmlspecialchars($message));

            $sql = 'INSERT INTO tchat (id_membre, id_auteur, message, timestamp) VALUES ('.$idm.', '.$_SESSION['id'].', "'.$message.'", "'.time().'")';
            if($req = mysql_query($sql)){
                $r['error'] = 'ok';
            } else {
                $r['error'] = "Erreur SQL";
            }
        } else {
            $r['error'] = "message";
            $r['message'] = array("type"=>"alert", "mess"=>"Vous devez vous connecter");
        }
        
        echo json_encode($r);
        break;


    case 'getMessages':
        $idm = (isset($_POST['idm']))?$_POST['idm']:'6';

        $sql = "SELECT tchat.id AS id, tchat.id_membre AS idm, tchat.id_auteur AS ida,
                       tchat.message AS message, tchat.timestamp AS time, tchat.lu AS lu, tchat.ignorer AS ignorer,
                       membres.id, membres.pseudo AS pseudo, membres.rang AS rang
                FROM tchat
                INNER JOIN membres
                   ON tchat.id_auteur = membres.id
                WHERE tchat.id_membre = '".$idm."' AND tchat.timestamp > ".$_SESSION['lastTchatTime']."
                ORDER BY tchat.timestamp ASC";

        if($req = $BDD->query($sql)){
            $last_time = $_SESSION['lastTchatTime'];
            $last_id = $_SESSION['lastTchatID'];
            $last_mess = $_SESSION['lastTchatMess'];
            $date_mess = '';
            $timestamp_mess = 0;
            $to_refresh = '';
            $to_add = '';
            $grp_mess = '';
            $messages = '';
            $mess = '';
            $send_participants = '';
            
            $req_mbr = $BDD->query("SELECT id, pseudo, rang
                        FROM membres
                        WHERE id = '".$idm."'");
            $mbr = mysql_fetch_assoc($req_mbr);
            $url_avatar = 'images/avatars/'.$idm.'.png';
            $url_avatar = (is_file('../'.$url_avatar)) ? ROOT.$url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
            $participants[$idm] = array(
                'id' => $idm,
                'pseudo' => $mbr['pseudo'],
                'avatar' => $url_avatar,
                'rang' => $mbr['rang'],
                'last_mess' => 0
            );
            
            while($m = mysql_fetch_assoc($req)){
                $url_avatar = 'images/avatars/'.$m['ida'].'.png';
                $url_avatar = (is_file('../'.$url_avatar)) ? ROOT.$url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
                
                if($last_id != (int) $m['ida'] || ($last_id == (int) $m['ida'] && abs($last_time-$m['time']) > 3600)){
                    if($last_id != $_SESSION['lastTchatID']){
                        $grp_mess .= '<div class="date_mess '.$to_refresh.' t_'.$timestamp_mess.'">'.$date_mess.'</div>';
                        $grp_mess .= '</div>';
                        $last_mess = '';
                    }
                    $url_avatar = 'images/avatars/'.$m['ida'].'.png';
                    $url_avatar = (is_file('../'.$url_avatar)) ? ROOT.$url_avatar : DESIGN_DIR.'images/no_avatar.jpg';
                    $date_mess = parse_date($m['time'], true);
                    $timestamp_mess = $m['time'];
                    if((time()-$timestamp_mess) < 48*3600){
                        $to_refresh = 'to_refresh';
                    } else {
                        $to_refresh = '';
                    }

                    $grp_mess .= '<div class="grp_mess">';
                    $grp_mess .= '<div class="pseudo_mess">
                              <b>'.$m['pseudo'].'</b>
                              <img src="'.$url_avatar.'" alt="Avatar de '.$m['pseudo'].'" height="35" />
                          </div>';
                }
                if($last_mess != $m['message']){
                    $add = ($grp_mess == '') ? true : false;
                    $grp_mess .= '<div class="mess">';
                        $grp_mess .= '<div class="text_mess"><b>•</b> '.stripslashes(ifdecode($m['message'])).'</div>';
                    $grp_mess .= '</div>';
                    
                    $last_id = $m['ida'];
                    $last_time = $m['time'];
                    
                    if($add){
                        $to_add .= $grp_mess;
                        $grp_mess = '';
                    }
                }
                
                if($grp_mess != '' && preg_match('`class="mess"`', $grp_mess) > 0){
                    $messages .= $grp_mess;
                }
                $grp_mess = '';
                
                $last_mess = $m['message'];

                $_SESSION['participants'][$m['ida']] = array(
                    'id' => $m['ida'],
                    'pseudo' => $m['pseudo'],
                    'avatar' => $url_avatar,
                    'rang' => $m['rang'],
                    'last_mess' => $m['time']
                );
            }
            if($messages != ''){
                $messages .= '<div class="date_mess '.$to_refresh.' t_'.$timestamp_mess.'">'.$date_mess.'</div>';
            }
            
            if($to_add != '' || $messages != ''){
                $_SESSION['lastTchatTime'] = $last_time;
                $_SESSION['lastTchatID'] = $last_id;
                $_SESSION['lastTchatMess'] = $last_mess;
                
                
                /*********************************************************************
                 * Mets à jour les participant + date dernier message
                 */
                $participants = $_SESSION['participants'];
                $i = 0;
                $last = '';
                $send_participants .= '<div id="participants_in">';
                $send_participants .= '<div class="participant_titre_out"><div class="participant_titre">'.pluralize(count($participants), "<b>{#}</b> participant{s}").'</div></div>';
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
                    
                    $send_participants .= '<div class="participant'.$last.'">
                              <img src="'.$mbr['avatar'].'" alt="Avatar de '.$mbr['pseudo'].'" height="50" />
                              <b>'.$mbr['pseudo'].'<br /><span class="l1">Dernier message</span><br /><span class="l2">'.$d.'</span></b><br />
                              <div class="content_plus">
                                  <i class="rang">'.$rangs[$mbr['rang']].'</i>';
                                  if(droit_dacces(10)){
                                      $send_participants .= '<hr />
                                      <u>Ses zDesigns ('.mysql_num_rows($designs).') : </u><br />';

                                      while($d = mysql_fetch_assoc($designs)){
                                          $send_participants .= '<a href="./mes_zdesigns-'.$d['id'].'.html" style="color: '.$etats[$d['active']]['couleur'].';" title="Statut : '.$etats[$d['active']]['etat'].'" rel="infobulle">
                                                    <i class="status_design">•</i> '.$d['titre'].'
                                                </a><br />';
                                      }
                                  }
                                  $send_participants .= '
                              </div>
                          </div>';
                }
                $send_participants .= '</div>';
                
                $_SESSION['lastNbParticipants'] = count($participants);
            }
            
            $r['participants'] = $send_participants;
            $r['to_add'] = $to_add;
            $r['messages'] = $messages;
            $r['error'] = 'ok';
        } else {
            $r['error'] = "Erreur SQL";
        }
        echo json_encode($r);
        break;
}
?>
