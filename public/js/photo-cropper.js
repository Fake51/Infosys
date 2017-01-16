/*global infosysExportWidth, infosysExportHeight, infosysCropUrl, infosysOriginalUrl */

(function () {
    'use strict';
    var elements = {},
        cropItSettings = {
            imageBackground: true,
            width: infosysExportWidth,
            height: infosysExportHeight
        },
        indicateSuccess = function () {
            alert('wooohoo');
        },
        indicateFailure = function () {
            alert('damn');
        },
        upload = function (data, url) {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {image: data},
                    success: resolve,
                    error: reject
                });
            });
        },
        uploadCropped = function () {
            return upload(elements.$cropper.cropit('export'), infosysCropUrl);
        },
        uploadOriginal = function (data) {
            return upload(elements.$cropper.cropit('export', {originalSize: true}), infosysOriginalUrl);
        },
        uploadPhoto = function () {
            Promise.all([
                uploadCropped(),
                uploadOriginal()
            ])
                .then(indicateSuccess)
                .catch(indicateFailure);
        },
        registerElements = function () {
            elements.$cropper = $('#image-cropper');
            elements.$uploadBtn = $('.upload-image-btn');
        },
        setup = function () {
            elements.$cropper.cropit(cropItSettings);
            elements.$uploadBtn.click(uploadPhoto);
        },
        init = function () {
            registerElements();
            setup();
        };

    $(init);
}());

