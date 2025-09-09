<?php

namespace Webkul\Invoice\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\Invoice\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        // Simple index; dedicated datagrid is not present. Render static admin view.
        return view('admin::invoices.index');
    }

    public function create()
    {
        return view('admin::invoices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'       => 'nullable|string|max:191',
            'amount'       => 'nullable|numeric',
            'status'       => 'nullable|string|max:50',
            'invoice_date' => 'nullable|date',
            'due_date'     => 'nullable|date',
        ]);

        // Fallback values if not provided
        if (!isset($data['number']) || $data['number'] === '') {
            $data['number'] = 'INV-' . str_pad((string) (Invoice::max('id') + 1), 6, '0', STR_PAD_LEFT);
        }

        // Minimal create to avoid schema mismatch; save only known columns if exist.
        $invoice = new Invoice();
        foreach ($data as $k => $v) {
            if (in_array($k, $invoice->getFillable())) {
                $invoice->{$k} = $v;
            }
        }
        $invoice->save();

        session()->flash('success', __('admin::app.common.create-success'));
        return redirect()->route('admin.invoices.index');
    }

    public function edit($id)
    {
        $invoice = Invoice::query()->findOrFail($id);
        return view('admin::invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::query()->findOrFail($id);

        $data = $request->validate([
            'number'       => 'nullable|string|max:191',
            'amount'       => 'nullable|numeric',
            'status'       => 'nullable|string|max:50',
            'invoice_date' => 'nullable|date',
            'due_date'     => 'nullable|date',
        ]);

        foreach ($data as $k => $v) {
            if (in_array($k, $invoice->getFillable())) {
                $invoice->{$k} = $v;
            }
        }
        $invoice->save();

        session()->flash('success', __('admin::app.common.update-success'));
        return redirect()->route('admin.invoices.index');
    }

    public function destroy($id)
    {
        $invoice = Invoice::query()->findOrFail($id);
        $invoice->delete();

        session()->flash('success', __('admin::app.common.delete-success'));
        return redirect()->route('admin.invoices.index');
    }

    // Optional endpoints used by routes file; provide no-op to avoid 404s
    public function search(Request $request)
    {
        return redirect()->route('admin.invoices.index');
    }

    public function massDestroy(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        if ($ids) {
            Invoice::whereIn('id', $ids)->delete();
        }
        session()->flash('success', __('admin::app.common.delete-success'));
        return redirect()->route('admin.invoices.index');
    }

    public function print($id)
    {
        // Simple redirect; later you can render pdf view 'admin::invoices.pdf'
        return redirect()->route('admin.invoices.index');
    }
}