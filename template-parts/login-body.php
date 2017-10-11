<?php
/**
 * Login body's customizer template
 *
 * @package Vingt DixSept\template-parts
 *
 * @since 1.1.0
 */
	 	switch ( vingt_dixsept_login_get_action() ) :

	 		case 'lostpassword' :
	 			get_template_part( 'template-parts/login', 'lostpassword' );
	 			break;

	 		case 'register' :
	 			if ( is_multisite() && vingt_dixsept_is_main_site() ) {
	 				get_template_part( 'template-parts/login', 'msregister' );
	 			} else {
	 				get_template_part( 'template-parts/login', 'register' );
	 			}
	 			break;

	 		case 'login' :
	 		default      :
				get_template_part( 'template-parts/login', 'login' );
				break;

		endswitch ;

		get_template_part( 'template-parts/login', 'footer' );
