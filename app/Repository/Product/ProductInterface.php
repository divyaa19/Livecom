<?php

namespace App\Repository\Product;

interface ProductInterface
{

    public function one(int $product_id);

    public function all(String $seller_id);

    public function search(String $search);

    public function home();

    public function allStreamProduct(String $seller_id);

    public function allStoreProduct(String $seller_id);

}
