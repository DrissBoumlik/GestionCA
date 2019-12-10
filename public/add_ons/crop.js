$(function () {
    var resize = $('#crop-box').croppie({
        enableExif: true,
        // enableOrientation: true,
        viewport: {
            width: 200,
            height: 200,
            type: 'square', // 'circle'
        },
        boundary: {
            width: 300,
            height: 300
        }
    });

    $('#picture').on('change', function () {
        var reader = new FileReader();
        reader.onload = function (e) {
            resize.croppie('bind',{
                url: e.target.result
            }).then(function(){
                console.log('jQuery bind complete');
            });
        };
        if (this.files.length) {
            reader.readAsDataURL(this.files[0]);
            $('#cropModal').modal('show');
        }
    });

    $('.crop_image').click(function(event){
        resize.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function(img){
            $('#user-picture').attr("src", img);
            $('#picture-data').val(img);
        })
    });
});
