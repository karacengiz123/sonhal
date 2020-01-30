
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<script
        src="jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous">
</script>

<script>

    function callReceive() {
      callData = {
         'type' : 'callReceive',
         'data' : {'callId':22,'startTime':34343434}
      }
    }
    $(function() {
      $.ajax({
        type: 'POST',
        url: 'http://127.0.0.1:8000/api/login_check',
        data: '{"_username":"ldaptest2@ibb.gov.tr","_password":"MY12080bxp"}',
        success: function(data) {
            window.localStorage.setItem('token',data.token);
          /**
           * Register Ol
           * Siebel Aç
           */
          test()
        },
        contentType: "application/json",
        // Auth:"Bearer "+token;
      });
    });
    function getCurrentDateTimeMySql() {
      var tzoffset = (new Date()).getTimezoneOffset() * 60000; //offset in milliseconds
      var localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0, 19).replace('T', ' ');
      var mySqlDT = localISOTime;
      return mySqlDT;
    }
    function test() {
      var date = getCurrentDateTimeMySql();
      $.ajax({
        type: 'POST',
        url: 'http://127.0.0.1:8000/api/agent_breaks',
        data: '{ "startTime": "'+date+'" }',
        headers: {
          'Authorization': "Bearer "+window.localStorage.getItem('token'),
          'accept': "application/ld+json",
          'Content-Type': "application/ld+json",
        },
        success: function(data) {
          console.log(data);
        },
        // Auth:"Bearer "+token;
      });
    }
</script>
</body>
</html>

curl -X POST -H "Content-Type: application/json" http://santral.localhost/api/login_check -d '{"_username":"sarpdoruk.aslan","_password":"35461468120"}'