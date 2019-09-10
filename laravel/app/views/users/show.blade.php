<fieldset>
    <legend class="clearfix">
        <div class="row">
            <div class ="col-xs-8">
                Account Information: {{ $user->userid }}
            </div>
            <div class="col-xs-4 text-right">
                @include('users.actions', array('user' => $user))
            </div>
        </div>
    </legend>

    <div class="row-fluid">
        <div class="col-md-6">
            <dl>
                <dt>
                    Fine:
                </dt>
                <dd>
                    ${{ $user->fine }}
                </dd>
                <dt>
                    First name:
                </dt>
                <dd>
                    {{ $user->fname }}
                </dd>
                <dt>
                    Last name:
                </dt>
                <dd>
                    {{ $user->lname }}
                </dd>
                <dt>
                    Email:
                </dt>
                <dd>
                    {{ $user->email }}
                </dd>
                <dt>
                    USC ID:
                </dt>
                <dd>
                    {{ $user->usc_id }}
                </dd>
                <dt>
                    Phone:
                </dt>
                <dd>
                    {{ $user->phone }}
                </dd>
                <dt>
                    Address:
                </dt>
                <dd>
                    {{ $user->address }}
                </dd>
                <dt>
                    City:
                </dt>
                <dd>
                    {{ $user->city }}
                </dd>
                <dt>
                    State:
                </dt>
                <dd>
                    {{ $user->state }}
                </dd>
                <dt>
                    Zip:
                </dt>
                <dd>
                    {{ $user->zip }}
                </dd>
            </dl>
        </div>
        <div class="col-md-6">
            <dl>
                <dt>
                    Departments:
                </dt>
                <dd>
                    <ul>
                    @foreach ($user->departments as $department)
                        <li>{{{ $department->deptName }}}</li>
                    @endforeach
                    </ul>
                </dd>
                <dt>
                    Access Areas:
                </dt>
                <dd>
                    <ul>
                    @foreach ($user->accessAreas as $access)
                        <li>{{{ $access->accessarea }}}</li>
                    @endforeach
                    </ul>
                </dd>
                <dt>
                    Class:
                </dt>
                <dd>
                    {{ $user->class }}
                </dd>
                <dt>
                    Status:
                </dt>
                <dd>
                @if ($user->status == User::STATUS_DEACTIVATED)
                    <span style="color:red"> {{ $user->getStatus() }} </span>
                @else
                    {{ $user->getStatus() }}
                @endif
                </dd>
                <dt>
                    Suspended:
                </dt>
                <dd>
                    {{ $user->suspended ? 'Yes' : 'No' }}
                </dd>
                <dt>
                    Role:
                </dt>
                <dd>
                    {{ $user->getRole() }}
                </dd>
                @if ($user->created_on)
                <dt>
                    Account Created:
                </dt>
                <dd>
                    {{ $user->created_on->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
            </dl>
        </div>
    </div>
</fieldset>