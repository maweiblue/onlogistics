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

require_once CUSTOM_CONTROLLER_DIR . '/RessourceGrid.php';


/**
 * RessourceGrid
 *
 */
class ProductRessourceGrid extends RessourceGrid
{
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param string $entity nom de l'objet
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        $params['itemsperpage'] = 200;
        $params['title'] = _('Product resources');
        parent::__construct($params);
    }
    
    // }}} 
    // ProductRessourceGrid::getMapping() {{{

    /**
     * Surchargée ici pour retourner un mapping spécifique.
     *
     * @access protected
     * @return array
     */
    protected function getMapping() {
        return array(
            'Product'=>array(
                'usedby'=>array('grid', 'searchform', 'addedit'),
                'label'=>_('Product'), 
                'shortlabel'=>_('Product'), 
                'required'=>true
            ),
            'Quantity'=>array(
                'usedby'=>array('grid', 'addedit'),
                'label'=>_('Quantity'),
                'shortlabel'=>_('Quantity')
            ),
            'CostType'=>array(
                'usedby'=>array('grid', 'addedit'),
                'label'=>_('Cost type'),
                'shortlabel'=>_('Cost type')
            ),
        );
    }

    // }}}
    // ProductRessourceGrid::getFeatures() {{{

    /**
     * Surchargée ici pour retourner les features spécifiques.
     *
     * @access protected
     * @return array
     */
    protected function getFeatures() {
        return array('searchform', 'grid', 'add', 'edit', 'del');
    }

    // }}}
    // ProductRessourceGrid::getGridFilter() {{{

    /**
     * Surchargée ici pour n'afficher que les acteurs génériques.
     *
     * @access protected
     * @return array
     */
    protected function getGridFilter() {
        return new FilterComponent(
            new FilterRule(
                'Type',
                FilterRule::OPERATOR_EQUALS,
                Ressource::RESSOURCE_TYPE_PRODUCT
            )
        );
    }

    // }}}
}

?>
