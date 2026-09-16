<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\ReinsuranceProduction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ReinsuranceProduction::query()
            ->orderByDesc('created_at');

        $search = $request->input('search');
        if ($search) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('policy_number', 'like', "%{$search}%")
                    ->orWhere('insured_name', 'like', "%{$search}%");
            });
        }

        $productions = $query->paginate(10)->withQueryString();

        return Inertia::render('Productions/Index', [
            'productions' => $productions,
            'search' => $search,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Productions/Form', [
            'production' => null,
        ]);
    }

    public function store(StoreProductionRequest $request): RedirectResponse
    {
        ReinsuranceProduction::create($request->validated());

        $redirect = to_route('productions.index')
            ->with('success', 'Data polis berhasil ditambahkan.');

        return $this->appendUPMismatchWarning($request->validated(), $redirect);
    }

    public function edit(ReinsuranceProduction $production): Response
    {
        return Inertia::render('Productions/Form', [
            'production' => $production,
        ]);
    }

    public function update(UpdateProductionRequest $request, ReinsuranceProduction $production): RedirectResponse
    {
        $production->update($request->validated());

        $redirect = to_route('productions.index')
            ->with('success', 'Data polis berhasil diperbarui.');

        return $this->appendUPMismatchWarning($request->validated(), $redirect);
    }

    private function appendUPMismatchWarning(array $data, RedirectResponse $redirect): RedirectResponse
    {
        $sum = (float) $data['sum_insured'];
        $retention = (float) $data['retention'];
        $ceded = (float) $data['ceded_amount'];
        $tolerance = max($sum * 0.01, 10000);

        if (abs(($retention + $ceded) - $sum) > $tolerance) {
            $total = $retention + $ceded;
            $redirect->with('warning', sprintf(
                'Perhatian: Uang Pertanggungan (UP Utama) sebesar Rp %s belum sama dengan jumlah Sendiri (Retention) ditambah UP Direasuransikan (Ceded) sebesar Rp %s. Mohon dicek kembali.',
                number_format($sum),
                number_format($total)
            ));
        }

        return $redirect;
    }

    public function destroy(ReinsuranceProduction $production): RedirectResponse
    {
        if ($production->claims()->exists()) {
            return to_route('productions.index')
                ->with('error', 'Polis tidak dapat dihapus karena masih memiliki data klaim.');
        }

        $production->delete();

        return to_route('productions.index')
            ->with('success', 'Data polis berhasil dihapus.');
    }
}
