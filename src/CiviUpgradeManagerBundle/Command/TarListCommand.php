<?php

namespace CiviUpgradeManagerBundle\Command;

use CiviUpgradeManagerBundle\BuildRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TarListCommand extends ContainerAwareCommand {
  protected function configure() {
    $this
      ->setName('tar:list')
      ->setDescription('Get a list of autogenerated tar files')
      ->addArgument('filter', InputArgument::OPTIONAL, 'A pattern to match against. May use wildcard "*".')
      ->setHelp('Get a list of autogenerated tar files

Examples:
$ bin/console tar:list
$ bin/console tar:list master/*
$ bin/console tar:list 4.6*/*drupal*
');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    /** @var BuildRepository $buildRepo */
    $buildRepo = $this->getContainer()->get('build_repository');

    if ($input->getArgument('filter')) {
      $files = $buildRepo->getFilesByWildcard($input->getArgument('filter'));
    }
    else {
      $files = $buildRepo->getFiles();
    }

    foreach ($files as $file) {
      $output->writeln($file['file']);
    }
  }

}
