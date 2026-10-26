







<?php
// Parent Class
class User {
    public $username;
    public $role;

    public function __construct($username, $role) {
        $this->username = $username;
        $this->role = $role;
    }

    public function login() {
        return $this->username . " logged in successfully.";
    }
}

// Child Class inheriting from User class
class Admin extends User {
    
    public function manageInventory() {
        return "Admin has permission to add, edit, and delete laboratory items.";
    }
}

// Creating an instance of the Admin class
$adminUser = new Admin("LabManager", "Administrator");

// Utilizing inherited and child-specific methods
echo $adminUser->login();             // Inherited from User class
echo $adminUser->manageInventory();   // Native to Admin class
?>