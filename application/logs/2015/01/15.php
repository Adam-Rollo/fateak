<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-01-15 00:58:17 --- EMERGENCY: Database_Exception [ 1040 ]: SQLSTATE[HY000] [1040] Too many connections ~ MODPATH/database/classes/Kohana/Database/PDO.php [ 59 ] in /var/www/fateak/modules/database/classes/Kohana/Database/PDO.php:248
2015-01-15 00:58:17 --- DEBUG: #0 /var/www/fateak/modules/database/classes/Kohana/Database/PDO.php(248): Kohana_Database_PDO->connect()
#1 /var/www/fateak/modules/database/classes/Kohana/Database.php(478): Kohana_Database_PDO->escape('Mnteo')
#2 /var/www/fateak/modules/database/classes/Kohana/Database/Query/Builder/Insert.php(149): Kohana_Database->quote('Mnteo')
#3 /var/www/fateak/modules/database/classes/Kohana/Database/Query.php(234): Kohana_Database_Query_Builder_Insert->compile(Object(Database_PDO))
#4 /var/www/fateak/modules/orm/classes/Kohana/ORM.php(1324): Kohana_Database_Query->execute(Object(Database_PDO))
#5 /var/www/fateak/modules/orm/classes/Kohana/ORM.php(1421): Kohana_ORM->create(NULL)
#6 /var/www/fateak/application/classes/Controller/Welcome.php(45): Kohana_ORM->save()
#7 /var/www/fateak/system/classes/Kohana/Controller.php(84): Controller_Welcome->action_index()
#8 [internal function]: Kohana_Controller->execute()
#9 /var/www/fateak/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Welcome))
#10 /var/www/fateak/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#11 /var/www/fateak/system/classes/Kohana/Request.php(997): Kohana_Request_Client->execute(Object(Request))
#12 /var/www/fateak/index.php(118): Kohana_Request->execute()
#13 {main} in /var/www/fateak/modules/database/classes/Kohana/Database/PDO.php:248