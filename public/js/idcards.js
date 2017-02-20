/*global document, window */

(function () {
    'use strict';
/*
210 x 297 = 2480 x 3508
190 x 277 = 2244 x 3272
*/

    var images,
        currentCanvas,
        CardCanvas = function () {
            this.canvas = document.createElement('canvas');
            this.canvas.setAttribute('width', this.CANVAS_WIDTH);
            this.canvas.setAttribute('height', this.CANVAS_HEIGHT);
            getContainer().appendChild(this.canvas);

            this.context = this.canvas.getContext('2d');

            this.xOffset = 0;
            this.yOffset = 0;
            this.nextLine = 0;
        },
        getContainer = function () {
            var container = document.querySelector('.idCardContainer');

            getContainer = function () {
                return container;
            };

            return container;
        },
        init = function () {
            Array.prototype.forEach.call(document.getElementsByTagName('img'), function (image) {
                if (!currentCanvas) {
                    currentCanvas = new CardCanvas();
                }

                if (!currentCanvas.addImageToCanvas(image)) {
                    currentCanvas = new CardCanvas();
                    currentCanvas.addImageToCanvas(image);
                }
            });
        };

    CardCanvas.prototype.CANVAS_WIDTH = 2480;
    CardCanvas.prototype.CANVAS_HEIGHT = 3508;

    CardCanvas.prototype.getContext = function () {
        return this.context;
    };

    CardCanvas.prototype.addImageToCanvas = function (image) {
        var requiredWidth  = image.width + 100,
            requiredHeight = image.height + 100;

        // determine space needed for image
        if (this.CANVAS_WIDTH - this.xOffset < requiredWidth) {
            this.yOffset += requiredHeight + 50;
            this.xOffset  = 0;

        }

        if (this.CANVAS_HEIGHT - this.yOffset < requiredHeight) {
            return false;
        }

        this.context.beginPath();
        this.context.moveTo(this.xOffset, this.yOffset + 49);
        this.context.lineTo(this.xOffset + image.width + 100, this.yOffset + 49);

        this.context.moveTo(this.xOffset + 49, this.yOffset);
        this.context.lineTo(this.xOffset + 49, this.yOffset + 100 + image.height);

        this.context.moveTo(this.xOffset, this.yOffset + 51 + image.height);
        this.context.lineTo(this.xOffset + image.width + 100, this.yOffset + 51 + image.height);

        this.context.moveTo(this.xOffset + 51 + image.width, this.yOffset);
        this.context.lineTo(this.xOffset + 51 + image.width, this.yOffset + 100 + image.height);

        this.context.closePath();
        this.context.stroke();

        this.context.drawImage(image, this.xOffset + 50, this.yOffset + 50);

        this.xOffset += requiredWidth + 50;

        return true;
    };

    init();
}());
