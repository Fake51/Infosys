<?php

    /**
     * Copyright (C) 2009-2012 Peter Lind
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
     * PHP version 5.3+
     *
     * @package    Infosys
     * @subpackage Controllers 
     * @author     Peter Lind <peter.e.lind@gmail.com>
     * @copyright  2009-2012 Peter Lind
     * @license    http://www.gnu.org/licenses/gpl.html GPL 3
     * @link       http://www.github.com/Fake51/Infosys
     */

    /**
     * default controller
     *
     * @package    Infosys
     * @subpackage Controllers 
     * @author     Peter Lind <peter.e.lind@gmail.com>
     */
class IndexController extends Controller
{
    protected $prerun_hooks = array(
        array('method' => 'checkUser','exclusive' => true, 'methodlist' => array('login', 'noAccess', 'kickOffSMSScript')),
    );

    /**
     * executes the sms script
     *
     * @access public
     * @return void
     */
    public function kickOffSMSScript()
    {
        $this->model->runAutomaticSMSSend();
        exit;
    }

    /**
     * Default method of the class
     *
     * @access public
     * @return void
     */
    public function main() {
        if (!empty($this->page->request->get->wildcardsearch)) {
            $participant_model = $this->model->factory('Participant');
            if (
                ($id = $this->page->request->get->wildcardsearch)
                && intval($id)
                && ($participant = $participant_model->findParticipant($id))
            ) {
                $querystring = !empty($this->page->request->get->payment_edit) ? '?payment_edit=true' : '';
                $this->hardRedirect($this->url('visdeltager', array('id' => intval($id))) . $querystring);
            }

            $session = $this->dic->get('Session');

            $session->search = array('wildcardsearch' => $this->page->request->get->wildcardsearch);
            $this->hardRedirect($this->url('show_search_result'));
        }

        $this->page->setTitle('Oversigt');
        $this->page->participant_data = $this->model->generateParticipantStats();
        $this->page->wear_data        = $this->model->generateWearStats();
        $this->page->food_data        = $this->model->generateFoodStats();
        $this->page->entrance_data    = $this->model->generateEntranceStats();
    }

    /**
     * handles users trying to log into the system
     *
     * @access public
     * @return void
     */
    public function login() {
        $this->page->setTitle('Login');
        if (!$this->page->request->isPost()) {
            return;
        }

        $post = $this->page->request->post;
        if (empty($post->user) || empty($post->pass)) {
            return;
        }

        if ($this->model->handleLogin($post->user, $post->pass)) {
            if ($post->user == 'mad') {
                $this->hardRedirect($this->url('food_handout'));
            }

            $this->hardRedirect($this->url('home'));
        } else {
            $this->errorMessage('Brugernavn eller kode var forkert. Prøv igen');
        }
    }

    /**
     * logs a user off the system
     *
     * @access public
     * @return void
     */
    public function logout()
    {
        $this->model->logout();
        $this->successMessage('Du er nu logget af');
        $this->page->setTemplate('login');
    }

    /**
     * shows a no access warning
     *
     * @access public
     * @return void
     */
    public function noAccess()
    {
        header('HTTP/1.1 403 No access');
    }
}
