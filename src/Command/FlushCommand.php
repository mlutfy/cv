<?php
namespace Civi\Cv\Command;

use Civi\Cv\Util\BootTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FlushCommand extends BaseCommand {

  use BootTrait;

  protected function configure() {
    $this
      ->setName('flush')
      ->setAliases(array())
      ->addOption('triggers', 'T', InputOption::VALUE_NONE, 'Rebuild triggers')
      ->setDescription('Flush system caches')
      ->setHelp('
Flush system caches
');
    $this->configureBootOptions();
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    // The main reason we have this as separate command -- so we can ignore
    // stale class-references that might be retained by the container cache.
    define('CIVICRM_CONTAINER_CACHE', 'never');
    $this->boot($input, $output);

    $params = array();
    if ($input->getOption('triggers')) {
      $params['triggers'] = TRUE;
    }

    $output->writeln("<info>Flushing system caches</info>");
    $result = $this->callApiSuccess($input, $output, 'System', 'flush', $params);
    return empty($result['is_error']) ? 0 : 1;
  }

}
