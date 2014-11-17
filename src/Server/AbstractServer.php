<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer\Server;

abstract class AbstractServer implements ServerInterface
{
    /**
     * Server name.
     * @var string
     */
    private $name;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param string $name
     * @param Configuration $configuration
     * @param Environment $environment
     */
    public function __construct($name, Configuration $configuration, Environment $environment)
    {
        $this->configuration = $configuration;
        $this->environment = $environment;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
