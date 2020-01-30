var tbxPro;
$(document).ready(function () {
    tbxPro = {
        config: {
            register: false,
            password: agent.Pass,
            displayName: agent.Display,
            uri: 'sip:' + agent.User + '@' + agent.Realm,
            wsServers: agent.WSServer,
            registerExpires: 30,
            iceCheckingTimeout: 500,
            traceSip: true,
            userAgentString: "TbxPro",
            log: {
                level: 0,
            }
        },
        ringtone: document.getElementById('ringtone'),
        ringbacktone: document.getElementById('ringbacktone'),
        dtmfTone: document.getElementById('dtmfTone'),

        Sessions: [],
        callTimers: {},
        callActiveID: null,
        callVolume: 1,
        Stream: null,

        agentStatus: 0,

        startRingTone: function () {
            try {
                tbxPro.ringtone.play();
            } catch (e) {
            }
        },

        stopRingTone: function () {
            try {
                tbxPro.ringtone.pause();
            } catch (e) {
            }
        },

        startRingbackTone: function () {
            try {
                tbxPro.ringbacktone.play();
            } catch (e) {
            }
        },

        stopRingbackTone: function () {
            try {
                tbxPro.ringbacktone.pause();
            } catch (e) {
            }
        },

        newSession: function (newSess) {

            if (newSess.direction == 'incoming') {

                if (newSess.callDirection != "Local") {
                    // if ([2,6,8,12,13,14,15,16,17,18].indexOf(parseInt(softVue.agentStatus)) > -1){
                    //     newSess.reject();
                    //     return false;
                    // }

                    // Hazır Değilse Her Türlü Reject Edilir. Local Arama Dışında
                    if (parseInt(softVue.agentStatus) != 1) {
                        newSess.reject();
                        return false;
                    }
                }

                if ((newSess.request.headers.hasOwnProperty('X-Cd')) && (newSess.request.headers["X-Cd"][0].raw != "Local")) {
                    if (parseInt(softVue.agentStatus) != 1) {
                        newSess.reject();
                        return false;
                    }
                    newSess.ctxid = newSess.request.headers['X-Uid'][0].raw;
                } else {
                    newSess.ctxid = newSess.request.call_id;
                }

            } else {
                newSess.ctxid = newSess.request.call_id;
            }

            if (tbxPro.callActiveID && tbxPro.callActiveID != newSess.ctxid) {
                newSess.reject();
                return false;
            }

            var startDate = moment().format('M/D/Y H:mm:ss');
            // var startDate = Ntpden Alınan Saat Gelecek
            var status;

            newSess.displayName = newSess.remoteIdentity.uri.user || newSess.remoteIdentity.displayName;

            tbxPro.callActiveID = newSess.ctxid;
            tbxPro.Sessions[newSess.ctxid] = newSess;

            toBeSentData = {
                "body": {
                    "Description": "+9" + newSess.remoteIdentity.uri.user + " - Gelen Çağrı",
                    "Planned": startDate,
                    "PlannedCompletion": "",
                    "Started": startDate,
                    "UserName": siebelUsername,
                    "Type": "Çağrı - Gelen",
                    "Status": "Tamamlandı",
                    "CallId": newSess.ctxid,
                    "SurveyFlg": newSess.surveyFlag,
                    "IVRKeys": ''
                }
            };

            if (newSess.direction == 'incoming') {

                // showNotifICall(newSess.remoteIdentity.uri.user);
                tbxPro.startRingTone();

                newSess.callID = newSess.request.headers['X-Uid'][0].raw;
                softVue.callQueue = newSess.request.headers['X-Nodest'][0].raw;
                newSess.XcallID = newSess.request.headers['X-Callid'][0].raw;
                newSess.ChannelId = newSess.request.headers['X-Channel'][0].raw;
                newSess.surveyFlag = newSess.request.headers['X-Poll'][0].raw == 1 ? 'Y' : 'N';
                newSess.callDirection = newSess.request.headers['X-Cd'][0].raw;
                igdasTelefonNo = newSess.remoteIdentity.uri.user;
                status = "Incoming: " + newSess.displayName;
                softVue.agentStatus = 12;
                stateCheckIntervalFunctionStop();

                if (newSess.callDirection == "Local") {
                    softVue.agentStates[softVue.agentStatus].title = "Local Gelen Arama";
                }else {
                    softVue.agentStates[softVue.agentStatus].title = "Gelen Arama";
                }

                vipModal(newSess.displayName.substr(newSess.displayName.length - 11));
                softVue.getIvrRoute(newSess.XcallID);
                softVue.ChannelId = newSess.ChannelId;
                if (newSess.surveyFlag == "Y") {
                    $("#surveyStatus").html("Ankete Katılacak")
                } else {
                    $("#surveyStatus").html("")
                }
                guideFindNumber(newSess.remoteIdentity.uri.user);

                if (newSess.callDirection != "Local") {
                    setTimeout(function () {
                        newSess.accept({
                            media: {
                                stream: tbxPro.Stream,
                                constraints: {audio: true, video: false},
                                render: {
                                    remote: $('#audioRemote').get()[0]
                                },
                                RTCConstraints: {"optional": [{'DtlsSrtpKeyAgreement': 'true'}]}
                            }
                        });
                    }, 3000);
                }

            } else {
                status = "Trying: " + newSess.displayName;
                tbxPro.startRingbackTone();
            }
            tbxPro.setCallSessionStatus(status);

            newSess.on('progress', function (e) {
                if (e.direction == 'outgoing') {
                    tbxPro.setCallSessionStatus('Calling...');
                }
            });

            newSess.on('connecting', function (e) {
                if (e.direction == 'outgoing') {
                    tbxPro.setCallSessionStatus('Connecting...');
                }
            });

            newSess.on('accepted', function (e) {
                softVue.btnMuteSpanText = "SESSİZ";
                softVue.btnHoldText = "BEKLET";

                if (newSess.callDirection == 'Local') {
                    softVue.agentStatus = 17;
                    stateCheckIntervalFunctionStop();
                } else {
                    softVue.agentStatus = 8;
                    stateCheckIntervalFunctionStop();
                }

                tbxPro.stopRingbackTone();
                tbxPro.stopRingTone();
                tbxPro.setCallSessionStatus('Answered');

                softVue.callId = newSess.ctxid;

                $("#arayanNo").val("SON NUMARA :  " + newSess.displayName);
                siebelCreateSR("9" + newSess.displayName, softVue.callId);
            });

            newSess.on('hold', function (e) {
                chronoTimer();
                softVue.agentStatus = 9;
                holdLogStart(newSess.ctxid, newSess.direction == 'incoming' ? 'inBound' : 'outBound');
            });


            newSess.on('unhold', function (e) {
                chronoTimer();
                softVue.agentStatus = 8;
                holdLogStop(newSess.ctxid, newSess.direction == 'incoming' ? 'inBound' : 'outBound');
                tbxPro.callActiveID = newSess.ctxid;
            });

            newSess.on('muted', function (e) {
                tbxPro.Sessions[newSess.ctxid].isMuted = true;
                tbxPro.setCallSessionStatus("Muted");
            });

            newSess.on('unmuted', function (e) {
                tbxPro.Sessions[newSess.ctxid].isMuted = false;
                tbxPro.setCallSessionStatus("Answered");
            });

            newSess.on('cancel', function (e) {

                if (tbxDebug == true) console.log("canceled");

                if (tbxDebug == true) console.log("bye => ");

                if (tbxDebug == true) console.log(newSess);

                document.getElementById('numPadInbound').value = '';
                softVue.btnMuteSpanText = "SESSİZ";
                softVue.btnHoldText = "BEKLET";

                tbxPro.stopRingTone();
                tbxPro.stopRingbackTone();
                tbxPro.setCallSessionStatus("Canceled");

                if (softVue.siebelFeedBackCall == true){
                    softVue.siebelFeedBackCall = false;
                }

                if (newSess == null) {
                    softVue.agentStatus = 1007;
                    stateCheck();
                    stateCheckIntervalFunction();
                } else {
                    if (newSess.callDirection == 'Local') {
                        softVue.agentStatus = 1007;
                        stateCheck();
                        stateCheckIntervalFunction();
                    } else {
                        toBeSentData.body.Status = "Meşgul";

                        if (softVue.siebelFeedBackCallData != null){
                            var endDate = moment().format('M/D/Y H:mm:ss');

                            toBeSentData.body.Description = softVue.siebelFeedBackCallData.payload.Description;
                            toBeSentData.body.PlannedCompletion = endDate;
                            toBeSentData.body.Type = softVue.siebelFeedBackCallData.payload.Type;

                            // if (softVue.siebelFeedBackCallData.payload.ActivitySubtype == "Geri Bildirim"){
                            //     toBeSentData.body.ActivitySubtype = "Çağrı - Giden";
                            // }else {
                                toBeSentData.body.ActivitySubtype = softVue.siebelFeedBackCallData.payload.ActivitySubtype;
                            // }


                            toBeSentData.body.ContactId = softVue.siebelFeedBackCallData.payload.ContactId;
                            toBeSentData.body.SRId = softVue.siebelFeedBackCallData.payload.SRId;
                        }

                        createActivity(toBeSentData);
                        softVue.acwStart(1);
                    }
                }

                if (this.direction == 'outgoing') {
                    tbxPro.callActiveID = null;
                    newSess = null;
                }
                getlastCalls();
                softVue.siebelFeedBackCallData = null;
                softVue.localOutGouing = false;
            });

            newSess.on('bye', function (e) {
                if (tbxDebug == true) console.log("bye");

                if (tbxDebug == true) console.log("bye => ");

                if (tbxDebug == true) console.log(newSess);

                document.getElementById('numPadInbound').value = '';
                softVue.btnMuteSpanText = "SESSİZ";
                softVue.btnHoldText = "BEKLET";
                $("#surveyStatus").html("");

                tbxPro.stopRingTone();
                tbxPro.stopRingbackTone();
                tbxPro.setCallSessionStatus("");

                toBeSentData.body.PlannedCompletion = moment().format('M/D/Y H:mm:ss').toLocaleString();

                if (softVue.siebelFeedBackCall == true){
                    softVue.siebelFeedBackCall = false;
                }
                if (newSess == null) {
                    softVue.agentStatus = 1007;
                    stateCheck();
                    stateCheckIntervalFunction();
                } else {
                    if (newSess.callDirection == 'Local') {
                        softVue.agentStatus = 1007;
                        stateCheck();
                        stateCheckIntervalFunction();
                    } else {

                        if (softVue.siebelFeedBackCallData != null){
                            var endDate = moment().format('M/D/Y H:mm:ss');

                            toBeSentData.body.Description = softVue.siebelFeedBackCallData.payload.Description;
                            toBeSentData.body.PlannedCompletion = endDate;
                            toBeSentData.body.Type = softVue.siebelFeedBackCallData.payload.Type;

                            // if (softVue.siebelFeedBackCallData.payload.ActivitySubtype == "Geri Bildirim"){
                            //     toBeSentData.body.ActivitySubtype = "Çağrı - Giden";
                            // }else {
                                toBeSentData.body.ActivitySubtype = softVue.siebelFeedBackCallData.payload.ActivitySubtype;
                            // }

                            toBeSentData.body.ContactId = softVue.siebelFeedBackCallData.payload.ContactId;
                            toBeSentData.body.SRId = softVue.siebelFeedBackCallData.payload.SRId;
                        }

                        createActivity(toBeSentData);
                        softVue.acwStart(1);
                    }
                }

                softVue.callQueue = null;
                softVue.callIvrs = null;

                tbxPro.callActiveID = null;
                newSess = null;
                getlastCalls();
                softVue.siebelFeedBackCallData = null;
                softVue.localOutGouing = false;
            });

            newSess.on('failed', function (e) {
                if (tbxDebug == true) console.log("failed");

                if (tbxDebug == true) console.log("bye => ");

                if (tbxDebug == true) console.log(newSess);


                if (softVue.siebelFeedBackCall == true){
                    softVue.siebelFeedBackCall = false;
                }
                if (newSess == null) {
                    softVue.agentStatus = 1007;
                    stateCheck();
                    stateCheckIntervalFunction();
                } else {
                    if (newSess.callDirection == 'Local') {
                        softVue.agentStatus = 1007;
                        stateCheck();
                        stateCheckIntervalFunction();
                        if (softVue.localOutGouing == true){
                            Swal.fire({
                                title: '!!! -DİKKAT- !!!',
                                html: '<h3 style="float: left!important; font-weight: bold!important;">Aradığınız Numaraya Ulaşılamıyor..!!</h3>',
                                confirmButtonText: 'Kapat!',
                            }).then(function (result) {
                                if (result.dismiss == Swal.DismissReason.cancel) {
                                }else if (result.dismiss == Swal.DismissReason.backdrop){
                                }else if (result.value) {
                                }
                            });
                        }
                    } else {
                        toBeSentData.body.Status = "Meşgul";

                        if (softVue.siebelFeedBackCallData != null){
                            var endDate = moment().format('M/D/Y H:mm:ss');

                            toBeSentData.body.Description = softVue.siebelFeedBackCallData.payload.Description;
                            toBeSentData.body.PlannedCompletion = endDate;
                            toBeSentData.body.Type = softVue.siebelFeedBackCallData.payload.Type;

                            // if (softVue.siebelFeedBackCallData.payload.ActivitySubtype == "Geri Bildirim"){
                            //     toBeSentData.body.ActivitySubtype = "Çağrı - Giden";
                            // }else {
                                toBeSentData.body.ActivitySubtype = softVue.siebelFeedBackCallData.payload.ActivitySubtype;
                            // }

                            toBeSentData.body.ContactId = softVue.siebelFeedBackCallData.payload.ContactId;
                            toBeSentData.body.SRId = softVue.siebelFeedBackCallData.payload.SRId;
                        }

                        createActivity(toBeSentData);
                        softVue.acwStart(1);
                        if (newSess.direction != 'incoming'){
                            Swal.fire({
                                title: '!!! -DİKKAT- !!!',
                                html: '<h3 style="float: left!important; font-weight: bold!important;">Aradığınız Numaraya Ulaşılamıyor..!!</h3>',
                                confirmButtonText: 'Kapat!',
                            }).then(function (result) {
                                if (result.dismiss == Swal.DismissReason.cancel) {
                                }else if (result.dismiss == Swal.DismissReason.backdrop){
                                }else if (result.value) {
                                }
                            });
                        }
                    }
                }

                document.getElementById('numPadInbound').value = '';
                softVue.btnMuteSpanText = "SESSİZ";
                softVue.btnHoldText = "BEKLET";
                tbxPro.stopRingTone();
                tbxPro.stopRingbackTone();
                tbxPro.setCallSessionStatus('Terminated');
                getlastCalls();
                softVue.siebelFeedBackCallData = null;
                softVue.localOutGouing = false;
            });

            newSess.on('rejected', function (e) {
                if (tbxDebug == true) console.log("rejected");

                if (tbxDebug == true) console.log("bye => ");

                if (tbxDebug == true) console.log(newSess);

                document.getElementById('numPadInbound').value = '';

                softVue.btnMuteSpanText = "SESSİZ";
                softVue.btnHoldText = "BEKLET";
                $("#surveyStatus").html("");


                if (softVue.siebelFeedBackCall == true){
                    softVue.siebelFeedBackCall = false;
                }
                if (newSess == null) {
                    softVue.agentStatus = 1007;
                    stateCheck();
                    stateCheckIntervalFunction();
                } else {
                    if (newSess.callDirection == 'Local') {
                        softVue.agentStatus = 1007;
                        stateCheck();
                        stateCheckIntervalFunction();
                        if (softVue.localOutGouing == true){
                            Swal.fire({
                                title: '!!! -DİKKAT- !!!',
                                html: '<h3 style="float: left!important; font-weight: bold!important;">Aradığınız Numaraya Ulaşılamıyor..!!</h3>',
                                confirmButtonText: 'Kapat!',
                            }).then(function (result) {
                                if (result.dismiss == Swal.DismissReason.cancel) {
                                }else if (result.dismiss == Swal.DismissReason.backdrop){
                                }else if (result.value) {
                                }
                            });
                        }
                    } else {
                        toBeSentData.body.Status = "Meşgul";

                        if (softVue.siebelFeedBackCallData != null){
                            var endDate = moment().format('M/D/Y H:mm:ss');

                            toBeSentData.body.Description = softVue.siebelFeedBackCallData.payload.Description;
                            toBeSentData.body.PlannedCompletion = endDate;
                            toBeSentData.body.Type = softVue.siebelFeedBackCallData.payload.Type;

                            // if (softVue.siebelFeedBackCallData.payload.ActivitySubtype == "Geri Bildirim"){
                            //     toBeSentData.body.ActivitySubtype = "Çağrı - Giden";
                            // }else {
                                toBeSentData.body.ActivitySubtype = softVue.siebelFeedBackCallData.payload.ActivitySubtype;
                            // }

                            toBeSentData.body.ContactId = softVue.siebelFeedBackCallData.payload.ContactId;
                            toBeSentData.body.SRId = softVue.siebelFeedBackCallData.payload.SRId;
                        }

                        createActivity(toBeSentData);
                        softVue.acwStart(1);

                        if (newSess.direction != 'incoming'){
                            Swal.fire({
                                title: '!!! -DİKKAT- !!!',
                                html: '<h3 style="float: left!important; font-weight: bold!important;">Aradığınız Numaraya Ulaşılamıyor..!!</h3>',
                                confirmButtonText: 'Kapat!',
                            }).then(function (result) {
                                if (result.dismiss == Swal.DismissReason.cancel) {
                                }else if (result.dismiss == Swal.DismissReason.backdrop){
                                }else if (result.value) {
                                }
                            });
                        }
                    }
                }

                tbxPro.stopRingTone();
                tbxPro.stopRingbackTone();
                tbxPro.setCallSessionStatus('Rejected');
                tbxPro.callActiveID = null;
                newSess = null;
                getlastCalls();
                softVue.siebelFeedBackCallData = null;
                softVue.localOutGouing = false;
            });


        },

        // getUser media request refused or device was not present
        getUserMediaFailure: function (e) {
            window.console.error('getUserMedia failed:', e);
            tbxPro.setError(true, 'Media Error.', 'You must allow access to your microphone.  Check the address bar.', true);
        },

        getUserMediaSuccess: function (stream) {
            tbxPro.Stream = stream;
        },

        /**
         * sets the ui call status field
         *
         * @param {string} status
         */
        setCallSessionStatus: function (status) {
            $('#txtCallStatus').html(status);
        },

        /**
         * sets the ui connection status field
         *
         * @param {string} status
         */
        setStatus: function (status) {
            $("#txtRegStatus").html('<i class="fa fa-signal"></i> ' + status);
        },

        sipCall: function (target) {

            try {

                var s = tbxPro.phone.invite(target, {
                    media: {
                        stream: tbxPro.Stream,
                        constraints: {audio: true, video: false},
                        render: {
                            remote: $('#audioRemote').get()[0]
                        },
                        RTCConstraints: {"optional": [{'DtlsSrtpKeyAgreement': 'true'}]}
                    }
                });

                if(target.length == 3) {
                    softVue.agentStatus = 17;
                    s.callDirection = 'Local';
                    stateCheckIntervalFunctionStop();
                } else {
                    softVue.agentStatus = 8;
                    stateCheckIntervalFunctionStop();
                }

                s.direction = 'outgoing';

                tbxPro.newSession(s);

                // var callDirection = tbxPro.Sessions[tbxPro.callActiveID].callDirection;

            } catch (e) {
                throw(e);
            }
        },

        sipTransfer: function (sessionid, target) {

            if (!sessionid)
                sessionid = tbxPro.callActiveID;

            var s = tbxPro.Sessions[sessionid];

            if (!target)
                target = window.prompt('Enter destination number', '');

            tbxPro.setCallSessionStatus('<i>Transfering the call...</i>');
            s.refer(target);
        },

        sipHangUp: function (sessionid) {

            if (!sessionid)
                sessionid = tbxPro.callActiveID;

            var s = tbxPro.Sessions[sessionid];
            // s.terminate();
            if (!s) {
                return;
            } else if (s.startTime) {
                s.bye();
            } else if (s.reject) {
                s.reject();
            } else if (s.cancel) {
                s.cancel();
            }

        },

        sipSendDTMF: function (digit, transfer = null) {
            if (tbxDebug == true) console.log(digit);
            var a = tbxPro.callActiveID;
            if (a) {
                var s = tbxPro.Sessions[a];
                s.dtmf(digit, {duration: 70, interToneGap: 10});
                if (transfer == 1) {
                    tbxPro.phoneHoldButtonPressed();
                }
            }
        },

        phoneCallButtonPressed: function () {
            var s = tbxPro.Sessions[tbxPro.callActiveID];


            s.accept({
                media: {
                    stream: tbxPro.Stream,
                    constraints: {audio: true, video: false},
                    render: {
                        remote: $('#audioRemote').get()[0]
                    },
                    RTCConstraints: {"optional": [{'DtlsSrtpKeyAgreement': 'true'}]}
                }
            });
        },

        phoneMuteButtonPressed: function (sessionid) {
            if (!sessionid)
                sessionid = tbxPro.callActiveID;

            var s = tbxPro.Sessions[sessionid];

            if (s.isOnHold().local == true) {
                softVue.btnHoldText = "BEKLET";
                s.unhold();
            }

            if (!s.isMuted) {
                softVue.btnMuteSpanText = "SESSİZ ÇIK";
                s.mute();
            } else {
                softVue.btnMuteSpanText = "SESSİZ";
                s.unmute();
            }
        },

        phoneHoldButtonPressed: function (sessionid) {
            if (!sessionid)
                sessionid = tbxPro.callActiveID;
            var s = tbxPro.Sessions[sessionid];

            if (s.isMuted) {
                softVue.btnMuteSpanText = "SESSİZ";
                s.unmute();
            }
            if (s.isOnHold().local == true) {
                softVue.btnHoldText = "BEKLET";
                s.unhold();
            } else {
                softVue.btnHoldText = "BEKLET ÇIK";
                s.hold();
            }
        },


        setError: function (err, title, msg, closable) {

        },

        /**
         * Tests for a capable browser, return bool, and shows an
         * error modal on fail.
         */
        hasWebRTC: function () {

            if (navigator.webkitGetUserMedia) {
                return true;
            } else if (navigator.mozGetUserMedia) {
                return true;
            } else if (navigator.getUserMedia) {
                return true;
            } else {
                tbxPro.setError(true, 'Unsupported Browser.', 'Your browser does not support the features required for this phone.');
                window.console.error("WebRTC support not found");
                return false;
            }
        }
    };


    // Throw an error if the browser can't hack it.
    if (!tbxPro.hasWebRTC()) {
        return true;
    }

    tbxPro.phone = new SIP.UA(tbxPro.config);

    tbxPro.phone.on('connected', function (e) {
        tbxPro.setStatus("Connected");
    });

    tbxPro.phone.on('disconnected', function (e) {
        tbxPro.setStatus("Disconnected");
        softVue.agentStatus = 14;
        setStateEvent(14);
        // disable phone
        tbxPro.setError(true, 'Websocket Disconnected.', 'An Error occurred connecting to the websocket.');

        // remove existing sessions
        $("#sessions > .session").each(function (i, session) {
            tbxPro.removeSession(session, 500);
        });
    });

    tbxPro.phone.on('registered', function (e) {
        // window.onbeforeunload = closeEditorWarning;
        // window.onunload = closePhone;

        // This key is set to prevent multiple windows.
        localStorage.setItem('ctxPhone', 'true');

        tbxPro.setStatus("Ready");


        // Get the userMedia and cache the stream
        if (SIP.WebRTC.isSupported()) {
            SIP.WebRTC.getUserMedia({
                audio: true,
                video: false
            }, tbxPro.getUserMediaSuccess, tbxPro.getUserMediaFailure);
        }
    });

    tbxPro.phone.on('registrationFailed', function (e) {
        tbxPro.setError(true, 'Registration Error.', 'An Error occurred registering your phone. Check your settings.');
        tbxPro.setStatus("Error: Registration Failed");
    });

    tbxPro.phone.on('unregistered', function (e) {
        tbxPro.setError(true, 'Registration Error.', 'An Error occurred registering your phone. Check your settings.');
        tbxPro.setStatus("Error: Registration Failed");
    });

    tbxPro.phone.on('invite', function (incomingSession) {

        var s = incomingSession;

        s.direction = 'incoming';
        tbxPro.newSession(s);
    });
});
