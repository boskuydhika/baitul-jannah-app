<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Display a listing of the accounts.
     */
    public function index(Request $request): Response
    {
        $accounts = Account::query()
            ->orderBy('code')
            ->get()
            ->append(['formatted_balance', 'type_label']);

        return Inertia::render('Finance/Accounts/Index', [
            'accounts' => $accounts,
        ]);
    }
}
