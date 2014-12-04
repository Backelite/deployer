<?php

namespace Deployer\Utils;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Question\Question;

class Wallet {

    const FILE = '.deployer';

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var HelperSet
     */
    protected $helperSet;

    /**
     * @var string
     */
    protected $configFile;

    /**
     * @var array
     */
    protected $credentials = array();

    /**
     * Constructor
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     * @param string $dir
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet, $dir = null) {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;

        $folder = $dir === null ? $this->getHome() : $dir;
        $this->configFile = $folder . '/' . self::FILE;

        if (!file_exists($this->configFile)) {
            touch($this->configFile);
        } else {
            $this->credentials = json_decode(file_get_contents($this->configFile), true);
        }
    }

    /**
     * Get user home folder
     * @return mixed String or false if not found
     */
    protected function getHome() {
        return getenv('HOME');
    }

    /**
     * Get login registered for this id
     * @param string $id
     * @param string $message
     * @return mixed
     */
    public function getLogin($id, $message = null) {
        return $this->processCredential($id, 'login', self::getLoginQuestion($id, $message));
    }

    /**
     * Get login question
     * @param string $id
     * @param string $message
     * @return Question
     */
    public static function getLoginQuestion($id, $message = null) {
        $questionMessage = $message !== null ? $message : 'Login for ' . $id;
        return new Question('<question>' . $questionMessage . '</question> : ');
    }

    /**
     * Get login registered for this id
     * @param string $id
     * @param string $message
     * @return mixed
     */
    public function getPassword($id, $message = null) {
        return $this->processCredential($id, 'password', self::getPasswordQuestion($id, $message));
    }

    /**
     * Get password question
     * @param string $id
     * @return Question
     */
    public static function getPasswordQuestion($id, $message = null) {
        $questionMessage = $message !== null ? $message : 'Password for ' . $id;
        $question = new Question('<question>' . $questionMessage . '</question> : ');
        return $question->setHidden(true)->setHiddenFallback(true);
    }

    /**
     * Get credential element
     * @param string $id
     * @param string $element
     * @param Question $question
     * @return mixed String or null
     */
    protected function processCredential($id, $element, Question $question) {
        $credential = $this->getCredential($id, $element);

        if ($credential === null) {
            $credential = $this->askQuestion($question);

            if ($credential !== null) {
                $this->setCredential($id, $element, $credential);
            }
        }

        return $credential;
    }

    protected function askQuestion(Question $question) {
        $questionHelper = $this->helperSet->get('question');
        return $questionHelper->ask($this->input, $this->output, $question);
    }

    protected function getCredential($id, $element) {
        return isset($this->credentials[$id], $this->credentials[$id][$element]) ? $this->credentials[$id][$element] : null;
    }

    protected function setCredential($id, $element, $value) {
        $this->credentials[$id][$element] = $value;
        return $this;
    }

    public function saveCredentials() {
        $data = json_encode($this->credentials);
        $result = file_put_contents($this->configFile, $data);
        return !($result === false);
    }

}
