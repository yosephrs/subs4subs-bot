<?php
date_default_timezone_set('Asia/Jakarta');
require_once("sdata-modules.php");
/**
 * @Author: Eka Syahwan (sdata), Dani Hidayat
 * @Date:   2018-08-21
 * @Last Modified by:  Dani Hidayat
 * @Last Modified time: Bandung, 2018-10-21
*/
first:
echo "Masukkan URL Channel : ";$config['ytid'] = trim(fgets(STDIN));
echo "Masukkan Jumlah Worker : ";$config['worker'] = trim(fgets(STDIN));
echo "Masukkan Jeda Waktu    : ";$config['sleep'] = trim(fgets(STDIN));
echo "Target Points          : ";$config['target'] = trim(fgets(STDIN));
if(empty($config['worker']) or empty($config['sleep'])){
    echo "\033[31mError : \033[0m Anda belum memasukan jumlah worker/jeda waktu\n";
    goto first;
}
        $exx = explode("/",$config['ytid']);
        if (stripos($exx[4],"?")) {
        $ytid = substr($exx[4], 0, strpos($exx[4], "?"));
        }else{
        $ytid = $exx[4];
        }
	$url 	= array(); 
	for ($i=0; $i <$config['worker']; $i++) { 
        $urls[] = array(
            'url' 	=> 'https://zlcodesyt.websiteseguro.com/iniciar.php',
            'note' 	=> 'optional', 
        );
        $headers[] = array(
            'header' => array(
                "Connection: Keep-Alive",
                "Content-Type: application/x-www-form-urlencoded",
                "Host: zlcodesyt.websiteseguro.com",
                "cache-control: no-cache"
                        ),
            'post' => '#tipo:5#<->#$ytid'
          );
    }
    while(TRUE){
    echo "\nChannel ID : $ytid\nWorker : ".$config['worker']."\n";
    $respons = $sdata->sdata($urls , $headers);
    foreach ($respons as $key => $value) {
        //$rjson = json_decode($value[respons],true);
        $rhttp = json_decode($value[info][http_code],true);
        if($rhttp == "200"){
            echo "\033[0m[".($key+1)."] Points Earned!\033[32m +1 \033[0m\n";
        }elseif ($rhttp == "0") {
            echo "\033[0m[".($key+1)."]\033[31m FAILED! \033[0m (Fail Code : $rhttp)\n";
        }elseif ($rhttp == "503") {
            echo "\033[0m[".($key+1)."]\033[31m FAILED! \033[0m (Fail Code : $rhttp)\n";
        }else{
            print_r($respons);
        }
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://zlcodesyt.websiteseguro.com/iniciar.php",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "#tipo:7#<->#$ytid #####tk:ya29.Gls9BsDwQDqUZAKzj0z4qnwMNHBNtZD6kr2Ze1UtcdXPqGLP5_Jz6eNRwa-jvi9phW1mlZUVxMHQnLblMumL0pVTewlRPNZ8_Fi8lgdA7WBuAiR037BNFROOFRlY#",
      CURLOPT_HTTPHEADER => array(
        "Connection: Keep-Alive",
        "Host: zlcodesyt.websiteseguro.com",
        "cache-control: no-cache"
      ),
    ));    

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
    $re = '/"coins":"(.*?)"/';
preg_match($re, $response, $matches, PREG_OFFSET_CAPTURE, 0);
    if ($matches[1][0] >= $config['target']) {
        echo "Target Points Acquired : ".$config['target']."";
        exit;
    }
    echo "\n====== Task Completed | Earned : ".($config['worker']*1)." | Total : \033[32m".number_format($matches[1][0])." \033[0m=====\n";
    sleep($config['sleep']);
}
}
