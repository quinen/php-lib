<?php
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 09/06/20
 * Time: 14:57
 */

namespace QuinenLib\Utility;


class Git
{
    const DIR = '.git';

    protected $path;
    protected $currentBranch;
    protected $hashes = [];

    public function __construct()
    {
        if(!$this->getPath()){
            throw new \Exception('no '.self::DIR.' found');
        }
    }

    public function getPath()
    {
        if ($this->path === null) {

            $script = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['file'];

            while (($dirname = dirname($script)) && $dirname !== dirname($dirname) && !is_dir($dirname . DIRECTORY_SEPARATOR . self::DIR)) {
                $script = dirname($script);
            }

            if (!is_dir($dirname . DIRECTORY_SEPARATOR . self::DIR)) {
                return false;
            }

            $this->path = $dirname . DIRECTORY_SEPARATOR . self::DIR;
        }
        return $this->path;
    }

    public function getCurrentBranch()
    {
        if ($this->currentBranch === null) {
            if ($gitStr = file_get_contents($this->getPath() . DIRECTORY_SEPARATOR . 'HEAD')) {
                $this->currentBranch = rtrim(preg_replace("/(.*?\/){2}/", '', $gitStr));
            } else {
                return false;
            }
        }
        return $this->currentBranch;
    }

    public function getHash($branch = null, $isShort = true)
    {

        if ($branch === null) {
            $branch = $this->getCurrentBranch();
        }

        if (!isset($this->hashes[$branch])) {
            if ($hash = file_get_contents(implode(DIRECTORY_SEPARATOR, [$this->getPath(), 'refs', 'heads', $branch]))) {
                $this->hashes[$branch] = [trim($hash), trim(substr($hash, 0, 7))];
            }
        }

        return $this->hashes[$branch][intval($isShort)];
    }
}