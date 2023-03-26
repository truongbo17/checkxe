<?php

namespace Bo\Ecommerce\Enums;

enum ProductCondition
{
    case NEW;
    case USED;
    case BROKEN;

    public function getReadableCondition()
    {
        return match ($this) {
            ProductCondition::NEW    => 'New Product',
            ProductCondition::USED   => 'Used Product',
            ProductCondition::BROKEN => '!!BROKEN!!',
        };
    }
}
