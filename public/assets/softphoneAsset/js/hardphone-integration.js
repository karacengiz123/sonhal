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
                if (softVue.agentStatus === 12) {
                    stateCheckIntervalFunctionStop();
                    return false;
                }

                softVue.agentStatus = data.state;
                softVue.stateDetailText = data.text;
                softVue.stateDetailTimeStamp = data.timeStamp;

            }
        },
        complete:function (data) {
            if (data.status === 500){
                softVue.agentStatus = 13;
            }
            if (data.status === 0){
                softVue.agentStatus = 18;
            }
        }
    });

}

function acwStop() {

    if (parseInt(softVue.agentStatus) === 8){
        return false;
    }

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

function IVRTransfer(ivrNumber) {
    if (ivrNumber != "0") {
        ivrcon = ivrNumber.length;
        ivrPrefix = ivrcon === 1 ? 800 : 80;
        ivrNumber = ivrPrefix.toString() + ivrNumber.toString();
        tbxPro.sipTransfer(tbxPro.callActiveID, ivrNumber);
    }
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
    setTimeout(function () {
        softTimer.start({countdown: false, startValues: {seconds: time}});

        softTimer.addEventListener('secondsUpdated', function (e) {
            $('#inboundTimer').html(softTimer.getTimeValues().toString());
        });
    }, 100);
}

function countDownTimer(callback, countdownStart = 20) {
    var time = countdownStart;

    if (softVue.stateDetailTimeStamp !== 0) {
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

function createActivity(sentData) {
    var callDirection = tbxPro.Sessions[tbxPro.callActiveID].callDirection;

    if (callDirection !== 'Local') {
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
}

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
                    if (result.dismiss === Swal.DismissReason.cancel) {
                    }else if (result.dismiss === Swal.DismissReason.backdrop){
                    }else if (result.value) {
                    }
                })
            }
        }
    });
}

function siebelCreateSR($phoneNumber, callID) {
    var callDirection = tbxPro.Sessions[tbxPro.callActiveID].callDirection;

    if (callDirection !== 'Local') {
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