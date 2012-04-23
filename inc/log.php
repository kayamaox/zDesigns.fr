<?php
class log {
    private $defaultUrl = '../log.txt';
    private $dev = false;
    private $noDev = array('error', 'message', 'zexplorer', 'system');

    public function __construct($url='none', $dev=false) {
        if($url != 'none') $this->defaultUrl = $url;
        $this->dev = $dev;
    }

    public function addData($data, $url='none', $noDev=null){
        if($this->dev || in_array($noDev, $this->noDev)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $url = ($url != 'none') ? $url : $this->defaultUrl;
            $logOld = file_get_contents($url);

            // On ouvre le fichier
            $logFile = fopen($url, 'r+');

            $date = '['.date('d-m-Y').' '.date('H:i:s').']';
            $data = $date.' ['.$ip.'] '.$data;

            //fseek($logFile, 0);
            fputs($logFile, $data);
            fputs($logFile, "\r\n");
            fputs($logFile, $logOld);

            // 3 : quand on a fini de l'utiliser, on ferme le fichier
            fclose($logFile);
        }
    }
}
?>