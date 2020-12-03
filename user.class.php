<?php

class User {
    protected $db;

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function create($name = "", $email = "", $phone = "", $newsletter = 0)
    {
        $userID = $this->db->insert("INSERT INTO `users`( `name` , `email`, `phone`, `newsletter`) VALUES ( :name , :email, :phone, :newsletter)", [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'newsletter' => $newsletter
        ]);

        return $userID;
    }

    public function update($name = "", $email = "", $phone = "", $newsletter = 0)
    {
        return $this->db->update("UPDATE `users` SET `name`= :name , `phone`= :phone , `newsletter` = :newsletter WHERE `email`= :email", [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'newsletter' => $newsletter
        ]);
    }

    public function exists($email) {
        $result = $this->db->select("SELECT `id` FROM `users` WHERE `email`= :email LIMIT 1", ['email'=>$email]);
        if (empty($result)) {
            return false;
        }

        return true;
    }
}