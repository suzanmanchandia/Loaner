<?php

class FineController extends \Loaner\AdminController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = $this->dept->users()->where('fine', '<>', 0)->get();

        $this->layout->content = View::make( 'fines.index', array('users' => $users) );
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $total = 0;
        $totalPayments = 0;

        $loans = $user->loans()->with('kit', 'equipment')->withFines()->get();
        $payments = $user->finePayments;

        foreach ($loans as $loan)
        {
            $total += $loan->fine;
        }

        foreach ($payments as $payment)
        {
            $totalPayments += $payment->amount;
        }

        $user->calculateFine();

        $this->layout = View::make('fines.show', array(
                'user' => $user,
                'loans' => $loans,
                'total' => $total,
                'totalPayments' => $totalPayments,
                'balance' => $total - $totalPayments,
                'payments' => $payments,
            ));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        FinePayment::unguard();

        $validator = Validator::make(Input::all(), array(
                'amount' => 'required|min:1|max:'.$user->fine,
            ));

        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->all());
        }

        $amount = (float) Input::get('amount');

        $payment = new FinePayment();

        $payment->fill(array(
                'userid' => $user->userid,
                'userNum' => $user->userNum,
                'amount' => $amount
            ))->save();

        $user->calculateFine();

        return $this->dataResponse(array(
                'fine' => $user->fine,
            ));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
