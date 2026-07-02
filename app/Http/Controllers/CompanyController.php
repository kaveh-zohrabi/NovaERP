<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {}

    /**
     * Display a listing of companies.
     */
    public function index(): View
    {
        $companies = Company::withCount('users')
            ->latest()
            ->paginate(10);

        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View
    {
        return view('companies.create');
    }

    /**
     * Store a newly created company.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $company = $this->companyService->create(
            $request->validated(),
            $request->user(),
        );

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): View
    {
        $company->load('users', 'createdBy');

        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): View
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified company.
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $company = $this->companyService->update($company, $request->validated());

        return redirect()
            ->route('companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Activate the specified company.
     */
    public function activate(Company $company): RedirectResponse
    {
        $this->companyService->activate($company);

        return back()->with('success', 'Company activated.');
    }

    /**
     * Deactivate the specified company.
     */
    public function deactivate(Company $company): RedirectResponse
    {
        $this->companyService->deactivate($company);

        return back()->with('success', 'Company deactivated.');
    }
}
