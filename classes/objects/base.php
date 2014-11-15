<?php
namespace report_seriescompletion\objects;

class base extends \report_seriescompletion\object {
	protected static $_registry = [];
	protected $_id;
	public $meta;

	public function init()
	{
		parent::init();
		$this->postLoad();
	}

	public function postLoad()
	{

	}

	public static function loadObject($meta)
	{
		$id = static::generateId($meta);
		if (!($object = static::getById($id))) {
			$object = new static(['id' => $id, 'meta' => $meta]);
			static::$_registry[$id] = $object;
		}

		return $object;
	}

	public static function getById($id) {
		if (isset(static::$_registry[$id])) {
			return static::$_registry[$id];
		}
		return false;
	}

	public function setId($id)
	{
		$this->_id = $id;
	}

	public function getId()
	{
		if (!isset($this->_id)) {
			$this->_id = static::generateId($this->meta);
		}
		return $this->_id;
	}

	public static function generateId($meta)
	{
		if (!isset($meta['id'])) {
			throw \Exception("ID for ". get_called_class() ." not found");
		}
		return $meta['id'];
	}
}
?>