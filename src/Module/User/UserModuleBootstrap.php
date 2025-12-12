<?php

namespace Module\User;

use Module\User\Domain\Service\AccessTokenGenerator;
use Module\User\Infra\Security\SimpleAccessTokenGenerator;
use DI\Container;
use Module\Shared\Infra\ModuleBootstrap;
use Slim\App;

class UserModuleBootstrap implements ModuleBootstrap {
  /**
   * @param App<Container> $app
   */
    public function bootstrap(App $app): void {
      $container = $app->getContainer();
      $container?->set(AccessTokenGenerator::class, fn () => new SimpleAccessTokenGenerator());
    }
}
