<?php
/*
copyright @ medantechno.com
Modified @ Farzain - zFz
2017

*/

require_once('./line_class.php');
require_once('./unirest-php-master/src/Unirest.php');

$channelAccessToken = '6hXZyp7yjqOo4ueJ2gxJhY9kN5AdfG7FK+/xLkhDENlxjTG545cqX9TagwoUQX9vVpjA7cbawoHMytpyqmMD4ht3lpjt9+EAJpCNyuBf4DrLpBOYw35m5WzUROFWWiFwFzM7S9ZTPCZ/K0dcgyX55wdB04t89/1O/w1cDnyilFU='; //sesuaikan 
$channelSecret = 'ea6b68f197d7d3466da558f873cf8e42';//sesuaikan

$client = new LINEBotTiny($channelAccessToken, $channelSecret);

$userId 	= $client->parseEvents()[0]['source']['userId'];
$groupId 	= $client->parseEvents()[0]['source']['groupId'];
$replyToken = $client->parseEvents()[0]['replyToken'];
$timestamp	= $client->parseEvents()[0]['timestamp'];
$type 		= $client->parseEvents()[0]['type'];

$message 	= $client->parseEvents()[0]['message'];
$messageid 	= $client->parseEvents()[0]['message']['id'];

$profil = $client->profil($userId);

$pesan_datang = explode(" ", $message['text']);

$command = $pesan_datang[0];
$options = $pesan_datang[1];
if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

#-------------------------[Function]-------------------------#
function cuaca($keyword) {
    $uri = "http://api.openweathermap.org/data/2.5/weather?q=" . $keyword . ",ID&units=metric&appid=e172c2f3a3c620591582ab5242e0e6c4";

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
    $result = "Hello ^_^ dear This is Your Forecasted weather of ";
	$result .= $json['name'];
	$result .= " Dan Sekitarnya";
	$result .= "\n\nWeather : ";
	$result .= $json['weather']['0']['main'];
	$result .= "\n : ";
	$result .= $json['weather']['0']['description'];
    return $result;
}
#-------------------------[Function]-------------------------#

# require_once('./src/function/search-1.php');
# require_once('./src/function/download.php');
# require_once('./src/function/random.php');
# require_once('./src/function/search-2.php');
# require_once('./src/function/hard.php');

//show menu, saat join dan command /menu
if ($type == 'join' || $command == '/menu') {
    $text = "Hello, dear ^ _ ^I'm The Prediction The weather, you can know weather prediction in your region in accordance with the BMKG.";
    $balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
            array(
                'type' => 'text',
                'text' => $text
            )
        )
    );
}

//pesan bergambar
if($message['type']=='text') {
	    if ($command == '/cuaca') {

        $result = cuaca($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    }

}else if($message['type']=='sticker')
{	
	$balas = array(
							'replyToken' => $replyToken,														
							'messages' => array(
								array(
										'type' => 'text',									
										'text' => 'hey mujhse v koi baat karo'										
									
									)
							)
						);
						
}
if (isset($balas)) {
    $result = json_encode($balas);
//$result = ob_get_clean();

    file_put_contents('./balasan.json', $result);


    $client->replyMessage($balas);
}
?>
