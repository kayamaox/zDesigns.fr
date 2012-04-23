<?php $chronotemps = microtime();
$chronotemps = explode(' ', $chronotemps);
$chronofin = $chronotemps[1] + $chronotemps[0];
?>
<div id="footer">
<div style="margin-left:auto; margin-right: auto; text-align: center">
<a href="#" >Haut de Page</a>
</div>
<a href="./livredor.html">Livre d'or</a> | <a href="http://bugs.zdesigns.fr">Rapporter un bug</a> | <a href="./contact.html">Contactez-nous</a> | <a href="http://validator.w3.org/check?uri=referer">XHTML 1.0</a> | <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS 2.0</a>  <br />
Edité par : <a href="./membres-1.html">Cyril6789</a> et <a href="./membres-2.html">Kenny61</a><br />
<a href="../contact.html">Contactez-nous</a><br />
Page générée en <?php echo round(($chronofin - $chronodebut),6);?> secondes<br />
</div>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-5172410-1");
pageTracker._trackPageview();
</script>
<?php 
 ob_end_flush();// on termine la bufferisation
 mysql_close();
?>