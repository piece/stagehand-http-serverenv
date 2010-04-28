<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009-2010 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2009-2010 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      File available since Release 1.0.0
 */

/**
 * A utility which can be used to get some information of the Web server where
 * the application is running on.
 *
 * @package    Stagehand_HTTP_ServerEnv
 * @copyright  2009-2010 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 */
class Stagehand_HTTP_ServerEnv
{
    private static $proxyMeasures =
        array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_VIA',
            'HTTP_X_COMING_FROM',
            'HTTP_COMING_FROM'
        );

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

    /**
     * Gets the script name without QUERY_STRING and PATH_INFO.
     *
     * @return string
     */
    public static function getScriptName()
    {
        $relativeURI = self::getRelativeURI();

        $positionOfQuestion = strpos($relativeURI, '?');
        if ($positionOfQuestion) {
            $scriptName = substr($relativeURI, 0, $positionOfQuestion);
        } else {
            $scriptName = $relativeURI;
        }

        $pathInfo = self::getPathInfo();
        if (is_null($pathInfo)) {
            return $scriptName;
        }

        $positionOfPathInfo =
            strrpos($scriptName, str_replace('%2F', '/', rawurlencode($pathInfo)));
        if ($positionOfPathInfo) {
            return substr($scriptName, 0, $positionOfPathInfo);
        }

        return $scriptName;
    }

    /**
     * Gets the relative uri requested by the client. If the system has REQUEST_URI,
     * it has precedence. If not, the relative uri will be built usgin SCRIPT_NAME,
     * PATH_INFO, and QUERY_STRING.
     *
     * Note when using mod_rewrite in per-directory context on Apache, SCRIPT_NAME
     * does not indicate the original URI.
     *
     * @return string
     */
    public static function getRelativeURI()
    {
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            return str_replace('//', '/', $_SERVER['REQUEST_URI']);
        }

        if (strlen(@$_SERVER['QUERY_STRING'])) {
            $queryString = '?' . $_SERVER['QUERY_STRING'];
        } else {
            $queryString = '';
        }

        $pathInfo = Stagehand_HTTP_ServerEnv::getPathInfo();
        if (!is_null($pathInfo)) {
            $pathInfo = str_replace('%2F', '/', rawurlencode($pathInfo));
        }

        return str_replace('//', '/', $_SERVER['SCRIPT_NAME']) .
               $pathInfo .
               $queryString;
    }

    /**
     * Gets the absolute uri requested by the client.
     *
     * @return string
     */
    public static function getAbsoluteURI()
    {
        return self::getBaseURI() . self::getRelativeURI();
    }

    /**
     * Returns whether the application is accessed via reverse proxies.
     *
     * @return boolean
     */
    public static function usingProxy()
    {
        foreach (self::$proxyMeasures as $proxyMeasure) {
            if (array_key_exists($proxyMeasure, $_SERVER)) {
                return true;
            }
        }

        return false;
    }

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

        foreach (self::$proxyMeasures as $proxyMeasure) {
            if (array_key_exists($proxyMeasure, $_SERVER)) {
                return $_SERVER[$proxyMeasure];
            }
        }
    }

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

    /**
     * Checks whether the current connection is secure or not.
     *
     * @return boolean
     */
    public static function isSecure()
    {
        return array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on';
    }

    /**
     * Gets the base URI requested by a client.
     *
     * @return string
     */
    public static function getBaseURI()
    {
        if (Stagehand_HTTP_ServerEnv::isSecure()) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        if (Stagehand_HTTP_ServerEnv::isRunningOnStandardPort()) {
            $port = '';
        } else {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }

        return $scheme . '://' . $_SERVER['SERVER_NAME'] . $port;
    }
}

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
