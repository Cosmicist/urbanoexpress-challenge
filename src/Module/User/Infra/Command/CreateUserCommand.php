<?php

namespace Module\User\Infra\Command;

use Module\User\Application\UseCases\CreateUserUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command {
	private CreateUserUseCase $createUserUseCase;

	public function init(CreateUserUseCase $createUserUseCase) {
		$this->createUserUseCase = $createUserUseCase;
	}

	protected function configure(): void {
		$this
			->setName('user:create')
			->setDescription('Creates a new user in the system')
			->addArgument('name', InputArgument::REQUIRED)
			->addArgument('email', InputArgument::REQUIRED)
		;
	}

	public function execute(InputInterface $input, OutputInterface $output): int {
		try {
			$result = $this->createUserUseCase->execute(
				$input->getArgument('name'),
				$input->getArgument('email')
			);

			$output->writeln([
				'<info>User created successfully.</>',
				'<info>User ID:</> ' . $result['id'],
				'<info>Access Token:</> ' . $result['accessToken'],
			]);
		} catch (\Throwable $e) {
			$output->writeln('<error>Error: ' . $e->getMessage() . '</>');
			return 1;
		}

		return 0;
	}
}
