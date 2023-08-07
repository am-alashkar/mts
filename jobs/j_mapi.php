<?php
redirect_to_index();

$url = 'http://127.0.0.1/ebr/api/mobile_retail/';
$params=['d'=>'1.EOF'];
foreach (data::$get as $key=>$item) {
    $params[$key] = $item;
    $txt .= '<br><input type="hidden" name="'.$key.'" value="'.$item.'"> '.$key.' = '.$item;
}
$defaults = array(
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $params,
    CURLOPT_TCP_NODELAY => true,
    CURLOPT_RETURNTRANSFER =>true,
    CURLOPT_USERAGENT => 'UBXv9',
);
$ch = curl_init();
curl_setopt_array($ch,  $defaults );
$response = curl_exec($ch);
curl_close($ch);
// $response = json_decode($response,1);

print $response.'<hr>';
$e = explode('<->',$response);
$d = $e[1];
if (!$d && $e[0] != 'OK') $d = $e[0];
$response = data::decode($d);
print '<pre dir="ltr">';
var_dump($response);
print '</pre><br>'.$url;
?>
<hr>
<form method="post" id="form1">
<?php echo $txt;?>
<br><input type="submit" value="OK">
</form>
<hr>
<input type="text" size="40" id="key" placeholder="key" > <br>
    <input type="text" size="40" id="val" placeholder="value" ><br>
<button type="button" onclick="f()">
    Add
</button>
<script>

    function f() {
        var a = document.getElementById('form1');
        var k = document.getElementById('key').value;
        var v = document.getElementById('val').value;
        if (k && v)
        {
            a.innerHTML += '<br><input type="hidden" name="'+k+'" value="'+v+'"> '+k+' = '+v;
        } else
        if (k) document.getElementById(k).value = '';
    }
</script>
<?php
    die();