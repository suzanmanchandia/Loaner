@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'dashboard', 'title' => 'System Options' )) }}
@stop

@section('content')

<div class="row" id="content">

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Manageable Sections</div>
            <div class="panel-body">

                <div class="list-group">
                    <div class="list-group-item" data-panel="#access">
                        <h5 class="list-group-item-heading">Access Areas</h5>
                    </div>
                    <div class="list-group-item" data-panel="#categories">
                        <h5 class="list-group-item-heading">Categories</h5>
                    </div>
                    <div class="list-group-item" data-panel="#classes">
                        <h5 class="list-group-item-heading">Classes</h5>
                    </div>
                    <div class="list-group-item" data-panel="#locations">
                        <h5 class="list-group-item-heading">Locations</h5>
                    </div>
                    <div class="list-group-item" data-panel="#fines">
                        <h5 class="list-group-item-heading">Fines</h5>
                    </div>
                    <div class="list-group-item" data-panel="#deactivations">
                        <h5 class="list-group-item-heading">Notifications</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4" id="first">

        <div class="panel panel-default hide" id="access">
            <div class="panel-heading">Access Areas <a data-prompt="Add Access Area" data-target="access" data-toggle="tooltip" title="Add Access Area" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                @foreach ($areas as $item)
                    <div class="list-group-item" data-panel="#users-{{ $item->id }}" data-path="access" data-id="{{ $item->id }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{{ $item->accessarea }}}</h5>
                    </div>
                @endforeach
                </div>
            </div>
        </div>

        <div class="panel panel-default hide" id="categories">
            <div class="panel-heading">Categories <a data-prompt="Add Ccategory" data-target="categories" data-toggle="tooltip" title="Add Ccategory" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                @foreach ($categories as $item)
                    <div class="list-group-item" data-panel="#categories-{{ $item->equipCatID }}" data-path="categories" data-id="{{ $item->equipCatID }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{{ $item->equipCatName }}}</h5>
                    </div>
                @endforeach
                </div>
            </div>
        </div>

        <div class="panel panel-default hide" id="classes">
            <div class="panel-heading">Classes <a data-prompt="Add Class" data-target="classes" data-toggle="tooltip" title="Add Class" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                @foreach ($classes as $item)
                    <div class="list-group-item" data-path="classes" data-id="{{ $item->id }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{{ $item->classname }}}</h5>
                    </div>
                @endforeach
                </div>
            </div>
        </div>

        <div class="panel panel-default hide" id="locations">
            <div class="panel-heading">Locations <a data-prompt="Add Location" data-target="locations" data-toggle="tooltip" title="Add Location" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                @foreach ($locations as $item)
                    <div class="list-group-item" data-path="locations" data-id="{{ $item->locationID }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{{ $item->locationName }}}</h5>
                    </div>
                @endforeach
                </div>
            </div>
        </div>

        <div class="panel panel-default hide" id="fines">
            <div class="panel-heading">Amount</div>
            <div class="panel-body">
                <div class="list-group">
                    <div class="list-group-item" data-path="fines" data-id="none">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">Default Daily Amount: $<span>{{ $fine->defaultFine }}</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default hide" id="deactivations">
            <div class="panel-heading">Deactivation Notifications <a data-prompt="Add Email" data-target="emails" data-toggle="tooltip" title="Add Email" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                    @foreach ($emails as $item)
                    <div class="list-group-item" data-path="emails" data-id="{{ $item->id }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{{ $item->emailid }}}</h5>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="panel-heading">Message Notifications <a data-prompt="Add Email" data-target="notifications" data-toggle="tooltip" title="Add Email" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                    @foreach ($notifications as $item)
                        <div class="list-group-item" data-path="notifications" data-id="{{ $item->id }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                            <h5 class="list-group-item-heading">{{{ $item->email }}}</h5>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-4" id="second">

        @foreach ($categories as $category)
        <div class="panel panel-default hide" id="categories-{{ $category->equipCatID }}">
            <div class="panel-heading">Sub-Categories <a data-prompt="Add Subcategory to {{ $category->equipCatName }}" data-target="subcategories" data-extra='{"category": "{{ $category->equipCatID }}"}' data-toggle="tooltip" title="Add Subcategory" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                    @foreach ($category->subCategories as $item)
                    <div class="list-group-item" data-path="subcategories" data-id="{{ $item->equipSubCatID }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{{ $item->equipSubName }}}</h5>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

        @foreach ($areas as $area)
        <div class="panel panel-default hide" id="users-{{ $area->id }}">
            <div class="panel-heading">Users <a data-dialog="yes" data-href="javascript:;" data-toggle="tooltip" title="Add Users" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
            <div class="panel-body">
                <div class="list-group">
                    @foreach ($area->users as $item)
                    <div class="list-group-item">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
                        <h5 class="list-group-item-heading">{{ $item->fname }} {{ $item->lname }} - {{ $item->userid }}</h5>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

    </div>

</div>
@stop

@section('footer')
<script type="text/x-handlebars-template" id="newItem">
    <div class="list-group-item" data-path="@{{ path }}" data-id="@{{ id }}">
                        <span class="pull-right">
                            <a href="javascript:;" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                        </span>
        <h5 class="list-group-item-heading">@{{ title }}</h5>
    </div>
</script>
<script type="text/x-handlebars-template" id="newCategory">
    <div class="panel panel-default hide" id="categories-@{{ id }}">
        <div class="panel-heading">Sub-Categories <a data-prompt="Add Subcategory to @{{ name }}" data-target="subcategories" data-extra='{"category": "@{{ id }}"}' data-toggle="tooltip" title="Add Subcategory" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
        <div class="panel-body">
            <div class="list-group">
            </div>
        </div>
    </div>
</script>
<script type="text/x-handlebars-template" id="newAccess">
    <div class="panel panel-default hide" id="users-@{{ id }}">
        <div class="panel-heading">Users <a data-dialog="yes" data-href="javascript:;" data-toggle="tooltip" title="Add Users" class="pull-right btn btn-xs btn-success"><i class="fa fa-plus"></i></a></div>
        <div class="panel-body">
            <div class="list-group">
            </div>
        </div>
    </div>
</script>
<script>
    $(function(){
        $('#content').on('click', '.list-group-item[data-panel]', function(e) {
            if ($(this).hasClass('active') || $(e.target).is('a,.pull-right,.fa')) return;

            var $siblings = $($(this).data('panel')).removeClass('hide').siblings('.panel').addClass('hide');
            $('.active', $siblings).removeClass('active');
            $(this).addClass('active').siblings('.active').removeClass('active').trigger('inactive');
        }).on('inactive', function(){
            //$('.list-group-item[data-panel]').addClass('hide');
        });

        $('[data-panel="#categories"], [data-panel="#access"]').bind('inactive', function(){
            $('#second .panel').addClass('hide');
        });

        var FormSettings = {
            postURL: Handlebars.compile("{{ url('system') }}/@{{ action }}"),
            putURL: Handlebars.compile("{{ url('system') }}/@{{ action }}/@{{ id }}"),
            newItem: Handlebars.compile($('#newItem').html()),
            newCategory: Handlebars.compile($('#newCategory').html()),
            newAccess: Handlebars.compile($('#newAccess').html())
        };

        $('#content').on('newItem', '#categories', function(e, d){
            var $last = $('.list-group-item', this).last();
            $last.attr('data-panel', '#categories-'+ d.id);
            $('#second').append(FormSettings.newCategory(d));
        });
        $('#content').on('newItem', '#access', function(e, d){
            var $last = $('.list-group-item', this).last();
            $last.attr('data-panel', '#users-'+ d.id);
            $('#second').append(FormSettings.newAccess(d));
        });

        // Add new item
        $('#content').on('click', '[data-prompt]', function(e) {
            var $target = $(this).data('target'),
                $extra  = $(this).data('extra') || {},
                $prompt = $(this).data('prompt') || $(this).attr('title'),
                $head   = $(this).closest('.panel-heading'),
                $list   = $('.list-group', $head.next('.list-group'));

            if (!($target && $list.length)) return;

            var $title = prompt($prompt, '');

            if (!$title) return;

            $extra = $.extend({title: $title}, $extra);

            $.post( FormSettings.postURL( {action: $target} ), $extra, function(d){

                if (d.error)
                {
                    new PNotify({
                        title: 'Error',
                        text: '<strong>There were some errors:</strong>\n' + d.message,
                        type: 'error'
                    });
                }
                else
                {
                    PNotify.removeAll();
                    new PNotify({
                        title: 'Congrats!',
                        text: 'Item added.',
                        type: 'success'
                    });

                    $list.append(FormSettings.newItem($.extend({path: $target}, d.data))).trigger('newItem', d.data);
                }
            }, 'json').fail(function(x, s, t){
                new PNotify({
                    title: 'Error',
                    text: t,
                    type: 'error'
                });
            });
        });
        // Edit
        $('#content').on('click', '.pull-right .btn-info', function(e) {
            var $parent  = $(this).closest('[data-path]'),
                $target  = $parent.data('path'),
                $id      = $parent.data('id'),
                $content = $('.list-group-item-heading span', $parent).length ? $('.list-group-item-heading span', $parent) : $('.list-group-item-heading', $parent);

            if (!($target && $parent.length)) return;

            var $title = prompt('Edit Item', $content.text());
            if (!$title) return;

            $.post( FormSettings.putURL( {action: $target, id: $id} ), {'_method': 'put', title: $title}, function(d){

                if (d.error)
                {
                    new PNotify({
                        title: 'Error',
                        text: '<strong>There were some errors:</strong>\n' + d.message,
                        type: 'error'
                    });
                }
                else
                {
                    PNotify.removeAll();
                    new PNotify({
                        title: 'Success!',
                        text: d.message,
                        type: 'success'
                    });

                    $content.text($title);
                }
            }, 'json' ).fail(function(x, s, t){
                new PNotify({
                    title: 'Error',
                    text: t,
                    type: 'error'
                });
            });
        });
        // Delete
        $('#content').on('click', '.pull-right .btn-danger', function(e) {
            var $parent = $(this).closest('[data-path]'),
                $target = $parent.data('path'),
                $id     = $parent.data('id');

            if (!($target && $parent.length && confirm('Are you sure you want to delete this item? This cannot be undone!'))) return;

            $.post( FormSettings.putURL( {action: $target, id: $id} ), {'_method': 'delete'}, function(d){

                if (d.error)
                {
                    new PNotify({
                        title: 'Error',
                        text: '<strong>There were some errors:</strong>\n' + d.message,
                        type: 'error'
                    });
                }
                else
                {
                    PNotify.removeAll();
                    new PNotify({
                        title: 'Success!',
                        text: d.message,
                        type: 'success'
                    });

                    // Remove any referenced panels
                    var $panel = $parent.data('panel') || $parent.attr('data-panel');

                    if ($panel)
                    {
                        $($panel).remove();
                    }

                    $parent.trigger('deleteItem', {id: $id}).transitionOut();
                }
            }, 'json' ).fail(function(x, s, t){
                new PNotify({
                    title: 'Error',
                    text: t,
                    type: 'error'
                });
            });
        });
    });
</script>
@stop
