
var sec = setInterval(
    function ()
    {
        var keep =true;
        var testString = "";
        if (null !== $("div.col-lg-12:nth-child(8) > button:nth-child(1)"))
        {
            testString = $("div.col-lg-12:nth-child(8) > button:nth-child(1)").children[0].innerHTML.trim();
            if (testString === "الخطوة التالية")
            {
                $("div.col-lg-12:nth-child(8) > button:nth-child(1)").click();

            }
        }


        if (null !== $("#sco"))
        {
            $("#sco").click();
        }
        var yesBtn = $("button.mat-button:nth-child(1)");
        if (null !== yesBtn)
        {
            if (yesBtn.children[0].innerHTML.trim() === "نعم")
            yesBtn.click();
        } else
        if (null !== $("button.mat-focus-indicator:nth-child(1)"))
        {
            testString = $("button.mat-focus-indicator:nth-child(1)").children[0].innerHTML.trim();
            if (testString === "تثبيت")
            {

                $("button.mat-focus-indicator:nth-child(1)").click();
            }
        }
        if (null !== $("div.col-sm-12:nth-child(4) > button:nth-child(1)"))
        {
            if ($("div.col-sm-12:nth-child(4) > button:nth-child(1)").children[0].children[1].src !== "") {
                $("div.col-sm-12:nth-child(4) > button:nth-child(1)").click();
                clearInterval(sec);
            }
        }

    }
    ,100);
function STOP() {
    clearInterval(sec);
}
