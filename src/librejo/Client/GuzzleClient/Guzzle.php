<?php
namespace Librejo\Client\GuzzleClient;

use Guzzle\Http\Client;
use Librejo\Client\Auth\Auth;

class Guzzle {
	protected $Guzzle;
	protected $entityUri;
	protected $app_id;
	protected $auth;
	protected $credentials;
	protected $endpoints;

	// Creating a new Guzzle client
	// Called with new GuzzleClient\Guzzle($entityUri)
	public function __construct($entityUri, $credentials) {
		$this->Guzzle = new \Guzzle\Http\Client($entityUri);
		$this->entityUri = $entityUri;
		$this->credentials = $credentials;
		$this->auth = new Auth;
		$meta = $this->discover($entityUri);
		$this->endpoints = $meta['post']['content']['servers'][0]['urls'];
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
		try {
    		$response = $request->send();
		} 		
		catch (\Guzzle\Http\Exception\BadResponseException $e) {
    		echo '<p>Uh oh! ' . $e->getMessage().'</p>';
    		echo '<p>HTTP request URL: ' . $e->getRequest()->getUrl() . "</p>";
    		echo '<p>HTTP request: ' . $e->getRequest() . "</p>";
    		echo '<p>HTTP response status: ' . $e->getResponse()->getStatusCode() . "</p>";
    		echo '<p>HTTP response: ' . $e->getResponse() . "</p>";
    		echo '<p>JSON: ';
    		var_export($e->getResponse()->json());
    		echo "</p>";
		}
		$link = $response->getHeader('link')->raw();
		$link = preg_replace('/<(.*)>.*/', '$1', $link);
		$get = $this->Guzzle->get($link)->send();
		$response = array('App' => $response->json(), 'Credentials' => $get->json());
		$this->app_id = $response['App']['post']['id'];
		$this->hawk_key = $response['Credentials']['post']['content']['hawk_key'];
		return $response;
	}

	public function oauth($code) {
		$endpoint = $this->endpoints['oauth_token'];
		$port = $this->get_port($endpoint);
		$header = $this->auth->generate_header('hawk.1.header', 'POST', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $this->credentials['hawk_key'], $this->credentials['hawk_id'], $this->credentials['client_id']);
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

	public function send_post($post) {
		$endpoint = $this->endpoints['new_post'];
		$port = $this->get_port($endpoint);
		$type = $post['type'];
		$post = json_encode($post, JSON_UNESCAPED_SLASHES);
		$header = $this->auth->generate_header('hawk.1.header', 'POST', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $this->credentials['hawk_key'], $this->credentials['hawk_id'], $this->credentials['client_id']);
		$client = $this->Guzzle;
		$request = $client->post($endpoint, array('Authorization' => $header, 'Content-Type' => 'application/vnd.tent.post.v0+json; type="'.$type.'"', 'Accept' => 'application/json'), $post);
		$response = $request->send()->json();
		return $response;
	}

	public function get_posts($type) {
		$endpoint = $this->endpoints['posts_feed'];
		$port = $this->get_port($endpoint);
		$type = urlencode($type);
		$header = $this->auth->generate_header('hawk.1.header', 'GET', parse_url($endpoint)['path'].'?types='.$type, parse_url($endpoint)['host'], $port, $this->credentials['hawk_key'], $this->credentials['hawk_id'], $this->credentials['client_id']);
		$client = $this->Guzzle;
		$request = $client->get($endpoint.'?types='.$type, array('Authorization' => $header, 'Accept' => 'application/json'));
		$response = $request->send()->json();
		return $response;
	}

	public function get_single_post($id, $entity) {
		$endpoint = $this->endpoints['post'];
		$port = $this->get_port($endpoint);
		$endpoint = str_replace("{entity}", urlencode($entity), $endpoint);
		$endpoint = str_replace("{post}", $id, $endpoint);
		$header = $this->auth->generate_header('hawk.1.header', 'GET', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $this->credentials['hawk_key'], $this->credentials['hawk_id'], $this->credentials['client_id']);
		$request = $this->Guzzle->get($endpoint, array('Authorization' => $header, 'Accept' => 'application/vnd.tent.post.v0+json'));
		$response = $request->send()->json();
		return $response;
	}

	public function delete_post($id, $entity) {
		$endpoint = $this->endpoints['post'];
		$port = $this->get_port($endpoint);
		$endpoint = str_replace("{entity}", urlencode($entity), $endpoint);
		$endpoint = str_replace("{post}", $id, $endpoint);
		$header = $this->auth->generate_header('hawk.1.header', 'DELETE', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $this->credentials['hawk_key'], $this->credentials['hawk_id'], $this->credentials['client_id']);
		$request = $this->Guzzle->delete($endpoint, array('Authorization' => $header, 'Content-Type' => 'application/vnd.tent.post.v0+json;'));
		$response = $request->send()->json();
		return $response;
	}

	public function get_profile($entity) {
		$meta = $this->discover($entity);
		$profile = $meta['post']['content']['profile'];
		$endpoint = $meta['post']['content']['servers'][0]['urls']['post_attachment'];
		$endpoint = str_replace("{entity}", urlencode($entity), $endpoint);
		$endpoint = str_replace("{post}", "meta", $endpoint);
		$endpoint = str_replace("{name}", $meta['post']['attachments'][0]['name'], $endpoint);
		$headers = get_headers($endpoint);
		foreach ($headers as $header) {
			if (preg_match("/Location:.*/", $header)) {
                $location = $header;
                $location = str_replace("Location: ", "", $location);
            }
		}
		$profile['avatar'] = $entity.$location;
		return $profile;
	}

	public function update_post($id, $entity, $new_post) {
		$endpoint = $this->endpoints['post'];
		$endpoint = str_replace("{entity}", urlencode($entity), $endpoint);
		$endpoint = str_replace("{post}", $id, $endpoint);
		$port = $this->get_port($endpoint);
		$type = $new_post['type'];
		$new_post = json_encode($new_post, JSON_UNESCAPED_SLASHES);
		$header = $this->auth->generate_header('hawk.1.header', 'PUT', parse_url($endpoint)['path'], parse_url($endpoint)['host'], $port, $this->credentials['hawk_key'], $this->credentials['hawk_id'], $this->credentials['client_id']);
		$request = $this->Guzzle->put($endpoint, array('Authorization' => $header, 'Content-Type' => 'application/vnd.tent.post.v0+json; type="'.$type.'"'), $new_post);
		$response = $request->send();
		return $response;
	}


	public function get_port($url) {
		if(isset(parse_url($url)['port'])) {
			$port = parse_url($url)['port'];
		}
		else {
			$port = 443;
		}
		return $port;
	}
}