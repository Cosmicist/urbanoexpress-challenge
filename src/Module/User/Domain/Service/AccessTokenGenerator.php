<?php

namespace Module\User\Domain\Service;

use Module\User\Domain\User;

interface AccessTokenGenerator {
  public function generate(User $user): string;
}
