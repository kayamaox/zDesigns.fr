function Rebour() {
    var date1 = new Date();
    var date2 = new Date ("Mar 20 21:30:00 2011");
    var sec = (date2 - date1) / 1000;
    var n = 24 * 3600;
    if (sec > 0) {
        j = Math.floor(sec / n);
        h = Math.floor((sec - (j * n)) / 3600);
        mn = Math.floor((sec - ((j * n + h * 3600))) / 60);
        sec = Math.floor(sec - ((j * n + h * 3600 + mn * 60)));

        $('#jour .time').empty().append(j);
        $('#heure .time').empty().append(h);
        $('#min .time').empty().append(mn);
        $('#sec .time').empty().append(sec);

        $('#jour .unite').empty().append( (j > 1) ? 'Jours' : 'Jour' );
        $('#heure .unite').empty().append( (h > 1) ? 'Heures' : 'Heure' );
        $('#min .unite').empty().append( (mn > 1) ? 'Minutes' : 'Minute' );
        $('#sec .unite').empty().append( (sec > 1) ? 'Secondes' : 'Seconde' );
    }
    tRebour = setTimeout("Rebour();", 1000);
}