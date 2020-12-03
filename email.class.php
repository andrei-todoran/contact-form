<?php

class Email {
    protected $fromEmail;
    protected $fromName;
    protected $toEmail;
    protected $toName;
    protected $subject;
    protected $message;

    public function setFromName($name)
    {
        $this->fromName = $name;
    }

    public function setFromEmail($email)
    {
        $this->fromEmail = $email;
    }

    public function setToEmail($email)
    {
        $this->toEmail = $email;
    }

    public function setToName($name)
    {
        $this->toName = $name;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setMessage($parameters)
    {
        $this->message = <<<MESSAGE
Name: {$parameters['name']}
Email: {$parameters['email']}
Phone: {$parameters['phone']}
IP address: {$parameters['ip_address']}
Date: {$parameters['date']}

{$parameters['message']}
MESSAGE;
    }

    public function sendMail()
    {
        return mail($this->toEmail, $this->subject, $this->message);
    }


}