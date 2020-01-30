var formatTime = function (time) {
    return [
        Math.floor((time % 3600) / 60), // minutes
        ('00' + Math.floor(time % 60)).slice(-2) // seconds
    ].join(':');
};

        var wavesurfer = WaveSurfer.create({
    container: '#waveform',
    waveColor: 'dark',
    progressColor: 'green',
      height: 55,
      
      
});

wavesurfer.load('http://www.kozco.com/tech/organfinale.wav');

wavesurfer.on('ready', function () {
    $('.2').text(formatTime(wavesurfer.getDuration()) );

                var derece=wavesurfer.getDuration();
                var deger=wavesurfer.isPlaying()();
                $('#infoDuration').append(deger);
    // wavesurfer.play();


});



// Show current time
wavesurfer.on('audioprocess', function () {
    $('.1').text( formatTime(wavesurfer.getCurrentTime()) );

});

$('#sesAcKapa').click(function(){

    var ikon=$('#iconVolume');
    var klas=$('#iconVolume').attr('class');


    if (klas=="fa fa-volume-up") {
        ikon.removeClass('fa fa-volume-up');
         ikon.addClass('fa fa-volume-off');
        
    } else {
        ikon.removeClass('fa fa-volume-off');
         ikon.addClass('fa fa-volume-up');
        
    }




})
