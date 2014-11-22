#!/usr/bin/php
<?php
    /**
     * Copyright (C) 2009  Peter Lind
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
     * PHP version 5
     *
     * this file inits the framework. It needs a couple of set definitions (both paths should end with '/':
     * - INCLUDE_PATH: the full path to the include folder
     * - PUBLIC_PATH: the full path to the public folder
     * this one is optional, but as parts of the framework use it, best to init it
     * - PUBLIC_URI: the base part of the uri for the public folder
     *
     * @package    MVC
     * @subpackage Scripts
     * @author     Peter Lind <peter.e.lind@gmail.com>
     * @copyright  2009 Peter Lind
     * @license    http://www.gnu.org/licenses/gpl.html GPL 3
     * @link       http://www.github.com/Fake51/Infosys
     */

    define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../../public') . '/');
    define('INCLUDE_PATH', realpath(dirname(__FILE__) . '/..') . '/');
    define('ENVIRONMENT', 'dev');
    require_once(INCLUDE_PATH . '/bootstrap.php');

    $DB = new DB();
    $ef = new EntityFactory($DB);
    $log = new Log($DB);

    $user = $ef->create('User');
    $select = $user->getSelect()
        ->setWhere('user', '=', 'Admin');
    if ($user->findBySelect($select)) {
        die("Admin user already present" . PHP_EOL);
    }

    $pass = substr(md5(uniqid()), 0, 12);
    $user->pass = md5($pass);
    $user->user = 'Admin';
    $user->insert();

    $role = $ef->create('Role');
    $select = $role->getSelect()
        ->setWhere('name', '=', 'admin');
    if (!($role->findBySelect($select))) {
        $role->name = 'admin';
        $role->description = 'Administrator role - all powerful';
        $role->insert();
    }

    $priv = $ef->create('Privilege');
    $select = $priv->getSelect()
        ->setWhere('controller', '=', '*')
        ->setWhere('method', '=', '*');
    if (!($priv->findBySelect($select))) {
        $priv->controller = '*';
        $priv->method = '*';
        $priv->insert();
    }

    $rp = $ef->create('RolePrivilege');
    $select = $rp->getSelect()
        ->setWhere('role_id', '=', $role->id)
        ->setWhere('privilege_id', '=', $priv->id);
    if (!$rp->findBySelect($select)) {
        $rp->role_id = $role->id;
        $rp->privilege_id = $priv->id;
        $rp->insert();
    }

    $ur = $ef->create('UserRole');
    $select = $ur->getSelect()
        ->setWhere('role_id', '=', $role->id)
        ->setWhere('user_id', '=', $user->id);
    if (!$ur->findBySelect($select)) {
        $ur->role_id = $role->id;
        $ur->user_id = $user->id;
        $ur->insert();
    }
    $log->logToDB("Admin bruger installeret",'User',1);
    echo "Admin user created with username: Admin and password: " . $pass . PHP_EOL;
