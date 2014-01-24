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

/**
 * Class CommentaireListener
 * @package Commentaire\Listener
 * @author manuel raynaud <mraynaud@openstudio.fr>
 */
class CommentaireListener extends BaseAction implements EventSubscriberInterface
{

    public function isModuleIciRelais(OrderEvent $event)
    {
    	$mod_code = "IciRelais";
    	$search = ModuleQuery::create()
			->findOneByCode($mod_code);
		$icirelaiskey = $search->getId();
        if($event->getDeliveryModule() == $icirelaiskey) {
        	// Using raw data : correct me if i'm wrong
        	if(isset($_POST['pr_code'])) {
        		
        	} else {
        		throw new \ErrorException("No pick-up & go store choosed for IciRelais module");
        	}
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
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('isModuleIciRelais', 64)
        );
    }
}

?>