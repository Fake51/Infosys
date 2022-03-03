<?php
/**
 * Copyright (C) 2009-2012  Peter Lind
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
 * @category  Infosys
 * @package   Controllers 
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2009-2012 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */

/**
 * handles users, roles and such
 *
 * @category Infosys
 * @package  Controllers 
 * @author   Peter Lind <peter.e.lind@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GPL 3
 * @link     http://www.github.com/Fake51/Infosys
 */
class PhotoController extends Controller
{
    const REMINDER_DAYS = 7;

    protected $prerun_hooks = array(
        array('method' => 'checkUser', 'exclusive' => false, 'methodList' => ['showUploadForm']),
    );

    /**
     * shows a form for uploading a photo
     *
     * @access public
     * @return void
     */
    public function showUploadForm()
    {
        if(strtotime($this->config->get('con.signupend')) < strtotime('now')) {
            echo "Det er for sent at uploade photo. Tilmeldingen er slut og navneskiltene er ved at blive lavet.<br>";
            echo "It is too late to uploade a photo. Sign-up has ended and the name tags are already in the making<br>";
            exit;
        }

        if (!$this->checkIdentifier()) {
            return $this->send404();
        }

        $this->page->clearEarlyLoadJs();

        $this->page->registerLateLoadJS('jquery.2.2.4.min.js');
        $this->page->registerLateLoadJS('jquery.cropit.js');
        $this->page->registerLateLoadJS('photo-cropper.js');
        $this->page->includeCSS('photo-shopper.css');
        $this->page->includeCss('fontello-ebe72605/css/idtemplate.css');

        $this->page->photo_width  = 213;
        $this->page->photo_height = 295;
        $this->page->identifier   = $this->vars['identifier'];

        $this->page->existing_image = $this->model->getExistingImage($this->page->identifier);

        $this->page->layout_template = 'stripped.phtml';
    }

    /**
     * stores original photo upload
     *
     * @access public
     * @return void
     */
    public function storeOriginal()
    {
        if (!$this->checkIdentifier()) {
            return $this->send404();
        }

        $this->page->setBodyRendering(false);

        if (!$this->model->handlePhotoUpload($this->vars['identifier'], 'original', $this->page->request)) {
            $this->page->setStatus('500', 'Upload failed');

        } else {
            $this->log('Deltager #' . $this->model->getParticipantIdFromIdentifier($this->vars['identifier']) . ' har uploadet originalt billede', 'Photo', null);
        }
    }

    /**
     * stores cropped photo upload
     *
     * @access public
     * @return void
     */
    public function storeCropped()
    {
        if (!$this->checkIdentifier()) {
            return $this->send404();
        }

        $this->page->setBodyRendering(false);

        if (!$this->model->handlePhotoUpload($this->vars['identifier'], 'cropped', $this->page->request)) {
            $this->page->setStatus('500', 'Upload failed');

        } else {
            $this->log('Deltager #' . $this->model->getParticipantIdFromIdentifier($this->vars['identifier']) . ' har uploadet tilpasset billede', 'Photo', null);
        }
    }

    /**
     * checks that a valid identifier is provided
     *
     * @access protected
     * @return bool
     */
    protected function checkIdentifier()
    {
        return !empty($this->vars['identifier']) && $this->model->identifierExists($this->vars['identifier']);
    }

    /**
     * returns a 404 error page
     *
     * @access protected
     * @return void
     */
    protected function send404()
    {
        $this->page->setStatus('404', 'Not found');

        $this->page->setTemplate('generic/notfound');
        $this->page->layout_template = 'external.phtml';
        return;
    }

    /**
     * sends photo upload reminders to to people who
     * have not yet uploaded but should
     *
     * @access public
     * @return void
     */
    public function sendUploadReminders()
    {
        $participant_model = $this->model->factory('Participant');

        // loop over participants, get photo upload link, render email, send, log, done
        foreach ($this->model->fetchParticipantsToRemind(self::REMINDER_DAYS) as $participant) {
            $this->page->participant = $participant;
            $this->page->link        = $participant_model->getPhotoUploadLink($participant);

            if (!$participant->speaksDanish()) {
                $title = 'Fastaval: photo upload reminder';
                $this->page->setTemplate('photo/sendphotouploadreminderen');

            } else {
                $title = 'Fastaval: foto upload reminder';
                $this->page->setTemplate('photo/sendphotouploadreminderda');

            }

            $mail = new Mail($this->config);

            $mail->setFrom($this->config->get('app.email_address'), $this->config->get('app.email_alias'))
                ->setRecipient($participant->email)
                ->setSubject($title)
                ->setBodyFromPage($this->page);

            $mail->send();

            $this->log('Sent photo upload reminder email to ' . $participant->email, 'Photo email reminder', null);

        }

        exit;
    }

    /**
     * fetches list of participants that have not uploaded photos,
     * then redirects to list view
     *
     * @access public
     * @return void
     */
    public function seeMissingPhotos()
    {
        $participants = $this->model->fetchParticipantsToRemind(0);

        $participant_model = $this->model->factory('Participant');
        $participant_model->setSearchBaseIds($participants);

        $this->hardRedirect($this->url('show_search_result'));
    }

    /**
     * Download all uploaded photos with ID and name of organizer,
     * in a zip archive
     *
     * @access public
     * @return void
     */
    public function downloadPhotos(){
        // Path and file names
        $path = PUBLIC_PATH . 'uploads/';
        $archive = $path.'photos.zip';
        
        // Get all croped photos
        $photos = glob($path.'photo-cropped-*');

        if (!is_array($photos)) {
            echo "Ingen fotos er blevet uploaded";
            exit;
        }

        $participant_model = $this->model->factory('Participant');

        // create new zip archive
        $zip = new ZipArchive();
        $zip->open($archive, ZIPARCHIVE::CREATE );
        
        foreach ($photos as $photo){
            // Get identifier and file type from photo
            preg_match("/photo-cropped-(.+)\.(.+)/", $photo, $matches);
            $identifier = $matches[1];
            $filetype = $matches[2];

            // Find the participant(organizer) the photo belongs to 
            if ($participant = $participant_model->getParticipantFromPhotoidentifier($identifier)){
                // Add photo to zip file, with a more useful name
                $name = preg_replace("/\s+/","_", $participant->getName() );
                $filename = "{$participant->id}_$name.$filetype";
                $zip->addFile($photo,$filename);
            }

        }
        // Close the archive
        $zip->close();
        // Set headers for downloading the zip file
        header("Content-type: application/zip"); 
        header("Content-Disposition: attachment; filename=Arrangør_billeder_".date("Ymd").".zip"); 
        header("Pragma: no-cache"); 
        header("Expires: 0");
        // Add zip file to the response
        readfile($archive);
        // Delete the zip file afterwards
        unlink($archive);
        exit;
    }
}
