<?php

/**
 * Copyright (C) 2010-2013 Peter Lind
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
 * @category  Infosys
 * @package   Scripts
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2010-2013 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */

set_time_limit(0);

putenv('ENVIRONMENT=live');
require '../bootstrap.php';

$infosys->setup();
$dic = $infosys->getDIC();

$log    = $dic->get('Log');
$db     = $dic->get('DB');
$ef     = $dic->get('EntityFactory');

$participants = $ef->create('Deltagere')->findAll();

$usage = array(
    '2014-04-16 17:30:00' => array(1 => 0, 2 => 0, 3 => 0),
    '2014-04-17 17:30:00' => array(1 => 0, 2 => 0, 3 => 0),
    '2014-04-18 17:30:00' => array(1 => 0, 2 => 0, 3 => 0),
    '2014-04-19 17:30:00' => array(1 => 0, 2 => 0, 3 => 0),
    '2014-04-20 17:00:00' => array(1 => 0, 2 => 0, 3 => 0),
);

$early_categories = array('Vegetar');

foreach ($participants as $participant) {
    foreach ($participant->getMadtider() as $madtid) {
        $link = $participant->getFoodItemLink($madtid);
        $food = $madtid->getMad();
        if (!$food || $food->kategori == 'Morgenmad') {
            continue;
        }

        if (in_array($food->kategori, $early_categories) || $participant->isBusyBetween($madtid->dato, date('Y-m-d H:i:s', strtotime($madtid->dato . ' + 2 hour')), 'spilleder')) {
            $link->time_type = 1;
            $link->update();
            $usage[$madtid->dato][1]++;

            continue;
        }

        $time_type       = getTimeType($usage, $madtid);
        $link->time_type = $time_type;
        $link->update();
        $usage[$madtid->dato][$time_type]++;
    }
}

$log->logToDB("Der er opsat madtids-valg for " . count($participants) . " deltagere.", "Script", 0);

function getTimeType(array $usage, MadTider $madtid) {
    $type = 0;
    $min  = 1000;
    foreach ($usage[$madtid->dato] as $id => $people) {
        if ($people < $min) {
            $type = $id;
            $min  = $people;
        }
    }

    return $type;
}
