<?php

namespace Mmieluch\LaravelServeCustomIni;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;

class ServeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server using custom INI file.';

    /**
     * Default location of custom php.ini file (in root of the project).
     *
     * @var string
     */
    protected $defaultIniPath = './php.ini';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function fire()
    {
        chdir($this->laravel->publicPath());

        $host = $this->input->getOption('host');

        $port = $this->input->getOption('port');

        $base = ProcessUtils::escapeArgument($this->laravel->basePath());

        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(true));

        $this->info("Laravel development server started on http://{$host}:{$port}/");

        if (defined('HHVM_VERSION')) {
            if (version_compare(HHVM_VERSION, '3.8.0') >= 0) {
                passthru("{$binary} -m server -v Server.Type=proxygen -v Server.SourceRoot={$base}/ -v Server.IP={$host} -v Server.Port={$port} -v Server.DefaultDocument=server.php -v Server.ErrorDocument404=server.php");
            } else {
                throw new Exception("HHVM's built-in server requires HHVM >= 3.8.0.");
            }
        } else {
            passthru($this->buildCommand($binary, $host, $port, $base));
        }
    }

    /**
     * Returns a command to pass through to shell.
     *
     * @param string $binary Usually full path to the PHP executable
     * @param string $host   Hostname
     * @param int    $port
     * @param string $base   Full path to Laravel project root
     *
     * @return string
     */
    protected function buildCommand($binary, $host, $port, $base)
    {
        $binary = $this->handleCustomIni($binary);

        $command = "{$binary} -S {$host}:{$port} {$base}/server.php";

        return $command;
    }

    /**
     * Adds parameter telling PHP built-in server to respect a custom
     * php.ini file.
     *
     * @param string $command Command built up to this point.
     *
     * @return string
     */
    protected function handleCustomIni($command)
    {
        $ini = $this->input->getOption('ini');

        // If --ini parameter was not specified, just return the command
        // is at has been constructed.
        if (!$ini) {
            return $command;
        }

        // Additional parameter will not work when escaped with single quotes.
        $command = str_replace("'", '', $command);

        // Determine the path
        $iniPath = ($ini === $this->defaultIniPath)
          ? $this->laravel->basePath() . '/php.ini'
          : realpath($ini);

        $this->info('Loading custom configuration file: ' . $iniPath, 'v');

        if (!file_exists($iniPath)) {
            $this->warn(sprintf(
              'File %s does not exist. Custom configuration will not be loaded.',
              $iniPath
            ));
        }

        // Append PHP parameter with a path to the configuration file.
        $command .= ' -c ' . $iniPath;

        return $command;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
          [
            'host',
            null,
            InputOption::VALUE_OPTIONAL,
            'The host address to serve the application on.',
            'localhost',
          ],

          [
            'port',
            null,
            InputOption::VALUE_OPTIONAL,
            'The port to serve the application on.',
            8000,
          ],

          [
            'ini',
            null,
            InputOption::VALUE_OPTIONAL,
            'Whether to load custom php.ini: null defaults to project root, otherwise will treat as a path',
            $this->defaultIniPath,
          ],
        ];
    }
}
