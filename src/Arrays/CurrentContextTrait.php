<?php

namespace QuinenLib\Arrays;

trait CurrentContextTrait
{
    /*
     *
     *  $this->request->getParam('plugin') . '.' .
            $this->request->getParam('controller')
     *
     * */
    protected $_defaultContext;
    protected $_currentContext;

    /**
     *
     * @return string
     */
    public function getCurrentContext()
    {
        return ($this->_currentContext !== null ? $this->_currentContext : $this->_defaultContext);
    }

    /**
     * @param $pluginController
     */
    public function setCurrentContext($currentContext)
    {
        $this->_currentContext = $currentContext;
    }

    public function getDefaultContext()
    {
        return $this->_defaultContext;
    }

    public function setDefaultContext($defaultContext)
    {
        $this->_defaultContext = $defaultContext;
    }
}