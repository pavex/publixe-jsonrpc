<?php

	namespace Publixe\JsonRpc;
	use Publixe;
	use Publixe\Environment;
	use Publixe\JsonRpc\Server\Response;
	use Publixe\JsonRpc\Server\Exception\InvalidRequestException;
	use Publixe\JsonRpc\Server\Exception\ParseErrorException;
	use Publixe\JsonRpc\Server\Exception\MethodNotFoundException;
	use \Exception as Exception;


/**
 * JSON-RPC server
 *
 * @author	Pavex <pavex@ines.cz>
 */

	class Server
	{


/** @type Object */
		private $model;





/**
 * @param Object
 */
		public function bindModel($model)
		{
			$this -> model = $model;
		}





/**
 * @param string
 * @param Object|Array
 * @return Object
 */
		private function callMethod($method, $params)
		{
			$callback = array($this -> model, $method);	
			if (!is_callable($callback)) {
				throw new MethodNotFoundException;
			}
			return call_user_func_array($callback, $params);
		}





/**
 * @param Object
 * @return Object
 */
		private function processPayloadUnit($unit)
		{
			if (!is_object($unit)) {
				throw new InvalidRequestException;
			}	
			if (!isset($unit -> method)) {
				throw new InvalidRequestException;
			}
			$id = isset($unit -> id) ? $unit -> id : NULL;
//			
			$method = $unit -> method;
			$params = isset($unit -> params) ? (is_object($unit -> params) ? [$unit -> params] : $unit -> params) : [];
			$result = $this -> callMethod($method, $params);
//
			$response = new Response();
			$response -> id = $id;
			$response -> result = $result;

			if ($response -> id === NULL) {
				unset($response -> id);
			}
			if ($response -> error === NULL) {
				unset($response -> error);
			}
			return $response;
		}





/**
 * @param Object|Array
 * @return Object|Array
 */
		public function processPayload($payload)
		{
			try {
				if (!$payload) {
					throw new ParseErrorException;
				}
				if (is_array($payload)) {
					$units = [];
					foreach ($payload as $unit) {
						$units[] = $this -> processPayloadUnit($unit);
					}
					return $units;
				}
				elseif (is_object($payload)) {
					return $this -> processPayloadUnit($payload);
				}
				throw new InvalidRequestException;
			}
//			catch (Publixe\Auth\Exception\IException $e) {
//				throw $e;
//			}
			catch (Exception $e) {
				$error = new \StdClass();
				$error -> message = $e -> getMessage();
//				$error -> exception = get_class($e);
				$error -> code = $e -> getCode();
//
// Append exception trace if development mode is on
//				if (Environment::isDev()) {
				if (defined('DEV')) {
					$error -> trace = $e -> getTrace();
				}
				$response = new Response();
				$response -> error = $error;		
				return $response;
			}
		}


	}


?>