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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Model\ModuleQuery;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\Order\OrderAddressEvent;
use Thelia\Core\Event\TheliaEvents;


use Thelia\Model\OrderAddressQuery;
/**
 * Class SetDeliveryModule
 * @package IciRelais\Listener
 * @author benjamin perche <bperche9@gmail.com>
 */
class SetDeliveryModule extends BaseAction implements EventSubscriberInterface
{
	protected function check_module($event) {
		$mod_code = "IciRelais";
    	$search = ModuleQuery::create()
			->findOneByCode($mod_code);
		$icirelaiskey = $search->getId();
		return $event->getDeliveryModule() == $icirelaiskey;
	}
		
    public function isModuleIciRelais(OrderEvent $event)
    {
        if($this->check_module($event)) {
        	// Using raw data : correct me if i'm wrong
        	if(isset($_POST['pr_code']) && !empty($_POST['pr_code'])) {
        		// SOAP a response
        		$con = new \SoapClient(__DIR__."/../Config/exapaq.wsdl", array('soap_version'=>SOAP_1_2));
				$response = $con->GetPudoDetails(array("pudo_id"=>$_POST['pr_code']));
				$xml = new \SimpleXMLElement($response->GetPudoDetailsResult->any);
				if(isset($xml->ERROR)) {
					throw new \ErrorException("Error while choosing pick-up & go store: ".$xml->ERROR);
				}
				
				$sess=$this->container->get('request')->getSession();
				
				$sess->set('IciRelais.company',(string)$xml->PUDO_ITEMS->PUDO_ITEM->NAME);
				$sess->set('IciRelais.address1',(string)$xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS1);
				$sess->set('IciRelais.address2',(string)$xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS2);
				$sess->set('IciRelais.address3',(string)$xml->PUDO_ITEMS->PUDO_ITEM->ADDRESS3);
				$sess->set('IciRelais.zipcode',(string)$xml->PUDO_ITEMS->PUDO_ITEM->ZIPCODE);
				$sess->set('IciRelais.city',(string)$xml->PUDO_ITEMS->PUDO_ITEM->CITY);
				$sess->set('IciRelais.updateDeliveryAddress',"true");
        	} else {
        		throw new \ErrorException("No pick-up & go store choosed for IciRelais delivery module");
        	}
        }
    }

	public function updateDeliveryAddress(OrderEvent $event) {
		$sess=$this->container->get('request')->getSession();
		if(!empty($sess->get('IciRelais.updateDeliveryAddress'))) {
			if(empty($sess->get('IciRelais.address1')) ||
				empty($sess->get('IciRelais.city')) ||
				empty($sess->get('IciRelais.zipcode')) ||
				empty($sess->get('IciRelais.company'))
				) {
					throw new \ErrorException("Got an error with IciRelais module. Please try again to checkout.");
				}
			$addr_to_update = OrderAddressQuery::create()->findPK($event->getOrder()->getDeliveryOrderAddressId());
			$addr_to_update->setCompany($sess->get('IciRelais.company'))
				->setAddress1($sess->get('IciRelais.address1'))
				->setAddress2($sess->get('IciRelais.address2'))
				->setAddress3($sess->get('IciRelais.address3'))
				->setZipcode($sess->get('IciRelais.zipcode'))
				->setCity($sess->get('IciRelais.city'))
				->save();
			$sess->set('IciRelais.company',"");
			$sess->set('IciRelais.address1',"");
			$sess->set('IciRelais.address2',"");
			$sess->set('IciRelais.address3',"");
			$sess->set('IciRelais.zipcode',"");
			$sess->set('IciRelais.city',"");
			$sess->set('IciRelais.updateDeliveryAddress',"");
			
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