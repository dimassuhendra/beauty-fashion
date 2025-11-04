<?php 
// Pengecekan koneksi (mengatasi error undefined variable $conn)
if (!isset($conn) || @$conn->connect_error) {
    $error_message = "Gagal terhubung ke database: " . (@$conn ? $conn->connect_error : "Variabel koneksi (\$conn) tidak terdefinisi.");
    $is_connected = false;
    $products = [];
    $total_pages = 0;
    $current_page = 1;
    $categories = [];
} else {
    $is_connected = true;
    $error_message = '';

    // ====================================================================
    // 2. LOGIKA PAGINATION, FILTER, DAN SORTING
    // ====================================================================
    
    // a. Konfigurasi Paging
    $limit = 25; // Default 25 produk per halaman
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $limit;
    
    // b. Inisialisasi Filter
    // KOREKSI: Menggunakan 'p.stock_quantity' sesuai skema database
    $where_clauses = ["p.stock > 0"]; // Default: hanya tampilkan stok tersedia
    $query_params = [];
    
    // c. Filter Kategori
    $category_filter = isset($_GET['category']) ? $_GET['category'] : '';
    if (!empty($category_filter)) {
        $category_filter = $conn->real_escape_string($category_filter);
        $where_clauses[] = "c.slug = '$category_filter'";
        $query_params['category'] = $category_filter;
    }

    // d. Filter Stok
    $stock_filter = isset($_GET['stock']) ? $_GET['stock'] : 'available'; // available atau all
    if ($stock_filter === 'all') {
        // Hapus filter stok_quantity > 0 jika user memilih 'Semua Stok'
        array_pop($where_clauses);
    }
    $query_params['stock'] = $stock_filter;


    // e. Filter Range Harga (Min & Max)
    $min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : 999999999;
    
    if ($min_price > 0) {
        $where_clauses[] = "p.price >= $min_price";
        $query_params['min_price'] = $min_price;
    }
    if ($max_price < 999999999 && $max_price > $min_price) {
        $where_clauses[] = "p.price <= $max_price";
        $query_params['max_price'] = $max_price;
    }

    // f. Filter Pencarian (Search)
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
    if (!empty($search_term)) {
        $search_safe = $conn->real_escape_string("%$search_term%");
        $where_clauses[] = "(p.name LIKE '$search_safe' OR p.sku LIKE '$search_safe')";
        $query_params['search'] = $search_term;
    }

    // g. Sorting (Urutan)
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'latest';
    $order_by = "ORDER BY p.id DESC"; // Default: Terbaru

    switch ($sort_by) {
        case 'price_asc':
            $order_by = "ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $order_by = "ORDER BY p.price DESC";
            break;
        case 'name_asc':
            $order_by = "ORDER BY p.name ASC";
            break;
        case 'name_desc':
            $order_by = "ORDER BY p.name DESC";
            break;
    }
    $query_params['sort_by'] = $sort_by;

    // h. Menggabungkan Klausul WHERE
    $where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";


    // ====================================================================
    // 3. EKSEKUSI QUERY
    // ====================================================================

    // Query untuk mengambil Kategori (untuk dropdown filter)
    $sql_categories = "SELECT name, slug FROM categories ORDER BY name ASC";
    $result_categories = $conn->query($sql_categories);
    $categories = [];
    if ($result_categories) {
        while ($row = $result_categories->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // Query COUNT Total Produk (untuk Pagination)
    $sql_count = "
        SELECT COUNT(p.id) as total_products
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        $where_sql
    ";
    $count_result = $conn->query($sql_count);
    $total_products = $count_result ? $count_result->fetch_assoc()['total_products'] : 0;
    $total_pages = ceil($total_products / $limit);

    // Query Utama untuk Produk
    // KOREKSI: Menggunakan 'p.stock_quantity' sesuai skema database
    $sql_products = "
        SELECT p.id, p.name, p.price, p.stock, c.name as category_name
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        $where_sql
        $order_by
        LIMIT $limit OFFSET $offset
    ";

    $result_products = $conn->query($sql_products);
    $products = [];
    if ($result_products) {
        while ($row = $result_products->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    // Fungsi untuk membuat URL Paging/Sorting
    function get_query_url($params_to_update = []) {
        global $query_params, $current_page;
        $params = array_merge($query_params, $params_to_update);
        // Hapus 'page' dari parameter jika hanya untuk sorting/filtering
        if (isset($params['page']) && $params['page'] == $current_page) {
             unset($params['page']);
        }
        return '?' . http_build_query($params);
    }
}
?>