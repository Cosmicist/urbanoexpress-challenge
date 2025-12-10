<?php

namespace Module\Shared\Infra\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Error\Renderers\JsonErrorRenderer;
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
  }

  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler
  ): ResponseInterface {
    try {
      $response = $handler->handle($request);
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

      $errorBody = substr(($this->errorRenderer)($exception, $this->displayErrorDetails), 6); // Remove 'Slim ' prefix

      $response = (new ResponseFactory())->createResponse();
      $response->getBody()->write($errorBody);

      return $response
        ->withStatus(500)
        ->withHeader('Content-Type', 'application/json');
    }

    return $response;
  }
}
