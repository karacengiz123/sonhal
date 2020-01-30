function iframeControl(entityId=0) {
    if (igdasIframe == true){
        var url = "ie://tbxdev.ibb.gov.tr/ivr/igdas/ie/link/"+entityId;

        $("#iFrameDiv").html('');
        $("#iFrameDiv").html('İGDAŞ TEMSİLCİSİ');
        window.open(url,"igdasWindow");

    } else {
        igdasControl()
    }
}

function igdasControl() {
    $.ajax({
        type: 'GET',
        url: '/api/igdas-frame-control',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        success: function(data) {
            if (data == 1) {
                $("#iFrameDiv").html('');
                $("#iFrameDiv").html('İGDAŞ TEMSİLCİSİ');
            }else {
                $("#iFrameDiv").html('');
                $("#iFrameDiv").html('<iframe src="'+siebeliFrameLink+'" id="siebelFrame" style="min-height: 100rem!important; padding-bottom: 120px!important;" onload="onMyFrameLoad();"></iframe>');
                // $("#iFrameDiv").html('CRM KAPANDI');
                // $("#iFrameDiv").html('<iframe src="'+siebeliFrameLink+'" id="siebelFrame" style="min-height: 100rem!important; padding-bottom: 120px!important;" onload="onMyFrameLoad();"></iframe>');
                // $("#iFrameDiv").html('CRM KAPANDI');
            }
        }
    });
}


function createCaseDesktop(igdasState,igdasTDCID,igdasTalepTipi,igdasIVRDurumKodu,igdasAgent,igdasCallID,igdasAciklama,igdasTelefonNo) {


    $.ajax({
        type: 'POST',
        url: '/api/ivr/igdas/pbx/createCaseForDesktop',
        headers: {
            'Authorization': "Bearer " + getTokenStorage(),
            'accept': "application/json",
            'Content-Type': "application/json",
        },
        data:"pState="+igdasState+"&pTDCID="+igdasTDCID+"&pTalepTipi="+igdasTalepTipi+"&pIVRDurumKodu="+igdasIVRDurumKodu+"&pAgent="+igdasAgent+"&pCallID="+igdasCallID+"&pAciklama="+igdasAciklama+"&pTelefonNo="+igdasTelefonNo,
        success: function(data) {
            obj=JSON.parse(JSON.stringify(data));
            igdasIframe = true;
            console.log(obj);
            console.log(igdasIframe);
            // console.log(obj._EntityId);
            console.log(obj._EntityId.toUpperCase());
            iframeControl(obj._EntityId.toUpperCase());
        }
    });
}