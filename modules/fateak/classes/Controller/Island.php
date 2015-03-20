<?php
/**
 * Island is not contain any <head> information or <body> tag.
 * It is more simple than land controller. Less Calculation, Less Variables.
 * Island would be cached by Nginx or Memcache, or display dymical information without cache.
 * When user's access is forbidden, it would return empty rather than 403 
 * Usage: ESI or URL request. Also in Land Controller.
 * Ajax: Island process Ajax request directory. 
 * 
 * @author Rollo - Fateak
 */
abstract class Controller_Island extends Controller
{

}
