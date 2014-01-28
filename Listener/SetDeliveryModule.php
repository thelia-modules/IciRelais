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

namespace IciRelais\Listener;

use IciRelais\IciRelais;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\Order\OrderAddressEvent;
use Thelia\Core\Event\TheliaEvents;

use IciRelais\Model\OrderAddressIcirelais;
use Thelia\Model\OrderAddressQuery;

/**
 * Class SetDeliveryModule
 * @package IciRelais\Listener
 * @author benjamin perche <bperche9@gmail.com>
 */
 
class SetDeliveryModule extends BaseAction implements EventSubscriberInterface
{
	protected function check_module($event) {
		return $event->getDeliveryModule() == IciRelais::getModCode();
	}
		
    public function isModuleIciRelais(OrderEvent $event)
    {
        if($this->check_module($event)) {

        	if(isset($_POST['pr_code']) && !empty($_POST['pr_code'])) {
        		// Get details w/ SOAP
        		$con = new \SoapClient(__DIR__."/../Config/exapaq.wsdl", array('soap_version'=>SOAP_1_2));
				$response = $con->GetPudoDetails(array("pudo_id"=>$_POST['pr_code']));
				$xml = new \SimpleXMLElement($response->GetPudoDetailsResult->any);
				if(isset($xml->ERROR)) {
					throw new \ErrorException("Error while choosing pick-up & go store: ".$xml->ERROR);
				}
				
				//We can't use Symfony Session because of smarty in order-delivery.html
				$_SESSION['IciRelaiscode']=$_POST['pr_code'];
				$_SESSION['IciRelaiscompany']=(string)$xml->PUDO_ITEMS->PUDO_ITEM->NAME;
				$_SESSION['IciRelaisaddress1']=(string)$xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS1;
				$_SESSION['IciRelaisaddress2']=(string)$xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS2;
				$_SESSION['IciRelaisaddress3']=(string)$xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS3;
				$_SESSION['IciRelaiszipcode']=(string)$xml->PUDO_ITEMS->PUDO_ITEM->ZIPCODE;
				$_SESSION['IciRelaiscity']=(string)$xml->PUDO_ITEMS->PUDO_ITEM->CITY;
				$_SESSION['IciRelaisupdateDeliveryAddress'] = "true";
        	} else {
        		throw new \ErrorException("No pick-up & go store choosed for IciRelais delivery module");
        	}
        } else {
        	if(isset($_SESSION['IciRelaisupdateDeliveryAddress'])) unset($_SESSION['IciRelaisupdateDeliveryAddress']);
        }
    }

	public function updateDeliveryAddress(OrderEvent $event) {
		if(isset($_SESSION['IciRelaisupdateDeliveryAddress'])) {
			if(!(isset($_SESSION['IciRelaisaddress1']) && !empty($_SESSION['IciRelaisaddress1'])) ||
				!(isset($_SESSION['IciRelaiscity']) && !empty($_SESSION['IciRelaiscity'])) ||
				!(isset($_SESSION['IciRelaiszipcode']) && !empty($_SESSION['IciRelaiszipcode'])) ||
				!(isset($_SESSION['IciRelaiscompany']) && !empty($_SESSION['IciRelaiscompany'])) ||
				!(isset($_SESSION['IciRelaiscode']) && !empty($_SESSION['IciRelaiscode']))
				) {
					throw new \ErrorException("Got an error with IciRelais module. Please try again to checkout.");
				}
			
			$savecode = new OrderAddressIcirelais();
			$savecode->setNew(true);
			$savecode->setId($event->getOrder()->getDeliveryOrderAddressId())
				->setCode($_SESSION['IciRelaiscode']) 
				->save();
				
			$update = OrderAddressQuery::create()
				->findPK($event->getOrder()->getDeliveryOrderAddressId())
				->setCompany($_SESSION['IciRelaiscompany'])
				->setAddress1($_SESSION['IciRelaisaddress1'])
				->setAddress2($_SESSION['IciRelaisaddress2'])
				->setAddress3($_SESSION['IciRelaisaddress3'])
				->setZipcode($_SESSION['IciRelaiszipcode'])
				->setCity($_SESSION['IciRelaiscity'])
				->save();
				
			unset($_SESSION['IciRelaiscode']);
			unset($_SESSION['IciRelaiscompany']);
			unset($_SESSION['IciRelaisaddress1']);
			unset($_SESSION['IciRelaisaddress2']);
			unset($_SESSION['IciRelaisaddress3']);
			unset($_SESSION['IciRelaiszipcode']);
			unset($_SESSION['IciRelaiscity']);
			unset($_SESSION['IciRelaisupdateDeliveryAddress']);
		}
	}

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('isModuleIciRelais', 64),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('updateDeliveryAddress', 256)
        );
    }
}
?>