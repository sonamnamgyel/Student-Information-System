<?php

class AdminProgrammesController extends AdminController {


    /**
     * Programme Model
     * @var programme
     */
    protected $programme;

    /**
     * Inject the models.
     * @param Programme $programme
     */
    public function __construct(Programme $programme)
    {
        parent::__construct();
        $this->programme = $programme;
    }

    /**
     * Display a listing of the programme.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = Lang::get('admin/programmes/title.programme_management');
        // Grab all the programmes
    	$programmes = $this->programme;
        // Show the page
        return View::make('admin/programmes/index', compact('programmes', 'title'));
    }

    /**
     * Show the form for adding a new programme.
     *
     * @return Response
     */

    public function getAdd()
    {
        //title
        $title = Lang::get('admin/programmes/title.add_a_new_programme');

        // Mode
        $mode = 'add';

        $programme = new Programme;
        $department_lists = Department::lists('department_name', 'id');

        // Show the page
        return View::make('admin/programmes/add_edit', compact('programme','title', 'mode', 'department_lists'));
    }
    
    /**
     * Store a newly added programme in storage.
     *
     * @return Response
     */
    public function postAdd()
    {
        $rules = array(
            'programme_code' => 'required|unique:programmes,programme_code',
            'programme_name' => 'required|unique:programmes,programme_name',
            'department_id' => 'required'
        );
        $customMessages = array(
           'department_id.required' => 'The department field is required.'
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            // add a new programme
            $user = Auth::user();

            //add the programme information
            $this->programme->programme_code = Input::get('programme_code');
            $this->programme->programme_name = Input::get('programme_name');
            $this->programme->department_id = Input::get('department_id');
            
            // was the new programme added?
            if($this->programme->save())
            {
                // Redirect to the new programme edit page if successful
                return Redirect::to('admin/programmes/' . $this->programme->id . '/edit')
                    ->with('success', Lang::get('admin/programmes/messages.add.success'));
            }

            // Redirect to the new programme add page if failed
            return Redirect::to('admin/programmes/add')
                ->with('error', Lang::get('admin/programmes/messages.add.error'));
        }
        // Form validation failed
        return Redirect::to('admin/programmes/add')->withInput()->withErrors($validator);
    }
    
    /**
     * Show the form for editing a programme.
     *
     * @return Response
     */
    public function getEdit($programme)
    {
        if( $programme->id) {
            $title = Lang::get('admin/programmes/title.programme_update');
            $mode = 'edit';
            $department_lists = Department::lists('department_name', 'id');
            
            return View::make('admin/programmes/add_edit', compact('programme', 'title', 'mode', 'department_lists'));
        }
        else
        {
            return Redirect::to('admin/programmes')->with('error', Lang::get('admin/programmes/messages.does_not_exist'));
        }
    }
    /**
     * Update the specified programme in storage.
     *
     * @return Response
     */
    
    public function postEdit($programme)
    {
        $rules = array(
            'programme_code' => 'required|unique:programmes,programme_code,'.$programme->id,
            'programme_name' => 'required|unique:programmes,programme_name,'.$programme->id,
            'department_id' => 'required'
        );
        $customMessages = array(
           'department_id.required' => 'The department field is required.'
        );
        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules, $customMessages);

        if ($validator->passes())
        {
            $user = Auth::user();
            //update the programme information
            $programme->programme_code = Input::get('programme_code');
            $programme->programme_name = Input::get('programme_name');
            $programme->department_id = Input::get('department_id');
            
            // Save if valid
            $programme->save();

        } else {
            return Redirect::to('admin/programmes/' . $programme->id . '/edit')->withErrors($validator);
        }

        // if no error on the model
        if(empty($programme->errors)) {
            // Redirect to the edit page with success message
            return Redirect::to('admin/programmes/' . $programme->id . '/edit')
                ->with('success', Lang::get('admin/programmes/messages.edit.success'));
        }
        // if error found on model
        else {
            // Redirect to the edit page with failure message
            return Redirect::to('admin/programmes/' . $programme->id . '/edit')
                ->withInput()
                ->withErrors($programme->errors);
        }
    }
    /**
     * Remove programme page.
     *
     * @param $programme
     * @return Response
     */
    public function getDelete($programme)
    {
        // Title
        $title = Lang::get('admin/programmes/title.programme_delete');

        // Show the page
        return View::make('admin/programmes/delete', compact('programme', 'title'));
    }

    /**
     * Remove the specified programme from storage.
     *
     * @param $programme
     * @return Response
     */
    public function postDelete($programme)
    {
        $id = $programme->id;
        
        $programme->delete();
        // Was the programme deleted?
        $programme = Programme::find($id);
        if ( empty($programme) )
        {
            // TODO needs to delete all of that programme's content
            return Redirect::to('admin/programmes')->with('success', Lang::get('admin/programmes/messages.delete.success'));
        }
        else
        {
            // There was a problem deleting the programme
            return Redirect::to('admin/programmes')->with('error', Lang::get('admin/programmes/messages.delete.error'));
        }
    }

    /**
     * Show a list of all the programmes formatted for Datatables.
     *
     * @return Datatables JSON
     */
    public function getData()
    {        
        $programmes = Programme::select(array('programmes.id', 'programmes.programme_code', 'programmes.programme_name', 
        	'programmes.department_id', 'programmes.id as tutor', 'programmes.id as student', 'id as actions' ))
            ->orderBy('programme_name');

        return Datatables::of($programmes)
            ->add_column('slno', '', 1)
            ->edit_column('department_id',function($row){
                $dep = Department::find($row->department_id);
                if($dep)
                    return $dep->department_name;
                else
                    return "--";
            })
            ->edit_column('student', '<span class="label label-success"> {{{ Student::where(\'programme_id\', $id)->where(\'registered\',\'Yes\')->count()  }}}</span>')
            ->edit_column('actions', '
            <div class="btn-group">
            <a href="{{{ URL::to(\'admin/programmes/\' . $id . \'/edit\' ) }}}" class="iframe btn btn-xs btn-primary"><i class="fa fa-pencil"></i> {{{ Lang::get(\'button.edit\') }}}</a>
            <a href="{{{ URL::to(\'admin/programmes/\' . $id . \'/delete\' ) }}}" class="iframe btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> {{{ Lang::get(\'button.delete\') }}}</a>
            </div>
            ')
            ->remove_column('id')
            ->make();
    }
}
