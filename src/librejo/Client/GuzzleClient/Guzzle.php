<?php
namespace Librejo\Client\GuzzleClient;

use Guzzle\Http\Client;
use Librejo\Client\Auth\Auth;

class Guzzle {
	protected $Guzzle;
	protected $entityUri;
	protected $app_id;
	protected $auth;

	// Creating a new Guzzle client
	// Called with new GuzzleClient\Guzzle($entityUri)
	public function __construct($entityUri) {
		$this->Guzzle = new \Guzzle\Http\Client($entityUri);
		$this->entityUri = $entityUri;
		$this->auth = new Auth;
	}

	// Returns the meta post of an entity
	// Called with $Guzzle->discover();
	public function discover() {
		$client = $this->Guzzle;
		$header = $client->head()->send();
		$header = $header->getHeader('link')->raw();
		$link = $header[0];
		$link = preg_replace('/<(.*)>.*/', '$1', $link);
		$meta = $client->get($link)->send();
		$meta = $meta->json();
		return $meta;
	}

	public function register_app($app, $post_endpoint) {
		$request = $this->Guzzle->post($post_endpoint, array('Content-Type' => 'application/vnd.tent.post.v0+json; type="https://tent.io/types/app/v0#"'), $app);
		$response = $request->send();
		$link = $response->getHeader('link')->raw();
		$link = preg_replace('/<(.*)>.*/', '$1', $link);
		$get = $this->Guzzle->get($link)->send();
		$response = array('App' => $response->json(), 'Credentials' => $get->json());
		$this->app_id = $response['App']['post']['id'];
		$this->hawk_key = $response['Credentials']['post']['content']['hawk_key'];
		return $response;
	}

	public function oauth($code, $app_id, $hawk_id, $hawk_key, $entity, $endpoint) {
		if(isset(parse_url($endpoint)['port'])) {
			$port = parse_url($endpoint)['port'];
		}
		else {
			$port = 443;
		}
		$header = $this->auth->generate_header('hawk.1.header', 'POST', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $hawk_key, $hawk_id, $app_id);
		$post = array('code' => $code, 'token_type' => 'https://tent.io/oauth/hawk-token');
		$post = json_encode($post, JSON_UNESCAPED_SLASHES);
		$client = $this->Guzzle;
		$request = $client->post($endpoint, array('Authorization' => $header, 'Content-Type' => 'application/json', 'Accept' => 'application/json'), $post);
		$response = $request->send()->json();
		$reponse = array('access_token' => $response['access_token'], 'hawk_key' => $response['hawk_key']);
		unset($response['hawk_algorithm']);
		unset($response['token_type']);
		return $response;
	}

	public function send_post($credentials, $post, $endpoint) {
		if(isset(parse_url($endpoint)['port'])) {
			$port = parse_url($endpoint)['port'];
		}
		else {
			$port = 443;
		}
		$type = $post['type'];
		$post = json_encode($post, JSON_UNESCAPED_SLASHES);
		$header = $this->auth->generate_header('hawk.1.header', 'POST', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $credentials['hawk_key'], $credentials['hawk_id'], $credentials['client_id']);
		$client = $this->Guzzle;
		$request = $client->post($endpoint, array('Authorization' => $header, 'Content-Type' => 'application/vnd.tent.post.v0+json; type="'.$type.'"', 'Accept' => 'application/json'), $post);
		$response = $request->send()->json();
		return $response;
	}

	public function get_posts($credentials, $type, $endpoint) {
		if(isset(parse_url($endpoint)['port'])) {
			$port = parse_url($endpoint)['port'];
		}
		else {
			$port = 443;
		}
		$type = urlencode($type);
		$header = $this->auth->generate_header('hawk.1.header', 'GET', parse_url($endpoint)['path'].'?types='.$type, parse_url($endpoint)['host'], $port, $credentials['hawk_key'], $credentials['hawk_id'], $credentials['client_id']);
		$client = $this->Guzzle;
		$request = $client->get($endpoint.'?types='.$type, array('Authorization' => $header, 'Accept' => 'application/json'));
		$response = $request->send()->json();
		return $response;
	}

	public function get_single_post($credentials, $id, $entity, $endpoint) {
		if(isset(parse_url($endpoint)['port'])) {
			$port = parse_url($endpoint)['port'];
		}
		else {
			$port = 443;
		}
		$endpoint = str_replace("{entity}", urlencode($entity), $endpoint);
		$endpoint = str_replace("{post}", $id, $endpoint);
		$header = $this->auth->generate_header('hawk.1.header', 'GET', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $credentials['hawk_key'], $credentials['hawk_id'], $credentials['client_id']);
		$request = $this->Guzzle->get($endpoint, array('Authorization' => $header, 'Accept' => 'application/vnd.tent.post.v0+json'));
		$response = $request->send()->json();
		return $response;
	}

	public function delete_post($credentials, $id, $entity, $endpoint) {
		if(isset(parse_url($endpoint)['port'])) {
			$port = parse_url($endpoint)['port'];
		}
		else {
			$port = 443;
		}
		$endpoint = str_replace("{entity}", urlencode($entity), $endpoint);
		$endpoint = str_replace("{post}", $id, $endpoint);
		$header = $this->auth->generate_header('hawk.1.header', 'DELETE', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $credentials['hawk_key'], $credentials['hawk_id'], $credentials['client_id']);
		$request = $this->Guzzle->delete($endpoint, array('Authorization' => $header, 'Content-Type' => 'application/vnd.tent.post.v0+json;'));
		$response = $request->send()->json();
		return $response;
	}
}
?>