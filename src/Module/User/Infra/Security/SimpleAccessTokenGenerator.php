<?php

namespace Module\User\Infra\Security;

use Module\User\Domain\Service\AccessTokenGenerator;
use Module\User\Domain\User;

class SimpleAccessTokenGenerator implements AccessTokenGenerator {
  public function generate(User $user): string {
    return bin2hex(openssl_random_pseudo_bytes(16));
  }
}
