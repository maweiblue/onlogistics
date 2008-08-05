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

require_once('config.inc.php');
require_once('Objects/Task.inc.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_SUPERVISOR,UserAccount::PROFILE_AERO_OPERATOR,
    UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_AERO_CUSTOMER)
);

// pour conserver les crit�res de recherche dans ACKList
SearchTools::ProlongDataInSession();

$errorMsg = _('Please select a task.');
$returnURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActivatedChainTaskList.php';

if (!isset($_REQUEST['ackId'])) {
    Template::errorDialog($errorMsg, $returnURL);
    exit;
}
$ack = Object::Load('ActivatedChainTask', $_REQUEST['ackId']);
if (Tools::isEmptyObject($ack)) {
    Template::errorDialog($errorMsg, $returnURL);
    exit;
}

$tskID = $ack->getTaskId();
if ($tskID == TASK_FLY_PREPARATION) {
    Tools::redirectTo('FlightPreparationEdit.php?ackId=' . $_REQUEST['ackId'] .
        '&retURL=' . $returnURL);
	exit;
}
if ($tskID == TASK_FLY) {
    Tools::redirectTo('FlightEdit.php?ackId=' . $_REQUEST['ackId'] .
        '&retURL=' . $returnURL);
	exit;
}
if ($tskID == TASK_ASSEMBLY || $tskID == TASK_SUIVI_MATIERE) {
    Tools::redirectTo('AssemblyEdit.php?ackId=' . $_REQUEST['ackId'] .
        '&retURL=' . $returnURL);
	exit;
}
if (isProductionTask($ack)) {
    Tools::redirectTo('ProductionTaskValidation.php?ackId=' . $_REQUEST['ackId'] .
        '&retURL=' . $returnURL);
	exit;
}

Tools::redirectTo($returnURL);

?>