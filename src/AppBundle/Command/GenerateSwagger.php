<?php


namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSwagger extends Command
{
    protected function configure()
    {
        $this->setName('swagger:generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $swagger = \Swagger\scan(__DIR__ . '/../Controller/');
        $output->write((string)$swagger);
    }
}
