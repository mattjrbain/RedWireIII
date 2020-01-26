$(function () {
    // Multiple images preview in browser
    let imagesPreview = function (input, placeToInsertImagePreview) {
        // $(placeToInsertImagePreview).html("");
        if (input.files) {
            let filesAmount = input.files.length;

            for (let i = 0; i < filesAmount; i++) {
                let reader = new FileReader();

                reader.onload = function (event) {
                    $($.parseHTML('<img>')).attr('src', event.target.result).attr('class', 'col-md-4 mt-2').css('max-height', '200px').appendTo(placeToInsertImagePreview);
                };

                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#annonce_images').on('change', function () {
        $('.gallery').html("");
        imagesPreview(this, 'div.gallery');
    });
});