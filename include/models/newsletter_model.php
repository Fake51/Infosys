<?php
/**
 * Copyright (C) 2010  Peter Lind
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
 * handles data access for the newsletter controller
 *
 * @package MVC
 * @subpackage Models
 */
class NewsletterModel extends Model
{

    private $subscribe_subject = 'Tilmelding til Fastavals nyhedsbrev';

    /** 
     * adds an email to the list of newsletter
     * subscribers
     *
     * @param string $email
     *
     * @access public
     * @return string
     */
    public function addSubscriber($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email adressen kan ikke valideres. Tjek den og prøv igen.";
        }

        if ($this->createEntity('NewsletterSubscriber')->findSubscribed($email)) {
            return "Du er allerede skrevet op til nyhedsbrevet.";
        }

        $token = md5($email . uniqid());

        if ($this->db->exec("INSERT INTO newslettersubscribers (email, token, status) VALUES (?, ?, 1)", $email, $token)) {

            $mail = new Mail('no-reply@fastaval.dk', $email, $this->subscribe_subject, $this->makeTextSubscribeBody($token));
            $mail->addHtmlBody($this->makeHtmlSubscribeBody($token));
            if ($mail->send()) {
                return '';

            } else {
                return 'Email udsendelse fejlede';
            }

        } else {
            return "Spøgelser i maskinen.";
        }
    }

    /**
     * returns the email text sent to new subscribers
     * when they sign up for the newsletter
     *
     * @param string $token Unique token for user
     *
     * @access protected
     * @return string
     */
    protected function makeTextSubscribeBody($token)
    {
        return <<<TXT
Hej,

Tak for din tilmelding til Fastavals nyhedsbrev.

For at færdiggøre din tilmelding skal du bruge følgende link
http://infosys.fastaval.dk/newsletter/confirm/{$token}

Hvis du mener du har fået denne email ved en fejl (dvs. du
er ikke interesseret i at tilmelde dig Fastavals nyhedsbrev)
så behøver du ikke gøre noget men kan bare ignorere denne
email.

Med venlig hilsen
Fastaval

TXT;
    }
    
    /**
     * returns the email text sent to new subscribers
     * when they sign up for the newsletter
     *
     * @param string $token Unique token for user
     *
     * @access protected
     * @return string
     */
    protected function makeHtmlSubscribeBody($token)
    {
        return <<<TXT
<p>Hej,</p>

<p>Tak for din tilmelding til Fastavals nyhedsbrev.</p>

<p>For at færdiggøre din tilmelding skal du bruge følgende link
<a href="http://infosys.fastaval.dk/newsletter/confirm/{$token}">http://infosys.fastaval.dk/newsletter/confirm/{$token}</a>

<p>Hvis du mener du har fået denne email ved en fejl (dvs. du
er ikke interesseret i at tilmelde dig Fastavals nyhedsbrev)
så behøver du ikke gøre noget men kan bare ignorere denne
email.</p>

<p>Med venlig hilsen</p>
<p>Fastaval</p>

TXT;
    }
    
    /**
     * confirms a user signing up for the newsletter
     *
     * @param string $token Unique token for user
     *
     * @access public
     * @return string
     */
    public function confirmSubscriber($token)
    {
        if (!($subscriber = $this->createEntity('NewsletterSubscriber')->findUnconfirmed($token))) {
            return 'Du er enten allerede tilmeldt nyhedsbrevet eller også mangler du at give os din email adresse.';
        }

        $subscriber->status = NewsletterSubscriber::SUBSCRIBED;
        $subscriber->update();

        return '';
    }

    /** 
     * removes an email from the newsletter list
     * subscribers
     *
     * @param string $token
     *
     * @access public
     * @return string
     */
    public function removeSubscriber($token)
    {
        $res = $this->db->query("SELECT * FROM newslettersubscribers WHERE token = ?", $token);
        if (count($res) != 1)
        {
            return "Du er ikke tilmeldt nyhedsbrevet.";
        }
        if ($this->db->exec("DELETE FROM newslettersubscribers WHERE token = ?", $token))
        {
            return '';
        }
        else
        {
            return "Spøgelser i maskinen.";
        }
    }

    /**
     * returns all newsletter subscribers
     *
     * @access public
     * @return array
     */
    public function getAllSubscribers() {
        return $this->createEntity('NewsletterSubscriber')->findAll();
    }

    /** 
     * handles update of a newsletter
     *
     * @param RequestVars $post
     * @param Newsletter  $newsletter
     *
     * @access public
     * @return bool
     */
    public function processNewsletterUpdate(RequestVars $post, Newsletter $newsletter) {
        if (empty($post->subject) || empty($post->content)) {
            $newsletter->errors = array('Mangler emne eller indhold');
        }
        $newsletter->subject = $post->subject;
        $newsletter->content = $post->content;
        if (!$newsletter->update()) {
            $newsletter->errors = array('Kunne ikke opdatere nyhedsbrev');
            return false;
        }
        return true;
    }

    /** 
     * handles creation of a newsletter
     *
     * @param RequestVars $post
     *
     * @access public
     * @return Newsletter
     */
    public function processNewsletterCreation(RequestVars $post) {
        $newsletter = $this->createEntity('Newsletter');
        $errors = array();
        if (empty($post->subject)) {
            $errors[] = "Der mangler et emne";
        } else {
            $newsletter->subject = $post->subject;
        }
        if (empty($post->content)) {
            $errors[] = "Der mangler indhold";
        } else {
            $newsletter->content = $post->content;
        }
        if ($errors) {
            $newsletter->errors = $errors;
            return $newsletter;
        }
        if (!$newsletter->insert()) {
            $errors[] = "Kunne ikke oprette nyhedsbrevet";
            $newsletter->errors = $errors;
        }
        return $newsletter;
    }

    /**
     * finds a newsletter by it's id
     *
     * @param int $id
     *
     * @access public
     * @return Newsletter
     */
    public function getNewsletter($id) {
        return $this->createEntity('Newsletter')->findById($id);
    }

    /**
     * returns all existing newsletters
     *
     * @access public
     * @return array
     */
    public function getAllMessages() {
        return $this->createEntity('Newsletter')->findAll();
    }

    /**
     * sends a newsletter email
     *
     * @param Newsletter           $newsletter
     * @param NewsletterSubscriber $email
     *
     * @access public
     * @return bool
     */
    public function sendNewsletterMail(Newsletter $newsletter, NewsletterSubscriber $subscriber) {
        $mail = new Mail('no-reply@fastaval.dk', $subscriber->email, $newsletter->subject, $newsletter->getTextBody($subscriber));
        $mail->addHtmlBody($newsletter->getHtmlBody($subscriber));
        return $mail->send();
    }

    /**
     * sends out a test email with the newsletter
     *
     * @param Newsletter $newsletter
     * @param string     $email
     *
     * @access public
     * @return bool
     */
    public function sendNewsletterTestMail(Newsletter $newsletter, $email) {
        $subscriber = $this->createEntity('NewsletterSubscriber');
        $subscriber->email = $email;
        return $this->sendNewsletterMail($newsletter, $subscriber, false);
    }

    /**
     * sends a newsletter
     *
     * @param Newsletter $newsletter
     * @param string     $email
     *
     * @access public
     * @return bool
     */
    public function sendNewsletter(Newsletter $newsletter) {
        $log = new Log($this->db);
        foreach ($this->getAllSubscribers() as $recipient) {
            $this->sendNewsletterMail($newsletter, $recipient);
            $this->registerNewsletterSend($newsletter, $recipient);
            $log->logToDB("Nyhedsbrev (ID: {$newsletter->id}) sendt til " . $recipient->email, "newsletter", $this->getLoggedInUser()->id);
        }
        return true;
    }

    /**
     * registers the time for a newsletter
     * send to a given subscriber
     *
     * @param Newsletter           $newsletter
     * @param NewsletterSubscriber $subscriber
     *
     * @access public
     * @return void
     */
    public function registerNewsletterSend($newsletter, $recipient) {
        $this->db->exec("INSERT INTO newsletters_subscribers (newsletter_id, subscriber_id, sent) VALUES (?, ?, NOW())", array($newsletter->id, $recipient->id));
    }

    /**
     * returns the last time a newsletter was sent
     *
     * @access public
     * @return string
     */
    public function getLastSend() {
        $result = $this->db->query("SELECT MAX(sent) AS last FROM newsletters_subscribers");
        return $result[0]['last'];
    }
}
