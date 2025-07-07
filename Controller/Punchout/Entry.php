<?php
namespace Reno\Punchout\Controller\Punchout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\Result\RedirectFactory;

class Entry extends Action
{
    protected $session;
    protected $customerFactory;
    protected $resultRedirectFactory;

    public function __construct(
        Context $context,
        CustomerSession $session,
        CustomerFactory $customerFactory,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->session = $session;
        $this->customerFactory = $customerFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $buyerCookie = $this->getRequest()->getParam('buyerCookie');
        if (!$buyerCookie) {
            return $this->resultRedirectFactory->create()->setPath('/');
        }

        // login hardcoded user (e.g. "ariba_user@example.com")
        $customer = $this->customerFactory->create()->loadByEmail("ariba_user@example.com");
        if ($customer && $customer->getId()) {
            $this->session->setCustomerAsLoggedIn($customer);
            $this->session->setData("punchout_mode", true);
            $this->session->setData("buyer_cookie", $buyerCookie);
        }

        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
    }
}
