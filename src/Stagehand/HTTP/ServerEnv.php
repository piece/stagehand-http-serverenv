<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Stagehand_HTTP_ServerEnv
 * @copyright  2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      File available since Release 1.0.0
 */

// {{{ Stagehand_HTTP_ServerEnv

/**
 * A utility which can be used to get some information of the Web server where
 * the application is running on.
 *
 * @package    Stagehand_HTTP_ServerEnv
 * @copyright  2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 */
class Stagehand_HTTP_ServerEnv
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    private static $_proxyMeasures = array('HTTP_X_FORWARDED_FOR',
                                           'HTTP_X_FORWARDED',
                                           'HTTP_FORWARDED_FOR',
                                           'HTTP_FORWARDED',
                                           'HTTP_VIA',
                                           'HTTP_X_COMING_FROM',
                                           'HTTP_COMING_FROM'
                                           );

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ getPathInfo()

    /**
     * Gets PATH_INFO string.
     *
     * @return string
     */
    public static function getPathInfo()
    {
        if (PHP_SAPI != 'cgi') {
            if (array_key_exists('PATH_INFO', $_SERVER)) {
                return $_SERVER['PATH_INFO'];
            }
        }

        if (array_key_exists('ORIG_PATH_INFO', $_SERVER)) {
            return $_SERVER['ORIG_PATH_INFO'];
        }
    }

    // }}}
    // {{{ getScriptName()

    /**
     * Gets the script name from the SCRIPT_NAME variable. The value of the PATH_INFO
     * variable is removed from it.
     *
     * @return string
     */
    public static function getScriptName()
    {
        $scriptName = str_replace('//', '/', $_SERVER['SCRIPT_NAME']);

        $pathInfo = self::getPathInfo();
        if (is_null($pathInfo)) {
            return $scriptName;
        }

        $positionOfPathInfo = strrpos($scriptName, $pathInfo);
        if ($positionOfPathInfo) {
            return substr($scriptName, 0, $positionOfPathInfo);
        }

        return $scriptName;
    }

    // }}}
    // {{{ usingProxy()

    /**
     * Returns whether the application is accessed via reverse proxies.
     *
     * @return boolean
     */
    public static function usingProxy()
    {
        foreach (self::$_proxyMeasures as $proxyMeasure) {
            if (array_key_exists($proxyMeasure, $_SERVER)) {
                return true;
            }
        }

        return false;
    }

    // }}}
    // {{{ getRemoteAddr()

    /**
     * Gets an IP address (or IP addresses) of the client making the request.
     *
     * @return string
     * @since Method available since Release 2.0.0dev1
     */
    public static function getRemoteAddr()
    {
        if (!self::usingProxy()) {
            return $_SERVER['REMOTE_ADDR'];
        }

        foreach (self::$_proxyMeasures as $proxyMeasure) {
            if (array_key_exists($proxyMeasure, $_SERVER)) {
                return $_SERVER[$proxyMeasure];
            }
        }
    }

    // }}}
    // {{{ isRunningOnStandardPort()

    /**
     * Checks whether or not the current process is running on standard port either 80
     * or 443.
     *
     * @return boolean
     */
    public static function isRunningOnStandardPort()
    {
        return $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443';
    }

    // }}}
    // {{{ isSecure()

    /**
     * Checks whether the current connection is secure or not.
     *
     * @return boolean
     */
    public static function isSecure()
    {
        return array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on';
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
