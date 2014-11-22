<?php
    /**
     * Copyright (C) 2011  Peter Lind
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
     * @copyright  2011 Peter Lind
     * @license    http://www.gnu.org/licenses/gpl.html GPL 3
     * @link       http://www.github.com/Fake51/Infosys
     */

    /**
     * handles the newsletters table
     *
     * @package MVC
     * @subpackage Entities
     */
class Newsletter extends DBObject
{

    /**
     * Name of database table
     *
     * @var string
     */
    protected $tablename = 'newsletters';

    /**
     * returns a text only version of the
     * newsletter
     *
     * @access public
     * @return string
     */
    public function getTextBody(NewsletterSubscriber $subscriber = null) {
        if (empty($this->content)) {
            return '';
        }
        return strip_tags(preg_replace('#<a\\s+[^>]*?href=(\'([^\']+?)\'|"([^"]+?)"|([^ ]+?))[^>]*?>(.*?)</a>#si', '$5 ($2$3$4)', $this->getHtmlBody($subscriber)));
    }

    /**
     * returns a html version of the
     * newsletter
     *
     * @access public
     * @return string
     */
    public function getHtmlBody(NewsletterSubscriber $subscriber = null) {
        if (empty($this->content)) {
            return '';
        }
        $unsub = empty($subscriber->token) ? '' : $this->getUnsubscribeLink($subscriber);
        return Markdown($this->content . $unsub);
    }

    /**
     * returns a bool on whether the
     * newsletter has been sent
     *
     * @access public
     * @return bool
     */
    public function hasBeenSent() {
        return !!$this->getDB()->query("SELECT * FROM newsletters_subscribers WHERE newsletter_id = {$this->id}");
    }

    public function getSentTime() {
        if (!$this->id) {
            return null;
        }
        $result = $this->getDB()->query("SELECT MIN(sent) AS sent FROM newsletters_subscribers WHERE newsletter_id = {$this->id}");
        return $result[0]['sent'];
    }

    /**
     * returns all recipients of newsletter
     *
     * @access public
     * @return array
     */
    public function getRecipients() {
        if (!$this->id) {
            return array();
        }
        $subscribers = array();
        foreach ($this->getDB()->query("SELECT subscriber_id FROM newsletters_subscribers WHERE newsletter_id = {$this->id}") as $row) {
            $subscribers[] = $row['subscriber_id'];
        }
        $select = $this->createEntity('NewsletterSubscriber')->getSelect()->setWhere('id', 'in', $subscribers);
        return $this->createEntity('NewsletterSubscriber')->findBySelectMany($select);
    }

    public function getUnsubscribeLink(NewsletterSubscriber $subscriber) {
        $routes = new Routes;
        return <<<TXT
---

Du får denne email fordi du har tilmeldt dig Fastavals nyhedsbrev. Hvis du ikke vil modtage flere emails fra os kan du framelde dig via:
{$routes->url('newsletter_unsubscribe', array('token' => $subscriber->token))}
TXT;
    }
}
