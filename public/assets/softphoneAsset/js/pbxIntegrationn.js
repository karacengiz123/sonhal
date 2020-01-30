// function getlastCalls() {
//     var inboundtext = "";
//     var outboundtext = "";
//     $.ajax({
//         type: 'GET',
//         url: '/api/softphone/getLastCalls',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if(tbxDebug==true) console.log(JSON.stringify(data));
//             obj = JSON.parse(JSON.stringify(data));
//             $.each(obj.inboundCallsList, function () {
//                 inboundtext += '<tr>\n' +
//                     '<td>'+this.time+'</td>\n' +
//                     '<td>'+this.number+'</td>\n' +
//                     '</tr>';
//             });
//             // console.log(text);
//             $("#inboundlastCalls").html(inboundtext);
//
//             $.each(obj.outboundCallsList, function () {
//                 outboundtext += '<tr>\n' +
//                     '<td>'+this.time+'</td>\n' +
//                     '<td>'+this.number+'</td>\n' +
//                     '</tr>';
//             });
//             // console.log(text);
//             $("#outboundlastCalls").html(outboundtext);
//         },
//         // Auth:"Bearer "+token;
//
//     });
//
// }
//
//
// function molaListesiCek() {
//     var text = "";
//     //var date = getCurrentDateTimeMySql();
//     $.ajax({
//         type: 'GET',
//         url: '/api/break_types',
//
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             //console.log("mola şeysi:");
//             if(tbxDebug==true) console.log(JSON.stringify(data));
//             obj = JSON.parse(JSON.stringify(data));
//
//             $.each(obj, function () {
//
//                 text += '<a class="dropdown-item" href="javascript:molaBasla(' + this['id'] + ');">' + this['name'] + '</a>';
//
//             });
//             // console.log(text);
//             $("#molaMenu").html(text);
//
//         },
//         // Auth:"Bearer "+token;
//
//     });
//
// }
//
// function acwMenuCek() {
//     var text = "";
//     //var date = getCurrentDateTimeMySql();
//     $.ajax({
//         type: 'GET',
//         url: '/api/acw_types',
//
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             //console.log("mola şeysi:");
//             //console.log(JSON.stringify(data));
//             obj = JSON.parse(JSON.stringify(data));
//
//             var roles = 0;
//             $.each(tokenVariableArray.roles, function () {
//                 if (this == "ROLE_TAKIM_LIDERI") {
//                     roles *= 0;
//                     return false;
//                 }
//                 roles += 1;
//             });
//
//             if (roles == 0) {
//                 $.each(obj, function () {
//                     text += '<a class="dropdown-item acwMenuList" href="javascript:acw(20,' + this['id'] + ');">' + this['name'] + '</a>';
//                 });
//             } else {
//                 $.each(obj, function () {
//                     if (this['name'] == "DIŞ ARAMA") {
//                         text += '<a class="dropdown-item acwMenuList" href="javascript:acw(20,' + this['id'] + ');">' + this['name'] + '</a>';
//                     }
//                     if (this['name'] == "SORU") {
//                         text += '<a class="dropdown-item acwMenuList" href="javascript:acw(20,' + this['id'] + ');">' + this['name'] + '</a>';
//                     }
//                 });
//             }
//
//             if(tbxDebug==true)  console.log(text);
//             // console.log(tokenVariableArray.roles);
//             $("#acwMenu").html(text);
//
//         },
//         // Auth:"Bearer "+token;
//
//     });
//
// }
//
// function questionAdd() {
//     var text = "";
//     $.ajax({
//         type: 'GET',
//         url: '/api/acw_types',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             obj = JSON.parse(JSON.stringify(data));
//             if(tbxDebug==true)  console.log(obj);
//             $.each(obj, function () {
//                 if (this['name'] == "SORU") {
//                     text += '<a class="dropdown-item acwMenuList" href="javascript:acwOutBound(20,' + this['id'] + ');">' + this['name'] + '</a>';
//                     return false;
//                 }
//             });
//             $("#outboundQuestion").html(text);
//         },
//     });
// }
//
//
// function ivrListesiCek() {
//     var text = "<option value=\"IvrTransferSec\">Yonlendirilecek IVR 'ı seçiniz..</option>";
//     //var date = getCurrentDateTimeMySql();
//     $.ajax({
//         type: 'GET',
//         url: '/api/ivrs?order%5Bdescription%5D=ASC',
//
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if(tbxDebug==true) console.log(JSON.stringify(data));
//             obj = JSON.parse(JSON.stringify(data));
//
//             $.each(obj, function () {
//                 text += '<option value=' + this['idx'] + '>' + this['description'] + ' - ' + this['title'] + '</option>';
//             });
//             //console.log(text);
//             $("#ivrMenu").html(text);
//             $("#ivrMenuOutbound").html(text);
//
//         },
//         // Auth:"Bearer "+token;
//
//     });
//
// }
//
//
// function getIvrRoute(call_id) {
//     var text = "";
//     //var date = getCurrentDateTimeMySql();
//     $.ajax({
//         type: 'GET',
//         url: '/api/ivr_logs?callId=' + call_id,
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             // console.log(JSON.stringify(data));
//             obj = JSON.parse(JSON.stringify(data));
//
//
//             //console.log(obj);
//
//             var inc = 1;
//             $.each(obj, function () {
//
//                 if(tbxDebug==true)  console.log(this);
//
//                 var ccc = this;
//
//                 $.ajax({
//                     type: 'GET',
//                     url: '/api/ivrs/' + this['ivrId'],
//
//                     headers: {
//                         'Authorization': "Bearer " + getTokenStorage(),
//                         'accept': "application/json",
//                         'Content-Type': "application/json",
//                     },
//                     success: function (data1) {
//                         objIvr = JSON.parse(JSON.stringify(data1));
//
//                         if(tbxDebug==true)  console.log(ccc.ivrId);
//                         if(tbxDebug==true)  console.log(objIvr.description);
//
//                         if(tbxDebug==true)  console.log(objIvr["title"]);
//                         // console.log("#ivr"+inc);
//                         $("#ivr" + inc).val(objIvr["title"]);
//                         inc++;
//                     },
//                 });
//
//             });
//         },
//     });
//
// }
//
//
// function getQueName(que_number, igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo) {
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
//                 createCaseDesktop(igdasState, igdasTDCID, igdasTalepTipi, igdasIVRDurumKodu, igdasAgent, igdasCallID, igdasAciklama, igdasTelefonNo)
//             }
//         }
//     });
// }
//
// function rehberKayitBul(numara) {
//     $.ajax({
//         type: 'GET',
//         url: '/api/guides?phone=' + numara,
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             if(tbxDebug==true)  console.log(JSON.stringify(data));
//             obj = JSON.parse(JSON.stringify(data));
//             $.each(obj, function () {
//                 var isim = this.nameSurname;
//                 var unvan = this.title;
//                 $.ajax({
//                     type: 'GET',
//                     url: "https://" + appServerLink + "" + this.guideGroupID,
//                     headers: {
//                         'Authorization': "Bearer " + getTokenStorage(),
//                         'accept': "application/json",
//                         'Content-Type': "application/json",
//                     },
//                     success: function (data1) {
//                         if(tbxDebug==true)  console.log(JSON.stringify(data1));
//                         obj1 = jQuery.parseJSON(JSON.stringify(data1));
//                         var mevki = obj1.name;
//                         $("#flashingText").html(mevki);
//                         $("#flashingText2").html(isim);
//                         $("#flashingText3").html(unvan);
//                         $('#exampleModal').modal({
//                             show: true
//                         });
//                     },
//                 });
//             });
//         },
//     });
// }
//
// function molaBitir() {
//     molaDisArama = 0;
//     console.log("molabitire girdim");
//     stoppSecondTimer();
//     // $('#breakTextModal').modal({
//     //     backdrop: 'static',
//     //     keyboard: false,
//     //     show: true,
//     //     escapeClose: false,
//     //     clickClose: false,
//     //     showClose: false
//     // });
//     $("#breakText").html("Lütfen Bekleyiniz");
//     $.ajax({
//         type: 'POST',
//         url: '/api/agent-break-updateee/'+molaChange,
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             molaChange = 0;
//             // console.log(data);
//             // console.log(data);
//             if(tbxDebug==true)  console.log(JSON.stringify(data));
//             // obj=JSON.parse(JSON.stringify(data));
//             $("#btnPause").css("display", "none");
//             $("#btnPause").attr('value', "Bitir");
//             btnUnRegister.disabled = false;
//             btnUnRegister.hidden = false;
//             // myTabContent.disable = false;
//             // myTabContent.hidden = false;
//             stopTimerOwn();
//             $("#breakText").html("İyi Çalışmalar");
//             setTimeout(function () {
//                 $('#breakTextModal').modal('hide');
//                 $("#breakText").html("");
//             }, 1000);
//         },
//         error: function (dataError) {
//             alert(dataError.responseJSON.detail);
//         }
//     });
// }
//
// function molaBasla(id) {
//
//     molaDisArama = 1;
//     //console.log("dısaramayı 1 yaptım. molaBasla hede1");
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
//                     molaChange = 1;
//                     disableAcw();
//                     stopTimerOwn();
//                     molaDisArama = 1;
//                     //console.log("dısaramayı 1 yaptım. molaBasla hede1");
//                 }
//                 if (obj.type == "break"){
//
//                     molaChange = 1;
//                     molaBitir();
//                     stopTimerOwn();
//                     molaDisArama = 1;
//                     //console.log("dısaramayı 1 yaptım. molaBasla hede1");
//                 }
//             }
//         }
//     });
//     $('#breakTextModal').modal({
//         backdrop: 'static',
//         keyboard: false,
//         show: true,
//         escapeClose: false,
//         clickClose: false,
//         showClose: false
//     });
//     $("#breakText").html("Lütfen Bekleyiniz");
//
//     $.ajax({
//         type: 'POST',
//         dataType: "json",
//         url: '/api/agent_breaks',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         data: "{\"breakType\": \"/api/break_types/" + id + "\"}",
//         success: function (data) {
//             if(tbxDebug==true)  console.log(data.breakType);
//             var breakTypeId = data.breakType.split('/');
//             if(tbxDebug==true)  console.log(breakTypeId[3]);
//             $.ajax({
//                 type: 'GET',
//                 dataType: "json",
//                 url: '/api/break_types/' + breakTypeId[3] + '',
//                 headers: {
//                     'Authorization': "Bearer " + getTokenStorage(),
//                     'accept': "application/json",
//                     'Content-Type': "application/json",
//                 },
//                 success: function (data) {
//                     stopTimerOwn();
//                     if(tbxDebug==true)  console.log(data);
//                     if(tbxDebug==true)  console.log("Mola talebiniz Onaylanmıştır. Molaya Çıkabilirsiniz.");
//                     if(tbxDebug==true)  console.log(JSON.stringify(data));
//                     if(tbxDebug==true)  obj=JSON.parse(JSON.stringify(data));
//                     $("#btnPause").css("display", "block");
//                     $("#btnPause").attr('value', "" + data.name + " Bitir");
//                     btnUnRegister.disabled = true;
//                     btnUnRegister.hidden = true;
//                     // myTabContent.disable = true;
//                     // myTabContent.hidden = true;
//                     // pauseAgentOnQue();
//                     startTimer();
//                     $('#breakTextModal').modal('hide');
//                     $("#breakText").html("");
//                 },
//                 error: function (dataError) {
//                     alert(dataError.responseJSON.detail);
//                 }
//             });
//         },
//         error: function (dataError) {
//             alert(dataError.responseJSON.detail);
//         }
//     });
//
// }
//
// var callFlashing;
//
// function callFlashingStart() {
//     callFlashing = setInterval(function () {
//         $("#myTabContent").css("background-color", function () {
//             this.switch = !this.switch;
//             return this.switch ? "limegreen" : ""
//         });
//     }, 800)
// }
//
// function callFlashingStop() {
//     clearInterval(callFlashing);
//     $("#myTabContent").css("background-color", "white");
// }
//
// function addUpdateRemove() {
//     $.ajax({
//         type: 'GET',
//         url: '/api/last-register-add-update-remove',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//             register = "on";
//             startTimer();
//             getlastCalls();
//             lastRegister();
//             molaKontrol();
//         }
//     });
// }
//
// function molaKontrol() {
//     if (register == "on") {
//         $.ajax({
//             type: 'GET',
//             url: '/api/agent-managament-control',
//             headers: {
//                 'Authorization': "Bearer " + getTokenStorage(),
//                 'accept': "application/json",
//                 'Content-Type': "application/json",
//             },
//             success: function (data) {
//                 if (data.status > 0) {
//                     if (data.refresh == "on"){
//                         setTimeout(function () {
//                             location.reload();
//                         },2000)
//                     }else {
//                         if (callType == "outBound") {}else {
//                             if (callType == "inBound") {}else {
//                                 if (callQuestion == 0) {
//                                     if (activeCall == false) {
//                                         $.ajax({
//                                             type: 'GET',
//                                             url: '/api/pause-control',
//                                             headers: {
//                                                 'Authorization': "Bearer " + getTokenStorage(),
//                                                 'accept': "application/json",
//                                                 'Content-Type': "application/json",
//                                             },
//                                             success: function (data) {
//                                                 obj = JSON.parse(JSON.stringify(data));
//                                                 if (obj.status == 1) {
//                                                     if (obj.type == "break") {
//                                                         var stop = stopTimerOwn();
//                                                         btnUnRegister.disabled = true;
//                                                         btnUnRegister.hidden = true;
//                                                         $("#doneToCall").css("display", "none");
//                                                         $("#btnPause").css("display", "block");
//                                                         $("#btnPause").html("" + obj.name + " Bitir");
//                                                         $("#btnPause").val("" + obj.name + " Bitir");
//                                                         var start = molaKontrolTimer(obj.time);
//                                                         // pauseAgentOnQue();
//                                                         acwAndBreakControl = 1;
//                                                     } else {
//                                                         if (obj.type == "bitir") {
//                                                             stopTimerOwn();
//                                                             $("#btnPause").css("display", "none");
//                                                             $("#doneToCall").css("display", "none");
//                                                             startTimer();
//                                                         } else {
//                                                             acwKontrol();
//                                                         }
//                                                     }
//                                                 } else {
//                                                     acwKontrol();
//                                                 }
//                                             }
//
//                                         });
//                                     }
//                                 }else {
//                                     callQuestionSave();
//                                 }
//                             }
//                         }
//                     }
//                 } else {
//                     readyControlTimer(data.endBreakTime);
//                     $("#btnPause").css("display", "none");
//                     $("#btnPause").attr('value', "Bitir");
//                     btnUnRegister.disabled = false;
//                     btnUnRegister.hidden = false;
//                     $('#doneToCall').html('Hazır Duruma Geç');
//                     $('#doneToCall').css('display', 'none');
//                     doneToCall.disabled = true;
//                     doneToCall.hidden = true;
//                     btnUnRegister.hidden = false;
//                     $('#btnUnRegister').css('display', 'block');
//                 }
//             }
//         });
//     }
// }
//
// function acwKontrol() {
//     if (callType == "outBound") {}else {
//         if (callType == "inBound") {}else {
//             if (callQuestion == 0) {
//                 if (activeCall == false) {
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
//                                 if (obj.type == "bitir") {
//                                     stopTimerOwn();
//                                     $("#btnPause").css("display", "none");
//                                     $("#doneToCall").css("display", "none");
//                                     startTimer();
//                                 } else {
//                                     if (obj.name == "DIŞ ARAMA") {
//                                         if (obj.time > 20) {
//                                             disableAcw();
//                                         } else {
//                                             btnUnRegister.disabled = true;
//                                             btnUnRegister.hidden = true;
//                                             $("#btnPause").css("display", "none");
//                                             $("#doneToCall").css("display", "block");
//                                             doneToCall.hidden = false;
//                                             doneToCall.disabled = false;
//                                             $("#doneToCall").html("" + obj.name + " Bitir");
//                                             $("#doneToCall").val("" + obj.name + " Bitir");
//                                             // pauseAgentOnQue();
//                                             acwAndBreakControl = 1;
//                                         }
//                                     } else {
//                                         if (obj.name == null) {
//                                             if (obj.time > 20) {
//                                                 disableAcw();
//                                             } else {
//                                                 btnUnRegister.disabled = true;
//                                                 btnUnRegister.hidden = true;
//                                                 $("#btnPause").css("display", "none");
//                                                 $("#doneToCall").css("display", "block");
//                                                 doneToCall.hidden = false;
//                                                 doneToCall.disabled = false;
//                                                 $("#doneToCall").html("Hazır Duruma Geç");
//                                                 $("#doneToCall").val("Hazır Duruma Geç");
//                                                 // pauseAgentOnQue();
//                                                 acwAndBreakControl = 1;
//                                             }
//                                         } else {
//                                             var stop = stopTimerOwn();
//                                             btnUnRegister.disabled = true;
//                                             btnUnRegister.hidden = true;
//                                             $("#btnPause").css("display", "none");
//                                             $("#doneToCall").css("display", "block");
//                                             doneToCall.hidden = false;
//                                             doneToCall.disabled = false;
//                                             $("#doneToCall").html("" + obj.name + " Bitir");
//                                             $("#doneToCall").val("" + obj.name + " Bitir");
//                                             var start = acwKontrolTimer(obj.time);
//                                             // pauseAgentOnQue();
//                                             acwAndBreakControl = 1;
//                                         }
//                                     }
//                                 }
//                             }
//                         }
//                     });
//                 } else {
//                     acwAndBreakControl = 2;
//                 }
//             }else {
//                 callQuestionSave();
//             }
//         }
//     }
// }
//
// function callQuestionOn() {
//     $.ajax({
//         type: 'GET',
//         url: '/api/oudbound-call-pauser',
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//         }
//     });
//     callQuestion = 1;
//     if(tbxDebug==true)  console.log(callQuestion);
//     btnSoru.disabled = true;
// }
//
// function callQuestionSave() {
//     if (callQuestion == 1) {
//         if (callType == "outBound") {
//             $.ajax({
//                 type: 'GET',
//                 url: '/api/acwTypeQuestion',
//                 headers: {
//                     'Authorization': "Bearer " + getTokenStorage(),
//                     'accept': "application/json",
//                     'Content-Type': "application/json",
//                 },
//                 success: function (data) {
//                     obj = JSON.parse(JSON.stringify(data));
//                     $.each(obj, function (key, value) {
//                         if (value.name == "SORU") {
//                             holdLogStop(callID,callType);
//                             callQuestion = 0;
//                             $("#btnSoruOutbound").css("display", "none");
//                             btnSoruOutbound.disabled = true;
//                             acwOutBound(0, value.id);
//                             callType = "";
//                         }
//                     });
//                 }
//             });
//         } else {
//             if (callType == "inBound") {
//                 $.ajax({
//                     type: 'GET',
//                     url: '/api/acwTypeQuestion',
//                     headers: {
//                         'Authorization': "Bearer " + getTokenStorage(),
//                         'accept': "application/json",
//                         'Content-Type': "application/json",
//                     },
//                     success: function (data) {
//                         obj = JSON.parse(JSON.stringify(data));
//                         $.each(obj, function (key, value) {
//                             if (value.name == "SORU") {
//                                 holdLogStop(callID,callType);
//                                 callQuestion = 0;
//                                 $("#btnSoru").css("display", "none");
//                                 btnSoru.disabled = true;
//                                 acw(0, value.id);
//                                 callType = "";
//                             }
//                         });
//                     }
//                 });
//             }
//         }
//
//     }
// }
//
// function holdLogStart(callID, callType) {
//     $.ajax({
//         type: 'POST',
//         url: '/api/hold-log-start/'+callID+"/"+callType,
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//         }
//     });
// }
//
// function holdLogStop(callID,callType) {
//     $.ajax({
//         type: 'POST',
//         url: '/api/hold-log-stop/'+callID+"/"+callType,
//         headers: {
//             'Authorization': "Bearer " + getTokenStorage(),
//             'accept': "application/json",
//             'Content-Type': "application/json",
//         },
//         success: function (data) {
//         }
//     });
// }
//
// function clearIvrValue() {
//     ivr1.value = "";
//     ivr2.value = "";
//     ivr3.value = "";
//     lastQue.value = "";
//
//
//     // $("#ivr1").attr('value','/');
//     // $("#ivr2").attr('value','/');
//     // $("#ivr3").attr('value','/');
//     // $("#lastQue").attr('value','/');
// }