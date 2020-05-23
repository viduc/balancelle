jQuery(document).ready(
    function () {
        $("#deleteAll").on(
            'click',
            function () {
                $("input[name*='delete_famille']").prop('checked', true);
            }
        );
        $("#deleteAllUndo").on(
            'click',
            function () {
                $("input[name*='delete_famille']").prop('checked', false);
            }
        );
        $("#permAll").on(
            'click',
            function () {
                $("input[name*='perm_famille']").prop('checked', true);
            }
        );
        $("#permAllUndo").on(
            'click',
            function () {
                $("input[name*='perm_famille']").prop('checked', false);
            }
        );
    }
);
