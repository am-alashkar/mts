<?php
if (!member::check()) redirect_to_index();
$m = new maintenance();
$m->createNewFromInput();

if ($m->hasError())
{
    create_error(str_replace("\n",'<br>',$m->getError()));
}
print style::big_success_gritter('الرجاء الانتظار','تم الاضافة بنجاج');
redirect(true,'','view/'.$m->getId().'/',true,'3000',false);

dd();