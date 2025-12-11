<?php

namespace Module\Shipping\Domain\Exception;

final class InvalidTransitionException extends \DomainException {
  public function __construct(string $fromStatus, string $toStatus) {
    parent::__construct("Invalid transition from '$fromStatus' to '$toStatus'");
  }
}
