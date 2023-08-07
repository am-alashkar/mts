<?php
redirect_to_index();

require './services/mobile_kazia/new_order.php';
job::$body = $html;