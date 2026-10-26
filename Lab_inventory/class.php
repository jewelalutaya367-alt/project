<?php
// Parent Class (Magulang na Class)
class User {
    public $username;
    public $password;

    public function login() {
        echo "Naka-log in na ang user.";
    }
}

// Child Class (Anak na Class na nagmamana gamit ang 'extends')
class Admin extends User {
    public function manageInventory() {
        echo "Kaya ng Admin pamahalaan ang Lab Inventory.";
    }
}

// Halimbawa ng paggamit:
$adminObj = new Admin();
$adminObj->username = "admin_josh"; // Nakuha mula sa User class
$adminObj->login();                 // Method mula sa User class
$adminObj->manageInventory();       // Sariling method ng Admin class
?>