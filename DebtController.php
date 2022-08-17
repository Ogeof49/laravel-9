<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use App\Models\Client;



class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Debt $debt, Request $request)
    {
               
        $clients = Client::all();
        $debts = Debt::latest();

        if($request->get('status') == 'archived') {
            $debts = $debts->onlyTrashed();
        }

        $debts = $debts->paginate(10);

        return view('admin.debts.index', compact('debts', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::all();
        return view('admin.debts.create')->withDebts($debts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Debt $debt, StoreDebtRequest $request)
    {
 
        //For demo purposes only. When creating stage or inviting a stage
        // you should create a generated random password and email it to the stage
        $debt->create(array_merge($request->validated()));

        return redirect()->route('debts.index')
            ->withSuccess(__('debt created successfully.'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Debt $debt) 
    {
        $debts = Debt::all();

        if($request->get('status') == 'bad') {
            $debts = $debts->onlyTrashed();
        }

        return view('admin.debts.edit', [
            'debt' => $debt,
            'debts' => $debts
        ]);
    }

    public function bad_debt(Debt $debt) 
    {
        $debts = Debt::all();
        $clients = Client::all();

        return view('admin.debts.bad_debt', compact('debts', 'clients'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Debt $debt, UpdateDebtRequest $request) 
    {
        $debt->update($request->validated());

        return redirect()->route('debts.index')
            ->withSuccess(__('debt updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Debt $debt) 
    {
        $debt->delete();

        return redirect()->route('debts.index')
            ->withSuccess(__('debt deleted successfully.'));
    }

    public function restore($id) 
    {
        Debt::where('id', $id)->withTrashed()->restore();

        return redirect()->route('debts.index', ['status' => 'archived'])
            ->withSuccess(__('debt restored successfully.'));
    }

    /**
     * Force delete stage data
     * 
     * @param stage $stage
     * 
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id) 
    {
        Debt::where('id', $id)->withTrashed()->forceDelete();

        return redirect()->route('debts.index', ['status' => 'archived'])
            ->withSuccess(__('debt force deleted successfully.'));
    }

    /**sho
     * Restore all archived stages
     * 
     * @param stage $stage
     * 
     * @return \Illuminate\Http\Response
     */
    public function restoreAll() 
    {
        Debt::onlyTrashed()->restore();

        return redirect()->route('debts.index')->withSuccess(__('All debts restored successfully.'));
    }
}

