<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dbPath = '/tmp/database.sqlite';
        $dsn = "sqlite:" . $dbPath;
        try {
            $this->pdo = new PDO($dsn);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initializeTables();
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeTables() {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS Users (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                username TEXT UNIQUE,
                password TEXT,
                email TEXT,
                role TEXT DEFAULT "user"
            )
        ');
        // Updated Products table to include an image_url column
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS Products (
                product_id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                description TEXT,
                price REAL,
                admin_only INTEGER DEFAULT 0,
                image_url TEXT
            )
        ');
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS Orders (
                order_id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                product_id INTEGER,
                order_date TEXT,
                FOREIGN KEY (user_id) REFERENCES Users(user_id),
                FOREIGN KEY (product_id) REFERENCES Products(product_id)
            )
        ');
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function authenticateUser($username, $password) {
        $stmt = $this->pdo->prepare('SELECT * FROM Users WHERE username = :username');
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && hash('sha256', $password) === $user['password']) {
            if ($user['role'] === "administrator") {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = 'administrator';
                return true;
            }
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function registerUser($name, $username, $password, $email) {
        $stmt = $this->pdo->prepare('
            INSERT INTO Users (name, username, password, email, role)
            VALUES (:name, :username, :password, :email, "user")
        ');
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password', hash('sha256', $password), PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("User registration failed: " . $e->getMessage());
            return false;
        }
    }

    public function getProducts() {
        $stmt = $this->pdo->prepare('
            SELECT p.*, COUNT(o.order_id) as order_count
            FROM Products p
            LEFT JOIN Orders o ON p.product_id = o.product_id
            GROUP BY p.product_id
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSampleData() {
        $stmt = $this->pdo->prepare('
            INSERT OR IGNORE INTO Users (name, username, password, email, role)
            VALUES ("Rick Sanchez", "ricksanchez", :password, "rick@citadel.com", "administrator")
        ');
        $stmt->bindValue(':password', hash('sha256', 'wubalubadubdub'), PDO::PARAM_STR);
        $stmt->execute();
        $stmt = $this->pdo->query('SELECT COUNT(*) as count FROM Products');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['count'] == 0) {
            // Updated insertion to include image_url for each product
            $this->pdo->exec('
                INSERT INTO Products (name, description, price, admin_only, image_url) VALUES
                ("Rick\'s Large Space Cruiser", "High-speed interstellar cruiser with warp drives.", 999.99, 0, "/assets/images/1.png"),
                ("Portal Gun", "Dimension-hopping device used by Rick.", 499.99, 0, "/assets/images/2.png"),
                ("Meeseeks Box", "Summons helpful, yet volatile, Mr. Meeseeks.", 29.99, 0, "/assets/images/3.png"),
                ("Save point device", "Creates time-save points to replay actions.", 59.99, 0, "/assets/images/4.png"),
                ("Omega device", "Top-tier contraption for universal resets.", 9999.99, 1, "/assets/images/5.png"),
                ("Solar Scepter", "Scepter harnessing solar power for cosmic tasks.", 199.99, 0, "/assets/images/6.png"),
                ("Purge Suit", "Heavy-duty combat armor for purge scenarios.", 399.99, 0, "/assets/images/7.png"),
                ("Laser Gun", "Standard-issue blaster for quick zaps.", 149.99, 0, "/assets/images/8.png"),
                ("Freeze Ray", "Ice-cold beam weapon to immobilize targets.", 199.99, 0, "/assets/images/9.png"),
                ("Dream Inceptor", "For infiltration and manipulation of dreams.", 799.99, 1, "/assets/images/10.png"),
                ("Microverse Battery", "Generates power by enslaving micro-civilizations.", 1299.99, 1, "/assets/images/11.png"),
                ("Butter Robot", "Existentially aware butter-passing assistant.", 299.99, 1, "/assets/images/12.png")
            ');
            $this->pdo->exec('
                INSERT OR IGNORE INTO Orders (user_id, product_id, order_date) VALUES
                (1, 2, datetime("now")),
                (1, 5, datetime("now")),
                (2, 12, datetime("now"))
            ');
        }
        $admin_users = array("Beth Smith");
        $regular_users = array(
            "Summer Smith", "Jerry Smith", "Morty Smith", "Jessica", "Mr. Meeseeks",
            "Birdperson", "Mr. Poopybutthole", "Squanchy", "Abradolf Lincler",
            "Tammy Guetermann", "Zeep Xanflorp", "Dr. Wong", "Gearhead",
            "Krombopulos Michael", "Pickle Rick", "Evil Morty", "Unity",
            "Noob-Noob", "Scary Terry", "Fart", "Heist-o-Tron", "Tricia Lange",
            "Ghost in a Jar", "Lucius Needful", "Brad Anderson", "Borpocian",
            "Glexo Slim Slom", "Shleemypants", "Tony"
        );
        foreach ($admin_users as $index => $name) {
            $i = $index + 1;
            $stmt = $this->pdo->prepare('
                INSERT OR IGNORE INTO Users (name, username, password, email, role)
                VALUES (:name, :username, :password, :email, "administrator")
            ');
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':username', "admin$i", PDO::PARAM_STR);
            $stmt->bindValue(':password', hash('sha256', 'adminpassword'), PDO::PARAM_STR);
            $stmt->bindValue(':email', "admin$i@root.htb", PDO::PARAM_STR);
            $stmt->execute();
        }
        foreach ($regular_users as $index => $name) {
            $i = $index + 1;
            $stmt = $this->pdo->prepare('
                INSERT OR IGNORE INTO Users (name, username, password, email, role)
                VALUES (:name, :username, :password, :email, "user")
            ');
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':username', "user$i", PDO::PARAM_STR);
            $stmt->bindValue(':password', hash('sha256', 'userpassword'), PDO::PARAM_STR);
            $stmt->bindValue(':email', "user$i@root.htb", PDO::PARAM_STR);
            $stmt->execute();
        }
    }
}

session_start();
$db = Database::getInstance();
$db->addSampleData();
?>
