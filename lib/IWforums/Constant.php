<?php

/**
 * @authors Josep Ferràndiz Farré <jferran6@xtec.cat>
 * @authors Joan Guillén Pelegay  <jguille2@xtec.cat> 
 * 
 * @par Llicència: 
 * GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * 
 * @copyright Departament d'Ensenyament. Generalitat de Catalunya 2012-2015
 */

/**
* Cataleg constants globals del mòdul.
*/
class IWforums_Constant
{
    
    // Forum subscription mode
    // 0: no subscription; 1:optional. Users must subscribe/unsubscribe; 2:subscription is default, allow unsubscriptions; 3:compulsory
    const NOT_ALLOWED = 0;    
    const VOLUNTARY   = 1;    
    const OPTIONAL    = 2;
    const COMPULSORY  = 3;
    
    // Forum access level    
    const NONE         = 0;
    const READ         = 1;
    const READ_WRITE   = 2;
    const RW_ADD_TOPIC = 3;
    const MODERATOR    = 4;
    
    // Default rows/page value
    const ITEMSPERPAGE = 12;

}
