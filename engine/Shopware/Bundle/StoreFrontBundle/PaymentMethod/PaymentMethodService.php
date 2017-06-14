<?php
declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\StoreFrontBundle\PaymentMethod;

use Shopware\Bundle\CartBundle\Domain\Cart\CalculatedCart;
use Shopware\Bundle\CartBundle\Domain\Rule\ValidatableFilter;
use Shopware\Bundle\StoreFrontBundle\Context\ShopContextInterface;

class PaymentMethodService
{
    /**
     * @var \Shopware\Bundle\StoreFrontBundle\PaymentMethod\PaymentMethodGateway
     */
    private $gateway;

    /**
     * @var ValidatableFilter
     */
    private $ruleFilter;

    public function __construct(PaymentMethodGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param CalculatedCart                                                 $calculatedCart
     * @param \Shopware\Bundle\StoreFrontBundle\Context\ShopContextInterface $context
     *
     * @return \Shopware\Bundle\StoreFrontBundle\PaymentMethod\PaymentMethod[]
     */
    public function getAvailable(
        CalculatedCart $calculatedCart,
        ShopContextInterface $context
    ): array {
        $payments = $this->gateway->getAll($context->getTranslationContext());

        $actives = array_filter($payments, function (PaymentMethod $paymentMethod) {
            return $paymentMethod->isActive();
        });

        return $actives;
    }
}
