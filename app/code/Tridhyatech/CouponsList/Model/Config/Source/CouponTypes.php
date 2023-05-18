<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_CouponsList
 */
namespace Tridhyatech\CouponsList\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CouponTypes implements ArrayInterface
{
    /**
     * Retrieve Custom Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('All Coupons')],
            ['value' => 2, 'label' => __('Coupons accordings to cart wise')]
        ];
    }
}
