<?php


namespace App\Command;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUser extends Command
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('user:create-admin')
            ->setDescription('Creates a user with admin role');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Enter details for user:');

        $helper = $this->getHelper('question');

        $question = new Question('- username: ');
        $userName = $helper->ask($input, $output, $question);

        $question = new Question('- email: ');
        $email = $helper->ask($input, $output, $question);

        $question = new Question('- password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);

        $this->createUser($userName, $email, $password);

        return 0;
    }

    private function createUser(string $userName, string $email, string $password) : void
    {
        $user = new User();

        $user->setUsername($userName)
            ->setEmail($email)
            ->setPassword(password_hash($password, PASSWORD_DEFAULT))
            ->setStatus(true)
            ->setRoles(['ROLE_API_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}