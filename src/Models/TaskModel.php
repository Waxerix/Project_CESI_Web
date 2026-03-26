<?php
namespace App\Models;

class TaskModel extends Model
{
    const TODO_STATUS = "todo";
    const DONE_STATUS = "done";

    /**
     * TaskModel constructor.
     *
     * @param mixed $connection The database connection. If null, a new FileDatabase connection will be created.
     */
    public function __construct($connection = null)
    {
        if (is_null($connection)) {
            $this->connection = new FileDatabase();
        } else {
            $this->connection = $connection;
        }
    }

    /**
     * Get all tasks from the model.
     *
     * @return array An array of all tasks.
     */
    public function getSelectForRole()
    {
        return $this->connection->getAllRole();
    }
    public function newUser(): void
    {
        
        if (isset($_POST['email'], $_POST['password'], $_POST['utilisateur'])) {

            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $status = trim($_POST['utilisateur']); // ✅ string, pas int

            if (empty($email) || empty($password) || empty($status)) {
                throw new \InvalidArgumentException((string) $status);
            }

            $this->connection->insertUser($email, $password, $status);
        }
    }
}