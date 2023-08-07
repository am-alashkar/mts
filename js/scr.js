$("#bonus_eligible_msg").show();
let target , bounce, bal_id, bb, startbal, minbal , ses_start, bal, mx, lp, rema,am , ses_profit ,mintar = 0;
let btn2 = '<button onclick="f()">Start again</button>';
function starter() {
    if (bounce) {
        bal_id = 'bonus_account_balance';
    } else {
        bal_id = 'balance';
    }
    bb = $("#"+bal_id).html();
    startbal = Math.floor(bb*100000000);
    if (mintar > 0 ) {
        minbal = mintar;
    } else {
        minbal = startbal - Math.floor( startbal*0.1);
    }
    ses_start = startbal;
    bal = mx = lp = rema = 0;
    am = Math.floor(startbal - minbal);
    ses_profit = 0;
    bb.replace(' BTC','');
    bb = parseFloat(bb);
    if (bounce) {
        target -= Math.ceil($("#balance").html()*100000000);
    }
    $("#bonus_eligible_msg").html('Your Target is '+target+' - Better not to go under '+minbal+btn2+'<br> <span id="area51" ></span><br>' +
        '<button onclick="autobet()">BET ?</button><br><span id="area52" ></span>');

}
function autobet() {
var d = Math.random()*100;
d = Math.round(d);
if (d>=50) {
    $("#double_your_btc_bet_hi_button").click();
} else {
    $("#double_your_btc_bet_lo_button").click();
}
}
function f() {
    bb = $("#"+bal_id).html();
    bb.replace(' BTC','');
    bb = parseFloat(bb);
    ses_start = Math.floor(bb*100000000);
    target = ses_start + (ses_start*0.2);
    if (mintar > 0 ) {
        minbal = mintar;
    } else {
        minbal = ses_start - (ses_start*0.1);
    }
    mx = 0;
    $("#bonus_eligible_msg").html('Your Target is '+target+' - Better not to go under '+minbal+btn2+'<br> <span id="area51" ></span><br>' +
        '<button onclick="autobet()">BET ?</button><br><span id="area52" ></span>');
}
var wor = function bet_c() {
    (function(){
        bb = $("#"+bal_id).html();
        bb.replace(' BTC','');
        bb = parseFloat(bb);
        bal = bb*100000000;
        mx = Math.max(mx,Math.floor(bal-ses_start));
        rema = Math.ceil(target - bal);
        am = Math.floor(bal - minbal);
        var win = $("#double_your_btc_bet_win").html();
        if (win !== "")
        {
            //console.log('New Bal : '+bal+' Profit : '+(bal-startbal));
            if ( bal-startbal !== 0 ) ses_profit = Math.round(bal-startbal);
            startbal = bal;
            $("#area51").html('Expense : '+Math.ceil(startbal-bal)+' Profit : '+Math.floor(bal-ses_start)+' Max : '+mx + '<br> Remaining : '+rema+
                ' Allowed margin : '+am+' Last Profit : '+ses_profit);
        } else {
            $("#area51").html('Expense : '+Math.ceil(startbal-bal)+' Profit : '+Math.floor(bal-ses_start)+' Max : '+mx+ '<br> Remaining : '+rema+
                ' Allowed margin : '+am+' Last Profit : '+ses_profit);
        }
        suj_bet();
        setTimeout(arguments.callee, 500);
    })();
}
function suj_bet() {
    var expense = Math.ceil(startbal-bal);
    var odd = $("#double_your_btc_payout_multiplier").val()*1;
    var suj = Math.ceil((expense+(expense*0.01)) / (odd-1) );
    $("#area52").html('Bet : '+suj+' Odd :'+odd);
}
target = 100000;
bounce = false;
mintar = 0;
starter();
wor();