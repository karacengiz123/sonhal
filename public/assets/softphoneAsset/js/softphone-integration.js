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
            if (tbxDebug == true) console.log(inboundtext);
            $("#inboundlastCalls").html(inboundtext);

            $.each(obj.outboundCallsList, function () {
                outboundtext += '<tr>\n' +
                    '<td>' + this.time + '</td>\n' +
                    '<td>' + this.number + '</td>\n' +
                    '</tr>';
            });
            if (tbxDebug == true) console.log(outboundtext);
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

function stateCheck() {
    if (softVue.proccessStatus == 1) {
        return false;
    }

    if ([13, 15, 18, 19, 20].indexOf(parseInt(softVue.agentStatus)) > -1) {
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
            if (tbxDebug == true) console.log(data);
            if (data.hasOwnProperty('state')) {
                if (data.state == 8) {
                    stateCheckIntervalFunctionStop();
                    return false;
                } else {
                    if ([12, 17].indexOf(parseInt(softVue.agentStatus)) > -1) {
                        stateCheckIntervalFunctionStop();
                        return false;
                    } else {
                        if ([2, 6, 99].indexOf(parseInt(softVue.agentStatus)) > -1) {
                            return false;
                        }
                    }
                }

                if (data.state != 14) {
                    var extensionInput = $("#dahiliNo").val();
                    if ([null, "", " ", "undefined", undefined].indexOf(extensionInput) > -1) {
                        softVue.agentStatus = 19;
                        return false;
                    }
                }

                softVue.agentStatus = data.state;
                softVue.stateDetailText = data.text;
                softVue.stateDetailTimeStamp = data.timeStamp;

                if ([2, 6, 99].indexOf(parseInt(softVue.agentStatus)) > -1) {
                    return false;
                } else {
                    chronoTimer(softVue.stateDetailTimeStamp);
                }
            }
        },
        complete: function (data) {
            if (data.status == 500) {
                softVue.agentStatus = 13;
            }
            if (data.status == 0) {
                softVue.agentStatus = 18;
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

function acw(acwTypeId, repeat) {
    repeat = repeat === undefined ? 1 : repeat + 1;
    setTimeout(function () {
        if (acwTypeId === 1) {
            if (softVue.agentStatus === 14) {
                return false;
            } else {
                softVue.agentStatus = 2;
                if (repeat === 1)
                    softVue.stateDetailTimeStamp = Math.round(Date.now() / 1000);
            }
        }
        var callIdData = null;
        if (["", " ", "  ", null].indexOf(softVue.callId) < 0) {
            callIdData = softVue.callId;
        }
        $.ajax({
            type: 'GET',
            url: '/api/softphone/acwLogStart/' + acwTypeId + '/' + callIdData,
            async: false,
            headers: {
                'Authorization': "Bearer " + getTokenStorage(),
                'accept': "application/json",
                'Content-Type': "application/json",
            },
            success: function (data, event) {
                softVue.agentStatus = data.state;
                softVue.stateDetailTimeStamp = Math.round(Date.now() / 1000);
                softVue.stateDetailText = data.text;
                softVue.proccessStatus = 0;
            },
            error: function (data) {
                if (data.status === 500 && repeat < 11) {
                    acw(acwTypeId, repeat);
                    return false;
                }
                softVue.agentStatus = 18;
            }
        });
    }, 500);
}

function acwStop(repeat) {
    if (tbxDebug == true) console.log("Acw Stop Function");
    repeat = repeat === undefined ? 1 : repeat + 1;
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
            softVue.stateDetailTimeStamp = Math.round(Date.now() / 1000);
            softVue.stateDetailText = data.text;
            softVue.proccessStatus = 0;
            
            if (softVue.siebelFeedBackCallValid == true) {
                siebelFeedBackCallSelectFunction();
            }
        },
        error: function (data) {
            if (data.status === 500 && repeat < 11) {
                acwStop(repeat);
                return false;
            }
            softVue.agentStatus = 18;
        }
    });
}

$('#ivr-manu').on('select2:select', function (e) {
    var id = e.params.data.id;
    var text = e.params.data.text;

    IVRTransfer(id);
});

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
            softVue.proccessStatus = 0;
        },
        error: function (errData) {
            softVue.proccessStatus = 0;
            Swal.fire({
                title: '!!! -DİKKAT- !!!',
                allowOutsideClick: false,
                closeOnClickOutside: false,
                html: '' +
                    '<h3 style="float: left!important; font-weight: bold!important;">Mola Limitiniz Dolmuştur. Lütfen Daha sonra Tekrar Deneyiniz..</h3>' +
                    '',
                confirmButtonText: 'Kapat',
            }).then(function (result) {
                if (result.dismiss == Swal.DismissReason.cancel) {
                } else if (result.dismiss == Swal.DismissReason.backdrop) {
                } else if (result.value) {
                }
            });
        }
    });
}

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
            softVue.proccessStatus = 0;
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
        ivrPrefix = ivrcon == 1 ? 800 : 80;

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
        allowOutsideClick: false,
        closeOnClickOutside: false,
        confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara!',
    }).then(function (result) {
        if (result.dismiss == Swal.DismissReason.cancel) {
        } else if (result.dismiss == Swal.DismissReason.backdrop) {
        } else if (result.value) {
            s_destination = callNumberTrim(result.value);
            var fullDtmfNumber;
            if (s_destination.length <= 3) {
                fullDtmfNumber = "*2" + s_prefix + s_destination + s_suffix;
            } else {
                fullDtmfNumber = "*2" + s_destination + s_suffix;
            }
            tbxPro.sipSendDTMF(fullDtmfNumber, 1);
            $("#arayanNoCopy").val(callNumberTrim(result.value));
            clickCopyNumber(callNumberTrim(result.value));
        }
    });

}

function registerStartTimer(time = 0) {
    softTimer = new easytimer.Timer();
}

registerStartTimer();

function chronoTimer(timeStamp = 0) {
    var time = 0;

    if (timeStamp != 0) {
        time = Math.round((Date.now() / 1000) - timeStamp)
    }
    softTimer.stop();
    softTimer = null;
    softTimer = new easytimer.Timer();
    setTimeout(function () {
        softTimer.start({countdown: false, startValues: {seconds: time}});

        softTimer.addEventListener('secondsUpdated', function (e) {
            $('#inboundTimer').html(softTimer.getTimeValues().toString());
        });
    }, 100);
}

function countDownTimer(callback, countdownStart = 20) {
    console.log("Down");
    var time = countdownStart;

    softTimer.stop();
    softTimer = null;
    softTimer = new easytimer.Timer();
    softTimer.start({countdown: true, startValues: {seconds: time}});
    softTimer.addEventListener('secondsUpdated', function (e) {
        $('#inboundTimer').html(softTimer.getTimeValues().toString());
    });

    softTimer.addEventListener('targetAchieved', function (e) {
        callback();
    });
}

function createActivity(sentData, session) {
    var callDirection = session.callDirection;

    if (callDirection != 'Local') {
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
                    sentData.body.PlannedCompletion = moment(new Date()).format('M/D/Y H:mm:ss');

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
}

function createChatActivity(sentData) {
    postData = JSON.stringify(sentData);

    if (tbxDebug == true) console.log("create activityde");
    if (tbxDebug == true) console.log(postData);
    isOutboundSRCall = false;
    $.ajax({
        type: 'POST',
        url: "/siebelCrm/createChatActivity",
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
                openIgdasWindow(igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo);
            } else {
                if (event.data.length == 0)
                    return;

                siebelData = JSON.parse(event.data);
                softVue.siebelFeedBackCallData = siebelData;

                var phoneNumberRes = siebelData.payload.phone.substr(2, siebelData.payload.phone.length);

                if (tbxDebug == true) console.log(siebelData);

                if (siebelData.action == 'outboundSRCall') {
                    softVue.agentStatus = 99;
                    Swal.fire({
                        title: 'Anket Araması',
                        input: 'tel',
                        inputValue: phoneNumberRes,
                        showCancelButton: true,
                        inputPlaceholder: 'Aranacak Telefon/Dahili',
                        focusConfirm: false,
                        allowOutsideClick: false,
                        closeOnClickOutside: false,
                        confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara',
                        cancelButtonText: '<i class="fa fa-close"></i> &nbsp; Vazgeç!',
                    }).then(function (result) {
                        if (result.dismiss == Swal.DismissReason.cancel) {
                            softVue.agentStatus = 1;
                            siebelFeedBackCallSelectFunction();
                        } else if (result.dismiss == Swal.DismissReason.backdrop) {
                            softVue.agentStatus = 1;
                        } else if (result.value) {
                            softVue.siebelFeedBackCallValid = true;
                            tbxPro.sipCall(result.value);
                        }
                    });

                }

                if (siebelData.action == 'outboundCall') {
                    softVue.agentStatus = 99;
                    Swal.fire({
                        title: 'Taziye Araması',
                        input: 'tel',
                        inputValue: phoneNumberRes,
                        showCancelButton: true,
                        inputPlaceholder: 'Aranacak Telefon/Dahili',
                        focusConfirm: false,
                        allowOutsideClick: false,
                        closeOnClickOutside: false,
                        confirmButtonText: '<i class="fa fa-phone"></i> &nbsp; Ara',
                        cancelButtonText: '<i class="fa fa-close"></i> &nbsp; Vazgeç!',
                    }).then(function (result) {
                        if (result.dismiss == Swal.DismissReason.cancel) {
                            softVue.agentStatus = 1;
                        } else if (result.dismiss == Swal.DismissReason.backdrop) {
                            softVue.agentStatus = 1;
                        } else if (result.value) {
                            softVue.siebelFeedBackCallValid = true;
                            tbxPro.sipCall(result.value);
                        }
                    });

                }
            }
        }
    });
}

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
                softVue.extensionNo = "DAHİLİ NO :  " + tokenVariableArray["extension"].substr(tokenVariableArray["extension"].length - 3);
                if ([2, 6].indexOf(parseInt(data.state)) > -1) {
                    softVue.acwStop();
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
                    allowOutsideClick: false,
                    closeOnClickOutside: false,
                    html: '' +
                        '<h3 style="float: left!important; font-weight: bold!important;">Name : ' + data.name + '</h3>' +
                        '<br>' +
                        '<h3 style="float: left!important; font-weight: bold!important;">Ünvan : ' + data.title + '</h3>' +
                        '<br><br>' +
                        '<h3 style="float: left!important; font-weight: bold!important;">Yetki : ' + data.titleGroup + '</h3>',
                    confirmButtonText: 'Kapat!',
                }).then(function (result) {
                    if (result.dismiss == Swal.DismissReason.cancel) {
                    } else if (result.dismiss == Swal.DismissReason.backdrop) {
                    } else if (result.value) {
                    }
                })
            }
        }
    });
}

function siebelFeedBackCallSelectFunction() {
    var siebelFeedBackCallSelect = $("#outBoundSelect").val();
    if (siebelFeedBackCallSelect != "pleaseSelect") {
        siebelFeedbackCall(siebelFeedBackCallSelect);
    } else {
        softVue.siebelFeedBackCallValid = false;
    }
}

function siebelFeedbackCall(type) {
    if (type != "pleaseSelect") {
        if (tbxDebug == true) console.log(type);
        document.getElementById("siebelFrame").contentWindow.postMessage('{"action":"outboundCTICall","payload":{"type":"' + type + '"}}', '*');
        // alert('{"action":"createSR","payload":{"phone":"'+sRemoteNumber+'","callID":"5c04e2eb90d56"}}');

        if (tbxDebug == true) console.log("feedback call yapılacak");
        if (tbxDebug == true) console.log(document.getElementById("siebelFrame"));
    }
}

function siebelCreateSR($phoneNumber, callID) {
    var directionCall = tbxPro.Sessions[tbxPro.callActiveID].direction;
    var callDirection = tbxPro.Sessions[tbxPro.callActiveID].callDirection;


    if (softVue.siebelFeedBackCallValid == true) {
        return false
    } else {
        if (directionCall == "outgoing") {
            return false;
        } else {
            if (callDirection == "Local") {
                return false
            }
        }
    }

    if (callDirection != 'Local') {
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
                    openIgdasWindow(igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo);
                } else {
                    if (tbxDebug == true) console.log('{"action":"createSR","payload":{"phone":"' + $phoneNumber + '","callID":"' + callID + '"}}');
                    document.getElementById("siebelFrame").contentWindow.postMessage('{"action":"createSR","payload":{"phone":"' + $phoneNumber + '","callID":"' + callID + '"}}', '*');
                }
            }
        });
    }
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

function getServerNtpTime() {
    $.ajax({
        type: 'GET',
        url: 'http://10.5.95.157/gettime.php',
        async: false,
        success: function (data) {
            return parseInt(data);
        },
        complete: function (data) {
            if (data.status != 200) {
                return Date.now() / 1000
            }
        }
    });
}

function clickCopyNumber(copyNumber) {
    var copyText = document.getElementById("arayanNoCopy");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
}

function callNumberTrim(callTrimNumber) {
    var splintStr = callTrimNumber.split(" ");
    var resultVal = "";
    for (var i = 0; i < splintStr.length; i++) {
        if (splintStr[i] != "") {
            if (splintStr[i] != " ") {
                resultVal += splintStr[i].trim();
            }
        }
    }
    return resultVal;
}