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
     * handles data for the photo MVC
     *
     * @package    MVC
     * @subpackage Models
     * @author     Peter Lind <peter.e.lind@gmail.com>
     */
class PhotoModel extends Model
{
    /**
     * checks that the given photo identifier exists
     *
     * @param string $identifier ID to check
     *
     * @access public
     * @return bool
     */
    public function identifierExists($identifier)
    {
        $result = $this->db->query('SELECT participant_id FROM participantphotoidentifiers WHERE identifier = ?', $identifier);

        return !!count($result);
    }

    /**
     * handles the uploaded image, if possible
     *
     * @param string  $identifier  ID of upload
     * @param string  $upload_type Type of upload to handle
     * @param Request $request     Request object
     *
     * @access public
     * @return bool
     */
    public function handlePhotoUpload($identifier, $upload_type, Request $request)
    {
        if (!$request->isPost()) {
            return false;
        }

        try {
            $datauri = new \DataUri($request->post->image);

            switch ($datauri->getType()) {
            case 'image/png':
                $suffix = 'png';
                break;

            case 'image/jpeg':
                $suffix = 'jpg';
                break;

            case 'image/gif':
                $suffix = 'gif';
                break;

            default:
                return false;
            }

            return !!file_put_contents(PUBLIC_PATH . 'uploads/photo-' . $upload_type . '-' . $identifier . '.' . $suffix, $datauri->getContent());

        } catch (FrameworkException $e) {
            return false;
        }
    }
}
