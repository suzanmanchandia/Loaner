<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Kit Deactivation</h2>

		<div>
           Kit deactivated by {{{ $note->name }}}<br>
            Kit ID: {{{ $kit->kitid }}}<br>
            Kit Description: {{{ $kit->kit_desc }}}<br>
            Disposal Type: {{{ $note->disposalType }}}<br>
            Notes: {{{ $note->deactivationNotes }}}<br>
            Time: {{ $note->deactivate_date->format(Config::get('formatting.dates.friendlyTime')) }}<br>
            Department ID: {{{ $kit->deptID }}}<br>
            Department Name: {{{ $kit->department->deptName }}}
		</div>
	</body>
</html>
