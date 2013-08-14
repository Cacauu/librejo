<?php
namespace Librejo\Client\Auth;

use Librejo\Client\GuzzleClient;

class Auth {

	protected $timestamp;
	protected $nonce;

	public function __construct() {
		$this->timestamp = time();
		$this->nonce = uniqid('Librejo_', true);
	}

	// Function to generate the auth header used for requests
	// Called with new Auth\Header(CONTENT)
	public function generate_header($header_type, $method, $request_uri, $host, $port, $hawk_key, $hawk_id, $app_id) {
		$data = $header_type."\n".$this->timestamp."\n".$this->nonce."\n".$method."\n".$request_uri."\n".$host."\n".$port."\n\n\n".$app_id."\n\n";
		$sha256 = hash_hmac('sha256', $data, $hawk_key, true);
		$mac = base64_encode($sha256);
		$auth_header = 'Hawk id="'.$hawk_id.'", mac="'.$mac.'", ts="'.$this->timestamp.'", nonce="'.$this->nonce.'", app="'.$app_id.'"';
		return $auth_header;
	}
}
?>