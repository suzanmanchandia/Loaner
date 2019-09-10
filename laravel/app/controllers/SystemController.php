<?php

class SystemController extends \Loaner\AdminController
{

    /**
     * Get the current item to work on
     * @return string
     */
    private function _item()
    {
        list($system, $item) = explode('.', Route::currentRouteName());

        return $item;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->layout->content = View::make(
            'system.index',
            array(
                'areas'         => $this->dept->accesssAreas()->with('users')->get(),
                'categories'    => EquipmentCategory::query()->with('subCategories')->get(),
                'classes'       => $this->dept->classes()->get(),
                'locations'     => $this->dept->locations()->get(),
                'payments'      => $this->dept->finePayments()->get(),
                'emails'        => NotificationEmail::query()->get(),
                'notifications' => $this->dept->emails()->get(),
                'fine'          => $this->dept->fineDefault()->count() ? $this->dept->fineDefault : new FineDefault(),
            )
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $item  = null;
        $title = Input::get('title');

        $rules = array();

        switch ($this->_item()) {
            // Access Areas
            case 'access':
                $item                = new AccessArea();
                $item->deptID        = $this->deptID;
                $item->accessarea    = $title;
                $rules['accessarea'] = sprintf('required|unique:%s,accessarea,NULL,accessarea,deptID,%s', $item->getTable(), $this->deptID);
                break;
            // Categories
            case 'categories':
                $item                  = new EquipmentCategory();
                $item->equipCatName    = $title;
                $rules['equipCatName'] = sprintf('required|unique:%s', $item->getTable());
                break;
            // Sub Categories
            case 'subcategories':
                $item                  = new EquipmentSubCategory();
                $item->equipSubName    = $title;
                $item->equipCatID      = Input::get('category');
                $rules['equipCatID']   = 'required';
                $rules['equipSubName'] = sprintf(
                    'required|unique:%s,equipSubName,NULL,equipSubName,equipCatID,%d',
                    $item->getTable(),
                    $item->equipCatID
                );
                break;
            // Classes
            case 'classes':
                $item               = new Classes();
                $item->deptID       = $this->deptID;
                $item->classname    = $title;
                $rules['classname'] = sprintf('required|unique:%s,classname,NULL,classname,deptID,%d', $item->getTable(), $this->deptID);
                break;
            // Locations
            case 'locations':
                $item                  = new Location();
                $item->deptID          = $this->deptID;
                $item->locationName    = $title;
                $rules['locationName'] = sprintf('required|unique:%s,locationName,NULL,locationName,deptID,%d', $item->getTable(), $this->deptID);
                break;
            // Emails
            case 'emails':
                $item             = new NotificationEmail();
                $item->emailid    = $title;
                $rules['emailid'] = sprintf('required|email|unique:%s', $item->getTable());
                break;
            // Department Notifications
            case 'notifications':
                $item             = new DepartmentEmail();
                $item->email      = $title;
                $item->deptID     = $this->deptID;
                $rules['email'] = sprintf('required|email|unique:%s,email,NULL,email,deptID,%d', $item->getTable(), $this->deptID);
                break;
        }

        if (!$item) {
            return $this->errorResponse(array('Invalid request.'));
        }

        if (!$item->save($rules)) {
            return $this->errorResponse($item->errors()->all());
        } else {
            return $this->dataResponse(
                array(
                    'id'    => $item->getKey(),
                    'title' => $title,
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $item  = null;
        $title = Input::get('title');

        $rules = array();

        try {
            switch ($this->_item()) {
                // Access Areas
                case 'access':
                    $item                = AccessArea::findOrFail($id);
                    $item->deptID        = $this->deptID;
                    $item->accessarea    = $title;
                    $rules['accessarea'] = sprintf('required|unique:%s,accessarea,NULL,accessarea,deptID,%s', $item->getTable(), $this->deptID);
                    break;
                // Categories
                case 'categories':
                    $item                  = EquipmentCategory::findOrFail($id);
                    $item->equipCatName    = $title;
                    $rules['equipCatName'] = sprintf('required|unique:%s', $item->getTable());
                    break;
                // Sub Categories
                case 'subcategories':
                    $item                  = EquipmentSubCategory::findOrFail($id);
                    $item->equipSubName    = $title;
                    $rules['equipSubName'] = sprintf(
                        'required|unique:%s,equipSubName,NULL,equipSubName,equipCatID,%d',
                        $item->getTable(),
                        $item->equipCatID
                    );
                    break;
                // Classes
                case 'classes':
                    $item               = Classes::findOrFail($id);
                    $item->deptID       = $this->deptID;
                    $item->classname    = $title;
                    $rules['classname'] = sprintf('required|unique:%s,classname,NULL,classname,deptID,%d', $item->getTable(), $this->deptID);
                    break;
                // Locations
                case 'locations':
                    $item                  = Location::findOrFail($id);
                    $item->deptID          = $this->deptID;
                    $item->locationName    = $title;
                    $rules['locationName'] = sprintf('required|unique:%s,locationName,NULL,locationName,deptID,%d', $item->getTable(), $this->deptID);
                    break;
                // Emails
                case 'emails':
                    $item             = NotificationEmail::findOrFail($id);
                    $item->emailid    = $title;
                    $rules['emailid'] = sprintf('required|email|unique:%s', $item->getTable());
                    break;
                // Department Notifications
                case 'notifications':
                    $item             = $this->dept->emails()->findOrFail($id);
                    $item->email      = $title;
                    $rules['email'] = sprintf('required|email|unique:%s,email,NULL,email,deptID,%d', $item->getTable(), $this->deptID);
                    break;
                // Default Fine
                case 'fines':
                    $item                 = $this->dept->fineDefault()->count() ? $this->dept->fineDefault : new FineDefault();
                    $item->defaultFine    = $title;
                    $item->deptID         = $this->deptID;
                    $rules['defaultFine'] = 'required|integer|min:0';
                    break;
            }

            if (!$item) {
                return $this->errorResponse(array('Invalid request.'));
            }
        } catch (ModelNotFoundException $ex) {
            return $this->errorResponse(array('The item you tried to update does not exist!'));
        }
        /* @var $item \Loaner\Model */

        if (!$item->updateUniques($rules)) {
            return $this->errorResponse($item->errors()->all());
        } else {
            return $this->successResponse('Item updated!');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $item  = null;

        try {
            switch ($this->_item()) {
                // Access Areas
                case 'access':
                    $item = AccessArea::findOrFail($id);
                    break;
                // Categories
                case 'categories':
                    $item = EquipmentCategory::findOrFail($id);
                    break;
                // Sub Categories
                case 'subcategories':
                    $item = EquipmentSubCategory::findOrFail($id);
                    break;
                // Classes
                case 'classes':
                    $item = Classes::findOrFail($id);
                    break;
                // Locations
                case 'locations':
                    $item = Location::findOrFail($id);
                    break;
                // Emails
                case 'emails':
                    $item = NotificationEmail::findOrFail($id);
                    break;
            }

            if (!$item) {
                return $this->errorResponse(array('Invalid request.'));
            }
        } catch (ModelNotFoundException $ex) {
            return $this->errorResponse(array('The item you tried to delete does not exist!'));
        }
        /* @var $item \Loaner\Model */

        $item->delete();
        return $this->successResponse('Item deleted!');
    }


}
