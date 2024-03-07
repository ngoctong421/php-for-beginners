<?php

class Category
{
    public static function getAll($conn)
    {
        $sql = "SELECT *
              FROM category
              ORDER BY id";
        $results = $conn->query($sql);

        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
}
