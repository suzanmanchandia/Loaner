// Variable that contains utilities and
var $Loaner = {
    // Activity spinner
    activity: $('<span class="activity-indicator fade fa fa-circle-o-notch fa-spin"><\/span>').appendTo('body'),
    modal: $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title"></h4></div><div class="modal-body tile-wrap"><p>One fine body&hellip;</p></div></div><span class="modal-next"><i class="fa fa-chevron-right"></i></span><span class="modal-prev"><i class="fa fa-chevron-left"></i></span></div></div>').modal( {
        show: false
    } ),
    sorter: {
        dir: 'asc'
    },
    tooltip: {
        container: 'body',
        selector: '[data-toggle="tooltip"]'
    },
    ajaxForm: {
        dataType: 'json',
        beforeSubmit: function(a, f) {
            // Call any ajaxValidate events bound to form 
            // and interrupt submission if necessary
            var e = $.Event('ajaxValidate');
            f.trigger(e);

            return !e.isDefaultPrevented();
        },
        success: function (d, x, s, f) {
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
                    text: d.message,
                    type: 'success'
                });
                f.trigger('contentUpdate', [d]);

                if (f.data('redirect'))
                {
                    var g = f.data('redirect');

                    $Loaner.stopReload = true;

                    setTimeout(function(){
                        self.location.href = g;
                    }, 300);
                }
                else if (f.data('refresh'))
                {

                    $Loaner.stopReload = true;

                    setTimeout(function(){
                        self.location.reload(true);
                    }, 300);

                }
            }
            $('[type=submit]', f).prop('disabled', false);
        },
        error: function (x, s, t, f) {
            new PNotify({
                title: 'Error',
                text: t,
                type: 'error'
            });
            $('[type=submit]', f).prop('disabled', false);
        }
    },
    stopReload: false
};

$(function(){

    // Unbind rails form handling
    $(document).undelegate('form', 'submit.rails');

    // Set notice styling
    PNotify.prototype.options.styling = "fontawesome";
    PNotify.prototype.options.hide    = false;

    // Show spinner on ajax requests and hide when complete
    $( document ).ajaxSend(function( event,request, settings ) {
        $Loaner.activity.addClass('in');
    }).ajaxComplete(function( event,request, settings ) {
        $Loaner.activity.removeClass('in');
    }).ajaxError(function( event,request, settings ) {
        $Loaner.activity.removeClass('in');
    });

    // Navbar dropdown for switching departments
    $('.navbar a[data-dept]').click(function(){
        $.post(appURL + 'dept', {deptID: $(this).data('dept')}, function() { self.location.reload(); } );
    });

    // Per Page onchange
    $('body').on('change', 'form .auto-submit', function(e){
        $(this).closest('form').submit();
    });

    // Show tooltips on items
    $("body").tooltip($Loaner.tooltip);

    // Any item with data-dialog attrobute shows a modal with content loaded from the href
    $('body').on('click', '[data-dialog]', function(e){
        e.preventDefault();

        // Large dialog
        if ($(this).data('dialog') == 'lg')
        {
            $('.modal-dialog', $Loaner.modal).addClass('modal-lg');
        }
        else
        {
            $('.modal-dialog', $Loaner.modal).removeClass('modal-lg');
        }

        var title = $(this).data('title') || $(this).data('original-title') || $(this).attr('data-original-title') || $(this).attr('title')

        $('.modal-title', $Loaner.modal).text( title );
        $('.modal-body', $Loaner.modal).empty().load( $(this).data('href'), function(responseText, textStatus, jqXHR){
            switch(jqXHR.status)
            {
                case 200:
                    try
                    {
                        var d = $.parseJSON(responseText);

                        if (d.status != 200)
                        {
                            new PNotify({
                                title: 'Error',
                                text: '<strong>There were some errors:</strong>\n' + d.message,
                                type: 'error'
                            });
                            $('.modal-body', $Loaner.modal).empty();
                            return;
                        }
                    }
                    catch (err)
                    {
                        $('.form-ajax', $Loaner.modal).bootstrapValidator().ajaxForm($Loaner.ajaxForm);
                    }

                    break;
                case 404:
                    $('.modal-body', $Loaner.modal).html('<p class="alert alert-danger">The item you tried to access was not found.</p>');
                    break;
                default:
                    $('.modal-body', $Loaner.modal).html('<p class="alert alert-danger">There was an error getting information from the server.</p>');
            }
            $Loaner.modal.modal('show');
        } );
    });

    // Any item with data-dialog attribute shows a modal with content loaded from the href
    $('body').on('click', '[data-table-dialog]', function(e){
        e.preventDefault();

        // Large dialog
        if ($(this).data('table-dialog') == 'lg')
        {
            $('.modal-dialog', $Loaner.modal).addClass('modal-lg');
        }
        else
        {
            $('.modal-dialog', $Loaner.modal).removeClass('modal-lg');
        }

        var title = $(this).data('title') || $(this).data('original-title') || $(this).attr('data-original-title') || $(this).attr('title'),
            href  = $(this).data('href') + '?' + $('table :checkbox').serialize();

        if (!$('table :checked').length)
        {
            return;
        }

        $('.modal-title', $Loaner.modal).text( title );
        $('.modal-body', $Loaner.modal).empty().load( href, function(responseText, textStatus, jqXHR){
            switch(jqXHR.status)
            {
                case 200:
                    try
                    {
                        var d = $.parseJSON(responseText);

                        if (d.status != 200)
                        {
                            new PNotify({
                                title: 'Error',
                                text: '<strong>There were some errors:</strong>\n' + d.message,
                                type: 'error'
                            });
                            $('.modal-body', $Loaner.modal).empty();
                            return;
                        }
                    }
                    catch (err)
                    {
                        $('.form-ajax', $Loaner.modal).bootstrapValidator().ajaxForm($Loaner.ajaxForm);
                    }

                    break;
                case 404:
                    $('.modal-body', $Loaner.modal).html('<p class="alert alert-danger">The item you tried to access was not found.</p>');
                    break;
                default:
                    $('.modal-body', $Loaner.modal).html('<p class="alert alert-danger">There was an error getting information from the server.</p>');
            }
            $Loaner.modal.modal('show');
        } );
    });

    // Bind validation and ajax submission
    $('.form-ajax').bootstrapValidator().ajaxForm($Loaner.ajaxForm);

    $('.sortable-table').on('click', '[data-column]', function(){
        var $column = $(this).data('column');
        var $params = $.extend($Loaner.sorter, $.parseParams(self.location.search));

        if ( (!$params.sf && $('.fa', this).length) || $params.sf == $column)
        {
            $params.dir = ($params.dir == 'asc' ? 'desc' : 'asc');
        }
        else
        {
            $params.dir = 'asc';
            $params['sf'] = $column;
        }

        if ($params.page)
        {
            try
            {
                delete $params.page;
            }
            catch (err)
            {
                // do nothing
            }
        }

        self.location.href = '?' + $.param($params);
    });

    $('input[data-type=date]').each(function(i, e){
        if (!$(e).data('date-format'))
        {
            $(e).attr('data-date-format', 'm/d/yyyy').data('date-format', 'm/d/yyyy');
        }

        var format = $(e).attr('data-date-format');

        if ($(e).val() == '0')
        {
            $(e).val('');
        }
        $(e).wrap($('<div class="input-group date"/>').data('date-format', format)).parent().append('<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>').datepicker({
            pickTime: false,
            autoclose: true
        });
    });

    $('input[data-suggest]').each(function(i, e){
        $(e).attr('autocomplete', 'off');

        var src = new Bloodhound({
            datumTokenizer: function(val) { return Bloodhound.tokenizers.whitespace(val.id + ' ' + val.name); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: $(e).data('source'),
                ttl: $(e).data('cache') == 'no' ? 1 : 300000
            }
        });

        // initialize the bloodhound suggestion engine
        src.initialize();

        $(e).typeahead({
            items: 4,
            highlight: true
        },{
            name: 'items',
            source: src.ttAdapter(),
            displayKey: 'id',
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'unable to find any items that match the current query',
                    '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<p><span class="title">#{{id}}</span><br>{{name}}</p>')
            }
        });
    });

    $('body').on('click', '[data-load]', function(e){
        e.preventDefault();

        var $load = $(this).data('load'),
            $target = $($(this).data('target'));

        $(this).addClass('active').siblings('.active').removeClass('active');

        $target.load( $load, function(responseText, textStatus, jqXHR){
            switch(jqXHR.status)
            {
                case 200:
                    $('body').animate({scrollTop: 0}, 300);
                    //$('body').animate({scrollTop: $target.offset().top}, 200);
                    break;
                case 404:
                    $target.html('<p class="alert alert-danger">The item you tried to access was not found.</p>');
                    break;
                default:
                    $target.html('<p class="alert alert-danger">There was an error getting information from the server.</p>');
            }
        } );
    });

    $('[data-filter]').each( function(i, e){

        var $filter = $(this).data('filter'),
            $items = $($(this).data('items'));

        $($filter).liveFilter(e, $items);
    });

    $('body').on('submit', '.autoload', function(e){
        e.preventDefault();

        var $url = this.action,
            $target = $(this).data('target') || $(this).parent();

        $url = $url + '?' + $(this).serialize();

        $target.load( $url, function(responseText, textStatus, jqXHR){
            switch(jqXHR.status)
            {
                case 200:
                    $('body').animate({scrollTop: 0}, 300);
                    break;
                case 404:
                    $target.html('<p class="alert alert-danger">The item you tried to access was not found.</p>');
                    break;
                default:
                    $target.html('<p class="alert alert-danger">There was an error getting information from the server.</p>');
            }
        } );
    });

    $('body').on('keypress', '.tabbable input, input.tabbable', function(e){
        if(e.which === 13){
            if(e.shiftKey){
                $.tabPrev();
            }
            else{
                $.tabNext();
            }
            $(this).trigger('change');
            e.preventDefault();
        }
    });

    // Mark/unmark all items
    $('body')
        .on('click', '.btn-mark', function(){
            $('.table :checkbox').prop('checked', true);
        })
        .on('click', '.btn-unmark', function(){
            $('.table :checkbox').prop('checked', false);
        });

});

$.fn.clearForm = function() {
    return this.each(function() {
        var type = this.type, tag = this.tagName.toLowerCase();
        if (tag == 'form')
            return $(':input',this).clearForm();
        if (type == 'text' || type == 'password' || tag == 'textarea')
            this.value = '';
        else if (type == 'checkbox' || type == 'radio')
            this.checked = false;
        else if (tag == 'select')
            this.selectedIndex = -1;
    });
};

/**
 *
 * @param options
 * @returns {*}
 */
$.fn.transitionOut = function(options) {

    var settings = $.extend({
        effects: {
            height: 0,
            opacity: 0,
            callback: function() {}
        },
        speed: 750
    }, options);

    return this.each(function(i, e) {
        var el = e;
        $(e).animate(settings.effects, settings.speed, function() { $(el).remove(); settings.callback(); });
    });
};
$.fn.bootstrapValidator.i18n.unique = $.extend($.fn.bootstrapValidator.i18n.unique || {}, {
    'default': 'You cannot checkout an item more than once.'
});