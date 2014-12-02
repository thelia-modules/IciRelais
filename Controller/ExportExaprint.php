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
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.     */
/*                                                                                   */
/*************************************************************************************/

namespace IciRelais\Controller;
use IciRelais\Form\ExportExaprintForm;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Translation\Translator;

use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;

/**
 * Class ExportExaprint
 * @package IciRelais\Controller
 * @author Thelia <info@thelia.net>
 */
class ExportExaprint extends BaseAdminController
{
    public static function getJSONpath()
    {
        return __DIR__."/../Config/exportdat.json";
    }
    public function export()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('IciRelais'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = new ExportExaprintForm($this->getRequest());
        $error_message = null;
        try {
            $vform = $this->validateForm($form);
            // After post checks (PREG_MATCH) & create json file & export file
            if(preg_match("#^\d{5}$#",$vform->get('zipcode')->getData()) &&
                preg_match("#^0[1-58-9]{1}\d{8}$#",$vform->get('tel')->getData()) &&
                preg_match("#^0[6-7]{1}\d{8}$#",$vform->get('mobile')->getData()) &&
                preg_match("#^[A-Z0-9\._%\+\-]{2,}@[A-Z0-9\.\-]{2,}\.[A-Z]{2,4}$#i",$vform->get('mail')->getData()) &&
                preg_match("#^\d{8}$#",$vform->get('expcode')->getData())
              ) {
                $file_path = self::getJSONpath();
                if ((file_exists($file_path) ? is_writable($file_path):is_writable(__DIR__."/../Config/"))) {
                    $file = fopen(self::getJSONpath(), 'w');
                    fwrite($file, json_encode(
                                array(
                                    "name"=>$vform->get('name')->getData(),
                                    "addr"=>$vform->get('addr')->getData(),
                                    "addr2"=>$vform->get('addr2')->getData(),
                                    "zipcode"=>$vform->get('zipcode')->getData(),
                                    "city"=>$vform->get('city')->getData(),
                                    "tel"=>$vform->get('tel')->getData(),
                                    "mobile"=>$vform->get('mobile')->getData(),
                                    "mail"=>$vform->get('mail')->getData(),
                                    "expcode"=>($vform->get('expcode')->getData())
                                )
                            )
                        );
                    fclose($file);
                } else {
                    throw new \Exception(Translator::getInstance()->trans("Can't write IciRelais/Config/exportdat.json. Please change the rights on the file and/or the directory."));

                }
              }
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
        }

        $this->setupFormErrorContext(
            'erreur export fichier exaprint',
            $error_message,
            $form
        );
        $this->redirectToRoute("admin.module.configure",array(),
            array ( 'module_code'=>"IciRelais",
                'current_tab'=>"configure_export_exaprint",
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction'));
    }
}
