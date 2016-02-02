<?php

namespace Chalet\CMS\Accommodation;

/**
 * CRUD system for extra distances for accommotions,
 * integrated in the currect cms-class
 *
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 * @package Chalet
 **/
class ExtraDistances
{

    const HEADER                  = 'Extra afstand';
    const FIELDNAME_DISTANCE_NAME = 'Afstand tot (benaming)';
    const FIELDNAME_DISTANCE      = 'Afstand tot';
    const FIELDNAME_IN_METERS     = '(in meters)';
    const FIELDNAME_ADDITION      = 'Toevoeging';

    /**
     * @var \DB_sql $db
     **/
    private $db;

    /**
     * @var \Configuration $config
     **/
    private $config;

    /**
     * @var integer $accID
     **/
    private $accID;

    /**
     * @var integer $userID
     **/
    private $userID;

    /**
     * @var array $post_data
     **/
    private $post_data = [];

    /**
     * @var string $other_language
     **/
    private $other_language;

    /**
     * @var string $ttv
     **/
    private $ttv;

    /**
     * @var array $distances
     **/
    private $distances;

    /**
     * @param \DB_sql $db
     * @param \Configuration $config
     * @param integer $accID
     * @param array $post_data
     */
    function __construct($db, $config, $accID, $post_data)
    {
        $this->db             = $db;
        $this->config         = $config;
        $this->accID          = $accID;
        $this->other_language = $config->cmstaal;
        $this->post_data      = $post_data;

        if ($this->other_language) {
            $this->ttv = '_' . $this->other_language;
        }
    }

    /**
     * get distances and render html form-elements
     *
     * @return string
     **/
    public function formElementsController()
    {
        $html = '';
        $counter = 0;

        if (empty($this->post_data)) {
            $distances = $this->getDistancesFromDatabase();
        } else {
            $distances = $this->getDistancesFromPostData();
        }

        foreach ($distances as $counter => $value) {

            $html .= $this->renderFormElements($counter, $value);

        }

        $html .= $this->renderFormElements($counter+1, []);

        return $html;
    }

    /**
     * render html form-elements for 1 distance
     *
     * @param integer $counter
     * @param array $distance database-data
     * @return string
     **/
    public function renderFormElements($counter, $distance)
    {
        $html = '';

        // header
        $html .= '<tr><td class="wtform_cell_colspan extra_distance" colspan="2" data-counter="' . $counter . '"><hr /><b>' . wt_he(self::HEADER) . ' ' . $counter . '</b></td></tr>';

        if ($this->other_language) {

            // Field name (multi-language)
            $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_DISTANCE_NAME) . ' NL</td><td class="wtform_cell_right">' . wt_he($distance['name']) . '</td></tr>';
            $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_DISTANCE_NAME) . ' ' . strtoupper($this->other_language). '</td><td class="wtform_cell_right"><input name="external_input[extradistance][' . $counter . '][name_other_language]" value="' . wt_he($distance['name_other_language']) . '" maxlength="255" class="wtform_input" type="text"></td></tr>';

        } else {

            // Field name (only Dutch)
            $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_DISTANCE_NAME) . '</td><td class="wtform_cell_right"><input name="external_input[extradistance][' . $counter . '][name]" value="' . wt_he($distance['name']) . '" maxlength="255" class="wtform_input extra_distance_name" type="text"></td></tr>';

        }

        // Field distance in meters
        $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_DISTANCE) . ' ';
        $html .= '<span class="distance_name">' . wt_he($distance['name']) . '</span> ' . wt_he(self::FIELDNAME_IN_METERS) . '</td><td class="wtform_cell_right"><input name="external_input[extradistance][' . $counter . '][distance]" value="' . wt_he($distance['distance']) . '" maxlength="10" class="wtform_input" type="text" pattern="\d*"></td></tr>';

        if ($this->other_language) {

            // Field addition (multi-language)
            $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_ADDITION) . ' NL</td><td class="wtform_cell_right">' . wt_he($distance['addition']) . '</td></tr>';
            $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_ADDITION) . ' ' . strtoupper($this->other_language) . '</td><td class="wtform_cell_right"><input name="external_input[extradistance][' . $counter . '][addition_other_language]" value="' . wt_he($distance['addition_other_language']) . '" maxlength="255" class="wtform_input" type="text"></td></tr>';

        } else {

            // Field addition (in Dutch)
            $html .= '<tr><td class="wtform_cell_left">' . wt_he(self::FIELDNAME_ADDITION) . ' </td><td class="wtform_cell_right"><input name="external_input[extradistance][' . $counter . '][addition]" value="' . wt_he($distance['addition']) . '" maxlength="255" class="wtform_input" type="text"></td></tr>';

        }

        return $html;
    }

    /**
     * set userid of logged in CMS user
     *
     * @param integer $userID
     * @return void
     **/
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * save new/changed distances to the database
     *
     * @return void
     **/
    public function saveFormData()
    {
        // get current data (necessary to be able to check for changes)
        $this->getDistancesFromDatabase();

        foreach ($this->post_data as $key => $value) {

            if ($value['name'] || $value['name_other_language']) {

                if ($this->other_language) {
                    $sql = "name" . $this->ttv . "='" . wt_as($value['name_other_language']) . "'";
                } else {
                    $sql = "name='" . wt_as($value['name']) . "'";
                }

                $sql .= ", distance='" . wt_as($value['distance']) . "'";

                if ($this->other_language) {

                    if ($value['addition_other_language']) {
                        $sql .= ", addition" . $this->ttv . "='" . wt_as($value['addition_other_language']) . "'";
                    } else {
                        $sql .= ", addition" . $this->ttv . "=NULL";
                    }

                } else {

                    if ($value['addition']) {
                        $sql .= ", addition='" . wt_as($value['addition']) . "'";
                    } else {
                        $sql .= ", addition=NULL";
                    }

                }

                $sql .= ", editdatetime=NOW()";

                $this->db->query("  INSERT INTO accommodatie_extra_distance
                                    SET accommodatie_id='" . intval($this->accID) . "',
                                    counter='" . intval($key) . "', adddatetime=NOW(), " . $sql . "
                                    ON DUPLICATE KEY UPDATE " . $sql . ";");

            } elseif(!$this->other_language) {

                $this->db->query("  DELETE FROM accommodatie_extra_distance
                                    WHERE accommodatie_id='" . intval($this->accID) . "'
                                    AND counter='" . intval($key) . "';");
            }
        }


        // make counter sequential
        $this->db->query("  SELECT @i:=0;");
        $this->db->query("  UPDATE accommodatie_extra_distance
                            SET counter = @i:=@i+1
                            WHERE accommodatie_id='" . intval($this->accID) . "' ORDER BY counter;");

        // check for changes and log in cmslog
        $this->checkChanges();
    }

    /**
     * get distances from previously posted data
     *
     * @return array
     **/
    function getDistancesFromPostData()
    {
        return $this->post_data;
    }

    /**
     * get distances from the database
     *
     * @return array database-data
     **/
    private function getDistancesFromDatabase()
    {
        if (null === $this->distances) {

            $this->distances = [];

            $this->db->query("SELECT counter, name, name" . $this->ttv . " AS name_other_language, distance, addition, addition" . $this->ttv . " AS addition_other_language FROM accommodatie_extra_distance WHERE accommodatie_id='" . intval($this->accID) . "';");
            while ($this->db->next_record()) {
                $this->distances[ $this->db->f('counter') ]['name']                    = $this->db->f('name');
                $this->distances[ $this->db->f('counter') ]['name_other_language']     = $this->db->f('name_other_language');
                $this->distances[ $this->db->f('counter') ]['distance']                = $this->db->f('distance');
                $this->distances[ $this->db->f('counter') ]['addition']                = $this->db->f('addition');
                $this->distances[ $this->db->f('counter') ]['addition_other_language'] = $this->db->f('addition_other_language');
            }
        }

        return $this->distances;
    }

    /**
     * check for changes and save in CMS-log
     *
     * @return void
     **/
    private function checkChanges()
    {

        $max = max( count($this->getDistancesFromDatabase()), count($this->getDistancesFromPostData()));

        if ($this->other_language) {

            $checkFields = [
                'name_other_language' => 'Naam afstand',
                'distance' => 'Afstand tot (in meters)',
                'addition_other_language' => 'Toevoeging',
            ];

        } else {

            $checkFields = [
                'name' => 'Naam afstand',
                'distance' => 'Afstand tot (in meters)',
                'addition' => 'Toevoeging',
            ];
        }

        for ($i = 1; $i <= $max; $i++) {

            foreach ($checkFields as $key => $value) {

                if ($this->getDistancesFromDatabase()[$i][$key] != $this->getDistancesFromPostData()[$i][$key]) {

                    $old_value = $this->getDistancesFromDatabase()[$i][$key];
                    $new_value = $this->getDistancesFromPostData()[$i][$key];

                    if ($this->other_language) {
                        $key = str_replace('other_language', $this->other_language, $key);
                    }

                    $this->db->query("
                               INSERT INTO cmslog SET user_id='".addslashes($this->userID)."',
                               cms_id='1', cms_name='accommodatie', record_id='".$this->accID."',
                               record_name='accommodatie',
                               table_name='accommodatie_extra_distance', field='".addslashes($key)."',
                               field_name='".addslashes('Extra afstand ' . $i . ' - ' . $value)."',
                               field_type='text',
                               previous='".addslashes($old_value)."',
                               now='".addslashes($new_value)."',
                               url='".addslashes($_SERVER["REQUEST_URI"])."',
                               savedate=NOW();");

                }
            }
        }
    }
}
