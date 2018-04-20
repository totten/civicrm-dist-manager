<?php

namespace CiviDistManagerBundle\Command;

use CiviDistManagerBundle\BuildRepository;
use CiviDistManagerBundle\RevDocRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RevListCommand extends ContainerAwareCommand {
  protected function configure() {
    $this
      ->setName('rev:list')
      ->setDescription('Get a list of revision documents')
      ->setHelp('Get a list of autogenerated tar files

Examples:
$ bin/console rev:list
');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    /** @var RevDocRepository $revRepo */
    $revRepo = $this->getContainer()->get('rev_doc_repository');

    $output->writeln(json_encode($revRepo->createRevDocs(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

  }

}