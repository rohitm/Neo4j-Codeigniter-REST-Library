<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* 
Author : Rohit. Manohar
Email : rohit.manohar@gmail.com
Date : Sept 13th 2011
*/

class Neoapi{

	function string_begins_with($string, $search){
		return (strncmp($string, $search, strlen($search)) == 0);
	}	

	function isServerOnline(){
		if ($this->request ('http://localhost:7474/') == NULL)
			return (false) ;
		else
			return (true) ;
	}
	
	function request($service, $data=NULL, $return_type="array", $delete=false){
		if (!$this->string_begins_with ($service, 'http://')) {
			$url = 'http://localhost:7474/'.$service ;
		} else {
			$url = $service ;
		}
				
		$fields = $data ;
		
		$fields_string = '' ;
		
		//url-ify the data for the POST
		if ($data != NULL)
			$post_data = json_encode($data) ;
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		
		if ($data != NULL){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

			$headers = array(
						'Content-Length: ' . strlen($post_data),
						'Content-Type: application/json',
						'Accept: application/json'
						);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		} else {
			if ($delete)
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');		
			else
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');		
		}
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true) ;

		//execute post
		$result = curl_exec($ch);
		
		//close connection
		curl_close($ch);	
		
		if ($result){
			if ($return_type == "array")
				return (json_decode($result, true)) ;
			else
				return (json_decode($result)) ;
		}
	}

	public function delete($service, $data=NULL, $return_type="array"){
		return ($this->request ($service, $data=NULL, $return_type="array", true)) ;
	}

	public function nodeExists($id){
		$response = $this->request('db/data/node/'.$id) ; 
		if (isset($response["exception"]))
			return (false) ;
		else
			return (true) ;
	}
	
	public function getNodeId($node){
		if (isset ($node['self'])){
			return (substr (strrchr($node['self'], '/'),1)) ;
		} else {
			return (false) ;
		}
	}
}
