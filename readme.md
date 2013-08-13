## Message

### Flash messaging system for Kohana framework 3.3

To use, download the source, extract and rename to message. 
Move that folder into your modules directory and activate in your bootstrap.

## Please help me! Update readme and add methods description

## Examples

Message::notice(array('a' => 'test', 'b' => 'test B!'));

$message = Message::instance();

$message->error('big error', TRUE);
or 
Message::error('big error');

echo $message->get('a'); 
or
echo $message->a;

echo $message->type(); 
or
echo Message::type();
 
if (is_string($message->get())) echo "yes"; else echo "no";
if (isset($message->b34)) echo "yes"; else echo "no";
if (isset($message->a)) echo "yes"; else echo "no";