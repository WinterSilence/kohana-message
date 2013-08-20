### Flash messaging system for Kohana framework 3.3

Uses cookies to store messages.

To use, download the source, extract and rename to message. 
Move that folder into your modules directory and activate in your bootstrap.

### Examples:

Instance message object:
	$message = Message::instance();

Config properties:
	Message::$cookie_key = 'flash_message';
	// Auto translate with using Kohana::message
	Message::$translate = FALSE;
	// Filename translation in `messages` folder
	Message::$translation_file = 'flash_message';

Constant message types:
	Message::ERROR;
	Message::NOTICE;
	Message::INFO;
	Message::SUCCESS;

Sending a message:
	$message->set(Message::SUCCESS, 'User info is updated');
	// Short version
	$message->success('User info is updated');
	// Set second param in try for translate, uses Kohana::message
	$message->error('Invalid CAPCHA value', TRUE);
	// Can send array of messages
	$message->error($validation->errors('user'));

Gets a message:
	Debug::dump($message->get()); 
	// return: array('a' => 'Message A', 'b' => 'Message B')
	echo $message->get('a'); 
	// Short version
	echo $message->a;

Get message type:
	echo $message->type();

Checks:
	echo (is_array($message->get()) ? 'yes' : 'no');
	echo (isset($message->b) ? 'yes' : 'no');

The alternative use - static methods:
	Message::set(Message::SUCCESS, 'User info is updated');
	Message::notice('Cleanup started');
	echo Message::get('a');
	echo Message::type();

## Please help me! Add methods description.
