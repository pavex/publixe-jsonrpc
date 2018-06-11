<?php

	namespace Publixe\JsonRpc\Server\Exception;
	use Publixe;


/**
 */
	class MethodNotFoundException extends AbstractException
	{


/**
 * @param string
 * @param int
 */
		public function __construct($message = 'Method not found', $code = -32601)
		{
			parent::__construct($message, $code);
		}


	}

?>