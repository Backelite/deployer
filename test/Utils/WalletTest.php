<?php

namespace Deployer\Utils;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

class WalletTest extends \PHPUnit_Framework_TestCase {

    protected $dirName = 'WalletTest';

    public function setUp() {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory($this->dirName));
    }

    public function testFileCreation() {
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild(Wallet::FILE));
        $this->getWallet();
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild(Wallet::FILE));
    }

    public function testGetNotExistingCredential() {
        $this->assertEquals('mockLogin', $this->getWallet()->getLogin('id'));
        $this->assertEquals('mockPassword', $this->getWallet()->getPassword('id'));
    }

    protected function getWallet() {
        $input = new \Symfony\Component\Console\Input\ArgvInput();
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $dir = vfsStream::url($this->dirName);
        return new Wallet($input, $output, $this->getHelperSet(), $dir);
    }

    protected function getHelperSet() {
        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper');
        $questionHelper->expects($this->any())
                ->method('ask')
                ->will($this->returnCallback(array('Deployer\Utils\WalletTest', 'questionHelperMockCallback')));

        $helperSetMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSetMock->expects($this->any())
                ->method('get')
                ->will($this->returnValue($questionHelper));

        return $helperSetMock;
    }

    public function questionHelperMockCallback() {
        $args = func_get_args();

        if (isset($args[2])) {
            $question = $args[2];
            switch ($question->getQuestion()) {
                case Wallet::getLoginQuestion('id')->getQuestion():
                    return 'mockLogin';
                case Wallet::getPasswordQuestion('id')->getQuestion():
                    return 'mockPassword';
            }
        }
        return 'mockResponse';
    }

}
