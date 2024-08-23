<?php
session_start(); // Session başlat

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "food_order";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kategori ID'sini al (varsa) ve ürünleri filtrele
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Kategori verilerini çek
$categorySql = "SELECT * FROM wp_tbl_category";
$categoryResult = $conn->query($categorySql);

// Ürünleri kategoriye göre filtrele
$sql = "SELECT * FROM wp_tbl_food";
if ($category_id > 0) {
    $sql .= " WHERE category_id = $category_id";
}
$result = $conn->query($sql);

// Sepet başlangıç değerlerini hesapla
$cartCount = isset($_SESSION["cart"]) ? count($_SESSION["cart"]) : 0;
$cartTotal = isset($_SESSION["cart"]) ? array_sum(array_column($_SESSION["cart"], "price")) : 0.00;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  

<style>

    /* Genel ayarlar */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Header Konteyner */
.header-container {
    background-image: url("photos/Adsız tasarım.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 60px 0;
    text-align: center;
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    border-radius: 8px;
    margin: 20px auto;
    max-width: 1200px;
}

/* Logo ve Başlıklar Konteyneri */
.logo-title-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
}

/* Logo Stil Ayarları */
.logo img {
    max-width: 120px;
    height: auto;
    border-radius: 50%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Başlıklar Konteyneri */
.title-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

/* Ana Başlık Stil Ayarları */
.website-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 50px;
    font-weight: bold;
    margin: 0;
    color: #ffeb3b;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
    letter-spacing: 2px;
}

/* Alt Başlık Stil Ayarları */
.website-subtitle {
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    color: #f1f1f1;
    margin-top: 10px;
}

/* Menü Başlığı */
.menu-title {
    font-family: 'Lobster', cursive;
    text-align: center;
    font-size: 32px;
    margin: 20px 0;
    color: #5A8F77;
}

/* Kategori Barı */
.category-bar {
    display: flex;
    flex-wrap: wrap; /* Kategorilerin taşmasını engellemek için */
    gap: 15px;
    background: linear-gradient(135deg, #0073aa, #005b8b);
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 20px;
    overflow-x: auto;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    font-family: 'Open Sans', sans-serif; /* Modern yazı tipi */
}

/* Kategori Butonları */
.category-bar a {
    color: white;
    text-decoration: none;
    padding: 12px 20px; /* Mobilde daha küçük bir padding */
    background: linear-gradient(135deg, #005b8b, #00416d);
    border-radius: 8px;
    transition: background 0.3s ease, transform 0.3s ease;
    font-size: 14px; /* Yazı boyutu mobil için biraz küçültüldü */
    display: block; /* Butonları tam genişlikte yapar */
    text-align: center; /* Yazıyı ortalar */
}

/* Buton Hover Efekti */
.category-bar a:hover {
    background: linear-gradient(135deg, #00416d, #005b8b);
    transform: scale(1.05); /* Hover'da büyütme efekti */
}

/* 'Add Category' Butonu */
.category-bar .add-category {
    background: linear-gradient(135deg, #28a745, #218838);
}

.category-bar .add-category:hover {
    background: linear-gradient(135deg, #218838, #28a745);
}

/* Kategoriler Konteyneri */
.category-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px auto;
    max-width: 1200px;
    flex-wrap: wrap;
}

/* Kategori Öğesi */
.category-item {
    text-align: center;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    width: 200px;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.category-item:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

/* Kategori Resmi */
.category-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

/* Kategori Başlığı */
.category-item h3 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
    font-family: 'Montserrat', sans-serif;
}

/* Menü Konteyneri */
.container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

/* Menü Öğesi */
.menu-item {
    border-bottom: 1px solid #ddd;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

/* Menü Öğesi Son Öğesi */
.menu-item:last-child {
    border-bottom: none;
}

/* Menü Öğesi Resmi */
.menu-item img {
    max-width: 150px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Menü Öğesi Detayları */
.item-details {
    flex: 1;
}

.item-details h2 {
    margin: 0;
    font-size: 1.6em;
    color: #333;
}

.item-details p {
    margin: 5px 0;
    color: #666;
    font-family: 'Open Sans', sans-serif;
}

.item-details .price {
    margin-top: 15px;
    font-weight: bold;
    color: #5A8F77;
    font-size: 22px;
}

/* Mobil Uyumluluk İçin Medya Sorguları */
@media (max-width: 768px) {
    .header-container {
        padding: 40px 0;
    }

    .website-title {
        font-size: 40px;
    }

    .menu-title {
        font-size: 28px;
    }

    .logo-title-container {
        flex-direction: column;
        gap: 10px;
    }

    .logo img {
        max-width: 100px;
    }

    .category-item {
        width: 150px;
    }

    .container {
        width: 95%;
    }

    .food-title {
        font-size: 36px;
    }

    .food-description {
        font-size: 18px;
    }

    .item-details h2 {
        font-size: 1.4em;
    }

    .item-details .price {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .header-container {
        padding: 30px 0;
    }

    .website-title {
        font-size: 30px;
    }

    .menu-title {
        font-size: 24px;
    }

    .logo img {
        max-width: 80px;
    }

    .category-item {
        width: 120px;
    }

    .container {
        width: 95%;
    }

    .food-title {
        font-size: 28px;
    }

    .food-description {
        font-size: 16px;
    }

    .item-details h2 {
        font-size: 1.2em;
    }

    .item-details .price {
        font-size: 18px;
    }
}

a {
    text-decoration: none;
}

    </style>
</head>
<body>
    <header class="header-container">
        <div class="logo-title-container">
            <div class="logo">
                <img src="photos/logo-color.png" alt="Logo">
            </div>
            <div class="title-container">
                <div class="website-title">FOOD</div>
                <div class="menu-title">TO</div>
                <div class="website-title">FOOD</div>
            </div>
        </div>
    </header>


    <!-- Kategoriler Konteyneri -->
    <div class="category-container">
        <?php
        if ($categoryResult->num_rows > 0) {
            $base_url = 'http://localhost/foodtofood/photos/CategoryPhotos/'; // Resimlerin bulunduğu dizin

            while ($category = $categoryResult->fetch_assoc()) {
                $categoryImage = $base_url . $category["image_name"];
                $categoryId = $category["id"];

                echo "<div class='category-item'>";
                echo "<a href='?category_id=" . $categoryId . "'>";
                echo "<img src='" . $categoryImage . "' alt='" . htmlspecialchars($category["title"], ENT_QUOTES) . "'>";
                echo "<h3>" . htmlspecialchars($category["title"], ENT_QUOTES) . "</h3>";
                echo "</a>";
                echo "</div>";
            }
        } else {
            echo "There is no category available.";
        }
        $conn->close();
        ?>
    </div>

    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $image_name = $row["image_name"];
                $image_path = 'photos/sitedekiyemekler/' . $image_name;

                echo "<div class='menu-item'>";
                echo "<img src='" . $image_path . "' alt='Ürün Fotoğrafı'>";
                echo "<div class='item-details'>";
                echo "<h2 class='food-title'>" . htmlspecialchars($row["title"], ENT_QUOTES) . "</h2>";
                echo "<p class='food-description'>" . htmlspecialchars($row["description"], ENT_QUOTES) . "</p>";
                echo "<p class='price'>$" . htmlspecialchars($row["price"], ENT_QUOTES) . " </p>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No product found.</p>";
        }
        ?>
    </div>
</body>

</html>







       