<?php

namespace Librejo\Client;

class Client {
	//Function returning the meta post of an entity
	public function discover($entityUri) {
		$header = get_headers($entityUri);
		if (!$header[0] == 'HTTP/1.1 200 OK') {
			return 'The request failed, please check the entered entity';
		}
		else {
			$link = $header[2];
			$link = str_replace('Link: <', '', $link);
			$link = str_replace('>; rel="https://tent.io/rels/meta-post"', "", $link);
			//return $link;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $entityUri.$link);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$meta = json_decode(curl_exec($ch));
			curl_close($ch);
			var_export($meta);
		}
	}
}

?>