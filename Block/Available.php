<?php 
/**
 * @category   AMBIKUK
 * @package    Ambikuk_ShipPerProduct
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ambikuk Salawean <ambikuk@gmail.com>
 */

class Ambikuk_ShipPerProduct_Block_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
	protected function getInfoText($carrierCode)
	{
		if ($text = Mage::getStoreConfig('carriers/'.$carrierCode.'/infotext')) {
            return $text;
        }
        return '';
	}
}
