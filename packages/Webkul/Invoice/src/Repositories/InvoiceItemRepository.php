<?php

namespace Webkul\Invoice\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Repositories\ProductRepository;

class InvoiceItemRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Invoice\Contracts\InvoiceItem';
    }

    /**
     * @return mixed
     */
    public function create(array $data)
    {
        if (empty($data['product_id'])) {
            return null;
        }

        $product = $this->productRepository->findOrFail($data['product_id']);

        $invoiceItem = parent::create(array_merge($data, [
            'sku'  => $product->sku,
            'name' => $product->name,
        ]));

        return $invoiceItem;
    }

    /**
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Invoice\Contracts\InvoiceItem
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $product = $this->productRepository->findOrFail($data['product_id']);

        $invoiceItem = parent::update(array_merge($data, [
            'sku'  => $product->sku,
            'name' => $product->name,
        ]), $id);

        return $invoiceItem;
    }
}
