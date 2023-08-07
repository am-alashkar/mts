<?php

if (!allowed('administration') ) {
    redirect_to_index();
}


job::$body = $html;
//data::$get->admincp_menu_open = 'menu-open';
//data::$get->lmscript .= '<script>
//$("[href=\'<!-- CONFIG_home_link -->admincp/settings/\']").addClass("'.style::variable('active').'");
//</script>';
data::$get->page_title = '{{Administration_Settings}}';