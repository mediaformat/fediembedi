<?php
/**
 * The Client class contains all the methods to
 * connect to your fediverse instance
 */
class FediClient
{
	private $instance_url;
	private $access_token;
	private $app;
	private static $acct_id;

	public function __construct($instance_url, $access_token = '') {
		$this->instance_url = $instance_url;
		$this->access_token = $access_token;
	}

	public function setStatic($param){
			 self::$acct_id = $param;
	 }

	 public function getStatic(){
       return self::$acct_id;
   }

	public function register_app($redirect_uri, $scopes = 'read') {

		$response = $this->_post('/api/v1/apps', array(
			'client_name' => 'FediEmbedi for WordPress',
			'redirect_uris' => $redirect_uri,
			'scopes' => $scopes,
			'website' => get_site_url()
		));

		if (!isset($response->client_id)){
			return "ERROR";
		}

		$this->app = $response;

		$params = http_build_query(array(
			'response_type' => 'code',
			'redirect_uri' => $redirect_uri,
			'scope' => $scopes,
			'client_id' =>$this->app->client_id
		));

		return $this->instance_url.'/oauth/authorize?'.$params;
	}

// 	public function register_client($redirect_uri, $scopes = 'read') {
//
// 		$response = $this->_get('/api/v1/oauth-clients/local');
//
// 		if (!isset($response->client_id)){
// 			return "ERROR";
// 		}
//
// 		$this->app = $response;
//
// 		$params = http_build_query(array(
// 			'scope' => $scopes,
// 			'client_id' =>$this->app->client_id,
// 			'client_secret' =>$this->app->client_secret
// 		));
//
// 		$access_token = $this->_post('/api/v1/oauth-clients/local');
// //		return $this->instance_url.'/users/token?'.$params;
// 	}

	public function verify_credentials($access_token){

		$headers = array(
			'Authorization'=>'Bearer '.$access_token
		);

		$response = $this->_get('/api/v1/accounts/verify_credentials', null, $headers);

		if(property_exists($response, 'id')){
				$this->setStatic($response->id);
		}

		return $response;
	}

	public function get_bearer_token($client_id, $client_secret, $code, $redirect_uri) {

		$response = $this->_post('/oauth/token',array(
			'grant_type' => 'authorization_code',
			'redirect_uri' => $redirect_uri,
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'code' => $code
		));

		return $response;
	}

	public function get_user_token($client_id, $client_secret) {

		$response = $this->_post('/oauth/token',array(
			'client_id' => $client_id,
			'client_secret' => $client_secret,
		));

		return $response;
	}

	public function get_client_id() {
		return $this->app->client_id;
	}

	public function get_client_secret() {
		return $this->app->client_secret;
	}

	public function getStatus($media = 'false', $pinned = 'false', $replies = 'false', $max_id = null, $since_id = null, $min_id = null, $limit = 10, $reblogs = 'false') {

		$headers = array(
			'Authorization'=> 'Bearer '.$this->access_token
		);

		$account_id = self::$acct_id;

		$query = http_build_query(array(
			'only_media' => $media,
			'pinned' => $pinned,
			'exclude_replies' => $replies,
			'max_id' => $max_id,
			'since_id' => $since_id,
			'min_id' => $min_id,
			'limit' => $limit,
			'exclude_reblogs' => $reblogs
		));

		$response = $this->_get("/api/v1/accounts/{$account_id}/statuses?{$query}", null, $headers);

		return $response;
	}

	public function getVideos($account_id, $is_channel) {

		$headers = array();

		if(!is_null($is_channel)){
				$response = $this->_get("/api/v1/video-channels/{$account_id}/videos", null, $headers);
		} else {
				$response = $this->_get("/api/v1/accounts/{$account_id}/videos", null, $headers);
		}

		return $response;
	}

	public function getTimelineHome() {

		$headers = array(
			'Authorization'=> 'Bearer '.$this->access_token
		);

		$account_id = self::$acct_id;

		$response = $this->_get("/api/v1/accounts/{$account_id}/lists", null, $headers);
		//$response = $this->_get("/api/v1/timelines/home?limit=20", null, $headers);

		return $response;
	}

	public function getAccount() {

		$headers = array(
			'Authorization'=> 'Bearer '.$this->access_token
		);

		$account_id = self::$acct_id;

		$response = $this->_get("/api/v1/accounts/{$account_id}", null, $headers);

		return $response;
	}

	public function getInstance() {

		$headers = array(
			'Authorization'=> 'Bearer '.$this->access_token
		);

		$account_id = self::$acct_id;

		$response = $this->_get("/api/v1/instance", null, $headers);

		return $response;
	}

	private function _post($url, $data = array(), $headers = array()) {
		return $this->post($this->instance_url.$url, $data, $headers);
	}

	public function _get($url, $data = array(), $headers = array()) {
		return $this->get($this->instance_url.$url, $data, $headers);
	}

	private function post($url, $data = array(), $headers = array()) {
		$args = array(
		    'headers' => $headers,
		    'body'=> $data,
		    'redirection' => 5
		);

		$response = wp_remote_post( $this->getValidURL($url), $args );
		if ( is_wp_error( $response ) ) {
		    $error_message = $response->get_error_message();

		} else {
		$responseBody = wp_remote_retrieve_body($response);
		return json_decode($responseBody);
	}

		return $response;
	}

	public function get($url, $data = array(), $headers = array()) {
		$args = array(
		    'headers' => $headers,
		    'redirection' => 5
		);
		$response = wp_remote_get( $this->getValidURL($url), $args );
		if ( is_wp_error( $response ) ) {
		    $error_message = $response->get_error_message();

		} else {
		$responseBody = wp_remote_retrieve_body($response);
		    return json_decode($responseBody);
		}

		return $response;
	}

	public function dump($value){
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}

	private function getValidURL($url){
		 if  ( $ret = parse_url($url) ) {
 			if ( !isset($ret["scheme"]) ){
				$url = "http://{$url}";
			}
		}
		return $url;

	}
}
