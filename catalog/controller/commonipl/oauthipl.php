<?php 

namespace Commonipl\ClientIpl;

interface iAauthClient
{
     public function get($parameters);
	
	public function post($data);
	
	public function put($data);
	
	public function delete($parameters = array());
}

?>