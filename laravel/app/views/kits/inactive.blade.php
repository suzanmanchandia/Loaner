
<div class="table-responsive">
    <table class="table table-striped table-hover action-table">
        <thead>
        <tr>
            {{ View::make( 'utils.table.header', array('name' => 'ID', 'id' => 'kitid', 'default' => 'kitid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Description', 'id' => 'kit_desc' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Loan Period', 'id' => 'loan_length' ) ) }}
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($kits as $kit)
        <tr>
            <td>{{ $kit->kitid }}</td>
            <td>{{ $kit->kit_desc }}</td>
            <td>{{ $kit->loan_length }}</td>
            <td class="text-right">
                <a data-toggle="tooltip" title="Reactivate" data-method="put" data-confirm="Are you sure you want to reactivate this kit?" class="btn btn-xs btn-success" href="{{ route( 'kits.activate', array($kit->kitid) ) }}"><i class="fa fa-share"></i></a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>