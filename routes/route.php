<?php
/**
 *  Register Routes
 */
class Route
{
	
	public function route_init()
	{
	    register_rest_route('api/v2', '/user/create', array(
	        'methods' => 'POST',
	        'callback' => array(new UserController,'create'),
	        //'permission_callback' => array(new Auth,'authentication_callback')
	    ));

	    register_rest_route('api/v2', '/user/login', array(
	        'methods' => 'POST',
	        'callback' => array(new UserController,'login')
	    ));

	    register_rest_route('api/v2', '/user/profile', array(
	        'methods' => 'GET',
	        'callback' => array(new UserController,'getById'),
	        'permission_callback' => array(new Auth,'authentication_callback')
	    ));

	    register_rest_route('api/v2', '/user/update', array(
	        'methods' => 'POST',
	        'callback' => array(new UserController,'update'),
	        'permission_callback' => array(new Auth,'authentication_callback')
	    ));

	    register_rest_route('api/v2', '/user/avatar', array(
	        'methods' => 'POST',
	        'callback' => array(new UserController,'changeAvatar'),
	        'permission_callback' => array(new Auth,'authentication_callback')
	    ));
	}
}
new Route();