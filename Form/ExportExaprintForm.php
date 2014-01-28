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


use Thelia\Form\BaseForm;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;
use IciRelais\Controller\ExportExaprint;

/**
 * Class ExportExaprintForm
 * @package IciRelais\Form
 * @author benjamin perche <bperche9@gmail.com>
 */
class ExportExaprintForm extends BaseForm {
	
	public function getName() {
		return "exportexaprintform";
	}

	protected function buildForm() {
		// Add value(s) if Config/exportdat.json exists
		
		if(file_exists(ExportExaprint::getJSONpath())) {
			$values = json_decode(file_get_contents(ExportExaprint::getJSONpath()),true);
		}
		$this->formBuilder
			->add('name', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s name'),
				'data' => (isset($values['name']) ? $values['name']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'name'
				)			))
			->add('addr', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s address1'),
				'data' => (isset($values['addr']) ? $values['addr']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'addr'
				)			))
			->add('addr2', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s address2'),
				'data' => (isset($values['addr2']) ? $values['addr2']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'addr2'
				)			))
			->add('zipcode', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s zipcode'),
				'data' => (isset($values['zipcode']) ? $values['zipcode']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'zipcode'
				)			))
			->add('city', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s city'),
				'data' => (isset($values['city']) ? $values['city']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'city'
				)			))
			->add('tel', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s phone'),
				'data' => (isset($values['tel']) ? $values['tel']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'tel'
				)			))
			->add('mobile', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s mobile phone'),
				'data' => (isset($values['mobile']) ? $values['mobile']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'mobile'
				)			))
			->add('mail', 'text', array(
				'label' => Translator::getInstance()->trans('Sender\'s email'),
				'data' => (isset($values['mail']) ? $values['mail']:""),
				'constraints' => array(new NotBlank()),
				'label_attr' => array(
					'for' => 'mail'
				)			))
			->add('assur', 'checkbox', array(
				'label' => Translator::getInstance()->trans('Package warranty'),
				'value' => (isset($values['assur']) ? $values['assur']:""),
				'label_attr' => array(
					'for' => 'assur'
				)			))
		;
	}
}
?>