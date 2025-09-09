<?php

namespace Webkul\Invoice\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Invoice\Contracts\Invoice;

class InvoiceRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'subject',
        'description',
        'person_id',
        'person.name',
        'user_id',
        'user.name',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected InvoiceItemRepository $invoiceItemRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Invoice::class;
    }

    /**
     * Create.
     *
     * @return \Webkul\Invoice\Contracts\Invoice
     */
    public function create(array $data)
    {
        $invoice = parent::create($data);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $invoice->id,
        ]));

        foreach ($data['items'] as $itemData) {
            $this->invoiceItemRepository->create(array_merge($itemData, [
                'invoice_id' => $invoice->id,
            ]));
        }

        return $invoice;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  array  $attribute
     * @return \Webkul\Invoice\Contracts\Invoice
     */
    public function update(array $data, $id, $attributes = [])
    {
        $invoice = $this->find($id);

        parent::update($data, $id);

        /**
         * If attributes are provided then only save the provided attributes and return.
         */
        if (! empty($attributes)) {
            $conditions = ['entity_type' => $data['entity_type']];

            if (isset($data['quick_add'])) {
                $conditions['quick_add'] = 1;
            }

            $attributes = $this->attributeRepository->where($conditions)
                ->whereIn('code', $attributes)
                ->get();

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $invoice->id,
            ]), $attributes);

            return $invoice;
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $invoice->id,
        ]));

        $previousItemIds = $invoice->items->pluck('id');

        if (isset($data['items'])) {
            foreach ($data['items'] as $itemId => $itemData) {
                if (Str::contains($itemId, 'item_')) {
                    $this->invoiceItemRepository->create(array_merge($itemData, [
                        'invoice_id' => $id,
                    ]));
                } else {
                    if (is_numeric($index = $previousItemIds->search($itemId))) {
                        $previousItemIds->forget($index);
                    }

                    $this->invoiceItemRepository->update($itemData, $itemId);
                }
            }
        }

        foreach ($previousItemIds as $itemId) {
            $this->invoiceItemRepository->delete($itemId);
        }

        return $invoice;
    }

    /**
     * Retrieves customers count based on date.
     *
     * @return number
     */
    public function getInvoicesCount($startDate, $endDate)
    {
        return $this
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->count();
    }
}
