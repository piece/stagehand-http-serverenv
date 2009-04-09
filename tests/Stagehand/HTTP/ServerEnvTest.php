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

// {{{ Stagehand_HTTP_ServerEnvTest

/**
 * Some tests for Stagehand_HTTP_ServerEnv.
 *
 * @package    Stagehand_HTTP_ServerEnv
 * @copyright  2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 */
class Stagehand_HTTP_ServerEnvTest extends PHPUnit_Framework_TestCase
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

    /**#@-*/

    /**#@+
     * @access public
     */

    /**
     * @test
     */
    public function getTheIpAddressOfTheClient()
    {
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';

        $this->assertEquals('1.2.3.4', Stagehand_HTTP_ServerEnv::getRemoteAddr());

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '5.6.7.8';

        $this->assertEquals('5.6.7.8', Stagehand_HTTP_ServerEnv::getRemoteAddr());
    }

    /**
     * @test
     */
    public function getTheScriptNameFromTheRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/path/to/foo.php';

        $this->assertEquals('/path/to/foo.php',
                            Stagehand_HTTP_ServerEnv::getScriptName()
                            );
    }

    /**
     * @test
     */
    public function getTheScriptNameFromTheScriptName()
    {
        $_SERVER['SCRIPT_NAME'] = '/path/to/foo.php';

        $this->assertEquals('/path/to/foo.php',
                            Stagehand_HTTP_ServerEnv::getScriptName()
                            );
    }

    /**
     * @test
     */
    public function removeQueryVariablesFromRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/foo.php?bar=baz';

        $this->assertEquals('/foo.php', Stagehand_HTTP_ServerEnv::getScriptName());
    }

    /**
     * @test
     */
    public function removePathInfoFromRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/foo.php/bar/baz';
        $_SERVER['PATH_INFO'] = '/bar/baz';

        $this->assertEquals('/foo.php', Stagehand_HTTP_ServerEnv::getScriptName());
    }

    /**
     * @test
     */
    public function removeQueryVariablesAndPathInfoFromRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '/foo.php/bar/baz?bar=baz';
        $_SERVER['PATH_INFO'] = '/bar/baz';

        $this->assertEquals('/foo.php', Stagehand_HTTP_ServerEnv::getScriptName());
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
