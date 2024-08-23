<?php
/*
Plugin Name: QR Menu Plugin
Plugin URI: http://cesilvanus.com
Description: A plugin to create a QR code for an online restaurant menu.
Version: 1.0
Author: Cesilvanus
Author URI: http://cesilvanus.com
License: GPL2
*/

defined('ABSPATH') or die('No script kiddies please!');

require_once(plugin_dir_path(__FILE__) . 'phpqrcode/qrlib.php');



function qrm_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tbl_food';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        description text NOT NULL,
        price float NOT NULL,
        category int NOT NULL,
        image_name varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'qrm_create_table');



function qrm_admin_styles() {
    wp_enqueue_style('qrm_admin_styles', plugins_url('qrm-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'qrm_admin_styles');







function qrm_create_menu() {
    add_menu_page(
        'QR Menu',
        'QR Menu',
        'manage_options',
        'qr-menu-plugin',
        'qrm_menu_page',
        'dashicons-menu',
        
    );


}
add_action('admin_menu', 'qrm_create_menu');




// QR kodunu üretme ve gösterme fonksiyonu
function generate_qr_code() {

    // Sabit URL
    $url_to_encode = 'https://kividigi.com';

   
    if (file_exists(plugin_dir_path(__FILE__) . 'phpqrcode/qrlib.php')) {
        require_once plugin_dir_path(__FILE__) . 'phpqrcode/qrlib.php';
        
        $tempDir = plugin_dir_path(__FILE__) . 'temp/';
        
        // Geçici dizini oluşturun
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $fileName = 'qr_' . md5($url_to_encode) . '.png';
        $filePath = $tempDir . $fileName;
        
        // QR kodun üretimi
        QRcode::png($url_to_encode, $filePath, QR_ECLEVEL_L, 4);
        
        // QR kodunun URL'si
        $qr_code_url = plugins_url('temp/' . $fileName, __FILE__);
        
        // QR kodunu görüntüleme
        echo '<h3>Generated QR Code:</h3>';
        echo '<img src="' . esc_url($qr_code_url) . '" alt="QR Code" />';
    } else {
        echo '<div class="error-message">QR kodu kütüphanesi bulunamadı!</div>';
    }
}




function qrm_menu_page() {
    ?>
<style>

/* Genel ayarlar */
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background-color: #f0f4f8; /* Daha açık bir arka plan rengi */
}

.wrap {
    background-color: #ffffff;
    border-radius: 12px;
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

/* Başlık ve logo */
.header {
    display: flex;
    flex-direction: column; /* Mobilde başlığı alt alta yerleştirmek için */
    align-items: center;
    margin-bottom: 20px;
    text-align: center;
}

.logo {
    width: 300px; 
    height: auto;
    margin-top: -50px;
}

h1 {
    color: #333;
    font-family: 'Arial', sans-serif;
    border-bottom: 3px solid #0073aa;
    padding-bottom: 12px;
    margin: 0;
    font-size: 24px;
}

/* Form tablosu */
.form-table {
    margin: 0;
    padding: 0;
    border: none;
}

.form-table th {
    text-align: left;
    padding: 12px 20px;
    background-color: #0073aa;
    color: #ffffff;
    font-family: 'Arial', sans-serif;
}

.form-table td {
    padding: 12px 20px;
    background-color: #ffffff;
    border-bottom: 1px solid #dddddd;
}

/* Giriş alanları ve butonlar */
input[type="text"], textarea, input[type="number"], select {
    width: 100%;
    padding: 10px;
    border: 1px solid #cccccc;
    border-radius: 8px;
    font-family: 'Arial', sans-serif;
}

input[type="file"] {
    padding: 10px;
    border: none;
    background-color: #dc3545; /* Kırmızı renk */
    color: #ffffff;
    font-family: 'Arial', sans-serif;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="file"]::file-selector-button {
    background-color: #dc3545; 
    color: #ffffff;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-family: 'Arial', sans-serif;
    cursor: pointer;
}

input[type="file"]::file-selector-button:hover {
    background-color: #c82333; /* Koyu kırmızı */
}

button {
    background-color: #0073aa;
    color: #ffffff;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-family: 'Arial', sans-serif;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #005a87;
}

.success-message {
    color: #4CAF50;
    font-weight: bold;
    margin-top: 20px;
}

.error-message {
    color: #F44336;
    font-weight: bold;
    margin-top: 20px;
}

/* Menü düzeni */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 10px 0;
}

.menu-item {
    border: 1px solid #dddddd;
    border-radius: 12px;
    overflow: hidden;
    background-color: #ffffff;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.menu-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}   

.menu-image {
    width: 100%;
    height: auto;
    border-bottom: 2px solid #ddd;
    margin-bottom: 10px;
    border-radius: 8px;
}

.menu-item h3 {
    margin: 0 0 10px;   
    font-size: 20px;
    color: #333;
}

.menu-item p {
    margin: 0;
    color: #666;
}

.menu-item p strong {
    color: #333;
}

.menu-item h4 {
    margin: 10px 0;
    font-size: 18px;
}

/* Buton stilleri */
.delete-form {
    display: inline-block;
    margin: 5px;
}

.delete-button {
    background-color: #d9534f; /* Kırmızı renk */
    border: none;
    color: white;
    padding: 12px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 8px; 
    width: 100%;
    transition: background-color 0.3s ease;
}

.delete-button:hover {
    background-color: #c9302c;
}

/* Düzenleme butonu stilizasyonu */
.edit-button {
    background-color: #007cba;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    width: 100% ;
    transition: background-color 0.3s ease;
    text-decoration: none;
}

.edit-button:hover {
    background-color: #005b8b;
}

.update-title {
    font-family: sans-serif;
    padding: 10px 15px;
    color: darkblue;
}

.search-button {
    background-color: #007cba;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    margin-bottom: 30px;
    margin-top: 20px;
}

.search-product-title {
    background-color: #007cba;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
}


/* Kategori barı */
.category-bar {
    display: flex;
    flex-wrap: wrap; /* Kategorilerin taşmasını engellemek için */
    gap: 15px;
    background-color: #0073aa;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 20px;
    overflow-x: auto;
}

.category-bar a {
    color: white;
    text-decoration: none;
    padding: 12px 20px; /* Mobil için daha küçük bir padding */
    background-color: #005b8b;
    border-radius: 8px;
    transition: background-color 0.3s ease;
    font-size: 14px; /* Yazı boyutu mobil için biraz küçülttüm*/
}

/* Hover efekti */
.category-bar a:hover {
    background-color: #00416d;
}

.category-bar .add-category {
    background-color: #28a745;
}

.category-bar .add-category:hover {
    background-color: #218838;
}

/* Mobil uyumlu menü düzeni */
@media (max-width: 768px) {
    .category-bar {
        flex-direction: column; /* Mobilde dikey olarak sıralar */
        align-items: stretch; /* Butonların tüm genişliği kaplaması için */
    }

    .category-bar a {
        width: 100%; 
        padding: 10px 0; /* Dikey padding, yanlardan sıfır */
        font-size: 16px; 
        text-align: center; 
    }
}


.qr-code-img {
    width: 100%; /* Resmi sayfa genişliğinde yapar */
    max-width: 300px; /* Maksimum genişlik */
    text-align: center;
    margin: 20px auto; /* Ortalar */
    height: auto;
}

/* Ürün ve kategori butonları */
.add-product-btn, .add-category-btn {
    width: 100%; 
    padding: 15px; /* Butonun kalınlığını ayarlar */
    background-color: #007cba; 
    color: white; 
    border: none; /* Kenar çizgisi kaldırır */
    text-align: center; 
    font-size: 18px; 
    cursor: pointer; /* Üzerine gelince imleç değişir */
    display: inline-block;
    margin-top: 10px; 
    font-weight: bold; /* Yazıyı kalın yapar */
    font-family: 'Arial', sans-serif;
    margin-bottom: 50px;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.add-product-btn:hover, .add-category-btn:hover {
    background-color: #0056b3; 
}

/* Kategori kartları */
.category-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.category-card {
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    width: 220px; /* Kart genişliği */
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
}

.category-card img {
    width: 100%;
    height: 150px; /* Kart yüksekliği */
    object-fit: cover;
    border-bottom: 2px solid #ddd;
}

.category-card h2 {
    margin: 10px 0;
    font-size: 18px;
    color: #333;
}

.category-card p {
    margin: 0;
    color: #666;
    font-size: 16px;
}

/* 404 sayfası */
.page-404 {
    text-align: center;
    margin-top: 50px;
}

.page-404 h2 {
    font-size: 36px;
    color: #0073aa;
}

.page-404 p {
    font-size: 18px;
    color: #666;
}

.page-404 a {
    color: #0073aa;
    text-decoration: none;
    font-weight: bold;
}






</style>

    <div class="wrap">
        <div class="header">
            <img src="<?php echo plugin_dir_url(__FILE__) . 'photos/logoWordpress.png'; ?>" alt="QR Menu Plugin Logo" class="logo" />
            <h1>QR Menu Plugin</h1>
        </div>
        <form method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Product Name</th>
                    <td><input type="text" name="title" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Description</th>
                    <td><textarea name="description" required></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Price</th>
                    <td><input type="number" step="0.01" name="price" required /></td>
                </tr>
               <tr valign="top">
                    <th scope="row">Category</th>
                    <td>
                        <select name="category" required>
                            <?php
                            global $wpdb;
                            $table_name_categories = $wpdb->prefix . 'tbl_category';
                            $categories = $wpdb->get_results("SELECT * FROM $table_name_categories");

                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->id) . '">' . esc_html($category->title) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Image</th>
                    <td><input type="file" name="image_name" accept="image/*" required /></td>
                </tr>
            </table>
                <input type="submit" name="add_product" value="Add Product" class="add-product-btn">
                
        </form>


      <div class="category-bar">
            <?php
            // CATEGORY BAR
            global $wpdb;
            $table_name_categories = $wpdb->prefix . 'tbl_category';
            $categories = $wpdb->get_results("SELECT * FROM $table_name_categories");
            

            foreach ($categories as $category) {
                echo '<a href="?page=qr-menu-plugin&category_id=' . esc_attr($category->id) . '" class="category-link">' . esc_html($category->title) . '</a> ';
            }
            ?>
            <a href="?page=qr-menu-plugin&category_id=all" class="category-link">All Categories</a>
            <a href="?page=qr-menu-plugin&action=add_category" class="category-link">Add Category</a>
            <a href="?page=qr-menu-plugin&action=generate_qr" class="category-link">QR Generator</a>
        </div>


        <?php

            if (isset($_POST['add_category'])) {
            global $wpdb;
            $table_name_categories = $wpdb->prefix . 'tbl_category';

            // Veriyi temizleyelim
            $category_name = sanitize_text_field($_POST['title']);
            $image_name = $_FILES['image_name']['name'];
            
            // Yükleme dizinini ayarlayalım
            $upload_dir = wp_upload_dir();
            $image_path = $upload_dir['path'] . '/' . basename($image_name);
            $image_url = $upload_dir['url'] . '/' . basename($image_name);

            // Dosyayı yükleyeme
            if (move_uploaded_file($_FILES['image_name']['tmp_name'], $image_path)) {
                // Veritabanına ekleme
                $wpdb->insert($table_name_categories, array(
                    'title' => $category_name,
                    'image_name' => $image_url // URL'yi veritabanında saklayama
                ));

                echo '<script>window.location.reload();</script>';
                echo '<div class="success-message">Kategori başarıyla eklendi!</div>';

                // Sayfayı yeniden yükleyerek kategori barında görünmesini sağlayalım
                echo '<script>window.location.reload();</script>';
            } else {
                echo '<div class="error-message">Görsel yüklenirken bir hata oluştu.</div>';
            }
        }


        if (isset($_GET['category_id']) && $_GET['category_id'] !== 'all') {
        $selected_category_id = intval($_GET['category_id']);
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tbl_food WHERE category_id = %d", $selected_category_id));
        } else {
            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}tbl_food");
        }

         // Eğer Add Category linkine tıklandıysa formu gösterir
        if (isset($_GET['action']) && $_GET['action'] == 'add_category') {
            ?>
            <form method="post" action="" enctype="multipart/form-data">
                <h2>Add New Category</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Category Name</th>
                        <td><input type="text" name="title" placeholder="Category Name" required /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Category Image</th>
                        <td><input type="file" name="image_name" accept="image/*" required /></td>
                    </tr>
                </table>
                <button type="submit"  class="add-category-btn" name="add_category">Add Category</button>
            </form>

             <h2>Existing Categories</h2>
            <div class="category-grid">
                <?php
                global $wpdb;
                $table_name_categories = $wpdb->prefix . 'tbl_category';
                $categories = $wpdb->get_results("SELECT * FROM $table_name_categories ORDER BY title ASC");

                foreach ($categories as $category) {
                    ?>
                    <div class="category-card">
                    <img src="<?php echo esc_url(wp_upload_dir()['baseurl'] . '/' . esc_attr($category->image_name)); ?>" alt="<?php echo esc_attr($category->title); ?>" class="category-image" />
                    <div class="category-info">
                        <h3><?php echo esc_html($category->title); ?></h3>
                        <a href="?page=qr-menu-plugin&action=edit_category&id=<?php echo esc_attr($category->id); ?>" class="edit-button">Edit</a>
                        <form method="post" action="" class="delete-form" style="display:inline;">
                            <input type="hidden" name="delete_category_id" value="<?php echo esc_attr($category->id); ?>" />
                            <button type="submit" class="delete-button" name="delete_category" onclick="return confirm('Are you sure you want to delete this category?');"> Delete </button>
                        </form>
                    </div>  
                </div>

                    <?php
                }
                    
        }



            // Kategori için silme butonuna tıklanıp tıklanmadığını kontrol et
            if (isset($_POST['delete_category'])) {
                global $wpdb;
                $table_name_category = $wpdb->prefix . 'tbl_category';
                $delete_id = intval($_POST['delete_category_id']);
                
                // Delete the category from the database
                $wpdb->delete($table_name_category, array('id' => $delete_id));

                // Reload the page to reflect the deletion
                echo '<script>window.location.reload();</script>';
            }

            // Check if delete button was clicked for food item
            if (isset($_POST['delete'])) {
                global $wpdb;
                $table_name_food = $wpdb->prefix . 'tbl_food';
                $delete_id = intval($_POST['delete_id']);
                
                // Delete the product from the database
                $wpdb->delete($table_name_food, array('id' => $delete_id));

                // Reload the page to reflect the deletion
                echo '<script>window.location.reload();</script>';
            }




        if (isset($_GET['action']) && $_GET['action'] == 'generate_qr') {
            ?>
        


            <?php




         // Sabit URL'yi tanımlayın
        $url_to_encode = 'https://kividigi.com';

        
        if (file_exists(plugin_dir_path(__FILE__) . 'phpqrcode/qrlib.php')) {
            require_once plugin_dir_path(__FILE__) . 'phpqrcode/qrlib.php';
            
            $tempDir = plugin_dir_path(__FILE__) . 'temp/';
            
            // Geçici dizini oluşturma
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $fileName = 'qr_' . md5($url_to_encode) . '.png';
            $filePath = $tempDir . $fileName;
            
            // QR kodunu üretin
            QRcode::png($url_to_encode, $filePath, QR_ECLEVEL_L, 4);
             

            // QR kodunun URL'sini alma
            $qr_code_url = plugins_url('temp/' . $fileName, __FILE__);
            
            // QR kodunu görüntüleme
            echo '<h3>Generated QR Code:</h3>';
            echo '<img class = "qr-code-img" src="'  . esc_url($qr_code_url) . '" alt="QR Code" />';    
        } else {
            echo '<div class="error-message">QR kodu kütüphanesi bulunamadı!</div>';
        }
    }



     // Ekleme işlemi
        if (isset($_FILES['image_name'])) {
           $table_name = $wpdb->prefix . 'tbl_food';

            // Upload image
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . '/' . basename($_FILES['image_name']['name']);
            $image_url = $upload_dir['url'] . '/' . basename($_FILES['image_name']['name']);

            if ($_FILES['image_name']['error'] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES['image_name']['tmp_name'], $upload_path)) {
                    // Veritabanına ekleme işlemi
                    $data = array(
                        'title' => sanitize_text_field($_POST['title']),
                        'description' => sanitize_textarea_field($_POST['description']),
                        'price' => floatval($_POST['price']),
                        'category_id' => intval($_POST['category']),
                        'image_name' => basename($_FILES['image_name']['name'])
                    );
                    $wpdb->insert($table_name, $data);
                    echo '<div class="success-message">Product added successfully   </div>';
                } else {
                    echo '<div class="error-message">Failed to upload image.</div>';
                }
            } else {
                echo '<div class="error-message">Image upload error code: ' . $_FILES['image_name']['error'] . '</div>';
            }
        }   
    


  /*  global $wpdb;
    $table_name_food = $wpdb->prefix . 'tbl_food';
    $table_name_categories = $wpdb->prefix . 'tbl_category'; // tbl_category tablosunu kullanıyoruz

    // Ürünleri ve kategorileri al
    $results = $wpdb->get_results("SELECT * FROM $table_name_food");
    $categories = $wpdb->get_results("SELECT * FROM $table_name_categories");

    // Kategorileri id'ye göre bir diziye çevir
    $category_map = [];
    foreach ($categories as $category) {
        // Kategori id ve adını diziye ekleyin
        $category_map[$category->id] = $category->title; // Kategori adı
    }*/



        // Ürünleri listele
        if ($results) {
            echo '<div class="menu-grid">';

                foreach ($results as $result) {


                    global $wpdb;
                    $table_name_food = $wpdb->prefix . 'tbl_food';
                    $table_name_categories = $wpdb->prefix . 'tbl_category';

                    // Ürünleri ve kategorileri almak
                    $results = $wpdb->get_results("SELECT * FROM $table_name_food");
                    $categories = $wpdb->get_results("SELECT * FROM $table_name_categories");

                    // Kategorileri category_map dizisine ekleme
                    $category_map = array();
                    foreach ($categories as $category) {
                        $category_map[$category->id] = $category->title; // Kategori adını eşle
                    }

                    // Kategori adını elde etme
                    $category_name = isset($category_map[$result->category_id]) ? $category_map[$result->category_id] : 'Unknown';

                    echo '<div class="menu-item">';
                    echo '<img src="' . esc_url(wp_upload_dir()['url'] . '/' . esc_attr($result->image_name)) . '" alt="' . esc_attr($result->title) . '" />';
                    echo '<h4>' . esc_html($result->title) . '</h4>';
                    echo '<p>' . esc_html($result->description) . '</p>';
                    echo '<p><strong>Category :</strong> ' . esc_html($category_name) . '</p>';
                    echo '<p><strong>Price:</strong> $' . esc_html($result->price) . '</p>';

                    // Delete Button
                    echo '<form method="POST" action="">';
                    echo '<input type="hidden" name="delete_id" value="' . esc_attr($result->id) . '">';
                    echo '<input type="submit" name="delete" value="Delete" class="delete-button" onclick="return confirm(\'Are you sure you want to delete this item?\');">';
                    echo '</form>';

                    // Edit Button
                    echo '<form method="GET" action="admin.php">';
                    echo '<input type="hidden" name="page" value="qr-menu-plugin-update">'; // Güncelleme sayfasına yönlendirme
                    echo '<input type="hidden" name="edit_id" value="' . esc_attr($result->id) . '">';
                    echo '<input type="submit" value="Edit" class="edit-button">';
                    echo '</form>';

                    echo '</div>';
                }
            echo '</div>';
        } else {
            echo '<p>No products found.</p>';
        }

            // Check if delete button was clicked
            if (isset($_POST['delete'])) {
                global $wpdb;
                $table_name_food = $wpdb->prefix . 'tbl_food';
                $delete_id = intval($_POST['delete_id']);
                
                // Delete the product from the database
                $wpdb->delete($table_name_food, array('id' => $delete_id));

                // Reload the page to reflect the deletion
                echo '<script>window.location.reload();</script>';
            }
            ?>
        </div>

   <?php
     


    /*$qr_code_url = plugin_dir_url(__FILE__) . 'photos/QRKivi.jpg';
    echo '<div class="qr-code">';
    echo '<h3>QR Code:</h3>';
    echo '<img src="' . esc_url($qr_code_url) . '" alt="QR Code">';
    echo '</div>'; */  ?>

   
 <div>
        <?php
        global $wpdb;
        $table_name_food = $wpdb->prefix . 'tbl_food'; // Tablo adının doğru olduğundan emin olun
        $table_name_categories = $wpdb->prefix . 'tbl_category'; // Kategoriler tablosu

      
        // Ürün Düzenleme
        if (isset($_GET['action']) && $_GET['action'] == 'qrm_edit_product' && isset($_GET['product_id'])) {
            if (!isset($_GET['qrm_update_nonce']) || !wp_verify_nonce($_GET['qrm_update_nonce'], 'qrm_update_action')) {
                wp_die('Nonce verification failed');
            }

            $product_id = intval($_GET['product_id']);
            
            // Veritabanından ürünü getir
            $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_food WHERE id = %d", $product_id));

            if ($product) {
                // Form submit edildiğinde güncelleme işlemi
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
                    if (!isset($_POST['qrm_update_nonce']) || !wp_verify_nonce($_POST['qrm_update_nonce'], 'qrm_update_action')) {
                        wp_die('Nonce verification failed');
                    }

                    $title = sanitize_text_field($_POST['title']);
                    $description = sanitize_textarea_field($_POST['description']);
                    $price = floatval($_POST['price']);
                    $category = intval($_POST['category']);

                    // Eğer yeni bir resim yüklendiyse, dosyayı işle
                    if (!empty($_FILES['image_name']['name'])) {
                        $uploaded = media_handle_upload('image_name', 0);
                        if (is_wp_error($uploaded)) {
                            echo '<div class="error-message">Image upload failed.</div>';
                        } else {
                            $image_name = basename(get_attached_file($uploaded));
                        }
                    } else {
                        $image_name = $product->image_name; // Eğer yeni bir resim yüklenmediyse, mevcut resmi kullan!!
                    }

                    // Ürünü güncelle
                    $updated = $wpdb->update(
                        $table_name_food,
                        array(
                            'title' => $title,
                            'description' => $description,
                            'price' => $price,
                            'category_id' => $category,
                            'image_name' => $image_name,
                        ),
                        array('id' => $product_id),
                        array('%s', '%s', '%f', '%d', '%s'),
                        array('%d')
                    );

                    if ($updated !== false) {
                        echo '<div class="success-message">Product updated successfully!</div>';
                    } else {
                        echo '<div class="error-message">Failed to update product.</div>';
                    }
                }

                ?>
                <h2>Edit Product</h2>
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('qrm_update_action', 'qrm_update_nonce'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Product Title</th>
                            <td><input type="text" name="title" value="<?php echo esc_attr($product->title); ?>" required /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Product Description</th>
                            <td><textarea name="description" rows="5" required><?php echo esc_textarea($product->description); ?></textarea></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Product Price ($)</th>
                            <td><input type="number" step="0.01" name="price" value="<?php echo esc_attr($product->price); ?>" required /></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Product Category</th>
                            <td>
                                <select name="category">
                                    <?php
                                    // Kategorileri al
                                    $categories = $wpdb->get_results("SELECT * FROM $table_name_categories");
                                    foreach ($categories as $cat) {
                                        echo '<option value="' . esc_attr($cat->id) . '"' . selected($cat->id, $product->category_id, false) . '>' . esc_html($cat->name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Product Image</th>
                            <td>
                                <?php if (!empty($product->image_name)): ?>
                                    <img src="<?php echo esc_url(wp_upload_dir()['url'] . '/' . esc_attr($product->image_name)); ?>" alt="<?php echo esc_attr($product->title); ?>" width="100">
                                <?php endif; ?>
                                <input type="file" name="image_name" />
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="update_product" value="1" />
                    <input type="submit" value="Update Product" />
                </form>
                <?php
            } else {
                echo '<div class="error-message">Product not found.</div>';
            }
        }

        ?>
    </div>
    <?php


    }


