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
 * @category  Infosys
 * @package   Framework
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2009-2012 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */

/**
 * deals with config values
 *
 * @category  Infosys
 * @package   Framework
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2009-2012 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */
class Config
{
    /**
     * data read in from config ini file
     *
     * @var array
     */
    protected $data;

    /**
     * holds parsed config data
     *
     * @var array
     */
    protected $parsed_data = array();

    /**
     * string describing environment
     *
     * @var string
     */
    protected $environment;

    /**
     * public constructor
     *
     * @param string $config_path Path to config .ini file
     * @param string $environment Which environment we're running in
     *
     * @access public
     * @return void
     */
    public function __construct($config_path, $environment = 'test')
    {
        if (!is_string($config_path) || substr($config_path, -4) !== '.ini' || !is_file($config_path)) {
            throw new FrameworkException('Config path is either not valid or does not point to a .ini file');
        }

        if (!is_array($this->data = parse_ini_file($config_path, true))) {
            throw new FrameworkException('Config file cannot be parsed');
        }

        $this->environment = $environment;

        $this->parseData();
    }

    /**
     * parses config data according to environment
     *
     * @access protected
     * @return void
     */
    protected function parseData()
    {
        foreach ($this->data as $environment => $data) {

            foreach ($data as $key => $value) {
                $this->parsed_data[$key] = $value;
            }

            if ($environment == $this->environment) {
                break;
            }
        }
    }

    /**
     * returns a value if it's available
     *
     * @param string $key Name of value to retrieve
     *
     * @access public
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->parsed_data[$key])) {
            return $this->parsed_data[$key];
        }

        return null;
    }
}
