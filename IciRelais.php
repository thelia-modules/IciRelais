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

namespace IciRelais;

use IciRelais\Model\IcirelaisFreeshippingQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Exception\OrderException;
use Thelia\Module\BaseModule;
use Thelia\Model\ModuleQuery;
use Thelia\Module\DeliveryModuleInterface;
use Thelia\Model\Country;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;

class IciRelais extends BaseModule implements DeliveryModuleInterface
{
    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    protected $request;
    protected $dispatcher;

    private static $prices = null;

    const JSON_PRICE_RESOURCE = "/Config/prices.json";

    public function postActivation(ConnectionInterface $con = null)
    {
        $this->getModuleModel()
            ->setLocale('fr_FR')
            ->setTitle("Livraison en point ICI relais par Exapaq")
            ->setChapo("Livraison 24/ 48H parmi 5 000 relais en France")
            ->setDescription("Choisissez la livraison sans contrainte avec ICI relais !

Faites-vous livrer dans l’un de nos 5 000 commerces de proximité disponibles sur toute la France.  Vous avez la liberté de choisir le commerce qui vous convient le mieux : près de chez vous ou encore près de votre travail, et d’aller y retirer votre colis 7/7 jours (*).

Dès que votre colis est livré dans l’espace ICI relais préalablement choisi, vous êtes automatiquement prévenus par email ou SMS de sa disponibilité.
Si vous ne pouvez pas le récupérer le jour même, pas de souci, votre colis reste disponible jusqu’à 9 jours !

ICI relais c’est l’assurance d’une livraison de qualité avec :
\t- Une livraison toute France en 24/48 H
\t- Un choix parmi plus de 5 000 commerces répartis sur toute la France et rigoureusement sélectionnés  (situation géographique, capacité de stockage, horaires d’ouverture…)
\t- Un suivi détaillé (tracing) de votre colis disponible 24/24H sur www.icirelais.com
\t- Une alerte (email / SMS) dès l’arrivée de votre colis dans l’espace ICI relais
\t- Des outils innovants en phase avec la tendance de mobilité : des applications ICI relais pour Iphone et Android.")
        ->save();

        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));
    }

    public static function getPrices()
    {
        if (null === self::$prices) {
            if (is_readable(sprintf('%s/%s', __DIR__, self::JSON_PRICE_RESOURCE))) {
                self::$prices = json_decode(file_get_contents(sprintf('%s/%s', __DIR__, self::JSON_PRICE_RESOURCE)), true);
            } else {
                self::$prices = null;
            }

        }

        return self::$prices;
    }

    public static function getPostageAmount($areaId, $weight)
    {
        $freeshipping = IcirelaisFreeshippingQuery::create()->getLast();
        $postage=0;
        if(!$freeshipping) {
            $prices = self::getPrices();

            /* check if IciRelais delivers the asked area */
            if (!isset($prices[$areaId]) || !isset($prices[$areaId]["slices"])) {
                throw new OrderException("Ici Relais delivery unavailable for the chosen delivery country", OrderException::DELIVERY_MODULE_UNAVAILABLE);
            }

            $areaPrices = $prices[$areaId]["slices"];
            ksort($areaPrices);

            /* check this weight is not too much */
            end($areaPrices);
            $maxWeight = key($areaPrices);
            if ($weight > $maxWeight) {
                throw new OrderException(sprintf("Ici Relais delivery unavailable for this cart weight (%s kg)", $weight), OrderException::DELIVERY_MODULE_UNAVAILABLE);
            }

            $postage = current($areaPrices);

            while (prev($areaPrices)) {
                if ($weight > key($areaPrices)) {
                    break;
                }

                $postage = current($areaPrices);
            }
        }

        return $postage;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function getPostage(Country $country)
    {
        $cartWeight = $this->getContainer()->get('request')->getSession()->getCart()->getWeight();

        $postage = self::getPostageAmount(
            $country->getAreaId(),
            $cartWeight
        );

        return $postage;
    }

    public function getCode()
    {
        return "Icirelais";
    }

    public static function getModCode()
    {
        $mod_code = "IciRelais";
        $search = ModuleQuery::create()
            ->findOneByCode($mod_code);

        return $search->getId();
    }
}
