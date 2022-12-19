#!/usr/bin/php -q
<?php

# Get the bilheteunico
require_once('/var/lib/asterisk/agi-bin/phpagi.php');
    $agi        = new AGI();
    $response1  = $agi->get_variable('URADINAMICA_RELATORIOS');
    $response   = str_replace("'" ,'"' , $response1['data']);
    $dados_info = json_decode(str_replace(';',',', $response), true);
foreach($dados_info as $row):
    $data       = $dados_info['data'];
    $sorteio    = end($dados_info['palavra_origem']);
    $bilhete    = $dados_info['bilheteunico'];
    $empresa    = $dados_info['empresa'];
    $destino    = $dados_info['tag'];
    $origem     = $dados_info['origem'];
endforeach;
$porfavoraguarde = $agi->stream_file("/var/lib/asterisk/sounds/ura/union/AguardeEnquantoTransfere");

#Bilhete de teste
// $bilhete = '1670519804.639';
// $origem = '1139952800';


# Finding the .wav
$path = '/var/spool/asterisk/union/';
$date = exec('date +%Y-%m-%d');
$wav = exec("find $path"."$date -iname *$bilhete.wav");

$resultado = str_replace("$path", '', $wav);
$resultado = str_replace("$date", '', $resultado);
$resultado = str_replace("/", '', $resultado);


# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';


# Imports the Google Cloud client library
use Google\Cloud\Speech\SpeechClient;
use Google\Cloud\Storage\StorageClient;

$projectId = '/etc/asterisk/union/agi/chave.json';


# Instantiates a client
$speech = new SpeechClient([
    'keyFilePath' => $projectId,
    'languageCode' => 'pt-BR',
]);


# The name of the audio file to transcribe
$fileName = $path . $date.'/'. $resultado;


# The audio file's encoding and sample rate
$options = [
    'encoding' => 'LINEAR16',
    'sampleRateHertz' => 8000,
];


# Detects speech in the audio file
$results = $speech->recognize(fopen($fileName, 'r'), $options);

foreach ($results as $result) {
    $texto = $result->alternatives()[0]['transcript'];
}


# Movidesk URL
$url = "https://api.movidesk.com/public/v1/tickets?token=qwerty";


#HTTP Header Params
$params = '{

   "type": 2,
   "subject": "Retornar contato suporte fora horário para o número '."$origem".'",
   "status": "Novo",
   "serviceFirstLevelId": "816800",
   "clients": [
       {
           "id": "824132968",
           "personType": 1,
           "profileType": 1
       }
   ],
   "createdBy": {
       "id": "824132968"
   },
   "actions": [
       {
           "type": 2,
           "description": "Nome do cliente: '."$texto".' o telefone que originou a chamada '."$origem".'"
       }
   ]
}';


$data = json_decode($params);
$data1 = json_encode($data);

// echo($data1);

# cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

// echo $result;


# Uploading wav file
$result = json_decode(str_replace(';',',', $result), true);

foreach($result as $brabo):
    $id = $result['id'];
endforeach;

$idd = "&id=$id";
$url = "https://api.movidesk.com/public/v1/ticketFileUpload?token=qwerty$idd&actionId=1";
$ch = curl_init("$url");
curl_setopt_array($ch, [    
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [          
      'file' => curl_file_create("$fileName")
    ]
]);
$resposta = curl_exec($ch);
curl_close($ch);


#Patch updating tag 
$urlpatch = "https://api.movidesk.com/public/v1/tickets?token=qwerty&id=$id";

$tag = '{
	"tags": ["URA - ANEXADO AUDIO"]
}';


$che = curl_init($urlpatch);
curl_setopt($che, CURLOPT_CUSTOMREQUEST, "PATCH");
curl_setopt($che, CURLOPT_POSTFIELDS, $tag);
curl_setopt($che, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($che, CURLOPT_RETURNTRANSFER, true);
$resulttag = curl_exec($che);
curl_close($che);



# Log
$date = new DateTime();
$date = $date->format('d-m-Y H:i:s');
$log = ("Data: $date, Origem: $origem, Ticket: $id, Descricao: $data1\n");
$fp = fopen("/tmp/ura.log", "a");
fwrite($fp, $log);
fclose($fp);  

#Hangup Client
$finaliza = $agi->exec_goto('union-uradin-12', 10, 1);

?>
