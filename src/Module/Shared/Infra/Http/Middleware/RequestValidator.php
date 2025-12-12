<?php

namespace Module\Shared\Infra\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Helper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteContext;
use Opis\JsonSchema\Validator;

final class RequestValidator {
	private Validator $validator;

	public function __construct(
		private ?string $bodySchema = null,
		private ?string $querySchema = null,
		private ?string $routeSchema = null
		) {
			$this->validator = new Validator();
			$this->validator->setMaxErrors(1000);
			$this->validator->setStopAtFirstError(false);
		}

		public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
			if (!$this->bodySchema && !$this->querySchema && !$this->routeSchema) {
				return $handler->handle($request);
			}

			$routeContext = RouteContext::fromRequest($request);
			$route = $routeContext->getRoute();

			if ($route === null) {
				$response = new ResponseFactory()->createResponse(StatusCodeInterface::STATUS_NOT_FOUND);
				$response->getBody()->write(json_encode(['error' => 'Not found']));
				return $response->withHeader('Content-Type', 'application/json');
			}

			$queryParams = Helper::toJSON($request->getQueryParams());
			$routeParams =  Helper::toJSON($route->getArguments());
			$jsonBody =  Helper::toJSON($request->getParsedBody());

			$bodyResult = $this->bodySchema ? $this->validator->validate($jsonBody, $this->bodySchema) : null;
			$queryResult = $this->querySchema ? $this->validator->validate($queryParams, $this->querySchema) : null;
			$routeResult = $this->routeSchema ? $this->validator->validate($routeParams, $this->routeSchema) : null;

			if (($bodyResult && !$bodyResult?->isValid()) || ($queryResult && !$queryResult?->isValid()) || ($routeResult && !$routeResult?->isValid())) {
				$formattedErrors = array_merge(
					!is_null($bodyResult) ? new ErrorFormatter()->format($bodyResult->error()) : [],
					!is_null($queryResult) ? new ErrorFormatter()->format($queryResult->error()) : [],
					!is_null($routeResult) ? new ErrorFormatter()->format($routeResult->error()) : []
				);

				$response = new ResponseFactory()->createResponse(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
				$response->getBody()->write(json_encode([
					'error' => 'Invalid request',
					'validation_errors' => $formattedErrors,
				]));
				return $response->withHeader('Content-Type', 'application/json');
			}

			$bodyResult?->isValid();


			return $handler->handle($request);
		}
	}
