<?php
/**
 * Celtic Joomla Command Line Interface
 *
 * Copyright (c) 2012-2013, Niels Braczek <nbraczek@bsds.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Niels Braczek nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Celtic\JoomlaCLI
 * @subpackage  Core
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   2012-2013 Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE, Version 3 (AGPL-3.0)
 * @link        http://www.bsds.de/
 * @since       File available since Release 1.0.0
 */

if (defined('JOOMLACLI_COMPOSER_INSTALL'))
{
    return;
}

$paths = array(
    __DIR__ . '/../vendor',
    __DIR__ . '/../../..'
);

foreach ($paths as $path)
{
    if (@is_dir($path . '/composer') && @is_file($path . '/autoload.php'))
    {
        require_once $path . '/autoload.php';
        define('JOOMLACLI_COMPOSER_INSTALL', $path . '/autoload.php');

        return;
    }
}

require_once 'Symfony/Component/Console/autoloader.php';

spl_autoload_register(
    function ($class)
    {
        static $classes = null;
        static $path = null;

        if ($classes === null)
        {
            $classes = array(
                'celtic\\joomlacli\\abstractcommand' => '/command.php',
                'celtic\\joomlacli\\driverfactory' => '/driver/factory.php',
                'celtic\\joomlacli\\installcommand' => '/commands/install.php',
                'celtic\\joomlacli\\joomla1_5driver' => '/driver/joomla1_5.php',
                'celtic\\joomlacli\\joomla1_6driver' => '/driver/joomla1_6.php',
                'celtic\\joomlacli\\joomla1_7driver' => '/driver/joomla1_7.php',
                'celtic\\joomlacli\\joomla2driver' => '/driver/joomla2.php',
                'celtic\\joomlacli\\joomla3driver' => '/driver/joomla3.php',
                'celtic\\joomlacli\\joomladriver' => '/driver/abstract.php',
                'celtic\\joomlacli\\versioncommand' => '/commands/version.php'
            );

            $path = dirname(__FILE__);
        }

        $cn = strtolower($class);

        if (isset($classes[$cn]))
        {
            require $path . $classes[$cn];
        }
    }
);
