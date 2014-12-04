<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer\Server;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Deployer\Environment;

abstract class AbstractServer implements ServerInterface
{
    /**
     * Server config.
     * @var Configuration
     */
    protected $config;

    /**
     * Server env.
     * @var Environment
     */
    protected $environment;

    /**
     * Logger
     * @var Logger
     */
    protected $logger;

    /**
     * @param Configuration $environment
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->environment = new Environment($this);
        $this->initLogger();
    }

    /**
     * Init logger
     */
    protected function initLogger() {
        $this->logger = new Logger($this->config->getName());
        $date = new \DateTime();
        $logDir = getcwd() . '/deploy/logs';
        if (file_exists($logDir) === false) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/' . $date->format('d-m-Y_H-i-s') . '.log';
        $handler = new StreamHandler($logFile);
        $this->logger->pushHandler($handler);
    }

    /**
     * Logger
     * @return Logger
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     *{@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    public function run($command) {
        $this->logger->addDebug($command);

        try {
            $result =  $this->execute($command);
        } catch (\RuntimeException $e) {
            $this->logger->addError($e->getMessage());
            throw $e;
        }

        $this->logger->addDebug(' :: ' . $result);

        return $result;
    }
}