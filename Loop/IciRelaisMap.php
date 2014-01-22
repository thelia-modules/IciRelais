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

use Thelia\Model\AddressQuery;
use Thelia\Core\Template\Loop\Address;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;

use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;

/**
 *
 * Price loop
 *
 *
 * Class Price
 * @package IciRelais\Loop
 * @author Benjamin Perche <bperche9@gmail.com>
 */
class IciRelaisMap extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument("address", 0)
        );
    }
	
	public function buildModelCriteria()
	{
		
		$search = AddressQuery::create();
		
		$address=$this->getAddress();
		if($address) {
			$search->filterById($address);
		}
		return $search;
	}

    public function parseResults(LoopResult $loopResult)
    {
    	foreach($loopResult->getResultDataCollection() as $address) {
    		$loopResultRow = new LoopResultRow();
			$loopResultRow->set("ADDRESS", $address->getAddress1());
			$loopResultRow->set("ZIPCODE", $address->getZipcode());
			$loopResultRow->set("CITY", $address->getCity());
			$loopResult->addRow($loopResultRow);
		}
        return $loopResult;

    }
}
