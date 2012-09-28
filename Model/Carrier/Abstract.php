<?php
/**
 * @category   AMBIKUK
 * @package    Ambikuk_ShipPerProduct
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ambikuk Salawean <ambikuk@gmail.com>
 */
abstract class Ambikuk_ShipPerProduct_Model_Carrier_Abstract extends Mage_Shipping_Model_Carrier_Flatrate
{
	protected $_code = '';
	protected $idProduct = array();

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!$this->getConfigFlag('active'))
		{
			return false;
		}

		$freeBoxes = 0;
		if ($request->getAllItems())
		{
			foreach ($request->getAllItems() as $item)
			{
				$this->idProduct[] = $item->getProduct()->getId();
				if ($item->getProduct()->isVirtual() || $item->getParentItem())
				{
					continue;
				}
				if ($item->getHasChildren() && $item->isShipSeparately())
				{
					foreach ($item->getChildren() as $child)
					{

						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual())
						{
							$freeBoxes += $item->getQty() * $child->getQty();
						}
					}
				}
				elseif ($item->getFreeShipping())
				{
					$freeBoxes += $item->getQty();
				}
			}
		}
		if (count($this->idProduct)==1 && $this->idProduct[0] == $this->getConfigData('id_product'))
		{
			$this->setFreeBoxes($freeBoxes);

			$result = Mage::getModel('shipping/rate_result');
			if ($this->getConfigData('type') == 'O')
			{
				$shippingPrice = $this->getConfigData('price');
			}
			elseif ($this->getConfigData('type') == 'I')
			{
				$shippingPrice = ($request->getPackageQty() * $this->getConfigData('price')) - ($this->getFreeBoxes() * $this->getConfigData('price'));
			}
			else
			{
				$shippingPrice = false;
			}

			$shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

			if ($shippingPrice !== false)
			{
				$method = Mage::getModel('shipping/rate_result_method');

				$method->setCarrier($this->_code);
				$method->setCarrierTitle($this->getConfigData('title'));

				$method->setMethod($this->_code);
				$method->setMethodTitle($this->getConfigData('name'));

				if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes())
				{
					$shippingPrice = '0.00';
				}


				$method->setPrice($shippingPrice);
				$method->setCost($shippingPrice);

				$result->append($method);
			}
			return $result;
		}
	}

	public function getAllowedMethods()
	{
		return array($this->_code => $this->getConfigData('name'));
	}
}
