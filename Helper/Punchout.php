<?php
namespace Reno\Punchout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\HTTP\Client\Curl;

class Punchout extends AbstractHelper
{
    protected $cart;
    protected $session;
    protected $curl;

    // Ganti sesuai middleware kamu
    const MIDDLEWARE_CART_URL = 'https://localhost/punchout/cart';

    public function __construct(
        Context $context,
        Cart $cart,
        SessionManagerInterface $session,
        Curl $curl
    ) {
        $this->cart = $cart;
        $this->session = $session;
        $this->curl = $curl;
        parent::__construct($context);
    }

    public function isPunchoutSession()
    {
        return (bool) $this->session->getData('punchout_mode');
    }

    public function getBuyerCookie()
    {
        return $this->session->getData('buyer_cookie') ?? '';
    }

    public function prepareCartData()
    {
        $items = [];
        foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
            $items[] = [
                'sku' => $item->getSku(),
                'description' => $item->getName(),
                'quantity' => (int) $item->getQty(),
                'price' => (float) $item->getPrice(),
                'currency' => $this->cart->getQuote()->getQuoteCurrencyCode()
            ];
        }

        return [
            'buyerCookie' => $this->getBuyerCookie(),
            'returnUrl' => 'https://postman-echo.com/post', // ganti nanti ke real Ariba
            'items' => $items
        ];
    }

    public function sendCartToMiddleware()
    {
        $cartData = $this->prepareCartData();

        try {
            $this->curl->addHeader("Content-Type", "application/json");
            $this->curl->post(self::MIDDLEWARE_CART_URL, json_encode($cartData));
            $response = $this->curl->getBody();
            return $response;
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}
