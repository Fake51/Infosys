<?php
/**
 * Copyright (C) 2009-2012 Peter Lind
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 * PHP version 5.3+
 *
 * sets up the environment, with various defines
 * contains autoload function too
 *
 * @category  Infosys
 * @package   UnitTests
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2009-2012 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */

require __DIR__ . '/../../include/framework/autoload.php';

/**
 * responsible for autoloading classes
 *
 * @category Infosys
 * @package  UnitTests
 * @author   Peter Lind <peter.e.lind@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GPL 3
 * @link     http://www.github.com/Fake51/Infosys
 */
class AutoLoadTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests class normalizing
     *
     * @access public
     * @return void
     */
    public function testNormalizeClass()
    {
        $autoload = new Autoload(array());
        $this->assertTrue($autoload->normalizeClass('Config') === 'config');
        $this->assertTrue($autoload->normalizeClass('DB') === 'db');
        $this->assertTrue($autoload->normalizeClass('DBObject') === 'dbobject');
        $this->assertTrue($autoload->normalizeClass('AutoLoad') === 'auto_load');
    }

    /**
     * tests if a class file exists
     *
     * @access public
     * @return void
     */
    public function testCheckClassFile()
    {
        $autoload = new Autoload(array());
        $this->assertFalse($autoload->checkClassFile('Autoload'));

        $autoload = new Autoload(array(realpath(__DIR__ . '/../../include/framework/') . '/'));
        $this->assertTrue($autoload->checkClassFile('Autoload'));
    }

    /**
     * tests the loading function with an inexistent class
     *
     * @access public
     * @return void
     */
    public function testBadAutoload()
    {
        $autoload = new Autoload(array());
        $this->setExpectedException('Exception');
        $autoload->autoloader('xyz');
        $xyz = new xyz();
    }
}
