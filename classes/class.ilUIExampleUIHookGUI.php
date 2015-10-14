<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * User interface hook class
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilUIExampleUIHookGUI extends ilUIHookPluginGUI
{
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;

	public function __construct() {
		global $ilCtrl;
		$this->ctrl = $ilCtrl;
	}

	protected function isINCourseGUI() {

		foreach($this->ctrl->getCallHistory() as $GUIClassesArray) {

		if($GUIClassesArray['class'] == 'ilObjCourseGUI')
			return true;
		}
		return false;
	}

	/**
	 * Modify GUI objects, before they generate ouput
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par array of parameters (depend on $a_comp and $a_part)
	 */
	function modifyGUI($a_comp, $a_part, $contextElements = array())
	{
		// currently only implemented for $ilTabsGUI

		// Zeigt an, in welchem Modul man sich gerade in ILIAS befindet
		/*
		if ($part == "tabs") {
			var_dump($this->ctrl->getCalHistory());
		}
		/**/

		// tabs hook
		// note that you currently do not get information in $a_comp
		// here. So you need to use general GET/POST information
		// like $_GET["baseClass"], $ilCtrl->getCmdClass/getCmd
		// to determine the context.
		//if ($a_part == "tabs")
		if ($a_part == "tabs" && $this->isINCourseGUI())
		{
			// $a_par["tabs"] is ilTabsGUI object
			/** @var ilTabsGUI $tabs */

			$tabs = $contextElements["tabs"];

			$this->ctrl->saveParameterByClass('ilCourseEmailSubscriptionGUI', 'ref_id');		// saveparameterbyclass behält Einstellungsvariablen (ref_id) auch bei neuem Link
			$tabs->addTab('courseSubscription', 'Mitglieder Einschreiben',
				$this->ctrl->getLinkTargetByClass(
					array('ilUIPluginRouterGUI', 'ilCourseEmailSubscriptionGUI'),
					'show'));
			//$tabs->addTab('test', 'test', 'test');

			/*
			$tabs = $contextElements["tabs"];
			$this->ctrl->saveParameterByClass('ilUIExampleUIHookGUI', 'ref_id');
			$tabs->addTab('courseSubscription', 'Mitglieder Einschreiben', $this->ctrl->getLinkTargetByClass(array('ilUIPluginRouterGUI', 'ilUIExampleUIHookGUI'), 'show'));
			*/
		}
	}

}
?>
