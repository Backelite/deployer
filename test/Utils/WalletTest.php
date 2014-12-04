<?php

namespace Deployer\Utils;

use Deployer\Deployer;
use Deployer\DeployerTester;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

class WalletTest extends DeployerTester {

    protected $dirName = 'WalletTest';

    public function setUp() {
        parent::setUp();
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory($this->dirName));
    }

    public function testFileCreation() {
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild(Wallet::FILE));
        $this->getWallet();
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild(Wallet::FILE));
    }

    public function testGetNotExistingCredential() {
        $this->assertEquals('login', $this->getWallet()->getLogin('id'));
        $this->assertEquals('password', $this->getWallet()->getPassword('id'));
    }

    protected function getWallet() {
        $deployer = Deployer::get();
        $dir = vfsStream::url($this->dirName);
        return new Wallet($deployer->getInput(), $deployer->getOutput(), $deployer->getHelperSet(), $dir);
    }

}
