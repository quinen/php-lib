<?php


namespace QuinenLib\Utility;

use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    /**
     * @var Git
     */
    private $git;

    protected function setUp(): void
    {
        $this->git = new Git();
    }

    public function testGetPath()
    {
        $this->assertStringEndsWith(".git", $this->git->getPath());
    }

    /**
     * @depends testGetPath
     */

    public function testGetCurrentBranch()
    {
        //  tests_unitaires aeeb91f5549417c28d6a2e00f567e4ca4f5c7446
        //  git rev-parse HEAD

        $gitStr = file_get_contents($this->git->getPath() . DIRECTORY_SEPARATOR . 'HEAD');
        $currentBranch = rtrim(preg_replace("/(.*?\/){2}/", '', $gitStr));
        $this->assertEquals($currentBranch, $this->git->getCurrentBranch());

    }

    /**
     * @depends testGetCurrentBranch
     */
    public function testGetHash()
    {
        $currentBranch = $this->git->getCurrentBranch();
        $hash_shell = trim(shell_exec('git rev-parse origin/' . $currentBranch));
        $hash_false = $this->git->getHash(null, false);
        $this->assertEquals($hash_false, $hash_shell);
        $hash_true = $this->git->getHash();
        $this->assertEquals($hash_true, substr($hash_shell, 0, 7));
    }

}