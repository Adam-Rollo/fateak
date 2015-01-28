<?php
/**
 * Const variables
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Setting the Routes
 *
 * @author     Rollo - Fateak
 */
if ( ! Route::cache()) {

    // Image resize
    Route::set('resize', 'media/imagecache/<type>/<dimensions>(/<file>)', array(
        'dimensions' => '\d+x\d+',
	'type'       => 'crop|fillfit|resize',
	'file'       => '.+'
    ))
    ->defaults(array(
	'controller' => 'resize',
	'action'     => 'image',
	'type'       => 'resize'
    ));

    // Static file serving (CSS, JS, images)
    Route::set('assets', 'assets(/<file>)', array('file' => '.+',))
        ->defaults(array(
	    'controller' => 'assets',
	    'action'     => 'load',
        )            
    );

}
