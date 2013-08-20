### Flash messaging system for Kohana framework 3.3

Uses cookies to store messages.

To use, download the source, extract and rename to message. 
Move that folder into your modules directory and activate in your bootstrap.

### Examples:

Instance message object:
<pre>
$message = Message::instance();
</pre>

Config properties:
<pre>
Message::$cookie_key = 'flash_message';
// Auto translate with using Kohana::message
Message::$translate = FALSE;
// Filename translation in `messages` folder
Message::$translation_file = 'flash_message';
</pre>

Constant message types:
<pre>
Message::ERROR;
Message::NOTICE;
Message::INFO;
Message::SUCCESS;
</pre>

Sending a message:
<pre>
$message->set(Message::SUCCESS, 'User info is updated');
// Short version
$message->success('User info is updated');
// Set second param in try for translate, uses Kohana::message
$message->error('Invalid CAPCHA value', TRUE);
// Can send array of messages
$message->error($validation->errors('user'));
</pre>

Gets a message:
<pre>
Debug::dump($message->get()); 
// return: array('a' => 'Message A', 'b' => 'Message B')
echo $message->get('a'); 
// Short version
echo $message->a;
</pre>

Get message type:
<pre>
echo $message->type();
</pre>

Checks:
<pre>
echo (is_array($message->get()) ? 'yes' : 'no');
echo (isset($message->b) ? 'yes' : 'no');
</pre>

The alternative use - static methods:
<pre>
Message::set(Message::SUCCESS, 'User info is updated');
Message::notice('Cleanup started');
echo Message::get('a');
echo Message::type();
</pre>

### Please help me! Add methods description.
