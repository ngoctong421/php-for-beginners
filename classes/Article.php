<?php

class Article
{
    public $id;
    public $title;
    public $content;
    public $published_at;
    public $image_file;
    public $errors = [  ];

    public static function getAll($conn)
    {
        $sql = "SELECT *
                FROM article
                ORDER BY id";
        $results = $conn->query($sql);

        return $results->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPage($conn, $limit, $offset, $only_published = false)
    {
        $condition = $only_published ? ' WHERE published_at IS NOT NULL' : '';

        $sql = "SELECT a.*, category.name AS category_name
                FROM (SELECT *
                      FROM article
                      $condition
                      ORDER BY id
                      LIMIT :limit
                      OFFSET :offset) AS a
                LEFT JOIN article_category
                ON a.id =  article_category.article_id
                LEFT JOIN category
                ON article_category.category_id = category.id";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $articles = [  ];

        $previous_id = null;

        foreach ($results as $row) {
            $article_id = $row[ 'id' ];

            if ($article_id !== $previous_id) {
                $articles[ $article_id ] = $row;
            }

            $articles[ $article_id ][ 'category_names' ][  ] = $row[ 'category_name' ];

            $previous_id = $article_id;
        }

        return $articles;
    }

    public static function getByID($conn, $id, $columns = '*')
    {
        $sql = "SELECT $columns
                FROM article
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Article');

        if ($stmt->execute()) {
            return $stmt->fetch();
        }
    }

    public static function getWithCategories($conn, $id, $only_published = false)
    {
        $sql = "SELECT article.*, category.name AS category_name
                FROM article
                LEFT JOIN article_category
                ON article.id = article_category.article_id
                LEFT JOIN category
                ON article_category.category_id = category.id
                WHERE article.id = :id";

        if ($only_published) {
            $sql .= ' AND article.published_at IS NOT NULL';
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories($conn)
    {
        $sql = "SELECT category.*
                FROM category
                LEFT JOIN article_category
                ON category.id = article_category.category_id
                WHERE article_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($conn)
    {
        if ($this->_validate()) {
            $sql = "UPDATE article
                    SET title = :title,
                        content = :content,
                        published_at = :published_at
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);

            if ('' === $this->published_at) {
                $stmt->bindValue(':published_at', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
            }

            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function setCategories($conn, $ids)
    {
        if ($ids) {
            $sql = "INSERT IGNORE INTO article_category(article_id, category_id)
            VALUES ";

            $values = [  ];

            foreach ($ids as $id) {
                $values[  ] = "($this->id, ?)";
            }

            $sql .= implode(", ", $values);

            $stmt = $conn->prepare($sql);

            foreach ($ids as $i => $id) {
                $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
            }

            $stmt->execute();
        }

        $sql = "DELETE FROM article_category
                WHERE article_id = $this->id";

        if ($ids) {
            $placeholders = array_fill(0, count($ids), '?');

            $sql .= " AND category_id NOT IN (" . implode(", ", $placeholders) . ")";
        }

        $stmt = $conn->prepare($sql);

        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
        }

        $stmt->execute();
    }

    protected function _validate()
    {
        if ('' == $this->title) {
            $this->errors[  ] = 'Title is required';
        }
        if ('' == $this->content) {
            $this->errors[  ] = 'Content is required';
        }

        return empty($this->errors);
    }

    public function delete($conn)
    {
        $sql = "DELETE FROM article
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function create($conn)
    {
        if ($this->_validate()) {
            $sql = "INSERT INTO article(title, content, published_at)
                    VALUES (:title, :content, :published_at)";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);

            if ('' === $this->published_at) {
                $stmt->bindValue(':published_at', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                $this->id = $conn->lastInsertId();

                return true;
            };
        } else {
            return false;
        }
    }

    public static function getTotal($conn, $only_published = false)
    {
        $condition = $only_published ? ' WHERE published_at IS NOT NULL' : '';

        return $conn->query("SELECT COUNT(*) FROM article$condition")->fetchColumn();
    }

    public function setImageFile($conn, $filename)
    {
        $sql = "UPDATE article
                SET image_file =:image_file
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':image_file', $filename, null === $filename ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function publish($conn)
    {
        $sql = "UPDATE article
                SET published_at = :published_at
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $datetime = date("Y-m-d H:i:s");

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':published_at', $datetime, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $datetime;
        }
    }
}
