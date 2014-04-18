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

namespace IciRelais\Form;

use IciRelais\IciRelais;
use Thelia\Form\BaseForm;
use Thelia\Core\Translation\Translator;
use Thelia\Model\OrderQuery;

/**
 * Class ExportExaprintSelection
 * @package IciRelais\Form
 * @author Thelia <info@thelia.net>
 */
class ExportExaprintSelection extends BaseForm
{
    public function getName()
    {
        return "exportexaprintselection";
    }

    protected function buildForm()
    {
        $entries = OrderQuery::create()
            ->filterByDeliveryModuleId(IciRelais::getModuleId())
            ->find();
        $this->formBuilder
            ->add('new_status_id', 'choice',array(
                    'label' => Translator::getInstance()->trans('server'),
                    'choices' => array(
                        "nochange" => Translator::getInstance()->trans("Do not change"),
                        "processing" => Translator::getInstance()->trans("Set orders status as processing"),
                        "sent" => Translator::getInstance()->trans("Set orders status as sent")
                    ),
                    'required' => 'true',
                    'expanded'=>true,
                    'multiple'=>false,
                    'data'=>'nochange'
                    )
                );
        foreach ($entries as $order) {
            $this->formBuilder
                ->add(str_replace(".","-",$order->getRef()), 'checkbox', array(
                    'label' => str_replace(".","-",$order->getRef()),
                    'label_attr' => array(
                        'for' => str_replace(".","-",$order->getRef())
                    )
                ))
                ->add(str_replace(".","-",$order->getRef())."-assur", 'checkbox')
            ;
        }
    }
}
