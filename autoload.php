<?php
spl_autoload_register( function ( $class ) {
	if ( ! strstr( $class, 'Lazy_Embeds' ) ) {
		return;
	}

	$result = str_replace( 'Lazy_Embeds' . '\\', '\\', $class );
	$result = str_replace( '\\', 'includes/class-', strtolower( $result ) );

	require $result . '.php';
});
