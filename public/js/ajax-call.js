alert('inicio')

function MeGusta(id) {

    $.ajax({
        type: 'POST',
        url: Routing.generate('likes', /* your params */ ),
        data: ({ id: id }),
        async: true,
        dataType: "json",
        success: function(data) {
            console.log('exit');
        }
    });
}