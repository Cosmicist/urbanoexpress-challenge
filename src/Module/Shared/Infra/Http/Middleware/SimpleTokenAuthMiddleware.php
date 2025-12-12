<?php

namespace Module\Shared\Infra\Http\Middleware;

use Module\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

final class SimpleTokenAuthMiddleware implements MiddlewareInterface {
  /** @var EntityRepository<User> */
  private $userRepo;

  public function __construct(EntityManagerInterface $em) {
    $this->userRepo = $em->getRepository(User::class);
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $authHeader = $request->getHeaderLine('Authorization');

    if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
      return $this->unauthorized();
    }

    $accessToken = substr($authHeader, 7); // Remove 'Bearer ' prefix
    /** @var User $user */
    $user = $this->userRepo->findOneBy([ 'accessToken' => $accessToken ]);
    if (!$user) {
      return $this->unauthorized();
    }

    return $handler->handle(
      $request->withAttribute('user', $user)
    );
  }

  private function unauthorized(): ResponseInterface {
    $response = new ResponseFactory()->createResponse(401);
    $response->getBody()->write(json_encode([
      'status' => 'error',
      'message' => 'Unauthorized: Invalid or missing access token.'
    ]));

    return $response->withHeader('Content-Type', 'application/json');
  }
}
