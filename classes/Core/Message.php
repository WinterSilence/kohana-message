<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Message is a class that lets you easily send messages
 * in your application (aka Flash Messages). They are stored in cookie.
 *
 * @package    Kohana/Message
 * @see        https://github.com/WinterSilence/kohana-message
 * @author     WinterSilence
 * @copyright  2013 © WinterSilence
 */
abstract class Core_Message {

	// Types of message
	const ERROR   = 'error';
	const NOTICE  = 'notice';
	const INFO    = 'info';
	const SUCCESS = 'success';

	// @var  Message
	protected static $_instance;

	// @var  string
	public static $cookie_key = 'flash_message';

	// @var  bool Auto translate message using Kohana::message()
	public static $translate = FALSE:

	// @var  string  File name translation in `messages/`
	public static $translation_file = 'flash_message':

	// TODO: !!!
	protected $_data = array();

	// @var  string
	protected $type = self::ERROR;

	// Instance message class
	public static function instance()
	{
		if (is_null(self::$_instance))
		{
			$class = get_called_class();
			self::$_instance = new $class();
		}
		return self::$_instance;
	}

	// Protected constructor
	protected function __construct()
	{
		if ($message = Cookie::get(self::$cookie_key))
		{
			list($this->_data, $this->_type) = unserialize($message);
			Cookie::delete(self::$cookie_key);
		}
	}

	// Protected clone method 
	protected function __clone(){}

	// Protected wakeup method 
	protected function __wakeup(){}

	
	public function set($type, $message = NULL, $translate = self::$translate)
	{
		if (is_null($message))
		{
			$message = $type;
			$type = $this->_type;
		}
		
		settype($message, 'array');
		
		if ($translate)
		{
			foreach ($message as $key => $value)
			{
				$message[$key] = Kohana::message(self::$translation_file, $value);
			}
		}
		
		$this->_type = $type;
		$this->_data = $message;
		
		Cookie::set(self::$cookie_key, serialize(array($message, $type)), Date::MINUTE);
		
		return $this;
	}
	
	public function error($message, $translate = NULL)
	{
		return $this->set(self::ERROR, $message, $translate);
	}

	
	public function notice($message, $translate = NULL)
	{
		return $this->set(self::NOTICE, $message, $translate);
	}

	
	public function info($message, $translate = NULL)
	{
		return $this->set(self::INFO, $message, $translate);
	}

	
	public function success($message, $translate = NULL)
	{
		return $this->set(self::SUCCESS, $message, $translate);
	}

	
	public function get($key = NULL)
	{
		if (empty($key))
		{
			return isset($this->_data[0]) ? $this->_data[0] : $this->_data;
		}
		return $this->offsetGet($key);
	}

	
	public function type($value = NULL)
	{
		if (empty($value))
		{
			return $this->_type;
		}
		$this->_type = (string) $value;
		return $this;
	}

	
	public static function __callStatic($name, $arguments)
	{
		if (in_array($name, array('set', 'error', 'notice', 'info', 'success', 'get', 'type')))
		{
			return call_user_func_array(array(self::instance(), $name), $arguments);
			// return (new ReflectionMethod('Message', $name))->invokeArgs(self::instance(), $arguments);
		}
		throw new Kohana_Exception('Call undefined static method :name', array(':name' => $name), 1);
	}

	
	public function __toString()
	{
		return implode($this->_data);
	}

} // End Message