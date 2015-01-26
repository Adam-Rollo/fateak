<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-01-16 01:31:10 --- EMERGENCY: ErrorException [ 8 ]: Undefined variable: memory_get_usage ~ APPPATH/classes/Controller/Welcome.php [ 56 ] in /var/www/fateak/application/classes/Controller/Welcome.php:56
2015-01-16 01:31:10 --- DEBUG: #0 /var/www/fateak/application/classes/Controller/Welcome.php(56): Kohana_Core::error_handler(8, 'Undefined varia...', '/var/www/fateak...', 56, Array)
#1 /var/www/fateak/system/classes/Kohana/Controller.php(84): Controller_Welcome->action_memory()
#2 [internal function]: Kohana_Controller->execute()
#3 /var/www/fateak/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Welcome))
#4 /var/www/fateak/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 /var/www/fateak/system/classes/Kohana/Request.php(997): Kohana_Request_Client->execute(Object(Request))
#6 /var/www/fateak/index.php(118): Kohana_Request->execute()
#7 {main} in /var/www/fateak/application/classes/Controller/Welcome.php:56
2015-01-16 02:21:05 --- EMERGENCY: ErrorException [ 4 ]: syntax error, unexpected 'unset' (T_UNSET), expecting ';' ~ APPPATH/classes/Controller/Welcome.php [ 68 ] in file:line
2015-01-16 02:21:05 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line