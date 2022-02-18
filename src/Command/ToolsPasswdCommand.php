<?php

namespace App\Command;

use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ToolsPasswdCommand extends Command
{
    protected static $defaultName = 'tools:passwd';
    protected static $defaultDescription = 'Add a short description for your command';
    private UserRepository $usrRepo;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher, UserRepository $usrRepo)
    {
        $this->usrRepo = $usrRepo;
        $this->hasher = $hasher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('pass', InputArgument::OPTIONAL, 'New admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $password = $input->getArgument('pass');

        try
        {    
            if ($password) 
            {
                $adminUser = $this->usrRepo->findOneBy(['username' => 'admin']);
                if($adminUser)
                {
                    $adminUser->setPassword($this->hasher->hashPassword($adminUser, $password));
                    $this->usrRepo->save($adminUser);
                }
            }
            $io->success('Password was changed.');
        }
        catch(Exception $ex)
        {
            $io->error('Error changing password: '. $ex);
        }
        
        return Command::SUCCESS;
    }
}
