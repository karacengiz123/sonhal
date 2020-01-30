var datatable;
var datatableupdatelink;
var tableFilling = false;
var listTableDataId;
var listTableDataText;
var listTableDatalink;
var tableParam = [];
var tableFunction;
var tableFunctionName;
var tableFunctionParam;
$("#btnRefreshTable").css("display","none");
time = setInterval(function(){
    if (tableFilling == true) {
        tenSecondLoadTable(datatableupdatelink,tableParam);
        $("#btnRefreshTable").css("display","block")
    }else {
        $("#btnRefreshTable").css("display","none")
    }
},10000);

function tenSecondLoadTable(datatableupdatelink,tableParam) {
    var info = datatable.page.info();
    var page = info.page;
    // console.log(page);
    if (tableParam.length > 0){
        $.ajax({
            type:'POST',
            url:tableParam[2],
            data:'tableId='+tableParam[0]+'&tableText='+tableParam[1],
            success: function (data) {
                obj=JSON.parse(JSON.stringify(data));
                var datas = obj.datas;
                datatable.clear();
                datatable.rows.add(datas);
                datatable.draw();
                datatable.page(page).draw(false);
            },
            error:function (data) {
                // console.log(data);
                var errSms = "Beklemedik bir Hata Oluştu";
                loadedModalStart(errSms);
                $("#loadModalAnimation").css("display", "none");
                $(".modal-content").css("background-color", "red");
                $(".modal-content").css("color", "white");
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        });
    }else {
        $.ajax({
            type: "GET",
            url: datatableupdatelink,
            success: function (data) {
                obj=JSON.parse(JSON.stringify(data));
                var datas = obj.datas;
                datatable.clear();
                datatable.rows.add(datas);
                datatable.draw();
                datatable.page(page).draw(false);
            },
            error:function (data) {
                // console.log(data);
                var errSms = "Beklemedik bir Hata Oluştu";
                loadedModalStart(errSms);
                $("#loadModalAnimation").css("display", "none");
                $(".modal-content").css("background-color", "red");
                $(".modal-content").css("color", "white");
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        });
    }
}

// function tenSecondLoadTable() {
//     window[tableFunctionName](tableFunctionParam);
// }

function loadedModalStart(errSms=null) {
    $("#loadCCPulse").modal({
        backdrop: 'static',
        keyboard: false,
        show: true,
        escapeClose: false,
        clickClose: false,
        showClose: false
    });
    if (errSms == null) {
        $("#LoadCCPulseText").html("Lütfen Bekleyiniz");
    }else {
        $("#LoadCCPulseText").html(errSms);
    }
}
function loadedModalFinish() {
    $("#loadCCPulse").modal('hide');
    $("#LoadCCPulseText").html("");
}
function clearSelect() {
    $("#listSelect").select2();
    $("#listSelect").select2("destroy");
    $("#listSelect").html("");
    $("#listSelect").select2();
}
function addSelectData(data) {
    $("#listSelect").select2({
        data:data
    });
}
function displayNoneSelect() {
    $("#listSelect").select2();
    $("#listSelect").select2("destroy");
    $("#listSelect").html("");
    $("#listSelect").css("display","none");
}
function displayBlockSelect() {
    $("#listSelect").css("display","block");
    $("#listSelect").select2();
}

function displayNoneTableDiv() {
    if (tableFilling == true){
        $("#listTableDiv").css("display","none");
        $('#listTable').dataTable().fnDestroy();
        $('#listTable').html("");
        tableFilling = false;
    }
}
function displayBlockTableDiv() {
    $("#listTableDiv").css("display","block");
}

function listSelectData(placeholderSelect,allSelect,listSelectDatalink) {
    var descriptionText = [];
    $.get(listSelectDatalink,
        function (data) {
            clearSelect();
            $("#listSelect").html("<option value='selectionProcess'>"+placeholderSelect+"</option>" +
                "<option value='selectAll'>"+allSelect+"</option>");
            for (var i=0; i<data.length; i++){
                descriptionText[i]={id:data[i].id,text:data[i].text};
            }
            addSelectData(descriptionText);
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
    );
}

// ON SELECT CHANGE START
$('#listSelect').on('select2:select', function (e) {
    listTableDataId = e.params.data.id;
    listTableDataText = e.params.data.text;
    loadedModalStart();
    tableParam =[listTableDataId,listTableDataText,listTableDatalink];
    listTableData(tableParam);
});
// ON SELECT CHANGE FINISH

function listTableData(tableParam){
    $.ajax({
        type:'POST',
        url:tableParam[2],
        data:'tableId='+tableParam[0]+'&tableText='+tableParam[1],
        success:function(data){
            console.log(data);
            obj=JSON.parse(JSON.stringify(data));
            var columns = obj.columns;
            var datas = obj.datas;
            console.log(data);
            console.log(datas);

            displayNoneTableDiv();
            displayBlockTableDiv();
            datatable = $('#listTable').DataTable({
                dom: 'lBfrtip',
                "language": {
                    url: '/assets/Turkish.json'
                },
                columns: columns,
                order:[],
                data:datas,
            });
            tableFilling = true;
            tableParam =[listTableDataId,listTableDataText,listTableDatalink];
            tableFunctionName = "listTableData";
            tableFunctionParam = tableParam;
            datatableupdatelink = tableParam[2];
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        error:function (data) {
            var errSms = "Beklemedik bir Hata Oluştu";
            loadedModalStart(errSms);
            $("#loadModalAnimation").css("display", "none");
            $(".modal-content").css("background-color", "red");
            $(".modal-content").css("color", "white");
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        completed:function (data) {
            if (data.status === 500){
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        }
    });
}

function agentAsQueueGetSelectAll(listTableDatalink) {
    $.ajax({
        type: "GET",
        url: listTableDatalink,
        success: function (data) {
            obj=JSON.parse(JSON.stringify(data));
            var colums = obj.columns;
            var datas = obj.datas;
            displayNoneTableDiv();
            displayBlockTableDiv();
            datatable = $('#listTable').DataTable({
                dom: 'lBfrtip',
                "language": {
                    url: '/assets/Turkish.json'
                },
                columns: colums,
                order:[],
                data:datas
            });
            tableFilling = true;
            tableFunctionName = "agentAsQueueGetSelectAll";
            tableFunctionParam = listTableDatalink;
            datatableupdatelink = listTableDatalink;
            tableParam = [];
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        error:function (data) {
            var errSms = "Beklemedik bir Hata Oluştu";
            loadedModalStart(errSms);
            $("#loadModalAnimation").css("display", "none");
            $(".modal-content").css("background-color", "red");
            $(".modal-content").css("color", "white");
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        completed:function (data) {
            if (data.status === 500){
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        }
    });
}

function callAsQueueGetSelectAll(listTableDatalink) {
    $.ajax({
        type: "GET",
        url: listTableDatalink,
        success: function (data) {
            obj=JSON.parse(JSON.stringify(data));
            var colums = obj.columns;
            var datas = obj.datas;
            displayNoneTableDiv();
            displayBlockTableDiv();
            datatable = $('#listTable').DataTable({
                dom: 'lBfrtip',
                "language": {
                    url: '/assets/Turkish.json'
                },
                columns: colums,
                order:[],
                data:datas
            });
            tableFilling = true;
            tableFunctionName = "callAsQueueGetSelectAll";
            tableFunctionParam = listTableDatalink;
            datatableupdatelink = listTableDatalink;
            tableParam = [];
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        error:function (data) {
            var errSms = "Beklemedik bir Hata Oluştu";
            loadedModalStart(errSms);
            $("#loadModalAnimation").css("display", "none");
            $(".modal-content").css("background-color", "red");
            $(".modal-content").css("color", "white");
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        completed:function (data) {
            if (data.status === 500){
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        }
    });
}

function summaryAll(listTableDatalink) {
    $.ajax({
        type: "GET",
        url: listTableDatalink,
        success: function (data) {
            obj=JSON.parse(JSON.stringify(data));
            var colums = obj.columns;
            var datas = obj.datas;
            displayNoneTableDiv();
            displayBlockTableDiv();
            datatable = $('#listTable').DataTable({
                dom: 'lBfrtip',
                "language": {
                    url: '/assets/Turkish.json'
                },
                columns: colums,
                order:[],
                data:datas
            });
            tableFilling = true;
            tableFunctionName = "summaryAll";
            tableFunctionParam = listTableDatalink;
            datatableupdatelink = listTableDatalink;
            tableParam = [];
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        error:function (data) {
            var errSms = "Beklemedik bir Hata Oluştu";
            loadedModalStart(errSms);
            $("#loadModalAnimation").css("display", "none");
            $(".modal-content").css("background-color", "red");
            $(".modal-content").css("color", "white");
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        completed:function (data) {
            if (data.status === 500){
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        }
    });
}

function chatAll() {
    $.ajax({
        type: "GET",
        url: listTableDatalink,
        success: function (data) {
            obj=JSON.parse(JSON.stringify(data));
            var colums = obj.columns;
            var datas = obj.datas;
            displayNoneTableDiv();
            displayBlockTableDiv();
            datatable = $('#listTable').DataTable({
                dom: 'lBfrtip',
                "language": {
                    url: '/assets/Turkish.json'
                },
                columns: colums,
                order:[],
                data:datas
            });
            tableFilling = true;
            tableFunctionName = "chatAll";
            tableFunctionParam = listTableDatalink;
            datatableupdatelink = listTableDatalink;
            tableParam = [];
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        error:function (data) {
            var errSms = "Beklemedik bir Hata Oluştu";
            loadedModalStart(errSms);
            $("#loadModalAnimation").css("display", "none");
            $(".modal-content").css("background-color", "red");
            $(".modal-content").css("color", "white");
            setTimeout(
                function () {
                    loadedModalFinish();
                    $("#loadModalAnimation").css("display", "block");
                    $(".modal-content").css("background-color", "white");
                    $(".modal-content").css("color", "black");
                }, 500);
        },
        completed:function (data) {
            if (data.status === 500){
                setTimeout(
                    function () {
                        loadedModalFinish();
                        $("#loadModalAnimation").css("display", "block");
                        $(".modal-content").css("background-color", "white");
                        $(".modal-content").css("color", "black");
                    }, 500);
            }
        }
    });
}