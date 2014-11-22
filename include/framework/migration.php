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
     * @package   Framework
     * @author    Peter Lind <peter.e.lind@gmail.com>
     * @copyright 2009 Peter Lind
     * @license   http://www.gnu.org/licenses/gpl.html GPL 3
     * @link      http://www.github.com/Fake51/Infosys
     */

    /**
     * DB migration class
     *
     * @package Framework
     * @author  Peter Lind <peter.e.lind@gmail.com>
     */
class Migration
{
    /**
     * db instance
     *
     * @var DB
     */
    protected $db;

    /**
     * records the version of the migration
     * set in the construct
     *
     * @var int
     */
    protected $version;

    /**
     * stores queries to migrate to next version
     *
     * @var array
     */
    public $up;

    /**
     * stores queries to migrate to previous version
     *
     * @var array
     */
    public $down;

    /**
     * stores a db instance
     *
     * @param DB  $db      - DB instance
     * @param int $version - version number of migration
     *
     * @access public
     * @return void
     */
    public function __construct(DB $db, $version)
    {
        $this->db = $db;
        $this->version = $version;
    }

    /**
     * runs the queries for migrating to next db version
     *
     * @throws FrameworkException
     * @access public
     * @return void
     */
    public function migrateUp()
    {
        if (empty($this->up) || !is_array($this->up) || !$this->db->getState())
        {
            throw new FrameworkException("No migrations to run or DB not in proper state");
        }
        $this->db->begin();
        try
        {
            foreach ($this->up as $query)
            {
                if (!$this->db->exec($query))
                {
                    $this->db->rollback();
                    throw new FrameworkException("Migration up failed: {$query}");
                }
            }
            $this->db->commit();
            $this->logMigration();
        }
        catch (DBException $e)
        {
            $this->db->rollback();
            throw new FrameworkException("Migration up failed: {$e->getMessage()}");
        }
    }

    /**
     * runs the queries for migrating to the previous db version
     *
     * @throws FrameworkException
     * @access public
     * @return void
     */
    public function migrateDown()
    {
        if (empty($this->down) || !is_array($this->down) || !$this->db->getState())
        {
            throw new FrameworkException("No migrations to run or DB not in proper state");
        }
        $this->db->begin();
        try
        {
            foreach ($this->down as $query)
            {
                if (!$this->db->exec($query))
                {
                    $this->db->rollback();
                    throw new FrameworkException("Migration down failed on query: {$query}");
                }
            }
            $this->db->commit();
            $this->logMigration();
        }
        catch (DBException $e)
        {
            $this->db->rollback();
            throw new FrameworkException("Migration down failed: {$e->getMessage()}");
        }
    }

    /**
     * adds a row to the version table, showing the commit of the migration
     *
     * @access public
     * @return void
     */
    public function logMigration()
    {
        try
        {
            $this->db->exec("INSERT INTO version (dbversion, appversion, committed) VALUES (?, ?, NOW())", array($this->version, APP_VERSION));
        }
        catch (DBException $e)
        {
            echo "Could not log migration. Error: {$e->getMessage()}" . PHP_EOL;
        }
    }
}
