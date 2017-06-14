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

namespace Shopware\Bundle\CartBundle\Infrastructure\Rule\Collector;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\CartBundle\Domain\Cart\CartContainer;
use Shopware\Bundle\CartBundle\Domain\Cart\CollectorInterface;
use Shopware\Bundle\CartBundle\Domain\Rule\RuleCollection;
use Shopware\Bundle\CartBundle\Domain\Rule\Validatable;
use Shopware\Bundle\CartBundle\Infrastructure\Rule\Data\RecentOrderRuleData;
use Shopware\Bundle\CartBundle\Infrastructure\Rule\RecentOrderRule;
use Shopware\Bundle\StoreFrontBundle\Common\StructCollection;
use Shopware\Bundle\StoreFrontBundle\Context\ShopContextInterface;

class RecentOrderRuleCollector implements CollectorInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function prepare(
        StructCollection $fetchDefinition,
        CartContainer $cartContainer,
        ShopContextInterface $context
    ): void {
    }

    public function fetch(
        StructCollection $dataCollection,
        StructCollection $fetchCollection,
        ShopContextInterface $context
    ): void {
        $rules = $dataCollection->filterInstance(Validatable::class);

        $rules = $rules->map(function (Validatable $validatable) {
            return $validatable->getRule();
        });

        $rules = new RuleCollection($rules);

        if (!$rules->has(RecentOrderRule::class)) {
            return;
        }

        if (!$customer = $context->getCustomer()) {
            return;
        }

        $time = $this->connection->fetchColumn(
            'SELECT MAX(ordertime) FROM s_order WHERE userID = :userId',
            [':userId' => $customer->getId()]
        );

        if ($time) {
            $time = new \DateTime($time);
        }

        $dataCollection->add(
            new RecentOrderRuleData($time),
            RecentOrderRuleData::class
        );
    }
}
