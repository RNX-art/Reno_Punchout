<?php
namespace Reno\Punchout\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Reno\Punchout\Helper\Punchout;
use Magento\Framework\Controller\Result\JsonFactory;

class Send extends Action
{
    protected $helper;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        Punchout $helper,
        JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $response = $this->helper->sendCartToMiddleware();
        return $result->setData(json_decode($response, true));
    }
}
