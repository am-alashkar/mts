<?php
data::$get->admincp_menu_open = style::variable('menu_open');
data::$get->admincp_menu_active = style::variable('active');
if (!allowed('administration')) {
    redirect_to_index();
}
if (!data::$get->all[1]) {
    redirect(true,'','admincp/users/');
}
$file = './admincp/admin_'.data::$get->all['1'].'.php';
if (file_exists($file)) {
    require $file;
    mark_active('admincp/'.data::$get->all['1']);
} else {
    create_error('The link is broken','404','a Gorilla 🦍');
}