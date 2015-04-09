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
     * @subpackage Entities
     * @author     Peter Lind <peter.e.lind@gmail.com>
     * @copyright  2009 Peter Lind
     * @license    http://www.gnu.org/licenses/gpl.html GPL 3
     * @link       http://www.github.com/Fake51/Infosys
     */

    /**
     * handles the Aktiviteter table
     *
     * @package MVC
     * @subpackage Entities
     */
class Aktiviteter extends DBObject
{

    /**
     * Name of database table
     *
     * @var string
     */
    protected $tablename = "aktiviteter";


    /**
     * performs a wildcard search across the tables fields
     *
     * @param string|int @input - term to look for
     * @access public
     * @return array
     */
    public function wildcardSearch($input) {
        $objects = array();
        $fields = $this->getColumns();
        $select = $this->getSelect();
        foreach ($fields as $field)
        {
            switch ($field)
            {
                case 'note':
                case 'foromtale':
                case 'navn':
                case 'title_en':
                    $select->setWhereOr($field, 'like', "%{$input}%");
                    break;
                default:
                    break;
            }
        }
        $select->setOrder('id', 'asc');
        $results = $this->findBySelectMany($select);
        if (!empty($results)) {
            $objects = $results;
        }
        return $objects;
    }

    /**
     * returns the afviklinger for the loaded aktivitet
     *
     * @access public
     * @return bool|array
     */
    public function getAfviklinger()
    {
        if (!$this->isLoaded())
        {
            return array();
        }
        $select = $this->createEntity('Afviklinger')->getSelect();
        $select->setWhere('aktivitet_id','=',$this->id);
        $select->setOrder('start', 'asc');
        return $this->createEntity('Afviklinger')->findBySelectMany($select);
    }

    /**
     * returns both normal schedules and attached multiblok schedules
     *
     * @access public
     * @return array
     */
    public function getCompleteScheduling()
    {
        $schedules = $this->getAfviklinger();
        foreach ($schedules as $schedule) {
            $schedules = array_merge($schedules, $schedule->getMultiBlok());
        }

        return $schedules;
    }

    /**
     * checks whether the aktivitet should have spilleder or not
     *
     * @access public
     * @return bool
     */
    public function needsSpilleder()
    {
        return $this->spilledere_per_hold > 0;
    }

    /**
     * returns array of activity types
     *
     * @access public
     * @return array
     */
    public function getAvailableTypes()
    {
        $types = $this->getValidColumnValues('type');
        return $types['values'];
    }

    /**
     * returns array of all participants already signed up for the activity
     *
     * @access public
     * @return array
     */
    public function getParticipantsOnTeams($type = '')
    {
        if (!$this->isLoaded())
        {
            return array();
        }
        $participants_on_teams = array();
        foreach ($this->getAfviklinger() as $afvikling)
        {
            foreach ($afvikling->getHold() as $hold)
            {
                foreach ($hold->getPladser() as $plads)
                {
                    if ($type && $type !== $plads->type) {
                        continue;
                    }

                    $participants_on_teams[] = $plads->deltager_id;
                }
            }
        }
        return $participants_on_teams;
    }

    /**
     * checks for empty properties and inserts defaults
     * in their place
     *
     * @access public
     * @return void
     */
    public function convertProblematicToDefault()
    {
        $this->kan_tilmeldes          = $this->kan_tilmeldes ? $this->kan_tilmeldes : 'nej';
        $this->varighed_per_afvikling = $this->varighed_per_afvikling ? $this->varighed_per_afvikling : 0;
        $this->min_deltagere_per_hold = $this->min_deltagere_per_hold ? $this->min_deltagere_per_hold : 0;
        $this->max_deltagere_per_hold = $this->max_deltagere_per_hold ? $this->max_deltagere_per_hold : 0;
        $this->spilledere_per_hold    = $this->spilledere_per_hold ? $this->spilledere_per_hold : 0;
        $this->pris                   = $this->pris ? $this->pris : 0;
        $this->lokale_eksklusiv       = $this->lokale_eksklusiv ? $this->lokale_eksklusiv : 'ja';
        $this->wp_link                = $this->wp_link ? $this->wp_link : 0;
        $this->type                   = $this->type ? $this->type : 'rolle';
        $this->tids_eksklusiv         = $this->tids_eksklusiv ? $this->tids_eksklusiv : 'ja';
        $this->sprog                  = $this->sprog ? $this->sprog : 'dansk';
        $this->replayable             = $this->replayable ? $this->replayable : 'nej';
        $this->hidden                 = $this->hidden ? $this->hidden : 'nej';
    }
}
