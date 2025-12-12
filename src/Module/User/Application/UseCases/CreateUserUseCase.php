<?php

namespace Module\User\Application\UseCases;

use Module\User\Application\Exception\ExistingUserException;
use Module\User\Domain\Service\AccessTokenGenerator;
use Module\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Module\Shared\Domain\Email;

class CreateUserUseCase {
  private EntityManagerInterface $em;
  private AccessTokenGenerator $accessTokenGenerator;

  /** @var EntityRepository<User> */
  private $repo;

  public function __construct(EntityManagerInterface $em, AccessTokenGenerator $accessTokenGenerator) {
    $this->em = $em;
    $this->repo = $em->getRepository(User::class);
    $this->accessTokenGenerator = $accessTokenGenerator;
  }

  public function execute(string $name, string $email) {
    if ($this->repo->findOneBy(['email.value' => $email])) {
      throw new ExistingUserException("User with email '$email' already exists.");
    }

		$email = Email::fromString($email);
    $user = new User($name, $email);
    $user->accessToken = $this->accessTokenGenerator->generate($user);

    $this->em->persist($user);
    $this->em->flush();

    return [
			'id' => $user->id,
			'accessToken' => $user->accessToken,
		];
  }
}
