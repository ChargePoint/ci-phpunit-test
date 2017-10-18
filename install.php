<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

foreach ([__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

require __DIR__ . '/Installer.php';

$getopt = new \GetOpt\GetOpt([

    \GetOpt\Option::create('s', 'silent', \GetOpt\GetOpt::NO_ARGUMENT)
        ->setDescription('Install silently'),

    \GetOpt\Option::create('a', 'application', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
        ->setDescription('Path to application directory')
        ->setArgument(new \GetOpt\Argument('application', null, 'application')),

]);

$getopt->process();

$installer = new Installer($getopt->getOption('silent'));
$installer->install($getopt->getOption('application'));
