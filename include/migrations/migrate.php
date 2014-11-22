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
     * @package   Framework
     * @author    Peter Lind <peter.e.lind@gmail.com>
     * @copyright 2009 Peter Lind
     * @license   http://www.gnu.org/licenses/gpl.html GPL 3
     * @link      http://www.github.com/Fake51/Infosys
     */

$file = file('../../env.txt');
if (empty($file) || count($file) > 1)
{
    die("Couldn't open environment file.");
}

mb_internal_encoding("UTF-8");
define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../../public') . '/'); 
define('INCLUDE_PATH', realpath(dirname(__FILE__) . '/..') . '/'); 
define('ENVIRONMENT', $file[0]);
require_once '../config.php';
require_once FRAMEWORK_FOLDER . 'exception.php';
require_once FRAMEWORK_FOLDER . 'autoload.php';

$db = new DB;
FrameworkException::setLog(new Log($db));

echo "Infosys DB migration script." . PHP_EOL;
echo "Infosys version: " . APP_VERSION . PHP_EOL . PHP_EOL;

if ($_SERVER['argc'] < 2)
{
    echo "Execute with -h for instructions." . PHP_EOL;
    exit(0);
}
$arguments = $_SERVER['argv'];
array_shift($arguments);

$reset_schema = $dryrun = $migration_version = $force = false;
$action = array();

foreach ($arguments as $key => $argument)
{
    switch (strtolower($argument))
    {
        // display help
        case "-h":
            display_help();
            exit(0);

        // display migration info
        case "-i":
            display_info($db);
            exit(0);

        case "-m":
            $action[] = 'redo_current_migration';
            break;

        case "-b":
            $action[] = 'reset_to_baseline';
            break;

        // dryrun switch
        case "-d":
            $dryrun = true;
            break;

        // force switch
        case "-f":
            $force = true;
            break;

        // reset schema switch
        case "-r":
            $reset_schema = true;
            break;

        // reinstall baseline
        case "-x":
            $action[] = 'reinstall_baseline';
            break;

        case "all":
            if ($migration_version)
            {
                echo "Bad parameter specified: {$argument}" . PHP_EOL;
                exit(1);
            }
            $migration_version = 'all';
            break;
        default:
            if (is_numeric($argument) && !$migration_version && $argument >= 0)
            {
                $migration_version = intval($argument);
            }
            else
            {
                echo "Bad parameter specified: {$argument}" . PHP_EOL;
                exit(1);
            }
    }
}
if ($dryrun)
{
    echo "Running in dryrun mode, no migrations will be committed" . PHP_EOL . PHP_EOL;
}

if (count($action) > 1)
{
    echo "Multiple, exclusive switches entered, aborting" . PHP_EOL;
    exit(1);
}
if (count($action) == 1)
{
    $action[0]($db, $dryrun, $force);
    exit(0);
}

if ($migration_version > 0 || $migration_version === 0)
{
    run_migrations($db, $migration_version, $dryrun, $reset_schema, $force);
    exit(0);
}

echo "Program error, aborting" . PHP_EOL;
exit(2);

function display_help()
{
    echo "Usage: " . basename(__FILE__) . " [option] [migration number]" . PHP_EOL . PHP_EOL;
    echo "Following switches can be used:" . PHP_EOL . PHP_EOL;
    echo "    -h    Displays this help" . PHP_EOL;
    echo "    -i    Displays info on migrations such as current state" . PHP_EOL;
    echo "    -d    Doesn't run any migrations, only indicates which" . PHP_EOL;
    echo "          migrations would be run" . PHP_EOL;
    echo "    -r    Runs all migrations down to baseline and then runs" . PHP_EOL;
    echo "          all migrations or up to specified migration" . PHP_EOL;
    echo "    -f    Forces the migrations to run, even if individual" . PHP_EOL;
    echo "          migrations fail" . PHP_EOL;
    echo "    -m    Runs the current migration down and up (basically" . PHP_EOL;
    echo "          a do-over)" . PHP_EOL;
    echo "    -b    Run all migrations down from current migration" . PHP_EOL;
    echo "          until baseline but doesn't run back up. Equal to" . PHP_EOL;
    echo "          running -r 0" . PHP_EOL;
    echo "    -x    Run all migrations down from current migration," . PHP_EOL;
    echo "          including baseline but doesn't run back up. Equals" . PHP_EOL;
    echo "          reinstalling the baseline" . PHP_EOL. PHP_EOL;
    echo "If switches -h, -i, -b or -d are specified, migration number is ignored." . PHP_EOL;
    echo "Specifying -r but leaving out migration number will assume all" . PHP_EOL;
    echo "migrations should be run." . PHP_EOL;
}

function display_info(DB $db)
{
    $current_migration = getCurrentVersion($db);
    echo "Current DB Version number: {$current_migration['dbversion']}" . PHP_EOL;
    echo "Migrated under App Version number: {$current_migration['appversion']}" . PHP_EOL;
    echo "Committed: {$current_migration['committed']}" . PHP_EOL;
    echo getTotalMigrationsDone($db) . " migrations done" . PHP_EOL;
    echo count(getAvailableMigrations()) . " migrations available" . PHP_EOL;
}

function getCurrentVersion(DB $db)
{
    try
    {
        $results = $db->query("SELECT * FROM version ORDER BY id DESC LIMIT 1");
        if (empty($results))
        {
            // empty means baseline exists
            return array(
                'dbversion'  => 0,
                'appversion' => 0,
                'committed'  => ''
            );
        }
        else
        {
            return $results[0];
        }
    }
    catch (DBException $e)
    {
        // on an exception, there isn't even a baseline
        return array(
            'dbversion'  => -1,
            'appversion' => 0,
            'committed'  => ''
        );
    }
}

function getTotalMigrationsDone(DB $db)
{
    try
    {
        $results = $db->query("SELECT COUNT(id) AS count FROM version");
        if (empty($results))
        {
            return 0;
        }
        else
        {
            return $results[0]['count'];
        }
    }
    catch (DBException $e)
    {
        return 0;
    }
}

function getAvailableMigrations()
{
    $migrations = array();
    foreach (new DirectoryIterator(realpath(dirname(__FILE__))) as $file)
    {
        $number = (int)array_shift(explode('_', $file->getFileName()));
        if (is_numeric($number) && $number != 0)
        {
            $migrations[$number] =  $file->getPathName();
        }
        if ($file == 'baseline.php')
        {
            $migrations[0] =  $file->getPathName();
        }
    }
    ksort($migrations);
    return $migrations;
}

function redo_current_migration(DB $db, $dryrun, $force)
{
    $current = getCurrentVersion($db);
    if ($current['dbversion'] < 1)
    {
        echo "No migrations has been run, nothing to redo" . PHP_EOL;
        return;
    }
    $migrations = getAvailableMigrations();
    if (empty($migrations[$current['dbversion']]))
    {
        echo "Could not find file for current migration, aborting" . PHP_EOL;
        return;
    }
    echo "Redoing migration {$current['dbversion']} ..." . PHP_EOL;
    $migration = new Migration($db, $current['dbversion']); 
    require $migrations[$current['dbversion']];
    echo "Running down migration for migration {$current['dbversion']}" . PHP_EOL;
    if (!$dryrun)
    {
        try
        {
            $migration->migrateDown();
        }
        catch (FrameworkException $e)
        {
            echo "Migrating failed with message: {$e->getMessage()}";
            if (!$force)
            {
                exit(1);
            }
        }
    }
    echo "Running up migration for migration {$current['dbversion']}" . PHP_EOL;
    if (!$dryrun)
    {
        try
        {
            $migration->migrateUp();
        }
        catch (FrameworkException $e)
        {
            echo "Migrating failed with message: {$e->getMessage()}";
            exit(1);
        }
    }
    echo "Migrations run" . PHP_EOL;
}

function reset_to_baseline(DB $db, $dryrun, $force)
{
    $current = getCurrentVersion($db);
    if ($current['dbversion'] < 1)
    {
        echo "No migrations has been run, cannot run migrations down" . PHP_EOL;
        return;
    }
    echo "Resetting database to baseline ..." . PHP_EOL;
    $migrations = array_reverse(getAvailableMigrations(), true);
    foreach ($migrations as $version => $m)
    {
        if ($version <= $current['dbversion'] && $version > 0)
        {
            $migration = new Migration($db, $version); 
            require $m;
            echo "Running down migration for migration {$version}" . PHP_EOL;
            if (!$dryrun)
            {
                try
                {
                    $migration->migrateDown();
                }
                catch (FrameworkException $e)
                {
                    echo "Migrating failed with message: {$e->getMessage()}";
                    if (!$force)
                    {
                        exit(1);
                    }
                }
            }
        }
    }
    // when running to to a migration, the migration is not included
    // so, log the migration manually
    $migration = new Migration($db, 0);
    $migration->logMigration();

    echo "Migrations run" . PHP_EOL;
}

function reinstall_baseline(DB $db, $dryrun, $force)
{
    $current = getCurrentVersion($db);
    echo "Resetting database to baseline ..." . PHP_EOL;
    $migrations = getAvailableMigrations();
    if (empty($migrations[0]))
    {
        echo "Baseline not available!" . PHP_EOL;
        return;
    }
    if ($current['dbversion'] >= 0)
    {
        echo "Rewinding migrations ..." . PHP_EOL;
        foreach (array_reverse($migrations, true) as $version => $m)
        {
            if ($version <= $current['dbversion'])
            {
                $migration = new Migration($db, $version); 
                require $m;
                echo "Running down migration for migration {$version}" . PHP_EOL;
                if (!$dryrun)
                {
                    try
                    {
                        $migration->migrateDown();
                    }
                    catch (FrameworkException $e)
                    {
                        echo "Migrating failed with message: {$e->getMessage()}";
                        if (!$force)
                        {
                            exit(1);
                        }
                    }
                }
            }
        }
        echo "Migrations rewinded" . PHP_EOL;
    }
    echo "Installing baseline ..." . PHP_EOL;
    $migration = new Migration($db, 0); 
    require $migrations[0];
    if (!$dryrun)
    {
        try
        {
            $migration->migrateUp();
        }
        catch (FrameworkException $e)
        {
            echo "Migrating failed with message: {$e->getMessage()}";
            exit(1);
        }
    }

    echo "Migrations run" . PHP_EOL;
}

function run_migrations(DB $db, $migration_version, $dryrun, $reset_schema, $force)
{
    $current = getCurrentVersion($db);
    $current_version = $current['dbversion'];
    if (intval($current_version) === intval($migration_version))
    {
        echo "Current version is equal to the one specified" . PHP_EOL;
        return;
    }

    $migrations = getAvailableMigrations();
    $reverse_migrations = array_reverse($migrations, true);

    if ($reset_schema)
    {
        echo "Rewinding migrations down to baseline ..." . PHP_EOL;
        // rewind all migrations first
        foreach ($reverse_migrations as $version => $m)
        {
            if ($version <= $current_version && $version > 0)
            {
                $migration = new Migration($db, $version); 
                require $m;
                echo "Running down migration for migration {$version}" . PHP_EOL;
                if (!$dryrun)
                {
                    try
                    {
                        $migration->migrateDown();
                    }
                    catch (FrameworkException $e)
                    {
                        echo "Migrating failed with message: {$e->getMessage()}";
                        if (!$force)
                        {
                            exit(1);
                        }
                    }
                }
            }
        }
        echo "Migrations rewinded to baseline" . PHP_EOL;
        $current_version = 0;
    }
    reset($reverse_migrations);
    $last_migration = key($reverse_migrations);
    if ($migration_version === 'all' || intval($migration_version) > $last_migration)
    {
        $target = $last_migration;
    }
    else
    {
        $target = intval($migration_version);
    }
    echo "Migrating to version {$target} ..." . PHP_EOL;
    if ($current_version > $target)
    {
        foreach ($reverse_migrations as $version => $m)
        {
            if ($version > $target && $version <= $current_version)
            {
                $migration = new Migration($db, $version); 
                require $m;
                echo "Running down migration for migration {$version}" . PHP_EOL;
                if (!$dryrun)
                {
                    try
                    {
                        $migration->migrateDown();
                    }
                    catch (FrameworkException $e)
                    {
                        echo "Migrating failed with message: {$e->getMessage()}";
                        if (!$force)
                        {
                            exit(1);
                        }
                    }
                }
            }
        }
        // when running to to a migration, the migration is not included
        // so, log the migration manually
        $migration = new Migration($db, $target);
        $migration->logMigration();
    }
    else
    {
        foreach ($migrations as $version => $m)
        {
            if ($version <= $target && $version > $current_version)
            {
                $migration = new Migration($db, $version); 
                require $m;
                echo "Running up migration for migration {$version}" . PHP_EOL;
                if (!$dryrun)
                {
                    try
                    {
                        $migration->migrateUp();
                    }
                    catch (FrameworkException $e)
                    {
                        echo "Migrating failed with message: {$e->getMessage()}";
                        if (!$force)
                        {
                            exit(1);
                        }
                    }
                }
            }
        }
    }
    echo "Migrations run to {$target}" . PHP_EOL;
}
