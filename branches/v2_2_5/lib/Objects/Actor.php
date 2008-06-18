<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a generated file, please do not edit.
 *
 * This file is part of onlogistics application.
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com>
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   CVS: $Id$
 * @link      http://www.onlogistics.com
 * @link      http://www.onlogistics.org
 * @since     File available since release 0.1.0
 * @filesource
 * $Source: /home/cvs/codegen/codegentemplates.py,v $
 */

/**
 * Actor class
 *
 * Classe contenant des m�thodes additionnelles
 */
class Actor extends _Actor {
    // Constructeur {{{

    /**
     * Actor::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    /**
     * this is an alias
     *
     * @return boolean
     */
    public function isGeneric()
    {
        return $this->GetGeneric();
    }

    /**
     * this is an alias
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->getActorCollection();
    }

    /**
     * this is an alias
     *
     * @return boolean
     */
    public function hasChildren()
    {
        $col = $this->getActorCollection();
        return ($col > 0);
    }

    /**
     * Methode addon qui recupere les OT qui ont 1 operation liee a une commande
     * passee par l'actor : utilise pour les UserAccount::PROFILE_ADMIN_VENTES
     *
     * @access public
     * @return Array
     */
    public function GetWorkOrderCollection()
    {
        require_once('SQLRequest.php');
        $SQLRequest = Request_WorkOrderList($this->GetId());
        $rs = ExecuteSQL($SQLRequest); // execution de la requete
        $WorkOrderArrayId = array();
        while (!$rs->EOF) {
            $WorkOrderArrayId[] = (int)$rs->fields['wkoId'];
            $rs->MoveNext();
        }
        return $WorkOrderArrayId;
    }

    /**
     * retourne le SupplierCustomer associ� � l'acteur et � l'acteur de
     * l'utilisateur connect�.
     *
     * @access public
     * @return SupplierCustomer
     */
    public function getSupplierCustomer(){
        require_once('Objects/SupplierCustomer.php');
        $auth = Auth::Singleton();
        $actor = $auth->getActor();
        $actorConnectedId = $actor->getId();
        $mapper = Mapper::singleton('SupplierCustomer');
        if ($actor instanceof Customer || $actor instanceof AeroCustomer) {
            $spc = $mapper->load(array('Customer'=>$this->getId()));
        } else {
            $spc = $mapper->load(array('Supplier'=>$actorConnectedId,
                'Customer'=>$this->getId()));
            if (Tools::isException($spc)) {
                $spc = $mapper->load(array('Customer'=>$actorConnectedId,
                          'Supplier'=>$this->getId()));
            }
        }
        if (Tools::isException($spc)) {
            $spc = new SupplierCustomer();
        }
        return $spc;
    }

    /**
     * Retourne La collection des Clients
     * Utilise une requete sql pour les perfs
     * @return ActorCollection Content of value
     * @access public
     */
    public function GetCustomerCollection() {
        require_once('SQLRequest.php');
        $ActorMapper = Mapper::singleton('Actor');
        $CustomerCollectionIds = GetCustomerCollectionIds($this->GetId());
        $CustomerCollection = $ActorMapper->loadCollection(
            array('Id'=>$CustomerCollectionIds));

        return $CustomerCollection;
    }

    /**
     * Retourne La collection des Clients
     * Utilise une requete sql pour les perfs
     * @return ActorCollection Content of value
     * @access public
     */
    public function GetSupplierCollection() {
        require_once('SQLRequest.php');
        $ActorMapper = Mapper::singleton('Actor');
        $SupplierCollectionIds = GetSupplierCollectionIds($this->GetId());
        $SupplierCollection = $ActorMapper->loadCollection(
            array('Id'=>$SupplierCollectionIds));
        return $SupplierCollection;
    }

    /**
     * AeroActor::getWeeklyPlanning()
     * Retourne le planning semaine de l'acteur ou d�clenche une erreur
     *
     * @access public
     * @return WeeklyPlanning
     */
    public function getWeeklyPlanning(){
        $site = $this->getMainSite();
        if (!($site instanceof Site)) {
            trigger_error(get_class($this) . '::getPlanning(): ' .
                sprintf(_('No site assigned to actor %s'), $this->getName()) .
                ' ' . _('Please correct.'), E_USER_ERROR);
        }
        $wplanning = $site->getPlanning();
        if (!($wplanning instanceof WeeklyPlanning)) {
            trigger_error(get_class($this) . '::getPlanning(): ' .
                sprintf(_('No planning assigned to site %s'), $site->getName()) .
                ' ' . _('Please correct.'), E_USER_ERROR);
        }
        return $wplanning;
    }

    /**
     * Retourne true si l'acteur peut �tre supprim� et false sinon.
     *
     * @access public
     * @return boolean
     */
    public function isDeletable(){
        require_once('SQLRequest.php');
        return request_actorIsDeletable($this->getId());
    }

    /**
     * Retourne true si l'acteur est fournisseur principal d'au moins un Product
     * non desactive. S'applique a Supplier et AeroSupplier.
     *
     * @param boolean $returnProductColl true s'il faut retourner la coll des
     * Products concernes
     * @access public
     * @return mixed boolean or collection of Products
     */
    public function isMainSupplier($returnProductColl=false) {
        if (!($this instanceof Supplier || $this instanceof AeroSupplier)) {
            return false;
        }
        $mapper = Mapper::singleton('ActorProduct');
        $coll = $mapper->loadCollection(
                array('Actor' => $this->getId(), 'Priority' => 1, 'Product.Activated' => 1),
                array(),
                array('Product'));

        if (!$returnProductColl) {
            return !($coll->getCount() == 0);
        }
        $pdtcoll = new Collection();
            foreach($coll as $actorProduct) {
                $pdtcoll->setItem($actorProduct->getProduct());
            }
        return $pdtcoll;
    }

    /**
     * Supprime les liens Product-MainSupplier, c'est a dire met a jour
     * les ActorProduct.Priority a 0
     * A appeler dans une transaction.
     *
     * @access public
     * @return mixed boolean or collection of Products
     */
    public function removeMainSupplierLinks() {
        $mapper = Mapper::singleton('ActorProduct');
        // Pas de lazy loading ici, pour pouvoir MAJ les ActorProduct
        $coll = $mapper->loadCollection(
                array('Actor' => $this->getId(), 'Priority' => 1, 'Product.Activated' => 1));
        foreach($coll as $actorProduct) {
            $actorProduct->setPriority(0);
            saveInstance($actorProduct);
        }
        return $coll;
    }

    /**
     * Retourne le 1er site de facturation trouve, oubien
     * le 1er site de facturation-livraison trouve, oubien le MainSite
     * @access public
     * @return Site
     */
    public function getInvoicingSite(){
        $InvoicingSite = false;
        $SiteCollection = $this->getSiteCollection(array('Type' => Site::SITE_TYPE_FACTURATION));
        if (!Tools::isEmptyObject($SiteCollection)) {
            $InvoicingSite = $SiteCollection->getItem(0);  // on prend le 1er trouve
        }
        else {
            unset($SiteCollection);
            $SiteCollection = $this->getSiteCollection(array('Type' => Site::SITE_TYPE_FACTURATION_LIVRAISON));
            if (!Tools::isEmptyObject($SiteCollection)) {
                $InvoicingSite = $SiteCollection->getItem(0);  // on prend le 1er trouve
            }
            else {
                $InvoicingSite = $this->getMainSite();
            }
        }
        return $InvoicingSite;
    }

    /**
     * Retourne une string a inserer en debut d'adresse
     *
     * @access public
     * @return string
     */
    public function getQualityForAddress() {
        $qualityArray = $this->getQualityConstArray();
    	$return = ($this->getQuality() == Actor::QUALITY_NONE)?
                '':$qualityArray[$this->getQuality()] . ' ';
        return $return;
    }

    /**
     * Fonction appel�e apr�s import de donn�es via glao-import.
     * Appel�e par le script d'import xmlrpc.
     *
     * @access public
     * @param  array $params un tableau de param�tres optionnel
     * @return boolean
     */
    public function onAfterImport($params = array()) {
        // � l'import on renseigne dans le mainsite de l'acteur le Owner avec
        // l'acteur en cours.
        $mainsite = $this->getMainSite();
        if (!($mainsite instanceof Site) || $mainsite->getOwnerID() > 0) {
            // pas de site trouv� ou owner d�j� renseign�
            return false;
        }
        $mainsite->setOwner($this);
        $mainsite->save();
        return true;
    }

    /**
     * G�re le logo de l'acteur:
     *    - le converti en png base 64
     *    - l'assigne � l'acteur
     *
     * XXX il faudrait utiliser le Upload du fw plut�t...
     * @access public
     * @param string $inputname le nom du widget file input
     * @param int    $width     la largeur maxi
     * @param int    $height    la hauteur maxi
     * @return mixed boolean ou Exception
     */
    public function setLogoFromFileInput($inputName, $width=260, $height=80) {
		$validTypes = array('png', 'jpeg', 'gif');
        // checks pr�alables
        if (!isset($_FILES[$inputName]) || empty($_FILES[$inputName]['name'])) {
            return false;
        }
        if(!isset($_FILES[$inputName])) {
            return false;
        }
        if($_FILES[$inputName]['error'] > UPLOAD_ERR_OK) {
            return new Exception($_FILES[$inputName]['error']);
        }
        $name_ext = explode('.', $_FILES[$inputName]['name']);
        $ext = strtolower(array_pop($name_ext));
        if($ext == 'jpg') {
            $ext = 'jpeg';
        }
		if (!in_array($ext, $validTypes)) {
            return new Exception(sprintf(E_UPLOAD_UNSUPPORTED_EXTENSION,
                    $_FILES['name'], implode(', ', $validTypes)));
		}
        $createfunc = 'imagecreatefrom' . $ext;
        // redimensionnement
        $source = $_FILES[$inputName]['tmp_name'];
        $darray = getimagesize($source);
        list($w, $h) = $darray;
        $thumb  = imagecreatetruecolor($w, $h);
        $image = $createfunc($source);
        imagealphablending($thumb, false);
        imagecopyresized($thumb, $image, 0, 0, 0, 0, $w, $h, $w, $h);
        imagesavealpha($thumb, true);
        ob_start();
        imagepng($thumb);
        $data = ob_get_contents();
        ob_end_clean();
        $this->setLogo(base64_encode($data));
        return true;
    }

    /**
     * Actor::isAvailableFor()
     * Retourne true si l'acteur est disponible pour le cr�neau $start-$end
     *
     * @param integer timestamp $start
     * @param integer timestamp $end
     * @access public
     * @return boolean
     */
    public function isAvailableFor($start, $end){
        require_once('PlanningTools.php');
        $wplanning = $this->getWeeklyPlanning();
        $ptools = new PlanningTools($wplanning);
        $range = $ptools->GetNextAvailableRange($start);

        if (is_array($range) && count($range) == 2 &&
            $range['Start'] == $start && $range['End'] > $end) {
            $unavailabilities = $wplanning->getUnavailabilityCollection();
            $count = $unavailabilities->getCount();
            for($i = 0; $i < $count; $i++){
                $unavail = $unavailabilities->getItem($i);
                $istart = DateTimeTools::MysqlDateToTimeStamp(
                    $unavail->getBeginDate());
                $iend = DateTimeTools::MysqlDateToTimeStamp(
                    $unavail->getEndDate());
                if (($istart > $start || $iend > $start) && $istart < $end) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Actor::getNextMeetingAction()
     * Retourne la 1ere Action de type MEETING a l'etat A FAIRE, trouvee
     * chronologiquement (sa date peut etre passee)
     * Retourne false si l'acteur est ni Customer, ni AeroCustomer
     *
     * @access public
     * @return object Action ou false
     */
    public function getNextMeetingAction() {
        if (!in_array(get_class($this), array('Customer', 'AeroCustomer'))) {
            $action = false;
            return $action;
        }
        require_once('Objects/FormModel.php');  // contient FormModel::ACTION_TYPE_MEETING
        $actionMapper = Mapper::singleton('Action');
        $actionColl = $actionMapper->loadCollection(
                array('State' => Action::ACTION_STATE_TODO,
                      'Actor' => $this->getId(),
                      'Type' => FormModel::ACTION_TYPE_MEETING),
                array('WishedDate' => SORT_ASC));

        if (Tools::isEmptyObject($actionColl)) {
            $action = false;
        }
        else {
            $action = $actionColl->getItem(0);
        }
        return $action;
    }

    /**
     * Actor::getLastProductIdsOrdered()
     * Retourne les ids des derniers Products command�s, lors de commandes dont
     * $this est l'Expeditor, et $destinator le Destinator
     *
     * @access public
     * @param object $destinator
     * @return array of integer
     */
    public function getLastProductIdsOrdered($destinator) {
        $pdtIds = array();

        $mapper = Mapper::singleton('ProductCommand');
        $commandColl = $mapper->loadCollection(
                array('Expeditor' => $this->getId(),
                      'Destinator' => $destinator->getId()),
                array('CommandDate' => SORT_DESC),
                array('Id'), 1, 1, 1);

        if (Tools::isEmptyObject($commandColl)) {
            return $pdtIds;
        }
        $cmd = $commandColl->getItem(0);
        $pdtCommandItemColl = $cmd->getCommandItemCollection(
                array(), array(), array('Product'));
        $count = $pdtCommandItemColl->getCount();
        for($i = 0; $i < $count; $i++) {
            $pdtIds[] = $pdtCommandItemColl->getItem($i)->getProductId();
        }
        return $pdtIds;
    }

    /**
     * Actor::getMiniAmountToOrder($currency)
     * Retourne le minimum HT qu'il est possible de commander
     * Va chercher l'info dans Actor.Category.MiniAmountToOrderCollection
     * oubien retourne 0
     *
     * @access public
     * @param object $currency
     * @return float
     */
    public function getMiniAmountToOrder($currency) {
        $categoryId = $this->getCategoryId();
        // Pas de Category associee
        if ($categoryId == 0) {
            return 0;
        }

        $mapper = Mapper::singleton('MiniAmountToOrder');
        $mato = $mapper->load(
                array('Currency' => $currency->getId(),
                      'Category' => $categoryId),
                array('Amount'));

        if (Tools::isEmptyObject($mato)) {
            return 0;
        }
        
        return $mato->getAmount();
    }
}

?>