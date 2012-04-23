<?php
if(!isset($_GET['iframe'])){
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Zozor vous parle !</title>
    <link rel="stylesheet" href="http://v2.zdesigns.fr/design/2/css/countdown.css" />
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            font-family: 'Trebuchet MS', 'Verdana', 'seref', 'sans-serif';
        }
        #main_out {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            font-family: 'Trebuchet MS', 'Verdana', 'seref', 'sans-serif';
        }
    </style>
    <script type="text/javascript" src="http://v2.zdesigns.fr/js/jquery.js"></script>
    <script type="text/javascript" src="http://v2.zdesigns.fr/js/twitter.js"></script>
</head>
<body>
    <?php
    }
    
    if(!isset($countdown)){
        $countdown = '';
    }

    $date1 = time();
    $date2 = 1300653000; // 21 Mars 2011 à 21h30
    $sec = $date2 - $date1;
    $n = 24 * 3600;

    $j = floor($sec / $n);
    $h = floor(($sec - ($j * $n)) / 3600);
    $mn = floor(($sec - ($j * $n + $h * 3600)) / 60);
    $sec = floor($sec - ($j * $n + $h * 3600 + $mn * 60));
    ?>
    <script type="text/javascript">
        var ifRebour = true;
        function Rebour(){
            var date1 = new Date();
            var date2 = new Date ("Mar 20 21:30:00 2011");
            var sec = (date2 - date1) / 1000;
            var n = 24 * 3600;
            if (sec > 0) {
                j = Math.floor(sec / n);
                h = Math.floor((sec - (j * n)) / 3600);
                mn = Math.floor((sec - ((j * n + h * 3600))) / 60);
                sec = Math.floor(sec - ((j * n + h * 3600 + mn * 60)));

                $('#jour .time').empty().append(j);
                $('#heure .time').empty().append(h);
                $('#min .time').empty().append(mn);
                $('#sec .time').empty().append(sec);

                $('#jour .unite').empty().append( (j > 1) ? 'Jours' : 'Jour' );
                $('#heure .unite').empty().append( (h > 1) ? 'Heures' : 'Heure' );
                $('#min .unite').empty().append( (mn > 1) ? 'Minutes' : 'Minute' );
                $('#sec .unite').empty().append( (sec > 1) ? 'Secondes' : 'Seconde' );
            }
            if(ifRebour){
                tRebour = setTimeout("Rebour();", 1000);
            }
        }
        $(function(){
            Rebour();
            
            setTimeout(function(){
                getTwitters('twit', {
                    id: 'zdesigns_fr',
                    count: 1,
                    ignoreReplies: true,
                    timeout: 100,
                    onTimeout: function(){
                        $('#twitter img').fadeOut(200, function(){
                            $('#twitter').empty().append('Le chargement bug !');
                        });
                    },
                    template: '%text%',
                    callback: function(){
                        $('#twitter').fadeOut(200, function(){
                            $('#twitter img').remove();
                            $('#twitter').empty().append($('#twit').html());
                            $('#twitter').fadeIn(200);
                        });
                    }
                });
            }, 3000);
        });
    </script>
    <div id="main_out" <?php echo $countdown; ?>>
        <div id="main">
            <div id="main_in">
                <div id="acces">
                    <a href="http://zdesigns.fr/">Aller sur le site</a>
                </div>
                <div id="compteur">
                    <div id="jour" class="bloc">
                        <div class="time"><?php echo $j; ?></div>
                        <div class="unite">Jours</div>
                    </div>
                    <div id="heure" class="bloc">
                        <div class="time"><?php echo $h; ?></div>
                        <div class="unite">Heures</div>
                    </div>
                    <div id="min" class="bloc">
                        <div class="time"><?php echo $mn; ?></div>
                        <div class="unite">Minutes</div>
                    </div>
                    <div id="sec" class="bloc">
                        <div class="time"><?php echo $sec; ?></div>
                        <div class="unite">Secondes</div>
                    </div>
                </div>
                <div id="twitter">
                    <img src="http://v2.zdesigns.fr/design/2/images/loading.gif" alt="Chargement..." height="30px" />
                </div>
                <div id="twit" style="display: none;"></div>
            </div>
        </div>
    </div>
    <?php if(isset($_GET['iframe'])){ ?>
    <style type="text/css">
        #main_out {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            font-family: 'Trebuchet MS', 'Verdana', 'seref', 'sans-serif';
        }
        html,
        body,
        #main_out {
            height: 100%;
        }
    </style>
    <?php
    }
    if(!isset($_GET['iframe'])){ ?>
</body>
</html>
<?php } ?>