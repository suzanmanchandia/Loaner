<script>
    jQuery(function($) {
        var $swap = function() {
            $('[data-access-dept]').addClass('hide');

            $('.dept').each(function(i, e){
                if ($(e).prop('checked'))
                {
                    $('[data-access-dept="' + $(e).val() + '"]').removeClass('hide');
                }
            });
        };

        $('.dept').bind('change.dept', $swap);

        $swap();
    });
</script>