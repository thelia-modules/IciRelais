<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace IciRelais;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Exception\OrderException;
use Thelia\Module\BaseModule;
use Thelia\Module\DeliveryModuleInterface;
use Thelia\Model\Country;
use Propel\Runtime\Connection\ConnectionInterface;

class IciRelais extends BaseModule implements DeliveryModuleInterface
{
    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */
     
    protected $request;
    protected $dispatcher;

    private static $prices = null;
    
    const JSON_PRICE_RESOURCE = "prices.json";
	
	/*
	private static function listTemplates($dir) {
		$files = array();
		if($handle = opendir($dir)) {
			while(false !== ($file = readdir($handle))) {
				if(preg_match("#(\.html)$#i", $file)) {
					$files[] = $file;
				}
			}
			closedir($handle);
		}
		return $files;
	}
	
	public function preActivation(ConnectionInterface $con = NULL) 
	{
		$dir = __DIR__."/templates/";
		$files = self::listTemplates($dir);
		
		foreach($files as $f) {
			$orig_path = __DIR__."/../../templates/frontOffice/default/";
			if(is_file($orig_path.$f)) {
				copy($orig_path.$f,$orig_path.$f.".bak");
				unlink($orig_path.$f);
				copy($dir.$f, $orig_path.$f);
			}
		}
	}
	
	public function preDeactivation(ConnectionInterface $con = NULL) 
	{
		$dir = __DIR__."/templates/";
		$files = self::listTemplates($dir);
		
		foreach($files as $f) {
			$orig_path = __DIR__."/../../templates/frontOffice/default/";
			if(is_file($orig_path.$f.".bak") && is_file($orig_path.$f)) {
				unlink($orig_path.$f);
				copy($orig_path.$f.".bak",$orig_path.$f);
				unlink($orig_path.$f.".bak");
			}
		}
	}
	*/
	public static function getPrices()
    {
        if(null === self::$prices) {
            self::$prices = json_decode(file_get_contents(sprintf('%s/Config/%s', __DIR__, self::JSON_PRICE_RESOURCE)), true);
        }

        return self::$prices;
    }
	
	public static function getPostageAmount($areaId, $weight)
    {
        $prices = self::getPrices();

        /* check if IciRelais delivers the asked area */
        if(!isset($prices[$areaId]) || !isset($prices[$areaId]["slices"])) {
            throw new OrderException("Ici Relais delivery unavailable for the chosen delivery country", OrderException::DELIVERY_MODULE_UNAVAILABLE);
        }

        $areaPrices = $prices[$areaId]["slices"];
        ksort($areaPrices);

        /* check this weight is not too much */
        end($areaPrices);
        $maxWeight = key($areaPrices);
        if($weight > $maxWeight) {
            throw new OrderException(sprintf("Ici Relais delivery unavailable for this cart weight (%s kg)", $weight), OrderException::DELIVERY_MODULE_UNAVAILABLE);
        }

        $postage = current($areaPrices);

        while(prev($areaPrices)) {
            if($weight > key($areaPrices)) {
                break;
            }

            $postage = current($areaPrices);
        }

        return $postage;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function getPostage(Country $country)
    {
        $cartWeight = $this->getContainer()->get('request')->getSession()->getCart()->getWeight();

        $postage = self::getPostageAmount(
            $country->getAreaId(),
            $cartWeight
        );

        return $postage;
    }
	
	public function getCode() {
		return "Icirelais";
	}
}


?>