<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Accounting\StoreAccountRequest;
use App\Http\Requests\Accounting\UpdateAccountRequest;
use App\Models\ChartOfAccount;
use App\Services\ChartOfAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartOfAccountController extends Controller
{
    public function __construct(
        private readonly ChartOfAccountService $accountService,
    ) {}

    public function index(Request $request): View
    {
        $accounts = ChartOfAccount::withCount('children')
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
            )
            ->whereNull('parent_id')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('accounting.accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $parentAccounts = ChartOfAccount::whereNull('parent_id')->orderBy('code')->get();

        return view('accounting.accounts.create', compact('parentAccounts'));
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $account = $this->accountService->create($request->validated(), $request->user());

        return redirect()->route('accounts.show', $account)->with('success', 'Account created successfully.');
    }

    public function show(ChartOfAccount $account): View
    {
        $account->load('children', 'parent');

        return view('accounting.accounts.show', compact('account'));
    }

    public function edit(ChartOfAccount $account): View
    {
        $parentAccounts = ChartOfAccount::whereNull('parent_id')
            ->where('id', '!=', $account->id)
            ->orderBy('code')
            ->get();

        return view('accounting.accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(UpdateAccountRequest $request, ChartOfAccount $account): RedirectResponse
    {
        $account = $this->accountService->update($account, $request->validated());

        return redirect()->route('accounts.show', $account)->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $account): RedirectResponse
    {
        $result = $this->accountService->delete($account);

        return back()->with('success', $result['message']);
    }
}
