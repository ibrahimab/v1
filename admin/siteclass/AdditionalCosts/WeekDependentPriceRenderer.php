<?php

namespace Chalet\AdditionalCosts;

/**
 * Render table with all week dependent prices
 *
 * used in additional costs below price table
 *
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 * @package Chalet
 **/
class WeekDependentPriceRenderer
{

    /**
     * database object
     *
     * @var object \DB_sql
     **/
    private $db;

    /**
     * type id
     *
     * @var integer $typeID
     **/
    private $typeID;

    /**
     * bijkomendekosten id
     *
     * @var integer $bkid
     **/
    private $bkid;

    /**
     * season id
     *
     * @var integer $bkid
     **/
    private $seasonID;

    /**
     * changed arrival dates
     *
     * @var array $bkid
     **/
    private $vertrekdag;

    /**
     * accommodation data
     *
     * @var array $accinfo
     **/
    private $accinfo;

    /**
     * database-data
     *
     * @var array $data
     **/
    private $data;

    /**
     * @param object \DB_sql
     * @param integer $bkid
     * @param integer $seasonID
     */
    function __construct(\DB_sql $db, $accinfo, $typeID, $bkid, $seasonID = 0)
    {
        $this->db       = $db;
        $this->accinfo  = $accinfo;
        $this->typeID   = $typeID;
        $this->bkid     = $bkid;
        $this->seasonID = $seasonID;

        // Controle op vertrekdagaanpassing?
        $vertrekdag_object = new \vertrekdagaanpassing($this->typeID);
        $this->vertrekdag = $vertrekdag_object->get_dates()[$this->typeID];

        // accommodation data
        $this->accinfo = \accinfo($this->typeID);

        // get price-data from database
        $this->data = $this->queryDatabase($this->bkid);

    }

    /**
     * render table with prices
     *
     * @return string
     **/
    public function render()
    {
        // @todo: convert to DI (with Pimple?)
        global $vars;

        $html = '';

        if(is_array($this->data['kosten'])) {
            if(min($this->data['kosten'])==max($this->data['kosten'])) {
                $html .= "<div style=\"font-weight: bold;font-style:italic;margin-top:1em;\">";
                $html .= wt_he($this->data['naam']);
                $html .= "&nbsp; : &nbsp;&euro;&nbsp;".number_format(abs(min($this->data['kosten'])),2,',','.');
                if(min($this->data['kosten'])<0) {
                    $html .= " ".html("korting", "popup_bijkomendekosten");
                }

                $html .= " ";
                if($this->data['perboekingpersoon']==1) {
                    $html .= html("perboeking", "popup_bijkomendekosten");
                } else {
                    $html .= html("perpersoonafk", "popup_bijkomendekosten");
                }

                $html .= "</div>";
            } else {
                $html .= "<table class=\"toeslagtabel\">";
                $html .= "<tr><th>".html("aankomstdatum", "popup_bijkomendekosten")."</th><th>";
                if($this->data['min_personen']) {
                    $html .= html("toeslag", "popup_bijkomendekosten");
                } elseif($this->data['perboekingpersoon']==1) {
                    $html .= html("perboeking", "popup_bijkomendekosten");
                } else {
                    $html .= html("perpersoon", "popup_bijkomendekosten");
                }
                $html .= "</th></tr>";
                foreach ($this->data['kosten'] as $key => $value) {
                    $html .= "<tr>";
                    $html .= "<td>".DATUM("D MAAND JJJJ", $key, $vars["taal"])."</td>";
                    $html .= "<td align=\"right\">&euro;&nbsp;".number_format($value, 2, ",", ".");
                    if($value<0) {
                        $html .= " ".html("korting", "popup_bijkomendekosten");
                    }
                    $html .= "</td>";
                    $html .= "</tr>";
                }
                $html .= "</table>";
            }
        }

        return $html;
    }

    /**
     * query database
     *
     * @return array
     **/
    private function queryDatabase() {

        // @todo: convert to DI (with Pimple?)
        global $vars;

        $data = [];

        // get info from table bijkomendekosten
        $this->db->query("SELECT b.naam".$vars["ttv"]." AS naam, b.omschrijving".$vars["ttv"]." AS toelichting, b.min_personen, b.perboekingpersoon FROM bijkomendekosten b WHERE b.bijkomendekosten_id='".intval($this->bkid)."';");

        if($this->db->next_record()) {

            $data['naam'] = $this->db->f("naam");

            if($this->db->f("toelichting")) {
                $data['toelichting'] = nl2br(wt_htmlent($this->db->f("toelichting"),true,true))."<br>";
            }
            if($this->db->f("min_personen")) {
                $data['min_personen'] = $this->db->f("min_personen");
            }
            $data['perboekingpersoon'] = $this->db->f("perboekingpersoon");
        }

        $this->db->query("SELECT seizoen_id, week, verkoop FROM bijkomendekosten_tarief WHERE bijkomendekosten_id='".intval($this->bkid)."' AND verkoop>0 AND week>'".time()."'" .($this->seasonID > 0 ? " AND seizoen_id='" . intval($this->seasonID) . "'" : ""). " ORDER BY week;");
        if($this->db->num_rows()) {
            while($this->db->next_record()) {

                $week = $this->db->f("week");
                if($this->vertrekdag[$this->db->f("seizoen_id")][date("dm",$week)] or $this->accinfo["aankomst_plusmin"]) {
                    $aangepaste_unixtime=mktime(0,0,0,date("m",$week),date("d",$week)+$this->vertrekdag[$this->db->f("seizoen_id")][date("dm",$week)]+$this->accinfo["aankomst_plusmin"],date("Y",$week));
                } else {
                    $aangepaste_unixtime=$week;
                }

                $data['kosten'][$aangepaste_unixtime] = $this->db->f("verkoop");
            }
        }

        return $data;

    }

}
