<?php

	namespace Publixe\JsonRpc\Server\Exception;
	use Publixe;


/**
 */
	class ParseErrorException extends AbstractException
	{


/**
 * @param string
 * @param int
 */
		public function __construct($message = 'Parse error', $code = -32700)
		{
			parent::__construct($message, $code);
		}


	}

?>