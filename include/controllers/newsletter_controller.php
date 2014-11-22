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
     * @subpackage Controllers 
     * @author     Peter Lind <peter.e.lind@gmail.com>
     * @copyright  2009 Peter Lind
     * @license    http://www.gnu.org/licenses/gpl.html GPL 3
     * @link       http://www.github.com/Fake51/Infosys
     */

    /**
     * controls newsletter functionality
     *
     * @package    MVC
     * @subpackage Controllers 
     * @author     Peter Lind <peter.e.lind@gmail.com>
     */
class NewsletterController extends Controller
{
    protected $prerun_hooks = array(
        array('method' => 'checkUser','exclusive' => true, 'methodlist' => array('subscribe', 'unsubscribe', 'confirm')),
    );

    /**
     * handles a user subscribing to the newsletter
     *
     * @access public
     * @return void
     */
    public function subscribe()
    {
        $this->page->layout_template = "external.phtml";
        $this->page->subscribed      = false;

        if ($this->page->request->isPost() && $this->page->request->post->email)
        {
            $msg = $this->model->addSubscriber($this->page->request->post->email);
            if (empty($msg)) {
                $this->successMessage("Tak for interessen! Vi har sendt dig en bekræftelses-email med et link du skal følge for at aktivere din tilmelding til Fastavals nyhedsbrev.");

            } else {
                $this->errorMessage("Vi kunne desværre ikke tilføje dig til listen. Det skyldes: " . $msg);
            }

            $this->page->subscribed = true;
        }
    }

    /**
     * handles a user unsubscribing from the newsletter
     *
     * @access public
     * @return void
     */
    public function unsubscribe()
    {
        $this->page->layout_template = "external.phtml";
        $msg = $this->model->removeSubscriber($this->vars['token']);
        if (empty($msg))
        {
            $this->successMessage("Vi har fjernet din email adresse fra listen vi sender vores nyhedsbrev ud til.");
        }
        else
        {
            $this->errorMessage("Vi kunne desværre ikke fjerne dig fra listen. Det skyldes: " . $msg);
        }
    }

    /**
     * handles a user confirming a subscription to the newsletter
     *
     * @access public
     * @return void
     */
    public function confirm()
    {
        $this->page->layout_template = "external.phtml";

        if (!($msg = $this->model->confirmSubscriber($this->vars['token']))) {
            $this->successMessage("Du er nu tilmeldt Fastavals nyhedsbrev.");

        } else {
            $this->errorMessage("Vi kunne desværre ikke færdiggøre din tilmelding. Det skyldes: " . $msg);
        }
    }

    /**
     * shows all newsletter subscribers
     *
     * @access public
     * @return void
     */
    public function subscribers() {
        $this->page->subscribers = $this->model->getAllSubscribers();
    }

    /**
     * handles newsletter creation
     *
     * @access public
     * @return void
     */
    public function create() {
        $this->page->errors = array();
        if ($this->page->request->isPost()) {
            $newsletter = $this->model->processNewsletterCreation($this->page->request->post);
            if ($newsletter->id) {
                $this->hardRedirect($this->url('newsletters_view', array('id' => $newsletter->id)));
            }

            if (!empty($newsletter->errors)) {
                foreach ($newsletter->errors as $error) {
                    $this->errorMessage($error);
                }
            }
            $this->page->subject = $newsletter->subject;
            $this->page->content = $newsletter->content;
        }
    }

    /**
     * handles newsletter editing
     *
     * @access public
     * @return void
     */
    public function edit() {
        $this->page->errors = array();
        if (!($newsletter = $this->model->getNewsletter($this->vars['id']))) {
            $this->errorMessage('Intet nyhedsbrev med det id');
            $this->hardRedirect($this->url('newsletters_home'));
        }
        $this->page->newsletter = $newsletter;
        if ($this->page->request->isPost()) {
            $post = $this->page->request->post;
            if ($this->model->processNewsletterUpdate($post, $newsletter)) {
                $this->hardRedirect($this->url('newsletters_view', array('id' => $newsletter->id)));
            }
            if (!empty($newsletter->errors)) {
                foreach ($newsletter->errors as $error) {
                    $this->errorMessage($error);
                }
            }
            $newsletter->subject = $post->subject;
            $newsletter->content = $post->content;
        }
    }

    /**
     * displays details of a given
     * newsletter
     *
     * @access public
     * @return void
     */
    public function view() {
        if (!($this->page->newsletter = $this->model->getNewsletter($this->vars['id']))) {
            $this->hardRedirect($this->url('newsletters_home'));
        }
        $this->page->recipients = $this->model->getAllSubscribers();
        $this->page->already_sent = $this->page->newsletter->hasBeenSent();
        $this->page->last_send = $this->model->getLastSend();
        $this->page->warn_about_time = strtotime($this->page->last_send) > strtotime('now - 7 days');
    }

    /**
     * displays overview of all created newsletters
     *
     * @access public
     * @return void
     */
    public function viewAll() {
        $this->page->messages = $this->model->getAllMessages();
    }

    /**
     * sends a test version of the newsletter
     *
     * @access public
     * @return void
     */
    public function sendTest() {
        if (!empty($this->page->request->get->email) && ($newsletter = $this->model->getNewsletter($this->vars['id']))) {
            $email = $this->page->request->get->email;
            try {
                $this->model->sendNewsletterTestMail($newsletter, $email);
                $this->log('Nyhedsbrevs-test sendt til: ' . $email, 'newsletter', $this->getLoggedInUser());
                $this->ajaxHeader();
            } catch (Exception $e) {
                header('HTTP/1.1 500 Failed');
                echo $e->getMessage();
            }
            exit;
        }
        header('HTTP/1.1 400 Lacking input');
    }

    /**
     * sends the newsletter to all subscribers
     *
     * @access public
     * @return void
     */
    public function send() {
        if ($newsletter = $this->model->getNewsletter($this->vars['id'])) {
            try {
                $this->model->sendNewsletter($newsletter);
                $this->ajaxHeader();
            } catch (Exception $e) {
                header('HTTP/1.1 500 Failed');
                echo $e->getMessage();
            }
            exit;
        }
        header('HTTP/1.1 400 Lacking input');
    }
    
    /**
     * handles ajax calls for newsletter methods
     *
     * @access public
     * @return void
     */
    public function ajax() {
        if ($this->vars['action'] == 'preview') {
            $this->ajaxHeader();
            echo Markdown($this->page->request->post->data);
            exit;
        }
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
}
