// ////////////// giden cağrılar için iki function yeterli olacak.
// /// birincisi geri bildirim cağrıları bunlar içinde sadece call stutusune bakmak yeterli ona gore datayı doldurup basacağız.
// ///ikincisi beyaz taziye ve beyaz itfaiye için olacak.
//
// window.addEventListener("message", receiveMessage, false);
//
//
// function receiveMessage(event) {
//
//     $.ajax({
//         type: 'GET',
//         url: '/api/igdas-frame-control',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if (data == 1) {
//                 getQueName(queNumber, igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo);
//             } else {
//                 if (event.data.length == 0)
//                     return;
//
//                 siebelData = JSON.parse(event.data);
//
//                 if (tbxDebug == true) console.log(siebelData);
//
//                 if (siebelData.action == 'outboundSRCall') {
//                     isOutboundSRCall = true;
//                     toBeSentData.body = siebelData.payload;
//
//                     txtPhoneNumber.value = toBeSentData.body.phone.replace('+', '');
//
//                     if (tbxDebug == true) console.log("outboundSrcall dayım");
//                     if (tbxDebug == true) console.log(toBeSentData);
//
//                     outBoundBeforeAcw(20);
//
//
//                 }
//
//                 if (siebelData.action == 'outboundCall') {
//                     toBeSentData.body = siebelData.payload;
//                     txtPhoneNumber.value = toBeSentData.body.phone.replace('+', '');
//
//                     if (tbxDebug == true) console.log("outboundcall dayım");
//                     if (tbxDebug == true) console.log(toBeSentData);
//                     outBoundBeforeAcw(20);
//                     $("#profile-tab").trigger('click');
//                     txtPhoneNumber.disabled = true;
//
//                 }
//             }
//         }
//     });
// }
//
//
// function siebelFeedbackCall(type) {
//
//     document.getElementById("siebelFrame").contentWindow.postMessage('{"action":"outboundCTICall","payload":{"type":"' + type + '"}}', '*');
//     //alert('{"action":"createSR","payload":{"phone":"'+sRemoteNumber+'","callID":"5c04e2eb90d56"}}');
//     if (tbxDebug == true) console.log("feedback call yapılacak");
// }
//
//
// function siebelCreateSR($phoneNumber, callID) {
//     $.ajax({
//         type: 'GET',
//         url: '/api/igdas-frame-control',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if (data == 1) {
//                 getQueName(queNumber, igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo);
//             } else {
//                 if (tbxDebug == true) console.log('{"action":"createSR","payload":{"phone":"' + $phoneNumber + '","callID":"' + callID + '"}}');
//                 document.getElementById("siebelFrame").contentWindow.postMessage('{"action":"createSR","payload":{"phone":"' + $phoneNumber + '","callID":"' + callID + '"}}', '*');
//             }
//         }
//     });
// }
//
//
// function siebelLogin(username, token, password) {
//
//     if (tbxDebug == true) console.log($username);
//     // document.getElementById("siebelFrame").contentWindow.postMessage('{ "action": "agentLogin", "payload": { "username": "CAGRIAGENT8", "password": "CAGRIAGENT8", "token": "' + window.localStorage.getItem("token") + '" }}', '*');
//     var pass = password;
//     window.localStorage.removeItem("pass");
//     document.getElementById("siebelFrame").contentWindow.postMessage('{ "action": "agentLogin", "payload": { "username": "' + username + '", "password": "' + pass + '", "token": "' + window.localStorage.getItem("token") + '" }}', '*');
//     if (tbxDebug == true) console.log('Siebel Login Tetiklendi.');
// }
//
// function siebelLogout() {
//     document.getElementById("siebelFrame").contentWindow.postMessage('{ "action": "crmLogout", "payload": {}', '*');
//
// }
//
// function startTimer() {
//     timer.start({countdown: false, startValues: {seconds: 0}});
//
//     timer.addEventListener('secondsUpdated', function (e) {
//         $('#inboundTimer').html(timer.getTimeValues().toString());
//     });
//
//     // console.log("st")
// }
//
// function stoppSecondTimer() {
//     secondTimer.stop();
//     secondTimer.addEventListener('stop', function (e) {
//         $('#inboundTimer').html('00:00:00');
//     });
// }
//
// function stopTimerOwn() {
//
//     timer.stop();
//     timer.addEventListener('stop', function (e) {
//         $('#inboundTimer').html('00:00:00');
//     });
//     $('#inboundTimer').html('00:00:00');
//
//     setTimeout(function () {
//         startTimer();
//     }, 1000)
// }
//
// function molaKontrolTimer(timeSeconds) {
//
//     timer.start({countdown: false, startValues: {seconds: timeSeconds}});
//
//     timer.addEventListener('secondsUpdated', function (e) {
//         $('#inboundTimer').html(timer.getTimeValues().toString());
//     });
//
// }
//
// function acwKontrolTimer(timeSeconds) {
//     stopTimerOwn();
//     timer.start({countdown: false, startValues: {seconds: timeSeconds}});
//
//     timer.addEventListener('secondsUpdated', function (e) {
//         $('#inboundTimer').html(timer.getTimeValues().toString());
//     });
//
// }
//
// function readyControlTimer(timeSeconds) {
//     stopTimerOwn();
//     timer.start({countdown: false, startValues: {seconds: timeSeconds}});
//
//     timer.addEventListener('secondsUpdated', function (e) {
//         $('#inboundTimer').html(timer.getTimeValues().toString());
//     });
//
// }
//
// function acwDisAramaKontrolTimer(timeSeconds) {
//
//     timer.start({countdown: true, startValues: {seconds: timeSeconds}});
//
//     timer.addEventListener('secondsUpdated', function (e) {
//         $('#inboundTimer').html(timer.getTimeValues().toString());
//     });
//     timer.addEventListener('targetAchieved', function (e) {
//         disableAcw();
//     });
//
// }
//
// function outBoundStartTimer() {
//
//     outBoundTimer.start({countdown: false, startValues: {seconds: 0}});
//
//     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//
//     // console.log("st - outbound")
// }
//
// function outboundStopTimerOwn() {
//
//     outBoundTimer.start({countdown: false, startValues: {seconds: 0}});
//     outBoundTimer.stop();
//
//     outBoundTimer.addEventListener('started', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('reset', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//
//     // $('#btnAramaktanVazgec').css('display', 'none');
//
//     stopOutbound();
//
//     // siebelFeedbackCall($("#outBoundSelect").val());
//
//     // outBoundBeforeAcw(20);
//
// }
//
// function stopOutbound() {
//     // console.log("outbound durdu");
//     $("#outBoundSelect").val("");
//     $("#txtPhoneNumber").val("");
//     $('#btnAramaktanVazgec').css('display', 'none');
//     //outboundStopTimerOwnAramakIstemiyor();
//
//
//     outBoundTimer.stop();
//     outBoundTimer.addEventListener('stop', function (e) {
//         $('#outBoundTimer').html('00:00:00');
//     });
//     $('#outBoundTimer').html('00:00:00');
//
// }
//
// function outboundStopTimerOwnAramakIstemiyor() {
//
//     outBoundTimer.start({countdown: false, startValues: {seconds: 0}});
//     outBoundTimer.stop();
//
//     outBoundTimer.addEventListener('started', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('reset', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//
//     var startDate = moment().format('M/D/Y H:mm:ss');
//     toBeSentData.body.Planned = startDate;
//     toBeSentData.body.PlannedCompletion = startDate;
//     toBeSentData.body.Started = startDate;
//     toBeSentData.body.UserName = siebelUsername;
//     toBeSentData.body.CallId = callID;
//     toBeSentData.body.Status = "Aranmaktan Vazgeçildi";
//     createActivity(toBeSentData);
//
//     ///yeni kayıt çek
//     // outBoundAfterAcw();
//     stopOutbound();
//
//     $('#btnAramaktanVazgec').css('display', 'none');
//     txtPhoneNumber.disabled = false;
//
// }
//
// function outboundStopTimerCallBtn() {
//
//     outBoundTimer.start({countdown: false, startValues: {seconds: 0}});
//     outBoundTimer.stop();
//
//     outBoundTimer.addEventListener('started', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('reset', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//
//     outBoundStartTimer();
//
//     $("#outBoundSelect").val("");
//     $("#txtPhoneNumber").val("");
//     $('#btnAramaktanVazgec').css('display', 'none');
//
// }
//
//
// function outBoundBeforeAcw(time) {
//     // console.log("out bound beforeda");
//
//     $('#btnAramaktanVazgec').css('display', 'block');
//     outBoundTimer.start({countdown: true, startValues: {seconds: time}});
//
//     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('targetAchieved', function (e) {
//         outboundCall('call-audio');
//         $('#btnAramaktanVazgec').css('display', 'none');
//     });
// }
//
// function outBoundAfterAcw() {
//     siebelFeedbackCall($("#outBoundSelect").val());
//     outBoundTimer.start({countdown: true, startValues: {seconds: 20}});
//
//     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//     });
//     outBoundTimer.addEventListener('targetAchieved', function (e) {
//         $('#btnAramaktanVazgec').css('display', 'none');
//     });
//
// }
//
// function acw(time, acwTypeId) {
//     molaDisArama = 1;
//
//
//     $.ajax({
//         type: 'GET',
//         url: '/api/pause-control',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function(data) {
//             obj=JSON.parse(JSON.stringify(data));
//             if (obj.status == 1){
//                 if (obj.type == "acw"){
//
//                     molaChange = 0;
//                     disableAcw();
//                     stopTimerOwn();
//                     molaDisArama = 1;
//                     //console.log("dısaramayı 1 yaptım. molaBasla hede1");
//                 }
//                 if (obj.type == "break"){
//                     console.log("moladaydı kapadım");
//                     molaChange = 0;
//                     molaBitir();
//                     stopTimerOwn();
//                     molaDisArama = 1;
//                     //console.log("dısaramayı 1 yaptım. molaBasla hede1");
//                 }
//             }
//         }
//     });
//
//
//     //console.log("dısaramayı 1 yaptım acw");
//     $.ajax({
//         type: 'GET',
//         url: '/api/agent-managament-control',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if (data > 0) {
//                 molaKontrol()
//
//             } else {
//
//                 // console.log(arguments.callee.caller.toString());
//
//                 if (acwTypeId == 0) {
//                     stopTimerOwn();
//                     // console.log("acw ye girdim ");
//                     timer.start({countdown: true, startValues: {seconds: time}});
//                     timer.addEventListener('secondsUpdated', function (e) {
//                         $('#inboundTimer').html(timer.getTimeValues().toString());
//                     });
//
//                     secondTimer.start({countdown: true, startValues: {seconds: time + 5}});
//                     secondTimer.addEventListener('secondsUpdated', function (e) {
//
//                     });
//                     secondTimer.addEventListener('targetAchieved', function (e) {
//                         disableAcw();
//                         startInterval();
//                     });
//
//                     $("#btnPause").css("display", "none");
//                     $("#btnPause").attr('value', "Bitir");
//                     $('#doneToCall').css('display', 'block');
//                     doneToCall.disabled = false;
//                     doneToCall.hidden = false;
//                     $("#doneToCall").html("Hazır Duruma Geç");
//                     $("#doneToCall").val("Hazır Duruma Geç");
//                     // pauseAgentOnQue();
//                     $.ajax({
//                         type: 'GET',
//                         url: '/api/acwLogStart/' + acwTypeId,
//                         headers: {
//                             'Authorization': "Bearer " + getTokenStorage(),
//                             'accept': "application/json",
//                             'Content-Type': "application/json",
//                         },
//                         success: function (data) {
//                         }
//                     });
//                 } else {
//
//                     $.ajax({
//                         type: 'GET',
//                         url: '/api/pause-control',
//                         headers: {
//                             'Authorization': "Bearer " + getTokenStorage(),
//                             'accept': "application/json",
//                             'Content-Type': "application/json",
//                         },
//                         success: function (data) {
//                             obj = JSON.parse(JSON.stringify(data));
//                             if (obj.status == 1) {
//                                 if (obj.type == "acw") {
//                                     molaChange = 1;
//                                     disableAcw();
//                                     stopTimerOwn();
//                                     molaDisArama = 1;
//                                 }
//                                 if (obj.type == "break") {
//                                     molaChange = 1;
//                                     molaBitir();
//                                     stopTimerOwn();
//                                     molaDisArama = 1;
//                                 }
//                             }
//                         }
//                     });
//
//                     $('#breakTextModal').modal({
//                         backdrop: 'static',
//                         keyboard: false,
//                         show: true,
//                         escapeClose: false,
//                         clickClose: false,
//                         showClose: false
//                     });
//                     $("#breakText").html("Lütfen Bekleyiniz");
//
//                     $.ajax({
//                         type: 'GET',
//                         url: 'api/acw_types/' + acwTypeId,
//                         headers: {
//                             'Authorization': "Bearer " + getTokenStorage(),
//                             'accept': "application/json",
//                             'Content-Type': "application/json",
//                         },
//                         success: function (data) {
//                             obj = JSON.parse(JSON.stringify(data));
//                             // console.log(obj.name);
//                             if (obj.name == "DIŞ ARAMA") {
//                                 stopTimerOwn();
//                                 // console.log("acw ye girdim ");
//                                 timer.start({countdown: true, startValues: {seconds: time}});
//                                 timer.addEventListener('secondsUpdated', function (e) {
//                                     $('#inboundTimer').html(timer.getTimeValues().toString());
//                                 });
//
//                                 secondTimer.start({countdown: true, startValues: {seconds: time + 5}});
//                                 secondTimer.addEventListener('secondsUpdated', function (e) {
//
//                                 });
//                                 secondTimer.addEventListener('targetAchieved', function (e) {
//                                     disableAcw();
//                                 });
//
//                                 $("#btnPause").css("display", "none");
//                                 $("#btnPause").attr('value', "Bitir");
//                                 $('#doneToCall').css('display', 'block');
//                                 doneToCall.disabled = false;
//                                 doneToCall.hidden = false;
//                                 $('#doneToCall').html(obj.name + ' Bitir');
//                                 // pauseAgentOnQue();
//                                 $.ajax({
//                                     type: 'GET',
//                                     url: '/api/acwLogStart/' + acwTypeId,
//                                     headers: {
//                                         'Authorization': "Bearer " + getTokenStorage(),
//                                         'accept': "application/json",
//                                         'Content-Type': "application/json",
//                                     },
//                                     success: function (data) {
//                                     }
//                                 });
//                             } else {
//                                 stopTimerOwn();
//                                 // console.log("acw ye girdim ");
//                                 timer.start({countdown: false, startValues: {seconds: 0}});
//
//                                 timer.addEventListener('secondsUpdated', function (e) {
//                                     $('#inboundTimer').html(timer.getTimeValues().toString());
//                                 });
//
//                                 $("#btnPause").css("display", "none");
//                                 $("#btnPause").attr('value', "Bitir");
//                                 startTimer();
//                                 $('#doneToCall').css('display', 'block');
//                                 doneToCall.disabled = false;
//                                 doneToCall.hidden = false;
//                                 $('#doneToCall').html(obj.name + ' Bitir');
//                                 // pauseAgentOnQue();
//                                 $.ajax({
//                                     type: 'GET',
//                                     url: '/api/acwLogStart/' + acwTypeId,
//                                     headers: {
//                                         'Authorization': "Bearer " + getTokenStorage(),
//                                         'accept': "application/json",
//                                         'Content-Type': "application/json",
//                                     },
//                                     success: function (data) {
//                                     }
//                                 });
//                             }
//                             setTimeout(function () {
//                                 $('#breakTextModal').modal('hide');
//                                 $("#breakText").html("");
//                             }, 1000);
//                         },
//                     });
//                 }
//
//
//             }
//         }
//
//     });
// }
//
// function disableAcw(molaBasla = 0) {
//     stoppSecondTimer();
//
//
//     if (callType == "outBound") {
//         if(molaDisArama == 1){}else {
//             $('#doneToCall').html('Hazır Duruma Geç');
//             $('#doneToCall').css('display', 'none');
//             doneToCall.disabled = true;
//             doneToCall.hidden = true;
//             btnUnRegister.hidden = false;
//             $('#btnUnRegister').css('display', 'block');
//         }
//     } else {
//         molaDisArama = 0;
//         console.log("dısaramayı 0 yaptım disableAcwELSE");
//         if (molaBasla == 0) {
//             // console.log(arguments.callee.caller.toString());
//             // console.log("acw den çıktım ");
//             stopTimerOwn();
//             // unPauseAgentOnQue();
//             $('#doneToCall').html('Hazır Duruma Geç');
//             $('#doneToCall').css('display', 'none');
//             doneToCall.disabled = true;
//             doneToCall.hidden = true;
//             btnUnRegister.hidden = false;
//             $('#btnUnRegister').css('display', 'block');
//
//             $.ajax({
//                 type: 'GET',
//                 url: '/api/acwLogStop/' + molaChange,
//                 headers: {
//                     'Authorization': "Bearer " + getTokenStorage(),
//                     'accept': "application/json",
//                     'Content-Type': "application/json",
//                 },
//                 success: function (data) {
//                     molaChange = 0;
//                 }
//             });
//         } else {
//             if (molaBasla == 1) {
//                 $('#breakTextModal').modal({
//                     backdrop: 'static',
//                     keyboard: false,
//                     show: true,
//                     escapeClose: false,
//                     clickClose: false,
//                     showClose: false
//                 });
//                 $("#breakText").html("Lütfen Bekleyiniz");
//                 // console.log(arguments.callee.caller.toString());
//                 // console.log("acw den çıktım ");
//                 stopTimerOwn();
//                 $('#doneToCall').css('display', 'none');
//                 doneToCall.disabled = true;
//                 doneToCall.hidden = true;
//                 btnUnRegister.hidden = false;
//                 $('#btnUnRegister').css('display', 'block');
//
//                 $.ajax({
//                     type: 'GET',
//                     url: '/api/acwLogStop/' + molaChange,
//                     headers: {
//                         'Authorization': "Bearer " + getTokenStorage(),
//                         'accept': "application/json",
//                         'Content-Type': "application/json",
//                     },
//                     success: function (data) {
//                         molaChange = 0;
//                     }
//                 });
//             } else {
//                 if (molaBasla == 2) {
//                     // console.log(arguments.callee.caller.toString());
//                     // console.log("acw den çıktım ");
//                     stopTimerOwn();
//                     $('#doneToCall').css('display', 'none');
//                     doneToCall.disabled = true;
//                     doneToCall.hidden = true;
//                     btnUnRegister.hidden = false;
//                     $('#btnUnRegister').css('display', 'block');
//                     // unPauseAgentOnQue();
//                     $.ajax({
//                         type: 'GET',
//                         url: '/api/acwLogStop/' + molaChange,
//                         headers: {
//                             'Authorization': "Bearer " + getTokenStorage(),
//                             'accept': "application/json",
//                             'Content-Type': "application/json",
//                         },
//                         success: function (data) {
//                             molaChange = 0;
//                         }
//                     });
//                 }
//             }
//         }
//
//         $("#breakText").html("İyi Çalışmalar");
//         setTimeout(function () {
//             $('#breakTextModal').modal('hide');
//             $("#breakText").html("");
//         }, 1000);
//         startInterval();
//     }
// }
// function acwOutBound(time, acwTypeId) {
//     if (molaDisArama == 1) {}else {
//         if (acwTypeId == 0) {
//             $.ajax({
//                 type: 'GET',
//                 url: '/api/acwLogStart/' + acwTypeId,
//                 headers: {
//                     'Authorization': "Bearer " + getTokenStorage(),
//                     'accept': "application/json",
//                     'Content-Type': "application/json",
//                 },
//                 success: function (data) {
//                     stopOutbound();
//                     // console.log("acw ye girdim outbound");
//                     outBoundTimer.start({countdown: true, startValues: {seconds: 20}});
//                     outBoundTimer.addEventListener('secondsUpdated', function (e) {
//                         $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//                     });
//
//                     secondTimer.start({countdown: true, startValues: {seconds: 25}});
//                     secondTimer.addEventListener('secondsUpdated', function (e) {
//
//                     });
//                     secondTimer.addEventListener('targetAchieved', function (e) {
//                         disableAcwOutBound();
//                         startInterval();
//                     });
//
//                     $('#disableAcwOutBoundBtn').css('display', 'block');
//                     disableAcwOutBoundBtn.disabled = false;
//                     disableAcwOutBoundBtn.hidden = false;
//                     // pauseAgentOnQue();
//                 }
//             });
//         } else {
//             $('#breakTextModal').modal({
//                 backdrop: 'static',
//                 keyboard: false,
//                 show: true,
//                 escapeClose: false,
//                 clickClose: false,
//                 showClose: false
//             });
//             $("#breakText").html("Lütfen Bekleyiniz");
//             $.ajax({
//                 type: 'GET',
//                 url: 'api/acw_types/' + acwTypeId,
//                 headers: {
//                     'Authorization': "Bearer " + getTokenStorage(),
//                     'accept': "application/json",
//                     'Content-Type': "application/json",
//                 },
//                 success: function (data) {
//                     obj = JSON.parse(JSON.stringify(data));
//                     // console.log(obj.name);
//                     if (obj.name == "DIŞ ARAMA") {
//                         // console.log("acw ye girdim outbound dış arama");
//                         outBoundTimer.start({countdown: true, startValues: {seconds: time}});
//
//                         outBoundTimer.addEventListener('secondsUpdated', function (e) {
//                             $('#inboundTimer').html(outBoundTimer.getTimeValues().toString());
//                         });
//                         outBoundTimer.addEventListener('targetAchieved', function (e) {
//                             disableAcw();
//                         });
//
//                         $('#disableAcwOutBoundBtn').css('display', 'block');
//                         disableAcwOutBoundBtn.disabled = false;
//                         disableAcwOutBoundBtn.hidden = false;
//                         // pauseAgentOnQue();
//                         $.ajax({
//                             type: 'GET',
//                             url: '/api/acwLogStart/' + acwTypeId,
//                             headers: {
//                                 'Authorization': "Bearer " + getTokenStorage(),
//                                 'accept': "application/json",
//                                 'Content-Type': "application/json",
//                             },
//                             success: function (data) {
//                             }
//                         });
//                         $('#breakTextModal').modal('hide');
//                         $("#breakText").html("");
//                     } else {
//                         // console.log("acw ye girdim ");
//                         outBoundTimer.start({countdown: false, startValues: {seconds: 0}});
//
//                         outBoundTimer.addEventListener('secondsUpdated', function (e) {
//                             $('#outBoundTimer').html(outBoundTimer.getTimeValues().toString());
//                         });
//
//                         $('#disableAcwOutBoundBtn').css('display', 'block');
//                         $('#disableAcwOutBoundBtn').html('SORU BİTİR');
//                         disableAcwOutBoundBtn.disabled = false;
//                         disableAcwOutBoundBtn.hidden = false;
//                         // pauseAgentOnQue();
//                         $.ajax({
//                             type: 'GET',
//                             url: '/api/acwLogStart/' + acwTypeId,
//                             headers: {
//                                 'Authorization': "Bearer " + getTokenStorage(),
//                                 'accept': "application/json",
//                                 'Content-Type': "application/json",
//                             },
//                             success: function (data) {
//                             }
//                         });
//                         $('#breakTextModal').modal('hide');
//                         $("#breakText").html("");
//                     }
//
//                 },
//             });
//         }
//     }
// }
//
// function disableAcwOutBound() {
//     stoppSecondTimer();
//     $.ajax({
//         type: 'GET',
//         url: '/api/acwLogStop/' + molaChange,
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             // console.log(arguments.callee.caller.toString());
//             console.log("acw den çıktım ");
//             stopOutbound();
//
//             $.ajax({
//                 type: 'GET',
//                 url: '/api/acwLogStop/' + molaChange,
//                 headers: {
//                     'Authorization': "Bearer " + getTokenStorage(),
//                     'accept': "application/json",
//                     'Content-Type': "application/json",
//                 },
//                 success: function (data) {
//                     molaChange = 0;
//                 }
//             });
//
//             // unPauseAgentOnQue();
//             $('#disableAcwOutBoundBtn').html('Hazır Duruma Geç');
//             $('#disableAcwOutBoundBtn').css('display', 'none');
//             disableAcwOutBoundBtn.disabled = true;
//             disableAcwOutBoundBtn.hidden = true;
//             homeBtn.hidden = false;
//             $('#homeBtn').css('display', 'block');
//             btnUnRegister.hidden = false;
//             $('#btnUnRegister').css('display', 'block');
//         }
//     });
//     startInterval();
// }
//
//
// // function pauseAgentOnQue() {
// //     $.get("https://"+appServerLink+"/ibb-staff/agent-pause/" + window.localStorage.getItem("org.doubango.identity.impi"), function (data) {
// //         console.log(data);
// //     });
// //
// // }
//
// // function unPauseAgentOnQue() {
// //
// //     $.get("https://"+appServerLink+"/ibb-staff/agent-unpause/" + window.localStorage.getItem("org.doubango.identity.impi"), function (data) {
// //         console.log(data);
// //     });
// //
// // }
//
//
// function createActivity(sentData) {
//
//     $.ajax({
//         type: 'GET',
//         url: '/api/igdas-frame-control',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if (data == 1) {
//
//             } else {
//                 postData = JSON.stringify(sentData);
//                 console.log("create activityde");
//                 console.log(postData);
//                 isOutboundSRCall = false;
//                 $.ajax({
//                     type: 'POST',
//                     url: "https://" + appServerLink + "/siebelCrm/createActivity",
//
//                     data: {body: postData},
//                     success:
//                         function (cevap) {
//
//                             console.log(cevap);
//
//                             // obj = JSON.parse(JSON.stringify(cevap));
//                             //
//                             // $.each(obj, function () {
//
//                             console.log("Activite Oluşturuldu. Aktivite ID=" + cevap.ActivityNumber);
//
//
//                             // });
//
//                         },
//                     error: function (error) {
//                         console.log(error);
//                     }
//                 });
//             }
//         }
//     });
// }
//
//
// function createAcwLogActivity(sentData) {
//     $.ajax({
//         type: 'GET',
//         url: '/api/igdas-frame-control',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if (data == 1) {
//
//             } else {
//                 postData = JSON.stringify(sentData);
//                 //yorum
//                 $.ajax({
//                     type: 'POST',
//                     url: "https://" + appServerLink + "/siebelCrm/createActivity",
//
//                     data: sentData,
//                     success:
//                         function (cevap) {
//                             console.log(cevap)
//
//                             obj = JSON.parse(JSON.stringify(cevap));
//
//                             $.each(obj, function () {
//
//                                 alert("Activite Oluşturuldu. Aktivite ID=" + this["ActivityNumber"]);
//
//
//                             });
//
//                         }
//                 });
//             }
//         }
//     });
// }