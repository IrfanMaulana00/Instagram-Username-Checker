<?php

function cekusername($username) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://www.instagram.com/$username/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_BINARYTRANSFER, true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Cookie: ig_did=46560F5D-802E-47B9-BF38-2AF4101D3B7F; csrftoken=O2w2bAjhMogi0RuI87CkH0wmEC9OCGMo; mid=X27AiQAEAAG4HVDY-UGRy06kdMDo; ig_nrcb=1"
    ),
    ));

    $response = curl_exec($curl);
    $get_title = explode("<title>", $response);
    $title = explode("</title>", $get_title[1])[0];
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
	
    return $httpcode;
}



echo "Masukan nama file : ";
$data = trim(fgets(STDIN));

$handle = fopen($data, "r");
$available = fopen("available.txt", "a+") or die("Unable to open file!");
$sampah = fopen("sampah.txt", "a+") or die("Unable to open file!");

if ($handle) {
    while (($username = str_replace("\r", "", str_replace("\n", "", fgets($handle)))) !== false) {
        if (strlen($username) < 2) {
            exit;
        }
        if ( !in_array($username, explode("\n", file_get_contents("./sampah.txt"))) ) {
            $cekusername = cekusername($username);
            
            if ( $cekusername == 404 ) {
                fwrite($available, "$username\n");
                echo "\033[92m[+] $username => Tersedia $cekusername\033[0m";
            } elseif($cekusername == 200) {
                echo "\033[91m[-] $username => Tidak tersedia\033[0m ";
            } else {
                echo "\033[91m[-] $username => Tidak tersedia | code : $cekusername\033[0m";
            }
            fwrite($sampah, "$username\n");
        } else {
            echo "\033[91m[-] $username => Skip\033[0m ";
        }
        echo PHP_EOL;
    }
    fclose($sampah);
    fclose($available);
} else {
    echo "\033[91mFile Error\033[0m ";
} 