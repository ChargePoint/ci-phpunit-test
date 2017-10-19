<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

require __DIR__ . '/Installer.php';

$silent = false;

// php update.php -s
if (isset($argv[1]) && $argv[1] === '-s') {
    $silent = true;
}

$installer = new Installer($silent);
$installer->update();
