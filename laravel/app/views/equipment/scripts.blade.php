<script>
$(function(){
    // Build list of items
    var $subs = {{ json_encode($subcategories) }};
    var sub = $('#equipment-equipSubCatID');

    // Bind change event
    $('#equipment-equipCatID').bind('change.sub', function(){

        var opt = $subs['cat'+$(this).val()];

        sub.empty().append($('<option/>').attr('value', '').text('Select SubCategory'));

        if (opt && opt.length)
        {
            for (var i=0; i< opt.length; i++)
            {
                sub.append($('<option/>').attr('value', opt[i].id).text(opt[i].name));
            }

            $('.equipSubCatID').removeClass('hide');

            sub.focus();
        }
        else
        {
            $('.equipSubCatID').addClass('hide');
        }

    }).trigger('change.sub');

// Set sub val based
    if (sub.data('val'))
    {
        sub.val(sub.data('val'));
    }
})
</script>
@include('kits.scripts')