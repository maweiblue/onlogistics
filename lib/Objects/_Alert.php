<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
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
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class _Alert extends Object {
    
    // Constructeur {{{

    /**
     * _Alert::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name i18n_string property + getter/setter {{{

    /**
     * Name foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Name = 0;

    /**
     * _Alert::getName
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getName($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Name) && $this->_Name > 0) {
            $this->_Name = Object::load('I18nString', $this->_Name);
        }
        $ret = null;
        if ($this->_Name instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Name->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Name->$getter();
            }
        }
        return $ret;
    }

    /**
     * _Alert::getNameId
     *
     * @access public
     * @return integer
     */
    public function getNameId() {
        if ($this->_Name instanceof I18nString) {
            return $this->_Name->getId();
        }
        return (int)$this->_Name;
    }

    /**
     * _Alert::setName
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setName($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Name = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Name = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Name instanceof I18nString)) {
                $this->_Name = Object::load('I18nString', $this->_Name);
                if (!($this->_Name instanceof I18nString)) {
                    $this->_Name = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Name->$setter($value);
            $this->_Name->save();
        }
    }

    // }}}
    // BodyAddon string property + getter/setter {{{

    /**
     * BodyAddon string property
     *
     * @access private
     * @var string
     */
    private $_BodyAddon = '';

    /**
     * _Alert::getBodyAddon
     *
     * @access public
     * @return string
     */
    public function getBodyAddon() {
        return $this->_BodyAddon;
    }

    /**
     * _Alert::setBodyAddon
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBodyAddon($value) {
        $this->_BodyAddon = $value;
    }

    // }}}
    // Template string property + getter/setter {{{

    /**
     * Template string property
     *
     * @access private
     * @var string
     */
    private $_Template = '';

    /**
     * _Alert::getTemplate
     *
     * @access public
     * @return string
     */
    public function getTemplate() {
        return $this->_Template;
    }

    /**
     * _Alert::setTemplate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTemplate($value) {
        $this->_Template = $value;
    }

    // }}}
    // UserAccount one to many relation + getter/setter {{{

    /**
     * UserAccount *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UserAccountCollection = false;

    /**
     * _Alert::getUserAccountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUserAccountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un param�tre est pass� on force le rechargement de la collection
        // on ne met en cache m�moire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Alert');
            return $mapper->getManyToMany($this->getId(),
                'UserAccount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en m�moire on la charge
        if (false == $this->_UserAccountCollection) {
            $mapper = Mapper::singleton('Alert');
            $this->_UserAccountCollection = $mapper->getManyToMany($this->getId(),
                'UserAccount');
        }
        return $this->_UserAccountCollection;
    }

    /**
     * _Alert::getUserAccountCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUserAccountCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getUserAccountCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_UserAccountCollection) {
            $mapper = Mapper::singleton('Alert');
            return $mapper->getManyToManyIds($this->getId(), 'UserAccount');
        }
        return $this->_UserAccountCollection->getItemIds();
    }

    /**
     * _Alert::setUserAccountCollectionIds
     *
     * @access public
     * @return array
     */
    public function setUserAccountCollectionIds($itemIds) {
        $this->_UserAccountCollection = new Collection('UserAccount');
        foreach ($itemIds as $id) {
            $this->_UserAccountCollection->setItem($id);
        }
    }

    /**
     * _Alert::setUserAccountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUserAccountCollection($value) {
        $this->_UserAccountCollection = $value;
    }

    /**
     * _Alert::UserAccountCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function UserAccountCollectionIsLoaded() {
        return ($this->_UserAccountCollection !== false);
    }

    // }}}
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'Alert';
    }

    // }}}
    // getObjectLabel() {{{

    /**
     * Retourne le "label" de la classe.
     *
     * @static
     * @access public
     * @return string
     */
    public static function getObjectLabel() {
        return _('Alerts');
    }

    // }}}
    // getProperties() {{{

    /**
     * Retourne le tableau des propri�t�s.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getProperties() {
        $return = array(
            'Name' => Object::TYPE_I18N_STRING,
            'BodyAddon' => Object::TYPE_HTML,
            'Template' => Object::TYPE_FILE);
        return $return;
    }

    // }}}
    // getLinks() {{{

    /**
     * Retourne le tableau des entit�s li�es.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getLinks() {
        $return = array(
            'UserAccount'=>array(
                'linkClass'     => 'UserAccount',
                'field'         => 'FromAlert',
                'linkTable'     => 'alertUserAccount',
                'linkField'     => 'ToUserAccount',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'AnswerModel'=>array(
                'linkClass'     => 'AnswerModel',
                'field'         => 'Alert',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Question'=>array(
                'linkClass'     => 'Question',
                'field'         => 'Alert',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
        return $return;
    }

    // }}}
    // getUniqueProperties() {{{

    /**
     * Retourne le tableau des propri�t�s qui ne peuvent prendre la m�me valeur
     * pour 2 occurrences.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getUniqueProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getEmptyForDeleteProperties() {{{

    /**
     * Retourne le tableau des propri�t�s doivent �tre "vides" (0 ou '') pour
     * qu'une occurrence puisse �tre supprim�e en base de donn�es.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getEmptyForDeleteProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getFeatures() {{{

    /**
     * Retourne le tableau des "fonctionalit�s" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getFeatures() {
        return array('grid', 'add', 'edit', 'del');
    }

    // }}}
    // getMapping() {{{

    /**
     * Retourne le mapping n�cessaires aux composants g�n�riques.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getMapping() {
        $return = array(
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BodyAddon'=>array(
                'label'        => _('Additional body'),
                'shortlabel'   => _('Additional body'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Template'=>array(
                'label'        => _('or HTML template'),
                'shortlabel'   => _('or HTML template'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>