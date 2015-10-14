<?php

/**
 * Class UIExample
 * @ilCtrl_isCalledBy ilCourseEmailSubscriptionGUI : ilUIPluginRouterGUI
 */

class ilCourseEmailSubscriptionGUI {

    /**
     * @var ilCtrl
     */
    protected $ctrl;
    protected $course;
    protected $tpl;
    protected $tabs;

    function __construct() {
        global $tpl, $ilCtrl, $ilTabs;
        $this->ctrl = $ilCtrl;
        $this->tpl = $tpl;
        $tpl->getStandardTemplate();
        $this->course = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->ctrl = $ilCtrl;
        $this->tabs = $ilTabs;

    }

    function executeCommand() {
        global $tpl, $ilCtrl, $ilLocator, $ilAccess;

        // Rechteprüfung
        if(!$ilAccess->checkAccess('write', '', $_GET['ref_id'])){
            ilUtil::sendFailure("Access Denied!");
            return;
        }

        $cmd = $this->ctrl->getCmd();

        $this->buildheader($ilLocator);

        //echo $cmd;
        //exit;

        switch($cmd) {
            case 'show':
                $this->show();                                  // Falls $cmd "view" ist, führe Funktion view() aus.
                break;
            case 'save':
                $this->save();                               // Falls $cmd "goodbye" ist, führe Funktion goodbye() aus.
                break;
        }

        $tpl->show();

    }

    function show() {
        global $tpl, $ilCtrl, $ilLocator, $ilAccess;
        $this->ctrl = $ilCtrl;

        $form = $this->buildform($tpl);

        //$this->tpl->setContent($form->getHTML() . "Speichern<br />" . "<a href='" . $this->ctrl->getLinkTarget($this, 'save') . "'>hier</a>");
        $this->tpl->setContent($form->getHTML());
    }

    private function save() {
        global $tpl, $ilCtrl;

        $form = $this->BuildForm();         // Formular bauen
        // Eingaben prüfen (Abhängig von SetRequired)
        if($form->CheckInput()) {
            $form->setValuesByPost();       // Lade die Benutzereingaben
            $emails = $form->getInput('emails');        // Speichere die E-Mails in eine Variable

            require_once("Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/UIExample/classes/class.ilEmailSubscriber.php");
            $subscriber = new ilEmailSubscriber($_GET['ref_id']);

            $emails = $subscriber->getEmailsFromString($emails);

            foreach($emails as $email) {
                $subscriber->subscribeEmail($email);
            }

            //var_dump($subscriber->getEmailsFromString($emails));
            //exit;

            //$emails_untereinander = "";
            //$eintremails_untereinander = ;

            ilUtil::sendSuccess("Die Nutzer folgender E-Mail-Adressen sind jetzt Kursmitglieder: ".$this->werteuntereinander($subscriber->getEmailsFound()), true);
            ilUtil::sendInfo("Die Nutzer folgender E-Mail-Adressen konnten nicht gefunden werden: ".$this->werteuntereinander($subscriber->getEmailsNotFound()), true);

            $this->ctrl->redirect($this, 'show');
            /*
            $ausgabestr .= "Die Nutzer folgender E-Mail-Adressen sind jetzt Kursmitglieder: ".$this->werteuntereinander($subscriber->getEmailsFound());
            $ausgabestr .= "<br /><br /><br />Die Nutzer folgender E-Mail-Adressen konnten nicht gefunden werden: ".$this->werteuntereinander($subscriber->getEmailsNotFound());
            $this->tpl->setContent($ausgabestr);    //Zeige die E-Mails im Content an
            */
        }
        else {
            $this->tpl->setContent("Nicht Speichern");
        }

    }

    /**
     * @param $ilLocator
     */

    function buildheader($ilLocator)
    {
        global $tpl, $ilCtrl, $ilLocator, $ilAccess;

    // Wir fügen in den folgenden zwei Zeilen den Locator hinzu. (Breadcrumbs über dem Titel).
        $ilLocator->addRepositoryItems($this->course->getRefId());
        $this->tpl->setLocator($ilLocator->getHTML());

        //var_dump($this->ctrl->getCallHistory());
        $this->tpl->setTitle($this->course->getTitle()); // Der Titel soll der Titel des Kurses sein
        $this->tpl->setDescription($this->course->getDescription()); // Die Beschreibung soll die Beschreibung des Kurses sein.
        $this->tpl->setTitleIcon(ilObject::_getIcon($this->course->getId(), 'big')); // Das Bild soll ein Kurs Icon sein.

        // Wir fügen einen Zurückknopf ein. Dieser soll die Members des Kurses anzeigen
        $this->ctrl->saveParameterByClass('ilObjCourseGUI', 'ref_id'); //Wir müssen die ref_id speichern, damit der Link zum richtigen Kurs zeigt
        $this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass(array(
            'ilRepositoryGUI',
            'ilObjCourseGUI'
        ), 'members'));
    }

    /**
     * @param $tpl
     */
    function buildform($tpl)
    {
        require_once("Services/Form/classes/class.ilPropertyFormGUI.php");
        $form = new ilPropertyFormGUI();
        $form->setTitle("Mitglieder einschreiben");
        $form->setDescription("Bitte geben Sie eine durch Kommata getrennte Liste von E-Mail-Adressen an.");

        require_once("Services/Form/classes/class.ilTextAreaInputGUI.php");
        $textarea = new ilTextAreaInputGUI('E-Mail-Adresse', 'emails');
        $textarea->setRequired(true);
        $textarea->setRows(20);

        $form->addItem($textarea);
        //$form->addCommandButton('TODO', 'Speichern');

        $this->ctrl->saveParameter($this, 'ref_id');
        $form->addCommandButton('save', 'Speichern');
        $form->setFormAction($this->ctrl->getFormAction($this));

        return $form;
    }

    /**
     * @param $emails
     * @param $eintremails_untereinander
     * @return string
     */
    private function werteuntereinander($emails, $eintremails_untereinander)
    {
        foreach ($emails as $aktemail) {
            $eintremails_untereinander .= " <br />" . $aktemail;
        }
        return $eintremails_untereinander;
    }

}