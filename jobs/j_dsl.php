<?php
redirect_to_index();

require './services/dsl/new_order.php';
job::$body = $html;