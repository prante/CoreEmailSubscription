<?php

class ilEmailSubscriber {

    /** @var ilCourseParticipants */
    protected $courseParticipants;

    /** @var string[] */
    protected $emailsFound;

    /** @var string[] */
    protected$emailsNotFound;

    function __construct($ref_id) {
        $this->courseObject = ilObjectFactory::getInstanceByRefId($ref_id);
        $this->courseParticipants = new ilCourseParticipants($this->courseObject->getId($ref_id));      // ??

        $this->emailsFound = array();
        $this->emailsNotFound = array();
    }

    /**
     * @param $string string A string containing some E-Mail addresses
     * @return string[] An array of all E-Mail addresses found in the given string.
     */
    public function getEmailsFromString($emailString)
    {
        preg_match_all("/[A-Za-z0-9_.-]+@[A-Za-z0-9_.-]+\\.[A-Za-z0-9_-][A-Za-z0-9_]+/uismx", $emailString, $matches);
        return $matches[0];



    }

    public function subscribeEmail($email) {
        $user_id = $this->getUserIdByEmail($email);
        if ($user_id) {
            $this->courseParticipants->add($user_id, IL_CRS_MEMBER);
            $this->emailsFound[] = $email;
        } else {
            $this->emailsNotFound[] = $email;
        }
    }

    /**
     * @return string[]
     */
    public function getEmailsFound()
    {
        return $this->emailsFound;
    }

    /**
     * @return string[]
     */
    public function getEmailsNotFound()
    {
        return $this->emailsNotFound;
    }

    public function getUserIdByEmail($email) {
        global $ilDB;

        $query = "SELECT * FROM usr_data WHERE email LIKE ".$ilDB->quote($email,'text');
        $result = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($result)) {
            return $row['usr_id'];
        }
        return false;
    }

}

?>