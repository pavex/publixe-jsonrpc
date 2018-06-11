<?php

	namespace Publixe\JsonRpc\Server\Exception;
	use Publixe;


/**
 */
	class InvalidRequestException extends AbstractException
	{


/**
 * @param string
 * @param int
 */
		public function __construct($message = 'Invalid request', $code = -32600)
		{
			parent::__construct($message, $code);
		}


	}

?>