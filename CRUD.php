<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Book Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label, button { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px; width: 100%; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .message { margin-top: 20px; color: #333; }
        hr { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Library Book Management System</h2>

    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Author:</label>
        <input type="text" name="author" required>

        <label>Published Year:</label>
        <input type="number" name="published_year" min="1000" max="9999" required>

        <label>Genre:</label>
        <input type="text" name="genre" required>

        <label>ID (for Update/Delete):</label>
        <input type="number" name="id">

        <button type="submit" name="action" value="create">Add Book</button>
        <button type="submit" name="action" value="update">Update Book</button>
        <button type="submit" name="action" value="delete">Delete Book</button>
    </form>

    <div class="message">
        <?php
        // Database connection
        $dsn = "mysql:host=localhost;dbname=library";
        $username = "root";
        $password = ""; // Adjust to your MySQL password if needed

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        // Function to CREATE a new book
        function createBook($pdo, $title, $author, $published_year, $genre) {
            $sql = "INSERT INTO books (title, author, published_year, genre) VALUES (:title, :author, :published_year, :genre)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['title' => $title, 'author' => $author, 'published_year' => $published_year, 'genre' => $genre]);
            echo "New book added successfully!<br>";
        }

        // Function to READ and display all books in a table
        function readBooks($pdo) {
            $sql = "SELECT * FROM books";
            $stmt = $pdo->query($sql);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($books) {
                echo "<h3>Current Books:</h3>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Published Year</th><th>Genre</th></tr>";
                foreach ($books as $book) {
                    echo "<tr><td>{$book['id']}</td><td>{$book['title']}</td><td>{$book['author']}</td><td>{$book['published_year']}</td><td>{$book['genre']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No books found.<br>";
            }
        }

        // Function to UPDATE a book's details
        function updateBook($pdo, $id, $title, $author, $published_year, $genre) {
            $sql = "UPDATE books SET title = :title, author = :author, published_year = :published_year, genre = :genre WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id, 'title' => $title, 'author' => $author, 'published_year' => $published_year, 'genre' => $genre]);
            echo "Book with ID $id updated successfully!<br>";
        }

        // Function to DELETE a book by ID
        function deleteBook($pdo, $id) {
            $sql = "DELETE FROM books WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            echo "Book with ID $id deleted successfully!<br>";
        }

        // Handling form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['action'];
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $published_year = $_POST['published_year'] ?? '';
            $genre = $_POST['genre'] ?? '';

            switch ($action) {
                case 'create':
                    createBook($pdo, $title, $author, $published_year, $genre);
                    break;
                case 'update':
                    if ($id) {
                        updateBook($pdo, $id, $title, $author, $published_year, $genre);
                    } else {
                        echo "Please provide the ID of the book you want to update.<br>";
                    }
                    break;
                case 'delete':
                    if ($id) {
                        deleteBook($pdo, $id);
                    } else {
                        echo "Please provide the ID of the book you want to delete.<br>";
                    }
                    break;
            }
        }

        // Display the books table on page load
        readBooks($pdo);
        ?>
    </div>
</body>
</html>
