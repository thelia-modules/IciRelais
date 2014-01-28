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

namespace IciRelais\Controller;
use IciRelais\IciRelais;
use Thelia\Model\AreaQuery;
use Thelia\Controller\Admin\BaseAdminController;

/**
 * Class EditPrices
 * @package IciRelais\Controller
 * @author benjamin perche <bperche9@gmail.com>
 */
class EditPrices extends BaseAdminController
{
	
	public function editprices()
	{
		// Get raw data & treat
		if( isset($_POST['operation']) && preg_match("#^add|delete$#", $_POST['operation']) &&
			isset($_POST['area']) && preg_match("#^\d+$#", $_POST['area']) &&
			isset($_POST['weight']) && preg_match("#^\d+\.?\d*$#", $_POST['weight'])
		  ) {
		  	// check if area exists in db
		  	$exists = AreaQuery::create()
				->findPK($_POST['area']);
			if($exists !== null) {
				$json_path= __DIR__."/../".IciRelais::JSON_PRICE_RESOURCE;
				$json_data = json_decode(file_get_contents($json_path),true);
				if((float)$_POST['weight'] > 0 && $_POST['operation'] == "add" 
				  && isset($_POST['price']) && preg_match("#\d+\.?\d{0,}#", $_POST['price'])) {
			  		$json_data[$_POST['area']]['slices'][$_POST['weight']] = $_POST['price'];
				} else if($_POST['operation'] == "delete") {
			  		unset($json_data[$_POST['area']]['slices'][$_POST['weight']]);
				} else {
					throw new \Exception("Weight must be superior to 0");
				}
				ksort($json_data[$_POST['area']]['slices']);
				$file = fopen($json_path, 'w');
				fwrite($file, json_encode($json_data));;
				fclose($file);
			} else {
				throw new \Exception("Area not found");
			}
		  } else {
		  	throw new \ErrorException("Arguments are missing or invalid");
		  }
		return $this->redirectToRoute("admin.module.configure",array(),
			array ( 'module_code'=>"IciRelais",  
				'_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction'));
	}
}
