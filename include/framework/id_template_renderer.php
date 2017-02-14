<?php
/**
 * Copyright (C) 2017  Peter Lind
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
 * @package   Framework 
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2017 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */

/**
 * handles all id template stuff
 *
 * @category Infosys
 * @package  Framework 
 * @author   Peter Lind <peter.e.lind@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GPL 3
 * @link     http://www.github.com/Fake51/Infosys
 */
class IdTemplateRenderer
{
    // todo:
    // caching
    // cache invalidation

    /**
     * public constructor
     *
     * @param Deltagere  $participant Participant to render template for
     * @param IdTemplate $template    Template to render
     *
     * @access public
     */
    public function __construct(Deltagere $participant, IdTemplate $template)
    {
        $this->participant = $participant;
        $this->template    = $template;
    }

    /**
     * converts the imagemagick image to image string
     *
     * @param  $image Image to convert
     *
     * @access protected
     * @return string
     */
    protected function convertToPngOutput($image)
    {
        ob_start();
        imagepng($image);
        $output = ob_get_contents();
        ob_end_clean();

        return base64_encode($output);
    }

    /**
     * loads an image from a path
     *
     * @param string $path Path to file to load
     *
     * @throws FrameworkException
     * @access protected
     * @return resource
     */
    protected function loadImage($path)
    {
        $info = getimagesize($path);

        if (!isset($info[2])) {
            throw new FrameworkException('Could not load image: ' . $path);
        }

        switch ($info[2]) {
        case IMAGETYPE_PNG:
            $bg = imagecreatefrompng($path);
            imagealphablending($bg, true);
            imagesavealpha($bg, true);
            return $bg;

        case IMAGETYPE_JPEG:
            return imagecreatefromjpeg($path);

        case IMAGETYPE_GIF:
            return imagecreatefromgif($path);

        default:
            throw new FrameworkException('Unrecognized background format');
        }
    }

    /**
     * creates a base image from the template background
     *
     * @param IdTemplate $template Template to process
     *
     * @access protected
     * @return resource
     */
    protected function createBackground(IdTemplate $template)
    {
        return $this->loadImage(PUBLIC_PATH . $template->getBackground());
    }

    /**
     * adds a photo template element
     *
     * @param resource  $image       GD image resource
     * @param array     $item        Template item
     * @param Deltagere $participant Participant to render for
     *
     * @access protected
     * @return resource
     */
    protected function addPhotoElement($image, array $item, Deltagere $participant)
    {
        $photo_path = $participant->getCroppedPhotoPath();

        if (!$photo_path) {
            return $image;
        }

        $photo = $this->loadImage($photo_path);

        imagecopyresampled($image, $photo, $item['x'], $item['y'], 0, 0, $item['width'], $item['height'], imagesx($photo), imagesy($photo));

        return $image;
    }

    /**
     * processes template items
     *
     * @param resource $agg Image to work on
     * @param $item
     *
     * @access protected
     * @return resource
     */
    protected function processTemplateItems($agg, $item)
    {
        switch ($item['itemtype']) {
        case 'photo':
            return $this->addPhotoElement($agg, $item, $this->participant);

        default:
            return $agg;
        }
    }

    /**
     * processes the template to render a composite image
     *
     * @access protected
     * @return string
     */
    protected function processTemplate()
    {
        // load background into image
        // process items
        // get image output

        return $this->convertToPngOutput(
            array_reduce(
                $this->template->getItems(),
                [$this, 'processTemplateItems'],
                $this->createBackground($this->template)
            )
        );
    }

    /**
     * fetches template as base64 encoded string, either from
     * cache or from processing it
     *
     * @access protected
     * @return string
     */
    protected function fetchTemplateData()
    {
        return $this->processTemplate();
    }

    /**
     * renders template as an image
     *
     * @access public
     * @return string
     */
    public function renderImage()
    {
        return '<img class="idCardContainer_card" src="data:image/png;base64,' . $this->fetchTemplateData() . '" alt=""/>';
    }
}
