
<?php

class DBController {
    private $dbHost = "localhost";
    private $dbUser = "root";
    private $dbPassword = "";
    private $dbName = "knowledge_exchange";
    public $connection;  // Changed to public so it can be accessed by other controllers

    public function openConnection() {
        $this->connection = new mysqli($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbName);

        if ($this->connection->connect_error) {
            echo "Error In Connection :" . $this->connection->connect_error;
            return false;
        } else {
            return true;
        }
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function select($query) {
        // Check if the connection is established
        if (!$this->connection) {
            $this->openConnection();
        }

        $result = $this->connection->query($query);

        if (!$result) {
            echo "Error in Query: " . $this->connection->error . "<br>";
            echo "Query: " . $query;
            return false;
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function insert($query) {
        // Check if the connection is established
        if (!$this->connection) {
            $this->openConnection();
        }

        $result = $this->connection->query($query);

        if ($result) {
            return $this->connection->insert_id;
        } else {
            echo "Error in Insert: " . $this->connection->error;
            return false;
        }
    }

    public function update($query) {
        // Check if the connection is established
        if (!$this->connection) {
            $this->openConnection();
        }

        $result = $this->connection->query($query);

        if ($result) {
            return true;
        } else {
            echo "Error in Update: " . $this->connection->error;
            return false;
        }
    }

    public function delete($query) {
        // Check if the connection is established
        if (!$this->connection) {
            $this->openConnection();
        }

        $result = $this->connection->query($query);

        if ($result) {
            return true;
        } else {
            echo "Error in Delete: " . $this->connection->error;
            return false;
        }
    }
    public function selectOne($query) {
        $result = $this->connection->query($query);
    
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Fetch a single associative array
        } else {
            return null; // No rows found
        }
}

}
?>