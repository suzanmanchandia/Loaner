
    {{ Form::select('cat', array('' => 'All Categories') + EquipmentCategory::query()->lists('equipCatName', 'equipCatID'), Input::get('cat'), array('class' => 'auto-submit form-control') ) }}
    {{ Form::select('subcat', array('' => 'All Sub-Categories') + EquipmentSubCategory::query()->lists('equipSubName', 'equipSubCatID'), Input::get('subcat'), array('class' => 'auto-submit form-control') ) }}
    {{ Form::select('cond', array('' => 'All Conditions') + Condition::query()->lists('condName', 'condID'), Input::get('cond'), array('class' => 'auto-submit form-control') ) }}
