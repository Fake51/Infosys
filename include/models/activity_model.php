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
     * @package    MVC
     * @subpackage Models
     * @author     Peter Lind <peter.e.lind@gmail.com>
     * @copyright  2009 Peter Lind
     * @license    http://www.gnu.org/licenses/gpl.html GPL 3
     * @link       http://www.github.com/Fake51/Infosys
     */

    /**
     * handles all data fetching for the activity MVC
     *
     * @package    MVC
     * @subpackage Models
     * @author     Peter Lind <peter.e.lind@gmail.com>
     */
class ActivityModel extends Model {

    /**
     * finds all Aktivitet entities
     *
     * @access public
     * @return bool|array
     */
    public function findAll()
    {
        $activities = array();
        foreach ($this->createEntity('Aktiviteter')->findAll() as $activity) {
            $activities[$activity->type][] = $activity;
        }

        foreach ($activities as $type => $bunch) {
            usort($activities[$type], function($a, $b) {return strcmp($a->navn, $b->navn);});
        }

        return $activities;
    }

    /**
     * returns list of times an activity is run, given the activity
     *
     * @param object $aktivitet - Aktiviteter entity
     * @access public
     * @return array
     */
    public function getAfviklingerForAktivitet($aktivitet)
    {
        if (!is_object($aktivitet) || !$aktivitet->isLoaded())
        {
            return array();
        }
        $select = $this->createEntity('Afviklinger')->getSelect();
        $select->setWhere('aktivitet_id','=',$aktivitet->id);
        return $this->createEntity('Afviklinger')->findBySelectMany($select);
    }

    /**
     * returns array of available activity types
     *
     * @access public
     * @return array
     */
    public function getActivityTypes()
    {
        return $this->createEntity('Aktiviteter')->getAvailableTypes();
    }


    /**
     * updates a given activity with data from POST
     *
     * @param object $activity - Aktiviteter entity
     * @param array $post - Aktivitet array from POST data
     * @access public
     * @return bool
     */
    public function updateActivity($activity, RequestVars $post)
    {
        if (!is_object($activity) || !$activity->isLoaded()) {
            return false;
        }

        foreach ($post->aktivitet as $field => $val) {
            $activity->$field = $val;
        }

        $activity->varighed_per_afvikling = str_replace(',', '.', $activity->varighed_per_afvikling);
        $activity->updated                = date('Y-m-d H:i:s');

        return $activity->update();
    }

    /**
     * creates an activity with data from POST
     *
     * @param array $post - Aktivitet array from POST data
     * @access public
     * @return bool|object
     */
    public function opretAktivitet(RequestVars $post)
    {
        $activity = $this->createEntity('Aktiviteter');
        foreach ($post->aktivitet as $field => $detail) {
            $activity->$field = $detail;
        }

        $activity->min_deltagere_per_hold = intval($activity->min_deltagere_per_hold);
        $activity->max_deltagere_per_hold = intval($activity->max_deltagere_per_hold);
        $activity->spilledere_per_hold    = intval($activity->spilledere_per_hold);
        $activity->varighed_per_afvikling = floatval(str_replace(',', '.', $activity->varighed_per_afvikling));
        $activity->pris                   = intval($activity->pris);

        $activity->wp_link   = $activity->wp_link ? $activity->wp_link : 0;
        $activity->teaser_dk = $activity->teaser_dk ? $activity->teaser_dk : '';
        $activity->teaser_en = $activity->teaser_en ? $activity->teaser_en : '';
        $activity->updated   = date('Y-m-d H:i:s');
        return (($activity->insert()) ? $activity : false);
    }

    /**
     * creates a scheduling for an activity with data from POST
     *
     * @param object $activity - Aktiviteter entity
     * @param array $post - Aktivitet array from POST data
     * @access public
     * @return bool|object
     */
    public function opretAfvikling($activity, RequestVars $post)
    {
        if (!is_object($activity) || !$activity->isLoaded()) {
            return false;
        }

        if (!isset($post->start) || !isset($post->end)) {
            return false;
        }

        $start_timestamp = strtotime($post->start);
        $end_timestamp   = strtotime($post->end);

        if ($start_timestamp >= $end_timestamp) {
            return false;
        }

        $start = date('Y-m-d H:i:s', $start_timestamp);
        $end   = date('Y-m-d H:i:s', $end_timestamp);
        $afv   = $this->createEntity('Afviklinger');

        $select = $afv->getSelect()
            ->setWhere('start' , '=', $start)
            ->setWhere('slut','=',$end)
            ->setWhere('aktivitet_id','=',$activity->id);

        if ($afv->findBySelect($select)) {
            return false;
        }

        $afv->aktivitet_id = $activity->id;
        $afv->start        = $start;
        $afv->slut         = $end;
        $afv->lokale_id    = intval($post->lokale_id);
        $afv->note         = $post->note ? $post->note : '';

        if ($afv->insert()) {
            $activity->updated = date('Y-m-d H:i:s');
            $activity->update();

            return $afv;
        }

        return false;
    }


    /**
     * updates an activity schedule with data from POST
     *
     * @param object $afvikling - Afviklinger entity
     * @param array $post - Aktivitet array from POST data
     * @access public
     * @return bool|object
     */
    public function updateAfvikling($afvikling, RequestVars $post)
    {
        if (!is_object($afvikling) || !$afvikling->isLoaded()) {
            return false;
        }

        if (!isset($post->start) || !isset($post->end)) {
            return false;
        }

        $start_timestamp = strtotime($post->start);
        $end_timestamp   = strtotime($post->end);

        if ($start_timestamp >= $end_timestamp) {
            return false;
        }

        $start = date('Y-m-d H:i:s', $start_timestamp);
        $end   = date('Y-m-d H:i:s', $end_timestamp);
        $afv   = $this->createEntity('Afviklinger');

        $select = $afv->getSelect()
            ->setWhere('start','=',$start)
            ->setWhere('slut','=',$end)
            ->setWhere('aktivitet_id','=',$afvikling->aktivitet_id)
            ->setWhere('id','!=',$afvikling->id);

        if ($afv->findBySelect($select)) {
            return false;
        }

        $afvikling->start     = $start;
        $afvikling->slut      = $end;
        $afvikling->lokale_id = intval($post->lokale_id);
        $afvikling->note      = $post->note;

        if ($afvikling->update()) {
            $activity          = $this->createEntity('Aktiviteter')->findById($afvikling->aktivitet_id);
            $activity->updated = date('Y-m-d H:i:s');
            $activity->update();

            return true;
        }

        return false;
    }

    /**
     * deletes an activity
     *
     * @param object $aktivitet - Aktiviteter entity
     * @access public
     * @return bool
     */
    public function deleteActivity($aktivitet)
    {
        if (!is_object($aktivitet) || !$aktivitet->isLoaded())
        {
            return false;
        }
        return $aktivitet->delete();
    }

    /**
     * deletes an scheduling
     *
     * @param object $afvikling - Afviklinger entity
     * @access public
     * @return bool
     */
    public function deleteAfvikling($afvikling)
    {
        if (!is_object($afvikling) || !$afvikling->isLoaded())
        {
            return false;
        }
        return $afvikling->delete();
    }

    /**
     * wrapper for call to Afviklinger->getAllDates()
     *
     * @access public
     * @return array
     */
    public function getAllDates()
    {
        return $this->createEntity('Afviklinger')->getAllDates();
    }

    /**
     * returns all created rooms
     *
     * @access public
     * @return array
     */
    public function getAllRooms()
    {
        return (($result = $this->createEntity('Lokaler')->findAll()) ? $result : array());
    }

    /**
     * returns array with activitites scheduled for the given dates
     *
     * @param array $dates - the dates to get scheduled activities for
     * @access public
     * @return array
     */
    public function getActivityForDates($dates)
    {
        if (!is_array($dates))
        {
            return array();
        }
        $result = array();
        foreach ($dates as $date)
        {
            $afviklinger = (($afv = $this->createEntity('Afviklinger')->getAfviklingerForDate($date)) ? $afv : array());
            if ($afviklinger)
            {
                foreach ($afviklinger as $afv)
                {
                    $result[$date][$afv->getAktivitet()->id][] = $afv;
                }
            }
        }
        return $result;
    }

    /**
     * returns the maximum hours of play time at the convention
     *
     * @access public
     * @return int - time in hours
     */
    public function getTotalPlaytime()
    {
        return $this->createEntity('Afviklinger')->getMaxPlaytime() / 3600;
    }

    /**
     * fetches details for activities starting at $time
     *
     * @param string $time
     *
     * @access public
     * @return array
     */
    public function getGameStartDetails($time)
    {
        if (false == strtotime($time)) {
            return array();
        }

        $af = $this->createEntity('Afviklinger');
        $select = $af->getSelect();
        $select->setWhere('start', '=', date('Y-m-d H:i:s', strtotime($time)));
        $afviklinger = $af->findBySelectMany($select);
        $result = array();

        foreach ($afviklinger as $afvikling) {
            $activity = $afvikling->getAktivitet();

            if (!in_array($activity->type, array('rolle', 'live', 'braet'))) {
                continue;
            }

            $result[] = array('run' => $afvikling, 'activity' => $activity);
        }

        usort($result, function ($a, $b) {
            return strcmp($a['activity']->navn, $b['activity']->navn);
        });

        return $result;
    }

    /**
     * returns the next available game start
     *
     * @access public
     * @return string
     */
    public function getNextGamestart()
    {
        $result = $this->db->query("SELECT `start` FROM afviklinger, aktiviteter WHERE `start` > NOW() AND aktiviteter.type IN ('rolle', 'live', 'braet') AND aktiviteter.id = afviklinger.aktivitet_id ORDER BY `start` ASC LIMIT 1");

        if ($result) {
            return $result[0]['start'];
        }

        return false;
    }

    /**
     * returns all available game starts
     *
     * @access public
     * @return array
     */
    public function getAllGamestarts()
    {
        $result = $this->db->query("SELECT distinct(`start`) AS start FROM afviklinger, aktiviteter WHERE aktiviteter.type IN ('rolle', 'live', 'braet') AND aktiviteter.id = afviklinger.aktivitet_id  ORDER BY `start` ASC");
        $return = array();

        if ($result) {
            foreach ($result as $r) {
                $return[] = $r['start'];
            }

        }

        return $return;
    }

    /**
     * checks if the logged in user is allowed to
     * run the requested gamestart
     *
     * @param string $time Time to check against
     *
     * @access public
     * @return bool
     */
    public function canRunGameStart($time)
    {
        return $this->inGameStartPeriod($time) && $this->getLoggedInUser()->hasRole('Infonaut');
    }

    /**
     * checks if the given time matches an active
     * game start period
     *
     * @param string $time Time to check against
     *
     * @access public
     * @return bool
     */
    public function inGameStartPeriod($time)
    {
        if (!is_string($time)) {
            return false;
        }

        $timestamp = strtotime($time);
        return true;

        return $timestamp >= (time() - 1800) && $timestamp <= (time() + 1800);
    }

    /**
     * returns a gamestart for the period
     * - empty if none has been created already
     *
     * @param string $time Time to get gamestart for
     *
     * @access public
     * @return GameStart
     */
    public function getGameStartByTime($time)
    {
        $gamestart = $this->createEntity('Gamestart');

        if ($existing = $gamestart->findByDatetime($time)) {
            return $existing;
        }

        $gamestart->datetime = $time;
        $gamestart->user_id  = $this->getLoggedInUser()->id;

        return $gamestart;
    }

    /**
     * returns a Gamestart by id if possible
     *
     * @param int $id Id of Gamestart to fetch
     *
     * @access public
     * @return Gamestart|null
     */
    public function getGameStart($id)
    {
        return $this->createEntity('Gamestart')->findById($id);
    }

    /**
     * fetch information about a gamestart
     *
     * @param DBObject $gamestart Gamestart to get information for
     *
     * @access public
     * @return array
     */
    public function getGameStartInformation(DBObject $gamestart, $last_update = null, $schedule_id = null)
    {
        $timestamp = 1;
        $output    = array();

        if ($last_update && (is_int($last_update) || ctype_digit($last_update))) {
            $timestamp = intval($last_update);
        }

        foreach ($gamestart->getGamestartSchedules() as $gamestart_schedule) {
            if (strtotime($gamestart_schedule->updated) < $timestamp) {
                continue;
            }

            if ($schedule_id && $schedule_id != $gamestart_schedule->schedule_id) {
                continue;
            }

            $minion = $this->createEntity('User')->findById($gamestart_schedule->user_id);

            $groups = $gamestart_schedule->getSchedule()->getAssignedByType('spiller');
            $assigned = array_sum(array_map(function($x) {return count($x);}, $groups));

            $output[$gamestart_schedule->schedule_id] = array(
                'status'            => $gamestart_schedule->status,
                'gamers_lacking'    => $assigned - $gamestart_schedule->gamers_present,
                'gms_present'       => $gamestart_schedule->getGMsPresent(),
                'reserves_offered'  => $gamestart_schedule->reserves_offered - $gamestart_schedule->reserves_accepted,
                'reserves_accepted' => $gamestart_schedule->reserves_accepted,
                'minion'            => array(
                    'id'   => $minion->id,
                    'user' => $minion->user,
                ),
            );
        }
        return $output;
    }

    /**
     * checks if the gamestart is owned by the current user
     *
     * @param Gamestart $gamestart Gamestart period to check for ownership
     *
     * @access public
     * @return bool
     */
    public function ownsGameStart(DBObject $gamestart)
    {
        return $this->getLoggedInUser()->id == $gamestart->user_id;
    }

    /**
     * tries to find and return a schedule
     *
     * @param int $id Id of schedule to return
     *
     * @access public
     * @return null|Afviklinger
     */
    public function getSchedule($id)
    {
        return $this->createEntity('Afviklinger')->findById($id);
    }

    /**
     * returns a gamestart for the period
     * - empty if none has been created already
     *
     * @param DBObject $schedule Schedule to get object for
     *
     * @throws FrameworkException
     * @access public
     * @return GameStartSchedule
     */
    public function getGameStartSchedule(DBObject $schedule)
    {
        if (!($gamestart = $this->createEntity('Gamestart')->findByDatetime($schedule->start))) {
            throw new FrameworkException('No gamestart exists for the schedule period: ' . $schedule->start);
        }

        $gamestart_schedule = $this->createEntity('GamestartSchedule');

        if ($existing = $gamestart_schedule->findByScheduleGamestart($schedule, $gamestart)) {
            return $existing;
        }

        $gamestart_schedule->gamestart_id      = $gamestart->id;
        $gamestart_schedule->schedule_id       = $schedule->id;
        $gamestart_schedule->user_id           = $this->getLoggedInUser()->id;
        $gamestart_schedule->gamers_present    = 0;
        $gamestart_schedule->gm_status         = '';
        $gamestart_schedule->status            = 0;
        $gamestart_schedule->reserves_offered  = 0;
        $gamestart_schedule->reserves_accepted = 0;
        $gamestart_schedule->updated           = date('Y-m-d H:i:s');

        return $gamestart_schedule;
    }

    /**
     * checks ownership of a gamestart schedule
     *
     * @param DBObject $gamestart_schedule Gamestart schedule to check ownership of
     *
     * @access public
     * @return bool
     */
    public function ownsGameStartSchedule(DBObject $gamestart_schedule)
    {
        return $this->getLoggedInUser()->id == $gamestart_schedule->user_id;
    }

    /**
     * updates gamestart schedule based on gamer changes
     * from a minion
     *
     * @param DBObject    $gamestart_schedule Schedule to update
     * @param RequestVars $post               Variables to use in update
     *
     * @throws FrameworkException
     * @access public
     * @return void
     */
    public function handleMinionGamerChange(DBObject $gamestart_schedule, RequestVars $post)
    {
        if (!$gamestart_schedule->isLoaded()) {
            throw new FrameworkException('Schedule is not loaded');
        }

        if (!isset($post->gamer_count)) {
            throw new FrameworkException('Lacking info in request: gamer_count');
        }

        $gamestart_schedule->gamers_present = intval($post->gamer_count);
        $gamestart_schedule->updated        = date('Y-m-d H:i:s');
        $gamestart_schedule->update();

        $this->log("Minion update gamestart detail: gamer count. For " . $gamestart_schedule->getActivity()->navn . ' at ' . $gamestart_schedule->getSchedule()->start, 'Spilstart', $this->getLoggedInUser());
    }

    /**
     * updates gamestart schedule based on gamer changes
     * from a minion - reserve specific update
     *
     * @param DBObject    $gamestart_schedule Schedule to update
     * @param RequestVars $post               Variables to use in update
     *
     * @throws FrameworkException
     * @access public
     * @return void
     */
    public function handleMinionReserveChange(DBObject $gamestart_schedule, RequestVars $post)
    {
        if (!$gamestart_schedule->isLoaded()) {
            throw new FrameworkException('Schedule is not loaded');
        }

        if (!isset($post->count)) {
            throw new FrameworkException('Lacking info in request: count');
        }

        if ($gamestart_schedule->reserves_accepted + $post->count > $gamestart_schedule->reserves_offered) {
            throw new FrameworkException('Not enough reserves available for request');
        }

        $gamestart_schedule->reserves_accepted += $post->count;
        $gamestart_schedule->updated        = date('Y-m-d H:i:s');
        $gamestart_schedule->update();

        $this->log("Minion update gamestart detail: accepted reserves. For " . $gamestart_schedule->getActivity()->navn . ' at ' . $gamestart_schedule->getSchedule()->start, 'Spilstart', $this->getLoggedInUser());
    }

    /**
     * updates gamestart schedule based on gamemaster changes
     * from a minion
     *
     * @param DBObject    $gamestart_schedule Schedule to update
     * @param RequestVars $post               Variables to use in update
     *
     * @throws FrameworkException
     * @access public
     * @return void
     */
    public function handleMinionGamemasterChange(DBObject $gamestart_schedule, RequestVars $post)
    {
        if (!$gamestart_schedule->isLoaded()) {
            throw new FrameworkException('Schedule is not loaded');
        }

        if (!isset($post->gamemaster_id) || !isset($post->state)) {
            throw new FrameworkException('Lacking info in request: gamemaster_id or state');
        }

        $original_status = $gamestart_schedule->getGMStatus();
        $update          = false;
        foreach ($original_status as $index => $gamemaster) {
            if ($gamemaster['gamemaster_id'] == $post->gamemaster_id) {
                $original_status[$index]['state'] = intval(!!$post->state);

                $update = true;
                break;
            }
        }

        if ($update) {
            $gamestart_schedule->updated = date('Y-m-d H:i:s');
            $gamestart_schedule->updateGMStatus($original_status);
        }

        $this->log("Minion update gamestart detail: gamemaster status. For " . $gamestart_schedule->getActivity()->navn . ' at ' . $gamestart_schedule->getSchedule()->start, 'Spilstart', $this->getLoggedInUser());
    }

    /**
     * handles changes from a gamestart, from the master
     *
     * @param int         $id   Id of gamestart to change details for
     * @param RequestVars $post Post vars
     *
     * @throws FrameworkException
     * @access public
     * @return void
     */
    public function handleMasterChange($id, RequestVars $post)
    {

        if (empty($post->type) || !in_array($post->type, array('add-reservist', 'remove-reservist', 'change-schedule-status'))) {
            throw new FrameworkException('Lacking type or wrong type of change for gamestart master changes');
        }

        if (!($gamestart = $this->getGameStart($id))) {
            throw new FrameworkException('No gamestart with that ID');
        }

        if (!($schedule = $this->getSchedule($post->schedule_id))) {
            throw new FrameworkException('No schedule with that ID');
        }

        if (!($gamestart_schedule = $this->getGameStartSchedule($schedule)) || !$gamestart_schedule->status) {
            throw new FrameworkException('Gamestart schedule is not active');
        }

        if ($post->type == 'add-reservist' || $post->type == 'remove-reservist') {
            $this->handleMasterReservistChange($gamestart_schedule, $post);

        } else {
            $this->handleMasterScheduleStatusChange($gamestart_schedule, $post);
        }
    }

    /**
     * handles changes from a master for schedule
     * status changes
     *
     * @param DBObject    $gamestart_schedule Schedule to change
     * @param RequestVars $post               Post data
     *
     * @throws FrameworkException
     * @access protected
     * @return void
     */
    protected function handleMasterScheduleStatusChange(DBObject $gamestart_schedule, RequestVars $post)
    {
        if (empty($post->status)) {
            throw new FrameworkException('Lacking status field in post data');
        }

        $gamestart_schedule->status  = intval($post->status);
        $gamestart_schedule->updated = date('Y-m-d H:i:s');
        $gamestart_schedule->update();
    }

    /**
     * handles changes from a master for reserves
     *
     * @param DBObject    $gamestart_schedule
     * @param RequestVars $post
     *
     * @throws FrameworkException
     * @access protected
     * @return void
     */
    protected function handleMasterReservistChange(DBObject $gamestart_schedule, RequestVars $post)
    {
        $amount = $post->type == 'remove-reservist' ? -1 : 1;

        $gamestart_schedule->reserves_offered += $amount;
        $gamestart_schedule->updated           = date('Y-m-d H:i:s');

        if ($gamestart_schedule->reserves_offered < 0) {
            $gamestart_schedule->reserves_offered = 0;
        }

        $gamestart_schedule->update();

        $this->log("Master update gamestart detail: reserves offered. For " . $gamestart_schedule->getActivity()->navn . ' at ' . $gamestart_schedule->getSchedule()->start, 'Spilstart', $this->getLoggedInUser());
    }

    public function fetchGameStartQueueData()
    {
        $query = '
SELECT
    id
FROM
    gamestarts
WHERE
    datetime BETWEEN NOW() - interval 30 MINUTE AND NOW() + interval 30 MINUTE
ORDER BY id DESC
LIMIT 1
';

        $result = $this->db->query($query);

        if (empty($result)) {
            return array();
        }

        $gamestart = $this->getGameStart($result[0]['id']);

        if ($gamestart->status != gamestart::OPEN) {
            return array();
        }

        $output = array();

        foreach ($gamestart->getGamestartSchedules() as $schedule) {
            $missing = $schedule->getMissingPlayers();

            if ($schedule->status != GameStartSchedule::OPEN || $missing === 0) {
                continue;
            }

            $activity = $schedule->getActivity();

            switch ($activity->type) {
            case 'rolle':
                $type = 'Scenarie / Scenario';
                break;

            case 'braet':
                $type = 'Brætspil / Boardgamer';
                break;

            case 'live':
                $type = 'Live / Larp';
                break;

            default:
                $type = '';
            }

            $output[] = array(
                'id' => $schedule->id,
                'titles' => array(
                    'da' => $activity->navn,
                    'en' => $activity->title_en,
                ),
                'author' => $activity->author,
                'type'   => $type,
                'gamers_needed' => $schedule->getMissingPlayers(),
            );
        }

        return $output;
    }

    /**
     * returns stats on participants karma
     *
     * @access public
     * @return array
     */
    public function getKarmaStats()
    {
        $karma = $this->buildKarma();

        $stats = $karma->calculate($this->createEntity('Deltagere')->findAll());

        return $stats;
    }

    /**
     * returns double booked GMs for the activitys schedules
     *
     * @param \Aktiviteter $activity Activity to get double bookings for
     *
     * @access public
     * @return array
     */
    public function getDoubleBookings(\Aktiviteter $activity)
    {
		$query = '
SELECT
    p.deltager_id,
    p.afvikling_id
FROM
    deltagere_tilmeldinger AS p
    JOIN afviklinger AS pa ON pa.id = p.afvikling_id
    JOIN deltagere_tilmeldinger AS s ON s.deltager_id = p.deltager_id AND s.afvikling_id != p.afvikling_id
    JOIN afviklinger AS sa ON sa.id = s.afvikling_id
WHERE
    p.tilmeldingstype = "spilleder"
    AND pa.aktivitet_id = ?
    AND (
        (pa.start > sa.start AND pa.start < sa.slut)
        OR (pa.start <= sa.start AND pa.slut >= sa.slut)
        OR (pa.slut > sa.start AND pa.slut < sa.slut)
    )
';

        $result = [];

        foreach ($this->db->query($query, [$activity->id]) as $row) {
            $result[$row['deltager_id']][$row['afvikling_id']] = true;
        }

        return $result;
    }

    /**
     * returns true if votes were cast for any
     * of the schedules given
     *
     * @param array $gamestartdetails Schedules to check for votes cast
     *
     * @access public
     * @return bool
     */
    public function votesCast(array $gamestartdetails)
    {
        $mapper = function ($x) {
            return intval($x['run']->id);
        };

        $schedule_ids = array_map($mapper, $gamestartdetails);

        $query = '
SELECT
    COUNT(*) AS count
FROM
    schedules_votes
WHERE
    schedule_id IN (' . implode(', ', $schedule_ids) . ')
    AND cast_at > "0000-00-00 00:00:00"
';

        $result = $this->db->query($query);
        $row    = reset($result);

        return !!$row['count'];
    }

    /**
     * attempts to fetch a vote given a code
     *
     * @param string $code Code to search by
     *
     * @access public
     * @return string|false
     */
    public function fetchVoteCode($code)
    {
        $query = '
SELECT
    code
FROM
    schedules_votes
WHERE
    code = ?
';

        $result = $this->db->query($query, [$code]);

        if (empty($result)) {
            return false;
        }

        return $result[0]['code'];
    }

    /**
     * attempts to fetch a vote given a code
     *
     * @param string $code Code to search by
     *
     * @access public
     * @return string|false
     */
    public function fetchVote($code)
    {
        $query = '
SELECT
    id,
    schedule_id,
    cast_at
FROM
    schedules_votes
WHERE
    code = ?
';

        $result = $this->db->query($query, [$code]);

        if (empty($result)) {
            return false;
        }

        return $result[0];
    }

    /**
     * returns activity given schedule id
     *
     * @param int $schedule_id ID of schedule to load activity for
     *
     * @access public
     * @return DbObject
     */
    public function fetchVoteActivity($schedule_id)
    {
        $schedule = $this->createEntity('Afviklinger')->findById($schedule_id);

        if (!$schedule) {
            return false;
        }

        return $schedule->getActivity();
    }

    /**
     * marks a vote as cast
     *
     * @param  $vote_id ID of vote to mark cast
     *
     * @access public
     * @return bool
     */
    public function markVoteCast($vote_id)
    {
        $query = '
SELECT
    id
FROM
    schedules_votes
WHERE
    id = ?
';

        $result = $this->db->query($query, [$vote_id]);

        if (!$result) {
            return false;
        }

        $query = '
UPDATE schedules_votes SET cast_at = NOW() WHERE id = ?
';

        return $this->db->exec($query, [$vote_id]);
    }
}
