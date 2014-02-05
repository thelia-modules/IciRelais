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

namespace IciRelais\Loop;
use Thelia\Core\Template\Loop\Order;
use IciRelais\IciRelais;
use Thelia\Model\OrderQuery;

/**
 * Class IciRelaisOrders
 * @package IciRelais\Loop
 * @author Thelia <info@thelia.net>
 */
class IciRelaisOrders extends Order
{
    const STATUS_PAID = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_SENT = 4;
    public function buildModelCriteria()
    {
        return OrderQuery::create()
            ->filterByDeliveryModuleId(IciRelais::getModCode())
            ->filterByStatusId(array(self::STATUS_PAID,self::STATUS_PROCESSING));
    }

}
