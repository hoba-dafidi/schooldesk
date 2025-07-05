<?php

use App\Http\Controllers\Admin\SchoolController as AdminSchoolController;
use App\Http\Controllers\Admin\SubdomainController as AdminSubdomainController;
use App\Http\Controllers\Admin\CustomDomainController as AdminCustomDomainController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // 🔹 School Management
    Route::get('/schools', [AdminSchoolController::class, 'index'])->name('schools.index');
    Route::get('/schools/create', [AdminSchoolController::class, 'create'])->name('schools.create');
    Route::post('/schools', [AdminSchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools/{school}/edit', [AdminSchoolController::class, 'edit'])->name('schools.edit');
    Route::put('/schools/{school}', [AdminSchoolController::class, 'update'])->name('schools.update');
    Route::delete('/schools/{school}', [AdminSchoolController::class, 'destroy'])->name('schools.destroy');

    // 🔹 Subdomain Management
    Route::get('/subdomains', [AdminSubdomainController::class, 'index'])->name('subdomains.index');
    Route::post('/subdomains', [AdminSubdomainController::class, 'store'])->name('subdomains.store');
    Route::put('/subdomains/{subdomain}', [AdminSubdomainController::class, 'update'])->name('subdomains.update');
    Route::delete('/subdomains/{subdomain}', [AdminSubdomainController::class, 'destroy'])->name('subdomains.destroy');

    // 🔹 Custom Domain Management
    Route::get('/custom-domains', [AdminCustomDomainController::class, 'index'])->name('custom-domains.index');
    Route::post('/custom-domains', [AdminCustomDomainController::class, 'store'])->name('custom-domains.store');
    Route::put('/custom-domains/{custom_domain}', [AdminCustomDomainController::class, 'update'])->name('custom-domains.update');
    Route::delete('/custom-domains/{custom_domain}', [AdminCustomDomainController::class, 'destroy'])->name('custom-domains.destroy');
});
