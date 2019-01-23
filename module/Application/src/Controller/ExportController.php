<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ExportController extends AbstractActionController
{

    /**
     *
     * @var \Application\Service\ExportService
     */
    protected $exportService;

    public function __construct($exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export accounts
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $result   = $this->exportService->process($this->identity());
        $response = $this->getResponse();
        $headers  = $response->getHeaders();

        $headers
            ->addHeaderLine('Content-Disposition', 'attachment; filename="accounts.xml"')
            ->addHeaderLine('Content-Type', 'text/xml');

        $response->setContent($result);

        return $response;
    }
}
