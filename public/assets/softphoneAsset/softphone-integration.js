function iframeControl() {
    $.ajax({
        type: 'GET',
        url: '/api/igdas-frame-control',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data == 1) {
                $("#iFrameDiv").html('');
                $("#iFrameDiv").html('İGDAŞ TEMSİLCİSİ');
            } else {
                $("#iFrameDiv").html('');
                $("#iFrameDiv").html('<iframe src="' + siebeliFrameLink + '" id="siebelFrame" style="min-height: 100rem!important; padding-bottom: 120px!important;" onload="onMyFrameLoad();"></iframe>');
                // $("#iFrameDiv").html('CRM KAPANDI');
                // $("#iFrameDiv").html('<iframe src="'+siebeliFrameLink+'" id="siebelFrame" style="min-height: 100rem!important; padding-bottom: 120px!important;" onload="onMyFrameLoad();"></iframe>');
                // $("#iFrameDiv").html('CRM KAPANDI');
            }
        }
    });
}


function getlastCalls() {
    var inboundtext = "";
    var outboundtext = "";
    $.ajax({
        type: 'GET',
        url: '/api/softphone/getLastCalls',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (tbxDebug == true) console.log(JSON.stringify(data));
            obj = JSON.parse(JSON.stringify(data));
            $.each(obj.inboundCallsList, function () {
                inboundtext += '<tr>\n' +
                    '<td>' + this.time + '</td>\n' +
                    '<td>' + this.number + '</td>\n' +
                    '</tr>';
            });
            if (tbxDebug == true) console.log(text);
            $("#inboundlastCalls").html(inboundtext);

            $.each(obj.outboundCallsList, function () {
                outboundtext += '<tr>\n' +
                    '<td>' + this.time + '</td>\n' +
                    '<td>' + this.number + '</td>\n' +
                    '</tr>';
            });
            if (tbxDebug == true) console.log(text);
            $("#outboundlastCalls").html(outboundtext);
        },
        // Auth:"Bearer "+token;

    });

}

getlastCalls();

function guideFindNumber(phoneNumber) {
    $.ajax({
        type: 'GET',
        url: '/api/guides?phone=' + phoneNumber,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (tbxDebug == true) console.log(JSON.stringify(data));
            obj = JSON.parse(JSON.stringify(data));
            $.each(obj, function () {
                var isim = this.nameSurname;
                var unvan = this.title;
                $.ajax({
                    type: 'GET',
                    url: "https://" + appServerLink + "" + this.guideGroupID,
                    headers: {
                        'Authorization': "Bearer " + getTokenStorage(),
                        'accept': "application/json",
                        'Content-Type': "application/json",
                    },
                    success: function (data1) {
                        if (tbxDebug == true) console.log(JSON.stringify(data1));
                        obj1 = jQuery.parseJSON(JSON.stringify(data1));
                        var mevki = obj1.name;
                        $("#flashingText").html(mevki);
                        $("#flashingText2").html(isim);
                        $("#flashingText3").html(unvan);
                        $('#exampleModal').modal({
                            show: true
                        });
                    },
                });
            });
        },
    });
}

function openIgdasWindow(igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo) {
    $.ajax({
        type: 'GET',
        url: '/api/igdas-frame-control',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data == 1) {
                createCaseDesktop(igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo)
            }
        }
    });
}


var callFlashing;

function callFlashingStart() {
    callFlashing = setInterval(function () {
        $("#softphone").css("background-color", function () {
            this.switch = !this.switch;
            return this.switch ? "limegreen" : ""
        });
    }, 800)
}

function callFlashingStop() {
    clearInterval(callFlashing);
    $("#softphone").css("background-color", "#0000ff");
}

function addUpdateRemove() {
    $.ajax({
        type: 'GET',
        url: '/api/last-register-add-update-remove',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {

        }
    });
}

function stateCheck() {
    if (softVue.progressStatus === 1) {
        return false;
    }

    $.ajax({
        type: 'GET',
        url: '/api/softphone/state',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data.hasOwnProperty('state')) {
                if (data.state === 8) {
                    stateCheckIntervalFunctionStop();
                    return false;
                }
                softVue.agentStatus = data.state;
                softVue.stateDetailText = data.text;
                softVue.stateDetailTimeStamp = data.timeStamp;
            }
        }
    });

}

function holdLogStart(callID, callType) {
    $.ajax({
        type: 'POST',
        url: '/api/hold-log-start/' + callID + "/" + callType,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
        }
    });
}

function holdLogStop(callID, callType) {
    $.ajax({
        type: 'POST',
        url: '/api/hold-log-stop/' + callID + "/" + callType,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
        }
    });
}

function acwStop() {

    if (parseInt(softVue.agentStatus) === 8)
        return;

    $.ajax({
        type: 'GET',
        url: '/api/softphone/acwLogStop',
        async: false,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            softVue.agentStatus = data.state;
            softVue.stateDetailText = data.text;
            softVue.progressStatus = 0;
        }
    });
}

function acw(acwTypeId) {
    setTimeout(function () {
        if (acwTypeId == 1) {
            if (softVue.agentStatus === 14) {
                return false;
            }
            // if (softVue.freeCall  === 1){
            //     softVue.freeCall = 0;
            //     return false;
            // }

            softVue.agentStatus = 2;
        }

        $.ajax({
            type: 'GET',
            url: '/api/softphone/acwLogStart/' + acwTypeId,
            async: false,
            headers: {
                'Authorization': "Bearer " + getTokenStorage(),
                'accept': "application/json",
                'Content-Type': "application/json",
            },
            success: function (data) {
                softVue.agentStatus = data.state;
                softVue.stateDetailTimeStamp = Math.round(Date.now() / 1000);
                softVue.stateDetailText = data.text;
                softVue.progressStatus = 0;
            }
        });
    }, 500);
}

$('#ivr-manu').on('select2:select', function (e) {
    var id = e.params.data.id;
    var text = e.params.data.text;

    IVRTransfer(id);

});

function breakStop() {

    $.ajax({
        type: 'GET',
        url: '/api/softphone/breakStop',
        async: false,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            softVue.agentStatus = data.state;
            softVue.stateDetailText = data.text;
            softVue.progressStatus = 0;
        }
    });
}

function breakStart(breakTypeId) {
    $.ajax({
        type: 'GET',
        url: '/api/softphone/breakStart/' + breakTypeId,
        async: false,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            softVue.agentStatus = data.state;
            softVue.stateDetailText = data.text;
            softVue.progressStatus = 0;
        }
    });
}

function showNotifICall(s_number) {
    // permission already asked when we registered
    var notification = new Notification('Gelen Çağrı Var', {
        icon: 'tbx.jpeg',
        body: s_number + " Seni Arıyor",
        image: "/assets/images/call-recorder-unlimited.jpg"
    });

    notification.onclick = function () {
        window.focus();
        this.close();
    }

}


function IVRTransfer(ivrNumber) {
    if (ivrNumber != "0") {
        ivrcon = ivrNumber.length;
        ivrPrefix = ivrcon === 1 ? 800 : 80;

        ivrNumber = ivrPrefix.toString() + ivrNumber.toString();

        tbxPro.sipTransfer(tbxPro.callActiveID, ivrNumber);

    }
}

function sipTransfer() {
    var s_prefix = '9341001';
    var s_suffix = "#";

    Swal.fire({
        title: 'Aranacak Telefon/Dahili yazınız',
        input: 'tel',
        inputPlaceholder: 'Aranacak Telefon/Dahili yazınız',
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara!',
    }).then(function (result) {
        if (result.value) {
            // softVue.transferCall = 1;
            s_destination = result.value;
            var fullDtmfNumber;
            if (s_destination.length <= 3) {
                fullDtmfNumber = "*2" + s_prefix + s_destination + s_suffix;
            } else {
                fullDtmfNumber = "*2" + s_destination + s_suffix;
            }
            tbxPro.sipSendDTMF(fullDtmfNumber, 1);
        }
    });

}

function registerStartTimer(time = 0) {
    // if (time != 0){
    //   time = softVue.stateDetailTimeStamp
    // }
    softTimer = new easytimer.Timer();
    // setTimeout(function () {
    //   softTimer.start({countdown: false, startValues: {seconds: time}});

    // softTimer.addEventListener('secondsUpdated', function (e) {
    //   $('#inboundTimer').html(softTimer.getTimeValues().toString());
    // });
    // }, 100);
}

//timer
function chronoTimer(timeStamp = 0) {
    var time = 0;

    if (timeStamp != 0) {
        time = Math.round((Date.now() / 1000) - timeStamp)
    }
    softTimer.stop();
    setTimeout(function () {
        softTimer.start({countdown: false, startValues: {seconds: time}});

        softTimer.addEventListener('secondsUpdated', function (e) {
            $('#inboundTimer').html(softTimer.getTimeValues().toString());
        });
    }, 100);
}

function countDownTimer(callback, countdownStart = 20) {
    var time = countdownStart;

    if (softVue.stateDetailTimeStamp != 0) {
        time = 20 - Math.round((Date.now() / 1000) - softVue.stateDetailTimeStamp)
    }

    if (time <= 0) {
        callback();
    } else {
        softTimer.stop();
        softTimer.start({countdown: true, startValues: {seconds: time}});
        softTimer.addEventListener('secondsUpdated', function (e) {
            $('#inboundTimer').html(softTimer.getTimeValues().toString());
        });

        softTimer.addEventListener('targetAchieved', function (e) {
            callback();
        });
    }
}


// Siebel Functions
function sendSurvey() {
    $.getJSON("https://" + tbxSipServer + "/api/poll_ob.php?user=amiuser&pass=amiuser&chan=" + softVue.ChannelId);
}

// Siebel Functions
function sendInbounSurvey() {
    $.getJSON("https://" + tbxSipServer + "/api/poll_ib.php?user=amiuser&pass=amiuser&chan=" + softVue.ChannelId);
}

function createActivity(sentData) {

    $.ajax({
        type: 'GET',
        url: '/api/igdas-frame-control',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data != 1) {
                postData = JSON.stringify(sentData);
                if (tbxDebug == true) console.log("create activityde");
                if (tbxDebug == true) console.log(postData);
                isOutboundSRCall = false;
                $.ajax({
                    type: 'POST',
                    url: "/siebelCrm/createActivity",

                    data: {body: postData},
                    success:
                        function (cevap) {
                            if (tbxDebug == true) console.log(cevap);
                            if (tbxDebug == true) console.log("Activite Oluşturuldu. Aktivite ID=" + cevap.ActivityNumber);
                        },
                    error: function (error) {
                        if (tbxDebug == true) console.log(error);
                    }
                });
            }
        }
    });
}


window.addEventListener("message", receiveMessage, false);

function setStateEvent(stateId) {
    $.ajax({
        type: 'GET',
        url: '/api/softphone/set-state-event/' + stateId,
        async: false,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            stateCheck();
        }
    });
}

function receiveMessage(event) {

    $.ajax({
        type: 'GET',
        url: '/api/igdas-frame-control',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data == 1) {
                getQueName(queNumber, igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo);
            } else {
                if (event.data.length == 0)
                    return;

                siebelData = JSON.parse(event.data);

                if (tbxDebug == true) console.log(siebelData);

                if (siebelData.action == 'outboundSRCall') {
                    softVue.agentStatus = 99;
                    Swal.fire({
                        title: 'Anket Araması',
                        input: 'tel',
                        inputValue: "05072303833",
                        showCancelButton: true,
                        inputPlaceholder: 'Aranacak Telefon/Dahili yazınız',
                        focusConfirm: false,
                        confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara',
                        cancelButtonText: '<i class="fa fa-close"></i> &nbsp; Vazgeç!',
                    }).then(function (result) {
                        if (result.value) {
                            tbxPro.sipCall(result.value);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            softVue.agentStatus = 1;
                        }
                    });

                }

                if (siebelData.action == 'outboundCall') {
                    softVue.agentStatus = 99;
                    Swal.fire({
                        title: 'Taziye Araması',
                        input: 'tel',
                        inputValue: "05072303833",
                        showCancelButton: true,
                        inputPlaceholder: 'Aranacak Telefon/Dahili yazınız',
                        focusConfirm: false,
                        confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara',
                        cancelButtonText: '<i class="fa fa-close"></i> &nbsp; Vazgeç!',
                    }).then(function (result) {
                        if (result.value) {
                            tbxPro.sipCall(result.value);
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            softVue.agentStatus = 1;
                        }
                    });

                }
            }
        }
    });
}

// function freeOutBoundCall() {
//
//     Swal.fire({
//         title: 'Serbest Arama',
//         input: 'tel',
//         inputValue: "",
//         showCancelButton: true,
//         inputPlaceholder: 'Aranacak Telefon/Dahili yazınız',
//         focusConfirm: false,
//         confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara',
//         cancelButtonText: '<i class="fa fa-close"></i> &nbsp; Vazgeç!',
//     }).then(function (result) {
//         if (result.value) {
//             softVue.freeCall = 1;
//             tbxPro.sipCall(result.value);
//         }else {
//             softVue.freeCall = 0;
//         }
//     })
//
// }


function onRegisterSipEvent() {
    if (tbxDebug == true) console.log("sip event girdi");
    $.ajax({
        type: 'GET',
        url: '/api/softphone/on-register-sip-event',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data.hasOwnProperty('state')) {
                $("#dahiliNo").val("DAHİLİ NO :  " + tokenVariableArray["extension"].substr(tokenVariableArray["extension"].length - 3));
                if (data.state === 2) {
                    countDownTimer(softVue.acwStop(1))
                }
                if (data.state === 6) {
                    countDownTimer(softVue.acwStop(3))
                }
                stateCheck();
            }
        }
    });
}

function vipModal(number) {
    $.ajax({
        type: 'GET',
        url: '/api/softphone-vip-modal/' + number,
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data.vipStatus == 1) {
                Swal.fire({
                    title: '!!! -DİKKAT- !!!',
                    html: '' +
                        '<h3 style="float: left!important; font-weight: bold!important;">Name : ' + data.name + '</h3>' +
                        '<br>' +
                        '<h3 style="float: left!important; font-weight: bold!important;">Ünvan : ' + data.title + '</h3>' +
                        '<br><br>' +
                        '<h3 style="float: left!important; font-weight: bold!important;">Yetki : ' + data.titleGroup + '</h3>',
                    confirmButtonText: 'Kapat!',
                }).then(function (result) {
                    if (result.value) {
                    } else {
                    }
                })
            }
        }
    });
}

function siebelFeedbackCall(type) {
    if (tbxDebug == true) console.log(type);
    document.getElementById("siebelFrame").contentWindow.postMessage('{"action":"outboundCTICall","payload":{"type":"' + type + '"}}', '*');
    //alert('{"action":"createSR","payload":{"phone":"'+sRemoteNumber+'","callID":"5c04e2eb90d56"}}');
    if (tbxDebug == true) console.log("feedback call yapılacak");
}


function siebelCreateSR($phoneNumber, callID) {
    $.ajax({
        type: 'GET',
        url: '/api/igdas-frame-control',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function (data) {
            if (data == 1) {
                getQueName(queNumber, igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo);
            } else {
                if (tbxDebug == true) console.log('{"action":"createSR","payload":{"phone":"' + $phoneNumber + '","callID":"' + callID + '"}}');
                document.getElementById("siebelFrame").contentWindow.postMessage('{"action":"createSR","payload":{"phone":"' + $phoneNumber + '","callID":"' + callID + '"}}', '*');
            }
        }
    });
}


function siebelLogin(username, token, password) {

    if (tbxDebug == true) console.log($username);
    // document.getElementById("siebelFrame").contentWindow.postMessage('{ "action": "agentLogin", "payload": { "username": "CAGRIAGENT8", "password": "CAGRIAGENT8", "token": "' + window.localStorage.getItem("token") + '" }}', '*');
    var pass = password;
    window.localStorage.removeItem("pass");
    document.getElementById("siebelFrame").contentWindow.postMessage('{ "action": "agentLogin", "payload": { "username": "' + username + '", "password": "' + pass + '", "token": "' + window.localStorage.getItem("token") + '" }}', '*');
    if (tbxDebug == true) console.log('Siebel Login Tetiklendi.');
}

function siebelLogout() {
    document.getElementById("siebelFrame").contentWindow.postMessage('{ "action": "crmLogout", "payload": {}', '*');

}