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
abstract class Core_Message
{
	// Types of message
	const ERROR   = 'error';
	const NOTICE  = 'notice';
	const INFO    = 'info';
	const SUCCESS = 'success';

	/**
	 * Message instance
	 * @var  Message
	 */
	protected static $_instance = NULL;

	/**
	 * Auto translate message using Kohana::message
	 * @var  string
	 */
	public static $cookie_key = 'flash_message';

	/**
	 * Auto translate message using Kohana::message
	 * @var  boolean
	 */
	public static $translate = FALSE;

	/**
	 * Filename translation in `messages/` folder
	 * @var  string
	 */
	public static $translation_file = 'flash_message';

	/**
	 * Message items
	 * @var  array
	 */
	protected $_data = array();

	/**
	 * Message type
	 * @var  string
	 */
	protected $_type = self::ERROR;

	/**
	 * Get instance Message
	 * 
	 * @return  Message
	 */
	public static function instance()
	{
		if (is_null(self::$_instance))
		{
			$class = get_called_class();
			self::$_instance = new $class;
		}
		return self::$_instance;
	}

	/**
	 * Class constructor protected from external call
	 *
	 * @return  void
	 * @uses    Cookie::get
	 * @uses    Cookie::delete
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
	 * Clone method protected from external call
	 * 
	 * @return  void
	 */
	protected function __clone(){}

	/**
	 * Wakeup method protected from external call
	 * 
	 * @return void
	 */
	protected function __wakeup(){}

	/**
	 * Send new message
	 * 
	 * @param   string   $type       
	 * @param   mixed    $message    Array or string
	 * @param   mixed    $translate  Translate message?
	 * @return  Message
	 * @uses    Kohana::message
	 * @uses    Cookie::set
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
				$message[$key] = Kohana::message(self::$translation_file, $value, $value);
			}
		}
		
		$this->_type = $type;
		$this->_data = $message;
		
		Cookie::set(self::$cookie_key, serialize(array($message, $type)), Date::MINUTE);
		
		return $this;
	}

	/**
	 * Send new error message. Wrapper for Message::_set
	 *     
	 * @param   mixed    $message    Array or string
	 * @param   mixed    $translate  Translate message?
	 * @return  Message
	 */
	protected function _error($message, $translate = NULL)
	{
		return $this->_set(self::ERROR, $message, $translate);
	}

	/**
	 * Send new note message. Wrapper for Message::_set
	 *     
	 * @param   mixed    $message    Array or string
	 * @param   mixed    $translate  Translate message?
	 * @return  Message
	 */
	protected function _notice($message, $translate = NULL)
	{
		return $this->_set(self::NOTICE, $message, $translate);
	}

	/**
	 * Send new info message. Wrapper for Message::_set
	 *     
	 * @param   mixed    $message    Array or string
	 * @param   mixed    $translate  Translate message?
	 * @return  Message
	 */
	protected function _info($message, $translate = NULL)
	{
		return $this->_set(self::INFO, $message, $translate);
	}

	/**
	 * Send new success message. Wrapper for Message::_set
	 *     
	 * @param   mixed    $message    Array or string
	 * @param   mixed    $translate  Translate message?
	 * @return  Message
	 */
	protected function _success($message, $translate = NULL)
	{
		return $this->_set(self::SUCCESS, $message, $translate);
	}

	/**
	 * Gets messages
	 *     
	 * @param   mixed  $key
	 * @return  mixed
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
	 * @param   mixed  $value
	 * @return  mixed
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
	 * @param   string  $name
	 * @param   array   $arguments
	 * @return  mixed
	 * @throw   Kohana_Exception
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
	 * @param   string  $name
	 * @param   array   $arguments
	 * @return  mixed
	 * @throw   Kohana_Exception
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
	 * @param   string  $name
	 * @return  mixed
	 */
	public function __get($name)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : NULL;
	}

	/**
	 * Triggered by calling isset() or empty() on inaccessible properties. 
	 *
	 * @param   string  $name
	 * @return  bool
	 */
	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}

	/**
	 * Allows a class to decide how it will react when it is treated like a string.
	 * 
	 * @return  string
	 */
	public function __toString()
	{
		return implode(PHP_EOL, $this->_data);
	}

} // End Message