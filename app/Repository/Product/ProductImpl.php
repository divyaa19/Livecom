<?php

namespace App\Repository\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductImpl implements ProductInterface
{

    protected Model $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function one(int $product_id)
    {
        return $this->product::with([
            'media',
            'specifications' => function ($query) {
                $query->select('id', 'product_id', 'specification_title', 'specification_value');
            },
            'shipment.regions',
            'buyModeData',
            'variation',
            'variations'
        ])
            ->find($product_id);
    }

    public function search(string $search)
    {
        return $this->product::with([
            'image'
        ])
            ->where('title', 'LIKE', $search)
            ->get();
    }


    public function all(string $seller_id)
    {
        return $this->product::with([
            'media',
            'specifications' => function ($query) {
                $query->select('id', 'product_id', 'specification_title', 'specification_value');
            },
            'shipment.regions',
            'buyModeData',
            'variations'
        ])
            ->where('store_id', $seller_id)
            ->paginate();
    }

    public function allStreamProduct(string $seller_id)
    {
        return $this->product::with([
            'media',
            'specifications' => function ($query) {
                $query->select('id', 'product_id', 'specification_title', 'specification_value');
            },
            'shipment.regions',
            'buyModeData',
            'variations'
        ])
            ->where('store_id', $seller_id)
            ->where('sell_mode', 'livestream')
            ->paginate();
    }

    public function allStoreProduct(string $seller_id)
    {
        return $this->product::with([
            'media',
            'specifications' => function ($query) {
                $query->select('id', 'product_id', 'specification_title', 'specification_value');
            },
            'shipment.regions',
            'buyModeData',
            'variations'
        ])
            ->where('store_id', $seller_id)
            ->where('sell_mode', 'livestore')
            ->paginate();
    }

    public function home()
    {
        return $this->product::with([
            'media',
            'specifications' => function ($query) {
                $query->select('id', 'product_id', 'specification_title', 'specification_value');
            },
            'shipment.regions',
            'buyModeData',
            'variations'
        ])
            ->where('status', 1)
            ->paginate();
    }
}
