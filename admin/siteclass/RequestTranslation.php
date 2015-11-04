<?php
namespace Chalet;

use       DB_sql;
use       tablelist;

/**
 * @author  Eric Minang <eric@chalet.nl>
 * @package Chalet
 */
class RequestTranslation
{
	/**
	 * @var \Configuration
	 */
	private $config;

	/**
	 * @var integer
	 */
	private $season;

	public function __construct($season)
	{
		$this->season = (int)$season;
	}

	/**
	 *
	 */
	public function setConfiguration($config)
	{
		$this->config = $config;
	}


	public function accommodations()
	{
		$db = $this->getDatabase();

		$db->query("SELECT a.accommodatie_id , a.wzt, a.naam, p.plaats_id, p.naam AS plaats,
		           		   l.leverancier_id, l.naam AS leverancier
					FROM    accommodatie a, plaats p, leverancier l
					WHERE 	a.leverancier_id = l.leverancier_id
					AND     a.plaats_id = p.plaats_id
					AND     a.wzt = " . $this->season . "
					AND     a.request_translation" . $this->config->ttv . " = 1");

		if ($db->num_rows()) {

			$tl = new tablelist;
			$tl->settings['systemid']   = 2;
			$tl->settings['arrowcolor'] = 'white';
			$tl->settings['path']		= $this->config->path;
			$tl->settings['th_id']		= 'col_';
			$tl->settings['td_class']	= 'col_';

			$tl->sort 					= ['naam'];
			$tl->sort_desc				= true;

			// which fields to show in table
			$tl->field_show('cms_accommodaties.php?show=1&bc=630wzt=2&archief=0&1k0=[ID]', 'Details bekijken');
			$tl->field_text('leverancier', 'Leverancier');
			$tl->field_text('plaats_id', 'Plaats');
			$tl->field_text('naam', 'Interne Naam');

			while ($db->next_record()) {

				// add_record($id,$key,$value,$sortvalue="",$datetime=false,$options="")
				$tl->add_record('naam', $db->f('accommodatie_id'), $db->f('naam'));
				$tl->add_record('plaats_id', $db->f('accommodatie_id'), $db->f('plaats'));
				$tl->add_record('leverancier', $db->f('accommodatie_id'), $db->f('leverancier'));
			}

			return $tl;
		}

		return false;

	}

	public function types()
	{
		$db = $this->getDatabase();
		$db->query("SELECT t.type_id , a.naam AS accommodatie, a.wzt, t.naam, p.plaats_id, p.naam AS plaats,
		           		   l.leverancier_id, l.naam AS leverancier
					FROM type t, accommodatie a, plaats p, leverancier l
					WHERE t.accommodatie_id = a.accommodatie_id
					AND   a.wzt = " . $this->season. "
					AND t.leverancier_id= l.leverancier_id
					AND a.plaats_id = p.plaats_id
					AND t.request_translation" . $this->config->ttv . " = 1");

		if ($db->num_rows()) {

			$tl = new tablelist;
			$tl->settings['systemid']   = 2;
			$tl->settings['arrowcolor'] = 'white';
			$tl->settings['path']		= $this->config->path;
			$tl->settings['th_id']		= 'col_';
			$tl->settings['td_class']	= 'col_';

			$tl->sort 					= ['naam'];
			$tl->sort_desc				= true;

			// which fields to show in table
			$tl->field_show('cms_types.php?show=2&bc=630&wzt=1&archief=0&1k0=&2k0=[ID]', 'Details bekijken');
			$tl->field_text('leverancier', 'Leverancier');
			$tl->field_text('plaats_id', 'Plaats');
			$tl->field_text('naam', 'Interne Naam');

			while ($db->next_record()) {

				// add_record($id,$key,$value,$sortvalue="",$datetime=false,$options="")
				$tl->add_record('naam', $db->f('type_id'), $db->f('accommodatie') . ' ' . $db->f('naam'));
				$tl->add_record('plaats_id', $db->f('type_id'), $db->f('plaats'));
				$tl->add_record('leverancier', $db->f('type_id'), $db->f('leverancier'));
			}

			return $tl;
		}

		return false;
	}

	public function getDatabase()
	{
		return new DB_sql;
	}
}