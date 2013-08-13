<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Message is a class that lets you easily send messages
 * in your application (aka Flash Messages). They are stored in cookie.
 *
 * @package    Kohana/Message
 * @link       http://github.com/WinterSilence/kohana-message
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
	public static $translate = FALSE;

	// @var  string  File name translation in `messages/`
	public static $translation_file = 'flash_message';

	// @var  array
	protected $_data = array();

	// @var  string
	protected $_type = self::ERROR;

	/**
	 * Get instance 
	 *
	 */
	public static function instance()
	{
		if (is_null(self::$_instance))
		{
			$class = get_called_class();
			self::$_instance = new $class();
		}
		return self::$_instance;
	}

	/**
	 * Protected constructor
	 *
	 */
	protected function __construct()
	{
		if ($message = Cookie::get(self::$cookie_key))
		{
			list($this->_data, $this->_type) = unserialize($message);
			Cookie::delete(self::$cookie_key);
		}
	}

	/**
	 * Protected clone method 
	 *
	 */
	protected function __clone(){}

	/**
	 * Protected wakeup method 
	 *
	 */
	protected function __wakeup(){}

	/**
	 * 
	 *
	 */
	protected function _set($type, $message = NULL, $translate = NULL)
	{
		if (is_null($message))
		{
			$message = $type;
			$type = $this->_type;
		}
		
		settype($message, 'array');
		
		if (is_null($translate))
		{
			$translate = self::$translate;
		}
		
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

	/**
	 * 
	 *
	 */
	protected function _error($message, $translate = NULL)
	{
		return $this->_set(self::ERROR, $message, $translate);
	}

	/**
	 * 
	 *
	 */
	protected function _notice($message, $translate = NULL)
	{
		return $this->_set(self::NOTICE, $message, $translate);
	}

	/**
	 * 
	 *
	 */
	protected function _info($message, $translate = NULL)
	{
		return $this->_set(self::INFO, $message, $translate);
	}

	/**
	 * 
	 *
	 */
	protected function _success($message, $translate = NULL)
	{
		return $this->_set(self::SUCCESS, $message, $translate);
	}

	/**
	 * 
	 *
	 */
	protected function _get($key = NULL)
	{
		if (is_null($key))
		{
			return isset($this->_data[0]) ? $this->_data[0] : $this->_data;
		}
		elseif (isset($this->_data[$key]))
		{
			return $this->_data[$key];
		}
	}

	/**
	 * Gets or sets message type
	 *
	 */
	protected function _type($value = NULL)
	{
		if (empty($value))
		{
			return $this->_type;
		}
		$this->_type = (string) $value;
		return $this;
	}

	/**
	 * Triggered when invoking inaccessible methods in an object context.
	 *
	 */
	public function __call($name, $arguments)
	{
		if (in_array($name, array('set', 'error', 'notice', 'info', 'success', 'get', 'type')))
		{
			return call_user_func_array(array($this, '_'.$name), $arguments);
			// return (new ReflectionMethod('Message', '_'.$name))->invokeArgs($this, $arguments);
		}
		throw new Kohana_Exception('Call undefined method :name', array(':name' => $name));
	}

	/**
	 * Triggered when invoking inaccessible methods in a static context. 
	 *
	 */
	public static function __callStatic($name, $arguments)
	{
		if (in_array($name, array('set', 'error', 'notice', 'info', 'success', 'get', 'type')))
		{
			return call_user_func_array(array(self::instance(), '_'.$name), $arguments);
			// return (new ReflectionMethod('Message', '_'.$name))->invokeArgs(self::instance(), $arguments);
		}
		throw new Kohana_Exception('Call undefined static method :name', array(':name' => $name));
	}

	/**
	 * Utilized for reading data from inaccessible properties. 
	 *
	 */
	public function __get($name)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : NULL;
	}

	/**
	 * Triggered by calling isset() or empty() on inaccessible properties. 
	 *
	 */
	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}

	/**
	 * Allows a class to decide how it will react when it is treated like a string.
	 *
	 */
	public function __toString()
	{
		return implode(PHP_EOL, $this->_data);
	}
	
} // End Message