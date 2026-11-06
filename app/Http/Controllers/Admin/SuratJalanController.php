<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDocument;
use App\Models\TariffSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryDocument::with([
            'order.customer.user',
            'assignment.driver.user',
            'assignment.vehicle',
            'issuer'
        ])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%")
                         ->orWhereHas('customer', function ($cq) use ($search) {
                             $cq->where('company_name', 'like', "%{$search}%");
                         });
                  })
                  ->orWhereHas('assignment.vehicle', function ($vq) use ($search) {
                      $vq->where('plate_number', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->paginate(10)->withQueryString();

        return view('admin.surat-jalan.index', compact('documents'));
    }

    public function show(DeliveryDocument $document)
    {
        $document->load([
            'order.customer.user',
            'assignment.driver.user',
            'assignment.vehicle',
            'issuer'
        ]);

        $tariff = TariffSetting::current();

        return view('admin.surat-jalan.show', compact('document', 'tariff'));
    }

    public function print(DeliveryDocument $document)
    {
        $document->load([
            'order.customer.user',
            'assignment.driver.user',
            'assignment.vehicle',
            'issuer'
        ]);

        $tariff = TariffSetting::current();

        return view('admin.surat-jalan.print', compact('document', 'tariff'));
    }

    public function downloadPdf(DeliveryDocument $document)
    {
        $document->load([
            'order.customer.user',
            'assignment.driver.user',
            'assignment.vehicle',
            'issuer'
        ]);

        $tariff = TariffSetting::current();

        $pdf = Pdf::loadView('admin.surat-jalan.pdf', compact('document', 'tariff'))
            ->setPaper('a4', 'portrait');

        $safeFilename = str_replace('/', '-', $document->document_number) . '.pdf';

        return $pdf->download($safeFilename);
    }
}

