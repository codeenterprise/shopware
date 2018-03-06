<?php declare(strict_types=1);

namespace Shopware\Api\Order\Event\OrderLineItem;

use Shopware\Api\Order\Collection\OrderLineItemBasicCollection;
use Shopware\Context\Struct\ShopContext;
use Shopware\Framework\Event\NestedEvent;

class OrderLineItemBasicLoadedEvent extends NestedEvent
{
    public const NAME = 'order_line_item.basic.loaded';

    /**
     * @var ShopContext
     */
    protected $context;

    /**
     * @var OrderLineItemBasicCollection
     */
    protected $orderLineItems;

    public function __construct(OrderLineItemBasicCollection $orderLineItems, ShopContext $context)
    {
        $this->context = $context;
        $this->orderLineItems = $orderLineItems;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): ShopContext
    {
        return $this->context;
    }

    public function getOrderLineItems(): OrderLineItemBasicCollection
    {
        return $this->orderLineItems;
    }
}