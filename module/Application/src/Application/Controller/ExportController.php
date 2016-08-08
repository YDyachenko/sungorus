<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

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
     * {@inheritdoc}
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'checkUserEncryptionKey'], 100);
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

        $headers->addHeaderLine('Content-Disposition', 'attachment; filename="accounts.xml"')
                ->addHeaderLine('Content-Type', 'text/xml');

        $response->setContent($result);

        return $response;
    }

}
