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
 * Mail exception type
 *
 * @package   Framework
 * @author    Peter Lind <peter.e.lind@gmail.com>
 */
class MailException extends FrameworkException
{
}

    /**
     * wrapper for SwiftMailer
     *
     * @package   Framework
     * @author    Peter Lind <peter.e.lind@gmail.com>
     */
class Mail
{

    /**
     * stores the message to send
     *
     * @var object
     */
    protected $message;

    /**
     * cache of attached entities
     *
     * @var array
     */
    private $attachments = array();

    /**
     * set up a message for sending
     *
     * @param string|array $from - from address
     * @param string|array $to - to address
     * @param string $subject - message subject
     * @param string $message - message to send
     * @param string|array $cc - addresses to cc
     * @param string|array $bcc - addresses to bcc
     * @param mixed $attachment - mail attachment
     *
     * @throws MailException
     * @access public
     * @return void
     */
    public function __construct($from, $to, $subject, $message, $cc = '', $bcc = '', $attachment = null)
    {
        require_once LIB_FOLDER . 'swift/lib/swift_required.php';
        if (is_string($from)) {
            if (!$this->validateEmail($from)) {
                throw new MailException("From address is invalid: {$from}");
            }
        } elseif (is_array($from)) {
            reset($from);

            if (!$this->validateEmail(key($from))) {
                throw new MailException("From address is invalid: " . key($from));
            }

        } else {
            throw new MailException("From address is invalid: " . gettype($from));
        }

        if (!$this->validateEmail($to)) {
            throw new MailException("To address is invalid: {$to}");
        }

        try {
            $this->_message = Swift_Message::newInstance()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($message, 'text/plain');
        } catch (Exception $e) {
            throw new MailException("Failed to create new message using SwiftMailer. Exception message: {$e->getMessage()}");
        }
    }

    /**
     * adds a html part to the message
     *
     * @param string $html
     *
     * @access public
     * @return $this
     */
    public function addHtmlBody($html) {
        $this->_message->addPart($html, 'text/html');
        return $this;
    }

    public function addAttachmentFile($filename, $content_type = null)
    {
        if (!is_file($filename)) {
            throw new MailException('Cannot access file to add as attachment');
        }

        $this->attachments[] = $attachment = Swift_Attachment::newInstance(file_get_contents($filename), basename($filename), $content_type);
        $this->_message->attach($attachment);

        return $this;
    }

    /**
     * sends the previously prepared email
     *
     * @throws MailException
     * @access public
     * @return bool
     */
    public function send()
    {
        if (!isset($this->_message)) {
            throw new MailException("No message set");
        }

        $transport = Swift_SmtpTransport::newInstance('localhost', 25);
        return !!Swift_Mailer::newInstance($transport)->send($this->_message);
    }

    /**
     * attempts to validate one or more email addresses
     *
     * @param string|array $email - string or array of strings to validate
     *
     * @access public
     * @return bool
     */
    protected function validateEmail($email)
    {
        if (is_string($email))
        {
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                return true;
            }
            return false;
        }
        else if (is_array($email))
        {
            foreach ($email as $e)
            {
                if (!filter_var($e, FILTER_VALIDATE_EMAIL))
                {
                    return false;
                }
                return true;
            }
        }
        return false;
    }
}
