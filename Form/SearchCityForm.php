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

namespace IciRelais\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;
use Thelia\Core\Translation\Translator;

class SearchCityForm extends BaseForm {
	public function getName() {
		return "searchcityform";
	}

	protected function buildForm() {
		$this->formBuilder
			->add('city', 'text', array(
				'label' => Translator::getInstance()->trans('city'),
				'label_attr' => array(
					'for' => 'city'
				),
				'constraints' => array(
					new NotBlank()
				)
			))
			->add('postal', 'text', array(
				'label' => Translator::getInstance()->trans('postal'),
				'label_attr' => array(
					'for' => 'postal'
				),
				'constraints' => array(
					new NotBlank()
				)
			))
			->add('pr_choice', 'radio', array(
				'constraints' => array( new NotBlank())
			))	
		;
	}
}
?>
