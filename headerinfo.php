<?php

include 'connect.php';

/* FETCH BRANDS */
$brandsQuery = mysqli_query($con, "SELECT * FROM brands WHERE display_status = 1");

$brands = [];

while($row = mysqli_fetch_assoc($brandsQuery)) {

    $brands[] = [
        "name" => $row['brandname'],
        "url"  => "all-products.php?brand=" . urlencode($row['brandname'])  // Fixed: was ?category=
    ];
}

/* FETCH CATEGORIES */
$categoriesQuery = mysqli_query($con, "SELECT * FROM category WHERE display_status = 1");

$categories = [];

while($row = mysqli_fetch_assoc($categoriesQuery)) {

    $categories[] = [
        "name" => $row['cname'],
        "url"  => "all-categories.php?category=" . urlencode($row['cname'])
    ];
}

?>