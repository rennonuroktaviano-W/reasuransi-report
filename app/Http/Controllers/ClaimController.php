<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClaimController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ReinsuranceClaim::query()
            ->with('production:id,policy_number,insured_name')
            ->orderByDesc('created_at');

        $search = $request->input('search');
        if ($search) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                    ->orWhereHas('production', function ($p) use ($search) {
                        $p->where('policy_number', 'like', "%{$search}%");
                    });
            });
        }

        $status = $request->input('status');
        if ($status) {
            $query->where('claim_status', $status);
        }

        $claims = $query->paginate(10)->withQueryString();

        return Inertia::render('Claims/Index', [
            'claims' => $claims,
            'search' => $search,
            'status' => $status,
            'statuses' => ['Approved', 'Under Investigation', 'Paid', 'Rejected'],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Claims/Form', [
            'claim' => null,
            'productions' => ReinsuranceProduction::query()
                ->orderBy('policy_number')
                ->select('id', 'policy_number', 'insured_name')
                ->get(),
        ]);
    }

    public function store(StoreClaimRequest $request): RedirectResponse
    {
        ReinsuranceClaim::create($request->validated());

        return to_route('claims.index')
            ->with('success', 'Data klaim berhasil ditambahkan.');
    }

    public function edit(ReinsuranceClaim $claim): Response
    {
        return Inertia::render('Claims/Form', [
            'claim' => $claim->load('production:id,policy_number,insured_name'),
            'productions' => ReinsuranceProduction::query()
                ->orderBy('policy_number')
                ->select('id', 'policy_number', 'insured_name')
                ->get(),
        ]);
    }

    public function update(UpdateClaimRequest $request, ReinsuranceClaim $claim): RedirectResponse
    {
        $claim->update($request->validated());

        return to_route('claims.index')
            ->with('success', 'Data klaim berhasil diperbarui.');
    }

    public function destroy(ReinsuranceClaim $claim): RedirectResponse
    {
        $claim->delete();

        return to_route('claims.index')
            ->with('success', 'Data klaim berhasil dihapus.');
    }
}
