<?php
data::$get->dd = 'None';
if (data::$get->ed) {
    $e = explode('&lt;-&gt;',data::$get->ed);
    $d = $e[1];
    if (!$d && $e[0] != 'OK') $d = $e[0];
    data::$get->dd = data::decode($d);
} else {
    data::$get->ed = 'OK<-><->EOF';
}

print '<form method="post">
<textarea name="ed" placeholder="Encoded" cols="40" rows="10">'.data::$get->ed.'</textarea>
<input type="submit">
</form>
';
dd(data::$get->dd);
die();