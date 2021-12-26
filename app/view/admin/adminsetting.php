<?php

class Adminsetting{
	
	private $options;

    public function register_page()
    {
        add_options_page(
            'Woo Services Setting', 
            'Woo Services Setting', 
            'manage_options', 
            'woo-services-settings', 
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page()
    {
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
            <?php
                settings_fields( 'my_option_group' );
                do_settings_sections( 'woo-services-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

	public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'woo_service_setting', // ID
            'Woo Service Setting', // Title
            array( $this, 'print_section_info' ), // Callback
            'woo-services-settings' // Page
        );  

        add_settings_field(
            'ac_key', // ID
            'Access Token', // Title 
            array( $this, 'ac_key_callback' ), // Callback
            'woo-services-settings', // Page
            'woo_service_setting' // Section           
        );      

        // add_settings_field(
        //     'title', 
        //     'Title', 
        //     array( $this, 'title_callback' ), 
        //     'woo-services-settings', 
        //     'woo_service_setting'
        // );      
    }

    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['ac_key'] ) )
            $new_input['ac_key'] = sanitize_text_field( $input['ac_key'] );

        // if( isset( $input['title'] ) )
        //     $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }

    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    public function ac_key_callback()
    {
    	$this->options = get_option( 'my_option_name' );
        printf(
            '<input type="text" id="ac_key" name="my_option_name[ac_key]" value="%s" />',
            isset( $this->options['ac_key'] ) ? esc_attr( $this->options['ac_key']) : ''
        );
    }

    // public function title_callback()
    // {
    //     printf(
    //         '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
    //         isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
    //     );
    // }
}

new Adminsetting();