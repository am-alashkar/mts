<?php

if (!member::check()) create_error('LOGIN PLEASE');

if (data::$get->field == 'msn')
{
    $mainbill = new maintenance(data::$get->id);
    if ($mainbill->hasError()) create_error('No ID');
    $mainbill->msn = data::$get->nv;
    $mainbill->saveMSN();
    print '<script>
$("#msn_fast_search_value").val("'.data::$get->nv.'");
$("#msn_fast_search_open").val("0");
</script>';
    if (data::$get->nv) create_error(data::$get->nv);

}