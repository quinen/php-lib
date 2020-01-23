<?php
/**
 * Created by PhpStorm.
 * User: laurent.d
 * Date: 1/23/20
 * Time: 12:06 PM
 */

namespace QuinenLib\Legacy;


class Controller
{
    /* @var Request $request */
    protected $request;
    protected $response;

    /**
     * Controller constructor.
     * @param Request|null $request
     * @param null $response
     */
    public function __construct(Request $request = null, $response = null)
    {

        $this->setRequest($request);

        $this->initialize();

        $this->setResponse($response);

    }

    public function initialize()
    {
    }

    protected function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    protected function setRequest($request)
    {
        if ($request === null) {
            $request = new Request();
        }
        $this->request = $request;
    }

}