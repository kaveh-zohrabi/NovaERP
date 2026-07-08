<?php

namespace App\Providers;

use App\Events\Audit\ModelAuditableEvent;
use App\Listeners\CreateAuditLogListener;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Stock;
use App\Models\User;
use App\Observers\AuditObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(ModelAuditableEvent::class, CreateAuditLogListener::class);

        User::observe(AuditObserver::class);
        Company::observe(AuditObserver::class);
        Product::observe(AuditObserver::class);
        Customer::observe(AuditObserver::class);
        PurchaseOrder::observe(AuditObserver::class);
        SalesOrder::observe(AuditObserver::class);
        Invoice::observe(AuditObserver::class);
        JournalEntry::observe(AuditObserver::class);
        Lead::observe(AuditObserver::class);
        Opportunity::observe(AuditObserver::class);
        Stock::observe(AuditObserver::class);
    }
}
