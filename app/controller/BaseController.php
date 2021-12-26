<?php

/**
 * user registeration
*/
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class BaseController
{
    public
    	 $__is_error = false;

    protected function __auth()
    {
        $woocommerce = new Client(
			    site_url(),
			    $_SERVER['HTTP_CONSUMER_KEY'],
			    $_SERVER['HTTP_CONSUMER_SECRET'],
			    [
			        'wp_api' => true,
			        'version' => 'wc/v3',
			        'query_string_auth' => true,
			        'verify_ssl' => false
			    ]
			);

        return $woocommerce;
    }

    protected function __validateRequestParams($input_params, $param_rules, $param_rules_messages = [])
    {
        $this->__params = $input_params;
        $this->__customMessages = [];
        if (!empty($param_rules_messages))
            $this->__customMessages = $param_rules_messages;

        $errors = [];

            foreach ($param_rules as $field => $value) {
            	if($value == ''){
            		$errors[$field] = $field.' cannot be empty';		
            	}
            }
        if(!empty($errors)){
	        $this->__is_error = true;
	        return $this->__sendError('Validation Error.', $errors);
    	}else{
    		return $response = [
	            'code' => 200,
	            'success' => true,
	            'message' => 'success',
	        ];
    	}
    }

    protected function __sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'code'    => $code,
            'message' => $error,
            'data'    => $errorMessages
        ];

        return wp_send_json($response, $code);
    }

    protected function __sendResponse($message, $data = [], $code = 200)
    {
        $response = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];

        return wp_send_json($response,200);
    }
}

new BaseController();
?>