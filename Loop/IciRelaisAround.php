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
 * Class IciRelaisAround
 * @package IciRelais\Loop
 * @author Benjamin Perche <bperche9@gmail.com>
 */
class IciRelaisAround extends BaseLoop implements PropelSearchLoopInterface
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
	    	$dateliv = date('d/m/Y');
	        $requestID = 1234;
			/*
	        $ville = str_replace(" ", "%", $ville);
	        $adresse = str_replace(" ", "%", $adresse);
			*/
			try {
				ini_set("soap.wsdl_cache_enabled", 0);
	    		$getPudoSoap = new \SoapClient(__DIR__."/../Config/exapaq.wsdl", array('soap_version'   => SOAP_1_2));
				var_dump($getPudoSoap->GetPudoList(array("GetPudoList"=>array("zipCode" => "63000"))));
				die();
			} catch(SoapFault $e) {
				$stderr = fopen("php://stderr", 'w');
				fprintf($stderr, "[%s %s - SOAP Error %d]: %s\n", $dateliv, date("H:i:s"),(int)$e->getCode(), (string)$e->getMessage());
				fclose($stderr);
			}
			
			$loopResult->addRow($loopResultRow);
		}
		return $loopResult;
    }
}
