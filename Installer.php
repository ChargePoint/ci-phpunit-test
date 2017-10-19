<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Installer
{
    const TEST_FOLDER = 'tests';

    private $silent = false;
    private $base_dir;

    public function __construct($silent = false)
    {
        $this->silent = $silent;
        $this->base_dir = dirname(__FILE__) . '/../../../';
    }

    public function install($app = 'application')
    {
        $this->copyFile(
            dirname(__FILE__) . '/application/tests/Bootstrap.php',
            $this->base_dir . '/' . static::TEST_FOLDER . DIRECTORY_SEPARATOR . 'Bootstrap.php'
        );

        $this->copyFile(
            dirname(__FILE__) . '/application/tests/phpunit.xml',
            $this->base_dir . '/' . static::TEST_FOLDER . DIRECTORY_SEPARATOR . 'phpunit.xml'
        );

        $this->recursiveCopy(
            dirname(__FILE__) . '/application/tests/mocks',
            $this->base_dir . '/' . static::TEST_FOLDER . DIRECTORY_SEPARATOR . 'mocks'
        );

        $this->fixPath($app);
    }

    /**
     * Fix paths in Bootstrap.php
     */
    private function fixPath($app)
    {
        $file = $this->base_dir . '/' . static::TEST_FOLDER . '/Bootstrap.php';
        $contents = file_get_contents($file);
        
        if (! file_exists($this->base_dir . 'system')) {
            if (file_exists($this->base_dir . 'vendor/codeigniter/framework/system')) {
                $contents = str_replace(
                    '$system_path = \'../../system\';',
                    '$system_path = \'../vendor/codeigniter/framework/system\';',
                    $contents
                );
            } else {
                throw new Exception('Can\'t find "system" folder.');
            }
        }
        
        if (! file_exists($this->base_dir . 'index.php')) {
            if (file_exists($this->base_dir . 'public/index.php')) {
                // CodeIgniter 3.0.6 and after
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').DIRECTORY_SEPARATOR);",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../public').DIRECTORY_SEPARATOR);",
                    $contents
                );
                // CodeIgniter 3.0.5 and before
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../../public').'/');",
                    $contents
                );
            } elseif (file_exists($this->base_dir . $app . '/public/index.php')) {
                // CodeIgniter 3.0.6 and after
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').DIRECTORY_SEPARATOR);",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../public').DIRECTORY_SEPARATOR);",
                    $contents
                );
                // CodeIgniter 3.0.5 and before
                $contents = str_replace(
                    "define('FCPATH', realpath(dirname(__FILE__).'/../..').'/');",
                    "define('FCPATH', realpath(dirname(__FILE__).'/../public').'/');",
                    $contents
                );
                if ($app != 'application') {
                    $contents = str_replace(
                        "\$application_folder = '../../application';",
                        "\$application_folder = '../../{$app}';",
                        $contents
                    );
                }
            } else if (! file_exists($this->base_dir . '../index.php')) {
                throw new Exception('Can\'t find "index.php".');
            }
        }
        
        file_put_contents($file, $contents);
    }

    /**
     * Recursive Copy
     *
     * @param string $src
     * @param string $dst
     */
    private function recursiveCopy($src, $dst)
    {
        @mkdir($dst, 0755);
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @mkdir($dst . '/' . $iterator->getSubPathName());
            } else {
                $this->copyFile($file, $dst . '/' . $iterator->getSubPathName());
            }
        }
    }

    /**
     * Recursive Unlink
     *
     * @param string $dir
     */
    private function recursiveUnlink($dir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file);
            } else {
                unlink($file);
            }
        }
        
        rmdir($dir);
    }

    /**
     * Copy a single file
     *
     * @param $src
     * @param $dst
     */
    private function copyFile($src, $dst)
    {
        $success = copy($src, $dst);
        if ($success) {
            if (!$this->silent) {
                echo 'copied: ' . $dst . PHP_EOL;
            }
        }
    }
}
