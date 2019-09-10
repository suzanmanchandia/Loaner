<script>
    jQuery(function($) {
        var $dept = $('select[name="deptID"]');
        var $swap = function() {
            $('[data-access-dept]').addClass('hide');

            $('[data-access-dept="' + $dept.val() + '"]').removeClass('hide');
        };

        $dept.bind('change.dept', $swap);

        $swap();
    });
</script>,