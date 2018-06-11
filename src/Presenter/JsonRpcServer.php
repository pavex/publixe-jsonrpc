<?php

	namespace Publixe\Presenter;
	use Publixe;
	use \Exception as Exception;


/**
 * JSON-RPC server for HTTP/Website
 *
 * @author	Pavex <pavex@ines.cz>
 */
	abstract class JsonRpcServer extends Publixe\Presenter\AbstractServer
	{


/** @var string */
		private $contents;





/**
 * @param string
 */
		protected function setHeader($name, $value)
		{
			$this -> getControl()
				-> getHttpResponse()
				-> setHeader($name, $value);
		}





/**
 * Default headers and preflight body for CORS
 */
		protected function headers($preflight)
		{
			$this -> setAccessControl('Origin', '*');
		}





/**
 * @param string
 * @param string
 */
		protected function setAccessControl($what, $value)
		{
			$this -> setHeader(sprintf("Access-Control-Allow-%s", ucfirst($what)), $value);
		}





/**
 */
		public function execute()
		{
			parent::execute();
//
			try {
				$httpRequest = $this -> getControl() -> getHttpRequest();
// Preflight
				$is_options = $httpRequest -> isMethod('OPTIONS');
				$this -> headers($is_options);
				if ($is_options) {
					return;
				}
//
				if ($httpRequest -> getContentType() != 'application/json') {
					throw new Publixe\Http\Exception\NotAcceptableException();
				}
				if (!$httpRequest -> isMethod('POST')) {
					throw new Publixe\Http\Exception\MethodNotAllowedException();
				}

// Init stand-alone server
				$server = new Publixe\JsonRpc\Server();
				$server -> bindModel($this);
//
				$raw_post_data = $httpRequest -> getRawPostData();
				$payload = @json_decode($raw_post_data);
				$data = $server -> processPayload($payload);
				$this -> contents = json_encode($data);
			}
//			catch (Publixe\Auth\Exception\ForbiddenException $e) {
//				throw new Publixe\Http\Exception\ForbiddenException();
//			}
//			catch (Publixe\Auth\Exception\UnauthorizedException $e) {
//				throw new Publixe\Http\Exception\UnauthorizedException();
//			}
			catch (Publixe\Http\Exception\IException $e) {
				throw $e;
			}
			catch (Exception $e) {
				throw $e;
			}
		}





/**
 * Render presenter output
 * @return string
 */
		public function render()
		{
			$this -> setHeader('X-Server-Type', 'JsonRPC 2.0');
			$httpResponse = $this -> getControl() -> getHttpResponse()
				-> setContentType('application/json');
//
			return $this -> contents;
		}


	}

?>