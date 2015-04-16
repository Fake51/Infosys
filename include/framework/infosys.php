<?php
/**
 * Copyright (C) 2009-2012  Peter Lind
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
 * app class - setups up everything and sandboxes it
 *
 * @category Infosys
 * @package  Framework
 * @author   Peter Lind <peter.e.lind@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GPL 3
 * @link     http://www.github.com/Fake51/Infosys
 */
class Infosys
{
    /**
     * instannce of the autoload class
     *
     * @var Autoload
     */
    protected $autoload;

    /**
     * instannce of the config class
     *
     * @var Config
     */
    protected $config;

    /**
     * instance of DB
     *
     * @var DB
     */
    protected $db;

    /**
     * instance of Log
     *
     * @var Log
     */
    protected $log;

    /**
     * instance of Messages
     *
     * @var Messages
     */
    protected $messages;

    /**
     * instance of Session
     *
     * @var Session
     */
    protected $session;

    /**
     * instance of Routes
     *
     * @var Routes
     */
    protected $routes;

    /**
     * instance of Request
     *
     * @var Request
     */
    protected $request;

    /**
     * instance of RequestHandler
     *
     * @var RequestHandler
     */
    protected $request_handler;

    /**
     * DIC object
     *
     * @var DIC
     */
    protected $dic;

    /**
     * public constructor
     *
     * @param string $config_path    Path to config file
     * @param string $exception_path Path to framework exception class
     * @param string $autoload_path  Path to autoload class
     *
     * @access public
     * @return void
     */
    public function __construct($config_path, $exception_path, $autoload_path)
    {
        if (!is_file($exception_path)) {
            throw new Exception('Exception class path is not valid');
        }

        include $exception_path;

        if (!is_file($autoload_path)) {
            throw new Exception('Autoload class path is not valid');
        }

        include $autoload_path;

        set_exception_handler(array($this, 'exceptionHandler'));

        $this->autoload = new Autoload(
            array(
                FRAMEWORK_FOLDER,
                CONTROLLER_FOLDER,
                MODEL_FOLDER,
                HELPER_FOLDER,
                LIB_FOLDER,
            )
        );
        spl_autoload_register(array($this->autoload, 'autoloader'));

        if (!($environment = getenv('ENVIRONMENT'))) {
            $environment = 'test';
        }

        $this->config = new Config($config_path, $environment);
        $this->dic    = new DIC();

        $this->dic->addReusableObject($this->autoload);
    }

    /**
     * sets up the app to receive and handle requests
     *
     * @access public
     * @return RequestHandler
     */
    public function setup()
    {
        if ($this->config->isSetupRequired()) {
            return $this;
        }

        $this->dic->addReusableObject(new DB($this->config))
            ->addReusableObject(new Log($this->dic->get('DB'), $this->config))
            ->addReusableObject(new Session($this->config->get('app.public_uri')))
            ->addReusableObject(new Messages($this->dic->get('Session')))
            ->addReusableObject(new Routes($this->config))
            ->addReusableObject(new EntityFactory($this->dic->get('DB'), $this->autoload))
            ->addReusableObject(new Layout($this->config, $this->dic->get('Routes')))
            ->addReusableObject(new Request($this->dic->get('Routes'), $this->config))
            ->addReusableObject(new Page($this->dic->get('Request'), $this->dic->get('Layout'), $this->dic->get('Messages'), $this->config->get('app.public_uri'), $this->config))
            ->addReusableObject(new SMSSender($this->dic->get('EntityFactory'), $this->config))
            ->addReusableObject(new LogSender($this->dic->get('EntityFactory'), $this->config));

        FrameworkException::setLog($this->dic->get('Log'));

        $this->dic->get('Layout')->setPage($this->dic->get('Page'));

        $this->request_handler = new RequestHandler($this->dic->get('Request'), $this->config, $this->dic);

        return $this;
    }

    /**
     * method docblock
     *
     * @param
     *
     * @access public
     * @return void
     */
    public function getDIC()
    {
        return $this->dic;
    }

    /**
     * wraps a call to the request handler
     *
     * @access public
     * @return mixed
     */
    public function handleRequest()
    {
        if ($this->config->isSetupRequired()) {
            return $this->doAppSetup();
        }

        return $this->request_handler->handleRequest();
    }

    /**
     * sets up app setup, for handling config writing
     * and database setup
     *
     * @access public
     * @return void
     */
    public function doAppSetup()
    {
        die('do app install');
    }

    /**
     * displays an error message when exceptions are not
     * caught before reaching here
     *
     * @param Exception $error Exception object
     *
     * @access public
     * @return void
     */
    public function exceptionHandler($error)
    {
        header('HTTP/1.1 500 Fail');

        $embedded = '';

        if ($error instanceof FrameworkException) {
            $error->logException();
        } else {
            $embedded = $error->getMessage();
        }

        echo <<<HTML
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="dk" lang="dk">
<head>
<title>Sorry - An error occurred</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body><p>While loading the webpage, an error occurred and we're unfortunately unable to show
you the site. Our apologies.<br /><br />The error has been logged, and will hopefully be fixed
soon.</p>
<!-- {$embedded} -->
</body></html>
HTML;
        exit();
    }
}
