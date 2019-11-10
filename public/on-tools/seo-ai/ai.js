    function $(id) {
        return document.getElementById(id);
    }
    var EventUtil = function() {};
    EventUtil.addEventHandler = function(obj, EventType, Handler) {
        if (obj.addEventListener) {
            obj.addEventListener(EventType, Handler, false);
        }
        else if (obj.attachEvent) {
            obj.attachEvent('on' + EventType, Handler);
        } else {
            obj['on' + EventType] = Handler;
        }
    }
    if ($("content")) {
        EventUtil.addEventHandler($('content'), 'propertychange', CountChineseCharacters);
        EventUtil.addEventHandler($('content'), 'input', CountChineseCharacters);
    }
    function showit(Word) {
        alert(Word);
    }
    function CountChineseCharacters() {
        Words = $('content').value;
        var W = new Object();
        var Result = new Array();
        var iNumwords = 0;
        var sNumwords = 0;
        var sTotal = 0;
        var iTotal = 0;
        var eTotal = 0;
        var otherTotal = 0;
        var bTotal = 0;
        var inum = 0;
        for (i = 0; i < Words.length; i++) {
            var c = Words.charAt(i);
            if (c.match(/[\u4e00-\u9fa5]/)) {
                if (isNaN(W[c])) {
                    iNumwords++;
                    W[c] = 1;
                }
                iTotal++;
            }
        }
        for (i = 0; i < Words.length; i++) {
            var c = Words.charAt(i);
            if (c.match(/[^\x00-\xff]/)) {
                if (isNaN(W[c])) {
                    sNumwords++;
                }
                sTotal++;
            } else {
                eTotal++;
            }
            if (c.match(/[0-9]/)) {
                inum++;
            }
        }

		$('shengyu').innerText = '2000' - (inum + iTotal+(eTotal - inum));
		$('zishu').innerText = inum + iTotal+ (eTotal - inum);
    }
var xmlhttp = null;
function createXmlHttp() {
	//非IE浏览器创建XmlHttpRequest对象
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	}
	if (window.ActiveXObject) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (e) {
			try {
				xmlhttp = new ActiveXObject("msxml2.XMLHTTP");
			}
			catch (ex) { }
		}
	}
}

function answers() {
	document.getElementById("reply").innerHTML = "正在伪原创中，请稍候...";
	createXmlHttp();
	if (!xmlhttp) {
		alert("创建xmlhttp对象异常！");
		return false;
	}
	var question = document.getElementById("content").value;
	var url = "/tools/seo-ai/get-ai.php";
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader( "Content-Type" , "application/x-www-form-urlencoded" );
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				var replyResult = xmlhttp.responseText;
				document.getElementById("reply").innerHTML = replyResult;
			}
		}
	}

	xmlhttp.send("info="+question);
}
