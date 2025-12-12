<?php

namespace Module\Shared\Infra\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Error\Renderers\JsonErrorRenderer;
use Slim\Exception\HttpException;
use Slim\Psr7\Factory\ResponseFactory;

final class ExceptionMiddleware implements MiddlewareInterface {
  private JsonErrorRenderer $errorRenderer;
  private LoggerInterface $logger;
  private bool $displayErrorDetails;

  public function __construct(
    JsonErrorRenderer $errorRenderer,
    LoggerInterface $logger,
    bool $displayErrorDetails = false
  ) {
    $this->errorRenderer = $errorRenderer;
    $this->logger = $logger;
    $this->displayErrorDetails = $displayErrorDetails;

		set_error_handler(function (int $severity, string $message, string $file, int $line) {
			if (!(error_reporting() & $severity)) {
				return false;
			}
			throw new \ErrorException($message, 0, $severity, $file, $line);
		});
  }

  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler
  ): ResponseInterface {
    try {
      $response = $handler->handle($request);
		} catch (HttpException $httpException) {
			$this->logger->warning(
				sprintf(
					'%s; Code: %s; File: %s; Line: %s',
					$httpException->getMessage(),
					$httpException->getCode(),
					$httpException->getFile(),
					$httpException->getLine()
				),
				$httpException->getTrace()
			);
			$errorBody = ($this->errorRenderer)($httpException, $this->displayErrorDetails);
			$response = (new ResponseFactory())->createResponse();
			$response->getBody()->write($errorBody);

			return $response
				->withStatus($httpException->getCode());
    } catch (\Throwable $exception) {
      $this->logger->error(
        sprintf(
          '%s; Code: %s; File: %s; Line: %s',
          $exception->getMessage(),
          $exception->getCode(),
          $exception->getFile(),
          $exception->getLine()
        ),
        $exception->getTrace()
      );

      $errorBody = str_replace('Slim ', '', ($this->errorRenderer)($exception, $this->displayErrorDetails));

      $response = (new ResponseFactory())->createResponse();
      $response->getBody()->write($errorBody);

      return $response
        ->withStatus(500)
        ->withHeader('Content-Type', 'application/json');
    }

    return $response;
  }
}
