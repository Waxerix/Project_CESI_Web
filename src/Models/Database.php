<?php
namespace App\Models;
/**
 * This interface represents a database.
 */
interface Database {
    /**
     * Retrieves all records from the database.
     *
     * @return array An array of records.
     */
    public function getAllRole();
    public function insertUser(string $email, string $password, string $role_id);

    
}