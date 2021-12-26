<?php
/**
 * Authentication
 */

class Auth{
	
	private $options;
	
	public function authentication_callback(WP_REST_Request $request)
	{
		$author_obj = get_user_by('id', $request->get_header('authorization'));
		if(isset($author_obj) && $request->get_header('authorization') != null && $author_obj->ID == $request->get_header('authorization')){
			$request['user_obj'] = $author_obj;
			return true;
		}else{
			$response = [
	            'code'    => 401,
	            'message' => 'Authorization-error',
	            'data'    => array('authorization_error'=>'Invalid authorization id')
	        ];

        	return wp_send_json($response, 401);
		}
	}

}