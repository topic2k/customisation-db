<?php
/**
*
* @package Titania
* @version $Id$
* @copyright (c) 2008 phpBB Customisation Database Team
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_TITANIA'))
{
	exit;
}

if (!class_exists('titania_database_object'))
{
	require TITANIA_ROOT . 'includes/core/object_database.' . PHP_EXT;
}

/**
* Class to titania revision.
* @package Titania
*/
class titania_revision extends titania_database_object
{

	/**
	 * Attachment Object
	 *
	 * @var object
	 */
	public $attachment = '';

	/**
	 * SQL Table
	 *
	 * @var string
	 */
	protected $sql_table		= TITANIA_REVISIONS_TABLE;

	/**
	 * SQL identifier field
	 *
	 * @var string
	 */
	protected $sql_id_field		= 'revision_id';

	public function __construct($contrib_id = false, $revision_id = false)
	{
		// Configure object properties
		$this->object_config = array_merge($this->object_config, array(
			'revision_id'			=> array('default' => 0),
			'contrib_id' 			=> array('default' => 0),
			'revision_validated'	=> array('default' => 0),
			'attachment_id' 		=> array('default' => 0),
			'revision_name' 		=> array('default' => '', 'max' => 255),
			'revision_time'			=> array('default' => (int) titania::$time),
			'validation_date'		=> array('default' => 0),
			'revision_version'		=> array('default' => ''),
		));

		$this->contrib_id = $contrib_id;
		$this->revision_id = $revision_id;
	}

	/**
	 *
	 */
	public function display()
	{
		phpbb::$template->assign_block_vars('revisions', array(
			'REVISION_ID'		=> $this->revision_id,
			'CREATED'			=> phpbb::$user->format_date($this->revision_time),
			'NAME'				=> censor_text($this->revision_name),
			'VERSION'			=> $this->revision_version,
			'VALIDATED_DATE'	=> ($this->validation_date) ? phpbb::$user->format_date($this->validation_date) : phpbb::$user->lang['NOT_VALIDATED'],

			'S_VALIDATED'		=> $this->revision_validated,
		));
	}

	/**
	 * Put the contrib item in the queue
	 */
	public function submit()
	{
		// Update the contrib_last_update if required here
		if (!titania::$config->require_validation)
		{
			$sql = 'UPDATE ' . TITANIA_CONTRIBS_TABLE . '
				SET contrib_last_update = ' . titania::$time . '
				WHERE contrib_id = ' . $this->contrib_id;
			phpbb::$db->sql_query($sql);
		}

		// Put in the queue
		if (titania::$config->use_queue)
		{

		}

		parent::submit();
	}

	/**
	 * Download URL
	 */
	public function get_url()
	{
		return titania_url::build_url('download', array('id' => $this->attachment_id));
	}
}
