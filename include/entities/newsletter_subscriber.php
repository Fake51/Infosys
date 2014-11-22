<?php
/**
 * Copyright (C) 2009-2013 Peter Lind
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
 * @copyright  2011-2013 Peter Lind
 * @license    http://www.gnu.org/licenses/gpl.html GPL 3
 * @link       http://www.github.com/Fake51/Infosys
 */

/**
 * handles the newslettersubscribers table
 *
 * @package MVC
 * @subpackage Entities
 */
class NewsletterSubscriber extends DBObject
{

    const UNCONFIRMED  = 1;
    const SUBSCRIBED   = 2;
    const UNSUBSCRIBED = 3;

    /**
     * Name of database table
     *
     * @var string
     */
    protected $tablename = 'newslettersubscribers';

    /**
     * tries to locate a subscriber by email
     *
     * @param string $email Email to search by
     *
     * @access public
     * @return NewsletterSubscriber|null
     */
    public function findSubscribed($email)
    {
        return $this->findByEmail($email, self::SUBSCRIBED);
    }

    /**
     * tries to locate a subscriber by email
     *
     * @param string $token Token to search by
     *
     * @access public
     * @return NewsletterSubscriber|null
     */
    public function findUnconfirmed($token)
    {
        return $this->findByToken($token, self::UNCONFIRMED);
    }

    /**
     * method docblock
     *
     * @param string $email  Email to search by
     * @param int    $status Optional status to search by
     *
     * @access public
     * @return NewsletterSubscriber|null
     */
    public function findByEmail($email, $status)
    {
        $select = $this->getSelect()
            ->setWhere('email', '=', $email);

        if ($status) {
            $select->setWhere('status', '=', $status);
        }

        return $this->findBySelect($select);
    }

    /**
     * method docblock
     *
     * @param string $token  Token to search by
     * @param int    $status Optional status to search by
     *
     * @access public
     * @return NewsletterSubscriber|null
     */
    public function findByToken($token, $status)
    {
        $select = $this->getSelect()
            ->setWhere('token', '=', $token);

        if ($status) {
            $select->setWhere('status', '=', $status);
        }

        return $this->findBySelect($select);
    }
}
