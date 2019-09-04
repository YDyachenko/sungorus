<?php

namespace Application\Controller;

use Application\Model\User;
use Application\Service\ExportService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ResponseInterface;

/**
 * @method User identity()
 */
class ExportController extends AbstractActionController
{

    /**
     * @var ExportService
     */
    protected $exportService;

    public function __construct($exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Export accounts
     * @return ResponseInterface
     */
    public function indexAction()
    {
        $result = $this->exportService->process($this->identity());
        /* @var Response $response */
        $response = $this->getResponse();
        $headers  = $response->getHeaders();

        $headers->addHeaderLine('Content-Disposition', 'attachment; filename="accounts.xml"')
                ->addHeaderLine('Content-Type', 'text/xml');

        $response->setContent($result);

        return $response;
    }
}
