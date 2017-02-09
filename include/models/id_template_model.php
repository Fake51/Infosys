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
 * handles all data fetching for the idtemplate MVC
 *
 * @package    MVC
 * @subpackage Models
 * @author     Peter Lind <peter.e.lind@gmail.com>
 */
class IdTemplateModel extends Model
{
    /**
     * deletes a template
     *
     * @param int $template_id Id of template to delete
     *
     * @access public
     * @return bool
     */
    public function deleteTemplate($template_id)
    {
        $template = $this->createEntity('IdTemplate')->findById($template_id);

        if (!$template || !$template->id) {
            return false;
        }

        return $template->delete();
    }

    /**
     * creates a template
     *
     * @param  $name Name of template to create
     *
     * @access public
     * @return false|IdTemplate
     */
    public function createTemplate($name)
    {
        $template = $this->createEntity('IdTemplate');

        $template->name = $name;

        if (!$template->insert()) {
            return false;
        }

        return $template;
    }

    /**
     * updates all of a templates data
     *
     * @param int   $template_id Id of template to update
     * @param array $data        Update data
     *
     * @access public
     * @return bool
     */
    public function updateTemplate($template_id, array $data)
    {
        $template = $this->createEntity('IdTemplate')->findById($template_id);

        if (!$template || !$template->id) {
            return false;
        }

        $template->name = $data['template']['name'];

        if (!$this->handleTemplateBackground($template, $data)) {
            return false;
        }

        if (!$template->update()) {
            return false;
        }

        return $this->handleTemplateItems($template, $data);
    }

    /**
     * saves a templates background
     *
     * @param IdTemplate $template Template to update
     * @param array      $data     Posted data
     *
     * @access public
     * @return bool
     */
    public function handleTemplateBackground(IdTemplate $template, array $data)
    {
        if (empty($data['template']['background']['dataUrl'])) {
            if (!empty($data['template']['background']['src']) && empty($template->background)) {
                $template->background = $data['template']['background']['src'];
            }

            return true;

        }

        $mime = mb_strtolower(preg_replace('#data:image/([^;]+);base64,.*#i', '$1', $data['template']['background']['dataUrl']), 'UTF-8');

        switch ($mime) {
        case 'jpeg':
            $suffix = '.jpg';
            break;

        case 'png':
            $suffix = '.png';
            break;

        case 'gif':
            $suffix = '.gif';
            break;

        default:
            return false;
        }

        $image = preg_replace('/^[^,]+,/', '', $data['template']['background']['dataUrl']);

        $filename = 'uploads/id_' . $template->id . '_bg' . $suffix;

        if (!file_put_contents(PUBLIC_PATH . $filename, base64_decode($image))) {
            return false;
        }

        $template->background = '/' . $filename;

        return true;
    }

    /**
     * updates a templates items
     *
     * @param IdTemplate $template Template to update
     * @param array      $data     Posted data
     *
     * @access public
     * @return bool
     */
    public function handleTemplateItems(IdTemplate $template, array $data)
    {
        $query = '
DELETE FROM idtemplates_items WHERE template_id = ?
';

        $this->db->exec($query, $template->id);

        if (!empty($data['template']['elements'])) {
            $args    = [];
            $inserts = [];

            foreach ($data['template']['elements'] as $element) {
                $inserts[] = '(?, ?, ?, ?, ?, ?, ?, ?)';
                array_push($args, $template->id, $element['type'], $element['x'], $element['y'], $element['width'], $element['height'], $element['rotation'], !empty($element['dataSource']) ? $element['dataSource'] : '');

            }

            $query = 'INSERT INTO idtemplates_items (template_id, itemtype, x, y, width, height, rotation, datasource) VALUES ';

            $query .= implode(', ', $inserts);

            if (!$this->db->exec($query, $args)) {
                return false;
            }

        }

        return true;
    }

    /**
     * returns all template data in a hierarchy
     *
     * @access public
     * @return array
     */
    public function fetchTemplateData()
    {
        $items = array_reduce($this->db->query('SELECT template_id, itemtype, x, y, width, height, rotation, datasource FROM idtemplates_items'), function ($agg, $next) {
            $agg[$next['template_id']][] = [
                'type'       => $next['itemtype'],
                'x'          => intval($next['x']),
                'y'          => intval($next['y']),
                'width'      => intval($next['width']),
                'height'     => intval($next['height']),
                'dataSource' => !empty($next['datasource']) ? $next['datasource'] : '',
                'rotation'   => intval($next['rotation']),
            ];

            return $agg;
        }, []);

        return array_map(function ($item) use ($items) {
            return [
                'id' => intval($item->id),
                'name' => $item->name,
                'background' => [
                    'dataUrl' => '',
                    'src' => $item->background,
                ],
                'elements' => !empty($items[$item->id]) ? $items[$item->id] : [],
            ];

        }, $this->createEntity('IdTemplate')->findAll());
    }

    /**
     * returns user category template data
     *
     * @access public
     * @return array
     */
    public function fetchCategoryData()
    {
        $connections = array_reduce($this->db->query('SELECT category_id, template_id FROM brugerkategorier_idtemplates'), function ($agg, $next) {
            $agg[$next['category_id']] = $next['template_id'];

            return $agg;
        }, []);

        $category_map = function ($x) use ($connections) {
            return [
                    'id'          => intval($x->id),
                    'name'        => ucfirst($x->navn),
                    'template_id' => isset($connections[$x->id]) ? intval($connections[$x->id]) : 0,
                   ];
        };

        return array_map($category_map, $this->createEntity('BrugerKategorier')->findAll());
    }

    /**
     * updates the user category template relationship
     *
     * @param int $category_id ID of category to update
     * @param int $template_id ID of template to update
     *
     * @access public
     * @return bool
     */
    public function updateCategoryTemplate($category_id, $template_id)
    {
        if (!$template_id) {
            $query = 'DELETE FROM brugerkategorier_idtemplates WHERE category_id = ?';

            try {
                $this->db->exec($query, [$category_id]);
                return true;
            
            } catch (FrameworkException $e) {
                return false;
            }
        }

        $query = 'INSERT INTO brugerkategorier_idtemplates SET category_id = ?, template_id = ? ON DUPLICATE KEY UPDATE template_id = ?';

        try {
            $this->db->exec($query, [$category_id, $template_id, $template_id]);
            return true;
        
        } catch (FrameworkException $e) {
            return false;
        }
    }
}
