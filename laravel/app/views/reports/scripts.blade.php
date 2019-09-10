<script>
    $(function(){
        $('#main').on('click', '.btn-print', function(){
            var w = window.open('','','width=600,height=600,location=0,menubar=0,scrollbars=0,status=0,toolbar=0,resizable=0');
            var h = $('.tab-pane.active').clone();

            $('.pull-right', h).remove();

            h.append($('<link />').attr({rel: 'stylesheet', type: 'text/css', href: appURL + 'packages/css/style.css'}));

            w.document.write(h.html());
            w.print();
        });
    })
    $("select").change(function () {
            this.form.submit();
        });
</script>