<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-01-07 03:31:48 --- EMERGENCY: ErrorException [ 1 ]: Call to undefined function mcrypt_get_key_size() ~ SYSPATH/classes/Kohana/Encrypt.php [ 124 ] in file:line
2015-01-07 03:31:48 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2015-01-07 03:32:07 --- EMERGENCY: ErrorException [ 1 ]: Call to undefined function mcrypt_get_key_size() ~ SYSPATH/classes/Kohana/Encrypt.php [ 124 ] in file:line
2015-01-07 03:32:07 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2015-01-07 03:32:22 --- EMERGENCY: ErrorException [ 1 ]: Call to undefined function mcrypt_get_key_size() ~ SYSPATH/classes/Kohana/Encrypt.php [ 124 ] in file:line
2015-01-07 03:32:22 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2015-01-07 03:37:23 --- EMERGENCY: ErrorException [ 1 ]: Call to undefined function mcrypt_get_key_size() ~ SYSPATH/classes/Kohana/Encrypt.php [ 124 ] in file:line
2015-01-07 03:37:23 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2015-01-07 03:38:06 --- EMERGENCY: ErrorException [ 1 ]: Call to undefined function mcrypt_get_key_size() ~ SYSPATH/classes/Kohana/Encrypt.php [ 124 ] in file:line
2015-01-07 03:38:06 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2015-01-07 03:46:22 --- EMERGENCY: ErrorException [ 1 ]: Call to undefined function mcrypt_get_key_size() ~ SYSPATH/classes/Kohana/Encrypt.php [ 124 ] in file:line
2015-01-07 03:46:22 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in file:line
2015-01-07 03:48:19 --- EMERGENCY: Kohana_Exception [ 0 ]: Cannot create instances of abstract Controller_Template ~ SYSPATH/classes/Kohana/Request/Client/Internal.php [ 87 ] in /var/www/fateak/system/classes/Kohana/Request/Client.php:114
2015-01-07 03:48:19 --- DEBUG: #0 /var/www/fateak/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Mock_Request_ede91c39), Object(Response))
#1 /var/www/fateak/system/tests/kohana/request/client/InternalTest.php(64): Kohana_Request_Client->execute(Object(Mock_Request_ede91c39))
#2 [internal function]: Kohana_Request_Client_InternalTest->test_response_failure_status('', 'Template', 'missing_action', 'kohana3/Templat...', 500)
#3 /usr/share/php/PHPUnit/Framework/TestCase.php(983): ReflectionMethod->invokeArgs(Object(Kohana_Request_Client_InternalTest), Array)
#4 /usr/share/php/PHPUnit/Framework/TestCase.php(838): PHPUnit_Framework_TestCase->runTest()
#5 /usr/share/php/PHPUnit/Framework/TestResult.php(648): PHPUnit_Framework_TestCase->runBare()
#6 /usr/share/php/PHPUnit/Framework/TestCase.php(783): PHPUnit_Framework_TestResult->run(Object(Kohana_Request_Client_InternalTest))
#7 /usr/share/php/PHPUnit/Framework/TestSuite.php(775): PHPUnit_Framework_TestCase->run(Object(PHPUnit_Framework_TestResult))
#8 /usr/share/php/PHPUnit/Framework/TestSuite.php(745): PHPUnit_Framework_TestSuite->runTest(Object(Kohana_Request_Client_InternalTest), Object(PHPUnit_Framework_TestResult))
#9 /usr/share/php/PHPUnit/Framework/TestSuite.php(705): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#10 /usr/share/php/PHPUnit/Framework/TestSuite.php(705): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#11 /var/www/fateak/modules/unittest/classes/Kohana/Unittest/TestSuite.php(51): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#12 /usr/share/php/PHPUnit/TextUI/TestRunner.php(349): Kohana_Unittest_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#13 /usr/share/php/PHPUnit/TextUI/Command.php(176): PHPUnit_TextUI_TestRunner->doRun(Object(Unittest_TestSuite), Array)
#14 /usr/share/php/PHPUnit/TextUI/Command.php(129): PHPUnit_TextUI_Command->run(Array, true)
#15 /usr/bin/phpunit(46): PHPUnit_TextUI_Command::main()
#16 {main} in /var/www/fateak/system/classes/Kohana/Request/Client.php:114
2015-01-07 03:49:24 --- EMERGENCY: Kohana_Exception [ 0 ]: Cannot create instances of abstract Controller_Template ~ SYSPATH/classes/Kohana/Request/Client/Internal.php [ 87 ] in /var/www/fateak/system/classes/Kohana/Request/Client.php:114
2015-01-07 03:49:24 --- DEBUG: #0 /var/www/fateak/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Mock_Request_e0d7701d), Object(Response))
#1 /var/www/fateak/system/tests/kohana/request/client/InternalTest.php(64): Kohana_Request_Client->execute(Object(Mock_Request_e0d7701d))
#2 [internal function]: Kohana_Request_Client_InternalTest->test_response_failure_status('', 'Template', 'missing_action', 'kohana3/Templat...', 500)
#3 /usr/share/php/PHPUnit/Framework/TestCase.php(983): ReflectionMethod->invokeArgs(Object(Kohana_Request_Client_InternalTest), Array)
#4 /usr/share/php/PHPUnit/Framework/TestCase.php(838): PHPUnit_Framework_TestCase->runTest()
#5 /usr/share/php/PHPUnit/Framework/TestResult.php(648): PHPUnit_Framework_TestCase->runBare()
#6 /usr/share/php/PHPUnit/Framework/TestCase.php(783): PHPUnit_Framework_TestResult->run(Object(Kohana_Request_Client_InternalTest))
#7 /usr/share/php/PHPUnit/Framework/TestSuite.php(775): PHPUnit_Framework_TestCase->run(Object(PHPUnit_Framework_TestResult))
#8 /usr/share/php/PHPUnit/Framework/TestSuite.php(745): PHPUnit_Framework_TestSuite->runTest(Object(Kohana_Request_Client_InternalTest), Object(PHPUnit_Framework_TestResult))
#9 /usr/share/php/PHPUnit/Framework/TestSuite.php(705): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#10 /usr/share/php/PHPUnit/Framework/TestSuite.php(705): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#11 /var/www/fateak/modules/unittest/classes/Kohana/Unittest/TestSuite.php(51): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#12 /usr/share/php/PHPUnit/TextUI/TestRunner.php(349): Kohana_Unittest_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)
#13 /usr/share/php/PHPUnit/TextUI/Command.php(176): PHPUnit_TextUI_TestRunner->doRun(Object(Unittest_TestSuite), Array)
#14 /usr/share/php/PHPUnit/TextUI/Command.php(129): PHPUnit_TextUI_Command->run(Array, true)
#15 /usr/bin/phpunit(46): PHPUnit_TextUI_Command::main()
#16 {main} in /var/www/fateak/system/classes/Kohana/Request/Client.php:114