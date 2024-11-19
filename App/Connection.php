<?php
namespace App;

class Connection
{

    public static function getDb()
    {
        try {
            return new \PDO(
                "mysql:host=localhost; dbname=biblioteca; charset=utf8",
                'root',
                'Bb&1101010'
            );

        } catch (\PDOException $e) {
            echo "Unable to connect to database. Please try again later.";

            error_log("Database connection error: " . $e->getMessage(), 3, __DIR__ . '/logs/erros.log');

            exit;
        }
    }

}
