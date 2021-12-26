<?php

/**
 * user registeration
*/
require('BaseController.php');
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class UserController extends BaseController
{
    private $woocommerce;

    function __construct()
	{
		$this->woocommerce = $this->__auth();
	}
	
    public function create(){

    	$param_rules['first_name'] = $_POST['first_name'];
        $param_rules['email'] = $_POST['email'];
        $param_rules['password'] = $_POST['password'];
        $param_rules['device_type'] = $_POST['device_type'];
        $param_rules['device_token'] = $_POST['device_token'];
        $param_rules['last_name'] = $_POST['last_name'];
        $param_rules['phone'] = $_POST['phone'];
        $params = $param_rules;

        $response = $this->__validateRequestParams($params, $param_rules);

        if ($this->__is_error == true){
            return $response;
        }else{
  
           	$schema = [
		        'email' => $params['email'],
		        'username' => strtolower($params['first_name']).'_'.date("YmdHsi"),
		        'password' => $params['password'],
		        'first_name' => $params['first_name'],
		        'last_name' => $params['last_name'],
		        'billing' => [
		            'first_name' => $params['first_name'],
		            'last_name' => $params['last_name'],
		            'company' => '',
		            'address_1' => '',
		            'address_2' => '',
		            'city' => '',
		            'state' => '',
		            'postcode' => '',
		            'country' => '',
		            'email' => $params['email'],
		            'phone' => $params['phone']
		        ],
		        'shipping' => [
		            'first_name' => '',
		            'last_name' => '',
		            'company' => '',
		            'address_1' => '',
		            'address_2' => '',
		            'city' => '',
		            'state' => '',
		            'postcode' => '',
		            'country' => '',
		        ]
		    	];

		    	$parsed_json = $this->woocommerce->post('customers', $schema);
		    	$check_exist_account = json_decode(json_encode($parsed_json), true);

		    	if(isset($check_exist_account['code']) && $check_exist_account['code'] == 'woocommerce_rest_authentication_error'){


		            return $this->__sendError($check_exist_account['code'],array('authentication_error'=>$check_exist_account['message']),401);


		        }elseif(isset($check_exist_account['code']) && $check_exist_account['code'] == 'registration-error-email-exists'){


			    	return $this->__sendError('registration-error-email-exists',array('email_exists'=>'An account is already registered with your email address.'),400);

		    	}else{

		    		return $this->__sendResponse('User register successfully.',$parsed_json,200);

		    	}
		    }
    }

    public function login(){

    	$param_rules['user_login'] = $_POST['user_login'];
        $param_rules['user_password'] = $_POST['user_password'];
        $param_rules['device_type'] = $_POST['device_type'];
        $param_rules['device_token'] = $_POST['device_token'];
        $params = $param_rules;


        $response = $this->__validateRequestParams($params, $param_rules);


        if ($this->__is_error == true){
            return $response;
        }else{
        	
        	$user = wp_signon( $params, false );
	        $check_account = json_decode(json_encode($user), true);
			
			if($check_account['errors']){

				return $this->__sendError('Invalid-Credentials',array('invalid_credentials'=>'Username and password does not match'),401);

			}else{
				if (isset($user->ID) && $user->ID != '') {
					$user_info = $this->woocommerce->get('customers/'.$user->ID);

					if(isset($user_info->code ) && $user_info->code == 'woocommerce_rest_authentication_error'){
						
						return $this->__sendError($user_info->code,array('authentication_error'=>$user_info->message),401);

					}else{
						// save device token

						$user_meta = json_decode(json_encode($user_info->meta_data), true);
						$user_set_device_info = array('key' => 'device_info_'.$user->ID, 'value' => array('device_type'=>$_POST['device_type'],'device_token'=>$_POST['device_token']));
						$metadata = [
					        'meta_data' => array($user_set_device_info)
					    ];
					    $update_device_info = $this->woocommerce->put('customers/'.$user->ID, $metadata);

					    return $this->__sendResponse('User login successfully.',$update_device_info,200);
					}
				}
			}
        }
    }

    public function getById($request){
    	$userId = $request['user_obj']->ID;

    	$user_info = $this->woocommerce->get('customers/'.$userId);

		if(isset($user_info->code ) && $user_info->code == 'woocommerce_rest_authentication_error'){
			
			return $this->__sendError($user_info->code,array('authentication_error'=>$user_info->message),401);

		}else{

			return $this->__sendResponse('User retrive successfully.',$user_info,200);
		}
    }

    public function update($request){

    	$param_rules['first_name'] = $_POST['first_name'];
        $param_rules['email'] = $_POST['email'];
        $param_rules['last_name'] = $_POST['last_name'];
        $param_rules['phone'] = $_POST['phone'];
        $params = $param_rules;

        $response = $this->__validateRequestParams($params, $param_rules);

        if ($this->__is_error == true){
            return $response;
        }else{
        	$schema = [
		        'email' => $params['email'],
		        'first_name' => $params['first_name'],
		        'last_name' => $params['last_name'],
		        'billing' => [
		            'first_name' => $params['first_name'],
		            'last_name' => $params['last_name'],
		            'company' => '',
		            'address_1' => '',
		            'address_2' => '',
		            'city' => '',
		            'state' => '',
		            'postcode' => '',
		            'country' => '',
		            'email' => $params['email'],
		            'phone' => $params['phone']
		        ]
		    ];

		    $user_info = $this->woocommerce->put('customers/'.$request['user_obj']->ID, $schema);

	        if(isset($user_info->code ) && $user_info->code == 'woocommerce_rest_authentication_error'){
	                        
	            return $this->__sendError($user_info->code,array('authentication_error'=>$user_info->message),401);

	        }else{
	    	       return $this->__sendResponse('User update successfully.',$user_info,200);
	        }

        }
    }	

    public function changeAvatar($request){

    	$param_rules['profilepicture'] = $_FILES['profilepicture'];
        $params = $param_rules;

        $response = $this->__validateRequestParams($params, $param_rules);

        if ($this->__is_error == true){
            return $response;
        }else{
        	$user_info = $this->woocommerce->get('customers/'.$request['user_obj']->ID);

        	if(isset($user_info->code ) && $user_info->code == 'woocommerce_rest_authentication_error'){

					return $this->__sendError($user_info->code,array('authentication_error'=>$user_info->message),401);
			}else{

        	$fields = array("user_id"=>$request['user_obj']->ID);
        	$dir = plugin_dir_path( __DIR__ ).'\upload';
			$upload_url = $dir.$_FILES['profilepicture']['name'];
			$temp = explode(".", $_FILES["profilepicture"]["name"]);
			$newfilename = $dir.'user_avtr_'.$request['user_obj']->ID.time().'_'.mt_rand().'.jpg';
			$move_file = move_uploaded_file($_FILES['profilepicture']['tmp_name'], $newfilename);

			if($move_file){
				$filenames[] = $dir.'/'.$newfilename;
				foreach ($filenames as $f){
				   $files[$f] = file_get_contents($f);
				}
				$url = site_url().'/wp-json/user/v2/profile_image';
				$curl = curl_init();
				$boundary = uniqid();
				$delimiter = '-------------' . $boundary;
				$post_data = $this->build_data_files($boundary, $fields, $files);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => 1,
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POST => 1,
				  CURLOPT_POSTFIELDS => $post_data,
				  CURLOPT_HTTPHEADER => array(
				    //"Authorization: Bearer $TOKEN",
				    "Content-Type: multipart/form-data; boundary=" . $delimiter,
				    "Content-Length: " . strlen($post_data)

				  ),
				));
				$response = curl_exec($curl);
				$info = curl_getinfo($curl);
				$result = json_decode(json_encode(json_decode($response)), true);
				if($result['code'] == 'uploading-error'){
					
					return $this->__sendError('uploading-error',array('uploading_error'=>'somthing went wrong please try again.'),401);

				}else{

					$user_meta = json_decode(json_encode($user_info->meta_data), true);
				    if(empty($user_meta)){
				            $new_avatar = array('key' => 'avatar_id', 'value' => $result['data']['avatar_url']);
				    }else{

				    		$new_avatar = array('key' => 'avatar_id', 'value' => $result['data']['avatar_url']);
				    }
				    $data = [
				        'meta_data' => array($new_avatar)
				    ];
				    $parsed_json = $this->woocommerce->put('customers/'.$request['user_obj']->ID, $data);

					return $this->__sendResponse('Avatar change successfully.',$parsed_json,200);
					//echo $response;
				}
				curl_close($curl);
				}
			}
        }
    }

    public function build_data_files($boundary, $fields, $files){
	    $data = '';
	    $eol = "\r\n";

	    $delimiter = '-------------' . $boundary;

	    foreach ($fields as $name => $content) {
	        $data .= "--" . $delimiter . $eol
	            . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
	            . $content . $eol;
	    }


	    foreach ($files as $name => $content) {
	        $data .= "--" . $delimiter . $eol
	            . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . $eol
	            //. 'Content-Type: image/png'.$eol
	            . 'Content-Transfer-Encoding: binary'.$eol
	            ;

	        $data .= $eol;
	        $data .= $content . $eol;
	    }
	    $data .= "--" . $delimiter . "--".$eol;


	    return $data;
	}
}

new UserController();
?>