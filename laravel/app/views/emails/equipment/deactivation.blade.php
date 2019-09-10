<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Equipment Deactivation</h2>

		<div>
           Equipment deactivated by {{{ $note->name }}}<br>
            Equipment ID: {{{ $equipment->equipmentid }}}<br>
            Equipment Model: {{{ $equipment->model }}}<br>
            Equipment Description: {{{ $equipment->equipment_desc }}}<br>
            Disposal Type: {{{ $note->disposalType }}}<br>
            Notes: {{{ $note->deactivationNotes }}}<br>
            Time: {{ $note->deactivate_date->format(Config::get('formatting.dates.friendlyTime')) }}<br>
            Department ID: {{{ $equipment->deptID }}}<br>
            Department Name: {{{ $equipment->department->deptName }}}
		</div>
	</body>
</html>
