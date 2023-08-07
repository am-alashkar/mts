<?php
$css = file_get_contents('./css/all.min.css');
mark_active('icons');
preg_match_all('/\"\}\.fa\-(.*?)\:before\{content\:\"/',$css,$arr);
$b = true;
foreach ($arr[1] as $item) {
    if ($b) {
        $b = 0;
        continue;
    }
    $html .= '<tr>
<td><i class="fa fa-'.$item.'" ></i></td><td>fa fa-'.$item.'</td><td style="width: 1px; background-color: black;"></td>
<td><i class="fas fa-'.$item.'" ></i></td><td>fas</td><td style="width: 1px; background-color: black;"></td>
<td><i class="fab fa-'.$item.'" ></i></td><td>fab</td><td style="width: 1px; background-color: black;"></td>
<td><i class="far fa-'.$item.'" ></i></td><td>far</td>
</tr>
';
}
$html = '<div class="card card-dark">
<div class="card-body">
<table class="table table-bordered table-hover">
'.$html.'
</table>
</div>
</div>';
job::$body = $html;