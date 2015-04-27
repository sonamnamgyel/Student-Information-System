<?php

class AdminAccountsController extends AdminController {


    /**
     * Account Model
     * @var Account
     */
    protected $account;
    
    /**
     * Student Model
     * @var Student
     */
    protected $student;

    /**
     * Inject the models.
     * @param Account $account
     * @param Student $student
     */
    public function __construct(Account $account, Student $student)
    {
        parent::__construct();
        $this->account = $account;
        $this->student = $student;
    }

    /**
     * Display a listing of the students for fee payment.
     *
     * @return Response
     */

    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/accounts/title.account_management');

        $account = $this->account;
        $departments = Department::lists('department_name', 'id');
        $programmes = Programme::lists('programme_code', 'id');
        $data = null;
        // Show the page with list of all students
        return View::make('admin/accounts/index', compact('title','account', 'departments' ,'programmes','data'));
    }
    
    public function postIndex()
    {
        // Title
        $title = Lang::get('admin/accounts/title.account_management');
        $account = $this->account;
        $programmes = Programme::lists('programme_code', 'id');
        $departments = Department::lists('department_name', 'id');
        
        // The following order is maintained in the route
        // This order should not break!!!
        $data['department'] = Input::get('department');
        $data['programme'] = Input::get('programme');
        $data['stdtype'] = Input::get('stdtype');
        $data['resident'] = Input::get('resident');
        $data['semester'] = Input::get('semester');
        $data['repeat'] = Input::get('repeat');
        $data['fee'] = Input::get('fee');
        $data['registered'] = Input::get('registered');
       
        // Show the page with list of all students
        return View::make('admin/accounts/index', compact('title','account', 'departments', 'programmes','data'));
    }

    /**
     * Show a list of student search results categorized by different fields and is formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData($dep=null, $prog=null, $type=null, $resident=null, $sem=null, $repeat=null, $fee_paid=null, $regis=null)
    {
        $searchResults = Student::leftJoin('programmes','students.programme_id','=','programmes.id')
            ->leftJoin('semesters', 'students.current_semester', '=', 'semesters.number')
            ->leftJoin('departments', 'programmes.department_id','=', 'departments.id')
            ->leftJoin('fees', 'students.id', '=', 'fees.student_id')
            ->leftJoin('course_student', 'students.id', '=', 'course_student.student_id')
            ->select(array('students.id','students.stdno',DB::raw('CONCAT_WS(\' \',students.fname,students.mname, students.lname) as name'),
                'students.stdtype', 'semesters.roman', 'programmes.programme_code', 'students.registered', 'fees.receipt_no', 'fees.paid','students.id as actions'))
            ->groupBy('students.id')
            ->orderBy('students.programme_id')
            ->orderBy('students.current_semester','desc')
            ->orderBy('students.fname')
            ->where(function($query) use ($dep, $prog, $type, $resident, $sem, $repeat, $fee_paid, $regis)
            {
                if($dep)
                    $query->where('departments.id', $dep);

                if($prog)
                    $query->where('students.programme_id', $prog);

                if($type)
                    $query->where('students.stdtype', $type);

                if($sem)
                    $query->where('students.current_semester', $sem);

                if($regis=='No'){
                    $query->where(function($query2) use ($regis) {
                        $query2->where('registered', 'No')
                            ->orWhere('registered', '')
                            ->orWhereNull('registered');
                   });
                }
                if($regis=='Yes')
                    $query->where('students.registered', 'Yes');
                
                if($fee_paid=='No'){
                    $query->where(function($query2) use ($fee_paid) {
                        $query2->where('fees.paid', 'No')
                            ->orWhere('fees.paid', '')
                            ->orWhereNull('fees.paid');
                   });
                }
                if($fee_paid=='Yes')
                    $query->where('fees.paid', 'Yes');

                if($resident)
                    $query->where('students.resident', $resident);

                if($repeat){
                    if($repeat == 'both'){
                        $query->where(function($query2) use ($repeat) {
                            $query2->where('course_student.type', 'Back')
                                    ->orWhere('course_student.type', 'Superback');
                        });
                    }else {
                        $query->where('course_student.type', $repeat);
                    }
                }

            });

        return Datatables::of($searchResults)
            ->edit_column('paid','@if($paid=="No")
                                        <span class="label label-danger">No</span>
                                    @elseif($paid=="Yes")
                                        Yes
                                    @endif')
            ->edit_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/accounts/\' . $id . \'/fee\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-paypal"></i>{{{ Lang::get(\'button.fee-payment\') }}}  </a>
            </div>
            ')
            ->make();
    }

    /**
     * Show the form for updating the fees of a student.
     *
     * @return Response
     */
    public function getFee($student)
    {
        if( $student->id) {
            $title = Lang::get('admin/accounts/title.fee_update');
            $mode = 'view';
            $prog = $student->programme;
            $account = Account::where('student_id', $student->id)->first();
            if($prog)
                $programme_name = $prog->programme_name;
            else
                $programme_name = "--";

            $semesters = DB::table('course_student')
                            ->leftJoin('course_programme','course_student.course_programme_id', '=', 'course_programme.pivot_id')
                            ->select('course_programme.semester_taken')
                            ->where('course_student.student_id', '=', $student->id)
                            ->groupBy('semester_taken')
                            ->orderBy('semester_taken', 'desc')
                            ->get();

            return View::make('admin/accounts/fee', compact('student','account', 'title', 'programme_name','semesters'));
        }
        else
        {
            return Redirect::to('admin/accounts')->with('error', Lang::get('admin/accounts/messages.does_not_exist'));
        }
    }
    
    /**
     * Update the fees of a specified student in storage.
     *
     * @return Response
     */
    
    public function postFee($student)
    {
        $rules = array(
            'paid' => 'required',
            'receipt_no' =>'required_if:paid,"Yes"',
            'amount' =>'required_if:paid,"Yes"',
        );
        $customMessages = array(
           'paid.required' => 'The fee paid field is required.',
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            $receipt_no = Input::get('receipt_no');
            $amount = Input::get('amount');

            $account = Account::firstOrNew(['student_id'=>$student->id]);
            $account->paid = Input::get('paid');
            if($account->paid=='Yes'){
                $account->receipt_no = $receipt_no;
                $account->amount = $amount;
            }else{
                $account->receipt_no = null;
                $account->amount = null;
            }

            if($account->save())
            {
                return Redirect::to('admin/accounts/' . $student->id . '/fee')
                    ->with('success', Lang::get('admin/accounts/messages.update.success'));
            }else{
                return Redirect::to('admin/accounts/' . $student->id . '/fee')
                ->with('error', Lang::get('admin/accounts/messages.update.error'));
            }

        } else {
            return Redirect::to('admin/accounts/' . $student->id . '/fee')->withErrors($validator);
        }       
    }
}