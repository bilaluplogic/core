<?php

/**
 * File: $Id$
 *
 * Mozilla js console logger
 *
 * @package logging
 * @copyright (C) 2003 by the Xaraya Development Team.
*/

/**
 * Include the base file
 *
 */
include_once ('./includes/loggers/xarLogger.php');

/**
 * MozJSConsoleLogger
 *
 * Uses Mozillas Javascript Console to log messages
 *
 * @package logging
 */
class xarLogger_mozilla extends xarLogger
{

    var $loggerdesc = "Mozilla Javascript Console Logger";

    var $commoncodeinserted = false;

    function getCommonCode() {
        // Common javascript to get a variable which has the logmessage method
        $code="netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');\n".
              "var con_service_class = Components.classes['@mozilla.org/consoleservice;1'];\n".
              "var iface = Components.interfaces.nsIConsoleService;\n".
              "var jsconsole = con_service_class.getService(iface);\n";
        return $code;
    }

   /**
    * Updates the Observer
    *
    * @param string $message Log message
    * @param int $level Level of priority of the message
    * @return boolean  True on success or false on failure.
    * @access public
    */
    function notify($message, $level)
    {
        static $commoncodeinserted = false;
        // FIXME: this code depends on a user setting to use principal codebase support (same origin policy)
        // it should be done with a signed script eventually, but this is rather complex
        // TODO: check on windows and browsers other than mozilla, to fall back gracefully
        if (!$commoncodeinserted) {
            $code = $this->getCommonCode();
            xarTplAddJavaScriptCode('body',$this->loggerdesc,$code);
            $commoncodeinserted = true;
        }
        $logentry = $this->getTime(). " - (" .$this->levelToString($level).")".$message;

        // Add \ for problematic chars and for each newline format unix, mac and windows
        $logentry = addslashes($logentry);
        $trans = array("\n" => "\\\n","\r" => "\\\r","\r\n" => "\\\r\n");
        $logentry = strtr($logentry,$trans);
        $code = "jsconsole.logStringMessage('$logentry');\n";
        xarTplAddJavaScriptCode('body', $this->loggerdesc, $code);
    }
 }


?>