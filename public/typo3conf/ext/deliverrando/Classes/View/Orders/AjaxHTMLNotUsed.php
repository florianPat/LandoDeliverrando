<?php

namespace MyVendor\Deliverrando\View\Orders;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\AbstractView;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;

//NOTE: This does not get used anymore. But it is a cool reference, isn`t it?

//NOTE: This render method gets called if no template for the ajax action in the controller (folder) Orders is found
// One would also name it JSON to output json (it must be one of the formats that TYPO3 supports)
class AjaxHTMLNotUsed extends AbstractView
{
    /**
     * @return string
     */
    public function render() : string
    {
        assert(isset($this->variables['orders']));
        $orders = $this->variables['orders'];

        $result = '';

        foreach($orders as $i => $order) {
            if($i == 0) {
                $result .= '<div class="card-deck">';
            } else if(($i % 2) === 0) {
                $result .= '</div><div class="card-deck">';
            }
            $uri = GeneralUtility::makeInstance(ObjectManager::class)->get(UriBuilder::class)->setTargetPageUid(5)->setArguments(['tx_deliverrando_bestellungen[action]' => 'finish', 'tx_deliverrando_bestellungen[controller]' => 'Orders', 'tx_deliverrando_bestellungen[order]' => $order->getUid()])->buildFrontendUri();

            $result .= '<div class="card">' .
                '<h2 class="card-header">Order id: ' . $order->getUid() . '</h2>' .
                '<div class="card-body">' .
                    '<h4 class="card-subtitle">Person</h4>' .
                    '<p class="card-text">Name: ' . $order->getPerson()->getName() . '</p>' .
                    '<p class="card-text">Address: ' . $order->getPerson()->getAddress() . '</p>' .
                    '<p class="card-text">Telephone number: ' . $order->getPerson()->getTelephonenumber() . '</p>' .
                    '<br />' .
                    '<div class="card-header">' .
                        'Products' .
                    '</div>' .
                    '<ul class="list-group">';
                        foreach($order->getProductDescriptions() as $productDesc) {
                            $result .= '<li class="list-group-item">x' . $productDesc->getQuantity() . ' ' . $productDesc->getProduct()->getName() . '</li>';
                        }
                    $result .= '</ul>'.
                '</div>' .
                '<div class="card-footer text-muted">' .
                    '<a href="' . $uri . '" class="card-link">Finished!</a>' .
                '</div>' .
            '</div>';
            if($i === $orders->count() - 1 && ($i % 2) === 0) {
                $result .= '<div class="card"></div>';
            }
        }
        $result .= '</div>';

        return $result;
    }
}