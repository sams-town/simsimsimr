/***********************************************
 *                                             *
 * szOftalmologisCanvas.js                     *
 * -----------------------                     *
 * Created by Syukron Zahri                    *
 * Copyright (c) Syukron Zahri @ 2016          *
 * Copy is permitted without any modifications *
 *                                             *
 ***********************************************/

import jQuery from 'jquery'

(function($){
    var backgroundImage = '<svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" width="8000" height="4000" viewBox="0 0 8000 4000" id="svg2" version="1.1"> <defs id="defs4" /> <metadata id="metadata7"> <rdf:RDF> <cc:Work rdf:about=""> <dc:format>image/svg+xml</dc:format> <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage" /> <dc:title></dc:title> </cc:Work> </rdf:RDF> </metadata> <circle r="1.5" cy="4000" cx="-2" style="fill:#000000;stroke:none" id="path4161" /> <path id="path4167" d="m 1900,1394.1422 100,-100 100,100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4169" d="m 1900,2605.8579 100,100 100,-100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4171" d="m 1394.1421,1900 -100,100 100,100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4173" d="m 2605.8578,1900 100,100 -100,100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <circle r="1.5" cy="1300.7501" cx="2000" style="fill:#000000;stroke:none;stroke-width:20;stroke-linecap:round;stroke-miterlimit:4;stroke-dasharray:none" id="path4175" /> <path id="path4177" d="m 2000,1300.75 0,1400" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4179" d="m 1300,2000.75 1400,0" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4167-1" d="m 1490.001,1631.4223 0,-141.4213 141.4213,0" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4169-2" d="m 2368.5776,2509.999 141.4213,0 10e-5,-141.4213" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4171-9" d="m 1490.001,2368.5777 0,141.4213 141.4213,0" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4173-0" d="m 2368.5776,1490.001 141.4214,0 0,141.4213" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <circle transform="matrix(0.70710678,-0.70710678,0.70710678,0.70710678,0,0)" r="1.5" cy="2128.9575" cx="-0.53034675" style="fill:#000000;stroke:none;stroke-width:20;stroke-linecap:round;stroke-miterlimit:4;stroke-dasharray:none" id="path4175-2" /> <path id="path4177-2" d="m 1505.0253,1505.7753 989.9494,989.9495" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4179-8" d="m 1505.0253,2495.7248 989.9495,-989.9495" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4167-3" d="m 5900,1393.3922 100,-100 100,100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4169-8" d="m 5900,2605.1079 100,100 100,-100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4171-4" d="m 5394.1421,1899.25 -100,100 100,100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4173-7" d="m 6605.8578,1899.25 100,100 -100,100" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <circle r="1.5" cy="1300" cx="6000" style="fill:#000000;stroke:none;stroke-width:20;stroke-linecap:round;stroke-miterlimit:4;stroke-dasharray:none" id="path4175-7" /> <path id="path4177-9" d="m 6000,1300 0,1400" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4179-2" d="m 5300,2000 1400,0" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4167-1-3" d="m 5490.001,1630.6723 0,-141.4213 141.4213,0" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4169-2-9" d="m 6368.5776,2509.249 141.4213,0 1e-4,-141.4213" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4171-9-0" d="m 5490.001,2367.8277 0,141.4213 141.4213,0" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4173-0-5" d="m 6368.5776,1489.251 141.4214,0 0,141.4213" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <circle transform="matrix(0.70710678,-0.70710678,0.70710678,0.70710678,0,0)" r="1.5" cy="4956.854" cx="2828.4272" style="fill:#000000;stroke:none;stroke-width:20;stroke-linecap:round;stroke-miterlimit:4;stroke-dasharray:none" id="path4175-2-5" /> <path id="path4177-2-7" d="m 5505.0253,1505.0253 989.9494,989.9495" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /> <path id="path4179-8-5" d="m 5505.0253,2494.9748 989.9495,-989.9495" style="fill:none;fill-rule:evenodd;stroke:#000000;stroke-width:20;stroke-linecap:round;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" /></svg>';
        
    function szCursor()
    {
        var brushImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAUzSURBVHja7FdbbBRVGP7nftluodtdihSBUikpFSilBYJEDRBjjBo0JuADJGrAECU+youGBN+Mb8QgQRFIgGgAMQKBGsolhSIUwabRGmjpbtttt7uzM7s7l53b8Z8tECQxQSjyILOZbHfOzJnv/77/+84pRQiBx3nQ8JiPJwDY238Uhg6+zU9cfJqSpiQ9ygPGc4AjFLisCz54dx6g8JNJO+B6FLAMi98ucAwDwxkVaIYDSeSgslwE0/GhjCfQP2JAuEwG3TAho6hQX/MUxHDcJ/cwYPTu+FC5uKbbHvx+HWMrNEUjNor+DyVww65nJirUrs27tSub2p30hZXBMFW6xceTlE7qUQHwVRdAnQCUF4KienlJtvPdVrX74wO+58gulKMIMp4SZLIuOA4CoahxZiCYz6aAZEWg8hMQkQB6fN9q/caOz/igC2wf8gjS0L0xMsZdAorBc4xmYjJAVATChMEeOvKanu7lTN0H13GBoh6RBKwYMQi51e0BEAf1NwkYQmNtUo8dKuiFtZ7nVdI0+oCmxh+AWL30FHHtu4Y88EgFFCauo+yi9Wo8mdrTl0j+mVa07ZZVbA4glMAgJWQ8AITq3twmT1/xM3F0IK6JrshBrnwVmF4V5LQ0eK4Hvk8ial5/f2AkczE+lOrMZLWNvu+HGQRC0w9mWerOYuSqQIo50Rj97Q0r0d4oVj37hxJ6QUoks59nlKwcvIDjBeAFAYJGIL4PwaOxygmDAs9vH1W0fbFopJdhaIiEhfsOojtJCKg/XrOEGa/sh7La/UK0HqKWAbYDXZqW+8F23YhvmeCiTIIgAcOyIEsCSAJfffqXrq1nLnV9srRpzlcvLWvZjIlogFO8L7fcxRuqiiURgvT7Nj7rgodxXB4uO1dXO2NFmSx3+zjuez6YRgGKCEaWeMgZFnR2X8d7fb6t49qmr7871tHVc/N5SRTQRcyDLEZ/73CcGEIh6ercObNaqqKRncFvZB8kngXa96D19PkStbzAg4jy9A4Mz91+4GjbweNnd4yk81UCN0YyeZjVEBstaDJzxtNT1k+dMuk9SeL1cFiG/sEknGw9A9nkABQUpfQaAYEERj1x7sr6L3YdudqbSK4KQND/ECD33bpBswbVR6ORb2bVTGsReeHc4Z9OgWFaKIsDyvAgpOJ9YBXyEDSiLIugFQqT9x5pPXy0rWMbTiCweP1eKv6dd6gxSXie/z0Wi724aOG8TysmlhtW0Q5CAWzLgtGBOKQH49iDFvAcX7Jna3vnB3sOnTifK+jzBYF9+A1JIAkW4r/+8vKtG9a9tXjh/IazHsZ0ES0TWNTI5yDV3wdaOlXqqKAhbw6lmg6dbG+7ER9ZCrhvuK0Is2XLlluzmuhE7H4+BJ6hABOaBJ7joA1JsDogrQxWj+mIL2ewqmAzoptFsIpWatmS5m/rZ9deQH6nDgyO1DilNYOGIrrF0gu4UWFBCslg2a50qfv6mqlVlZerJ0+6QW4t9g98BEUECRlU3jB75smPNqxdvvGd1SuaFzS0shxDLLxuGjpKkoBUoh9XVAtzxA19eeD4wZ7EaIvPcg/BAAZRUKmiqiWKaZopQeIFrq+psWFv3czpP0YjFa6q5Z9B7SXLMMDMa5igmC+E5kcyahPKqI4LAFEUgeM4lIUBLZcvUcNx7PBzixYcW9wyf2f15FgPukhwPS+iZVUpryoYxSSEN/azj2qv56I0aA9MS1FZMK9+V8206t3ZXKE2NZppzma16Y1z66411NX+Sj35z+gJgP89gL8EGAADXdSYGMzEBQAAAABJRU5ErkJggg==';
        var eraserImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAASESURBVHja7FdLb9xUFL4PPyeeh5ImGVIlSpRACgIUqaAuQAh27GCDxIIFQvyDlkpFQqISKo9K8AMQC1iismSLQEJQhHgsaAuqUElLIJMMmYfHHtvX917OdSaT8diehFUX1KNP47HvPf7ud75zrgdLKdHdPAi6y8c9AtrBCQsj0v3x5rOSkvs020Ro1BsEYxlz1G+0Ocb7l5R3zKkSc5bmfpKmdk3aBiKGnp434TBtK03Av73zfPO7658lphyNISTSKiVk16eBCEndClpdFHZcbtWnPyidWjyP0jP/mwK82U3Oia6lBkggQEwdYU2Dc5GeDXJIgah3Z/cc1jXsrC6ck1wcj4c95oHyo6ufm/PTvympU88gGHEvQCJi8DycDQSXiEaRv9k4G++5T1FMEAEORyFjQmbqXm1j7c088opU1HaThxUdSinv9s4lSijSiHYkMgRM10eVhdlPS0tz3wjGx2oFVOj1kQiiRJG8A1OCgmbnCW+n9QIrmSiy9InIlmGSOixObKyfJToV425WKwz3upPTC9x6t7be5ZI73KCIG6QQGQLYAVdAKVnL9avVh5Y/zlXBDxHr+cUqQJWwrr8S3tx6jcZgXohRhOGcg72gL9j+6kBKvNtb+fPKl9dFHFto1HgwVtW6fXJ2ghnUGK0zc/rUOlRUo6gvaLWpMQW4RBhkxsDOsM1btYdX3hdjFaHIiJChuOslq82XAZQKWNX7Y/sSgTGwnFxkUyAPwSwNVU4/8I49Xd1UFQAyHQLmspaLJGP7YZLGlYZKkf/X7iths/2cal4Cro0ja8JYHCKKEdI1t3LmwQscapxDcxoFAxYBVI2Ee0UQlKLW5vZlQZhNHFg1KD6KjAd8JjKOBk/gH278/e0/vfAMzVF8pmIjQyOF7V9AShfrzqtzNfMjPhbepHqagBuKjJkoPHXPC5/8/vfdr8Z3TjXN0imacYzBhRw/wsegdGt1ofIIxbg1OqQMZZpKQQC5TgEoe5CKKUv/evmE856A36NeUNtSAPe9IB4EkRkoEUMWn9xp9y8Ulu6BAr1YFvQWcLUQ5au/bl/rhWyRjO0H6vdsxUIUF/coiBGtLVQ3QLEbYjDIGTSjoQIciOQhVtsdxe7SXPltKbLBOUTshXH+RjUcI4xG239DeULAjipGdtUhAU3wQiDO0eKM/eFM2fyZCzle9kkaIpWiAg4E5O/40YuQ0qc11ehITh8wSVwMzJBF4nitXjmbt1KVRrfP0KTtUlmn0fIvcy40JHL6QCvUJmKvT8GQxhf1qnUlT4V+BMZVfb5ABpIoxR5re9HLhNKcFEDzOAoEan65XrsI5Rnn1b4bsKT0CjdLINF0g9cZl9UMAZvwI2EijmanyC/rdeetGFQYJaGCR9DM3KBYBZW+gMUrux3vpcw7oYnYsV4iMcfo/vnSRS8SpTtN7/ywrw++2l6YeGLK1AbNVI57Qb1bVDJ9QET94xFQr28g3F4fyi+IH290/GcowWU8YsCkA0K6SgaVBJMhO2UdXSPb8zXnE8fAvRSBe3/N/rcE/hVgAK3uraspeqZJAAAAAElFTkSuQmCC';
        
        var alphabetImage = function(){
            var textCanvas = document.createElement('canvas');
            var textContext = textCanvas.getContext('2d');
            
            textCanvas.width = 24;
            textCanvas.height = 24;
            
            textContext.font = '32px Serif';
            textContext.strokeStyle = 'black';
            textContext.fillStyle = 'black';
            textContext.fillText('A', 0, 24);
            
            return textCanvas.toDataURL();
        };
        var rotateObject = function(coordinates, origin, degree){
            var xNew, yNew;
            var newCoordinates = [];
            
            for (idx in coordinates) {
                xNew = ((Math.cos(degree / 180 * Math.PI) * (coordinates[idx].x - origin.x)) - (Math.sin(degree / 180 * Math.PI) * (coordinates[idx].y - origin.y))) + origin.x;
                yNew = ((Math.sin(degree / 180 * Math.PI) * (coordinates[idx].x - origin.x)) + (Math.cos(degree / 180 * Math.PI) * (coordinates[idx].y - origin.y))) + origin.y;
                
                newCoordinates.push({x: xNew, y: yNew});
            }
            
            return newCoordinates;
        }
        var getArrow = function(arrowMode, origin, size, direction){
            var coordinate;
            
            switch (arrowMode) {
                case 'full':
                    coordinate = [
                        {x: origin.x, y: origin.y + (size / 2)},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x - (size / 2), y: origin.y},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x + (size / 2), y: origin.y},
                    ];
                    break;
                case 'partial':
                    coordinate = [
                        {x: origin.x, y: origin.y + (size / 2)},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x - (size / 2), y: origin.y},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x + (size / 2), y: origin.y},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x - (size / 2), y: origin.y - (size / 2)},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x + (size / 2), y: origin.y - (size / 2)},
                    ];
                    break;
                case 'none':
                    coordinate = [
                        {x: origin.x, y: origin.y + (size / 2)},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x - (size / 2), y: origin.y - (size / 2)},
                        {x: origin.x, y: origin.y - (size / 2)},
                        {x: origin.x + (size / 2), y: origin.y - (size / 2)},
                    ];
                    break;
            }
            
            var degree = 0;
            
            switch (direction) {
                case 'N':
                    degree = 0;
                    break;
                case 'NE':
                    degree = 45;
                    break;
                case 'E':
                    degree = 90;
                    break;
                case 'SE':
                    degree = 135;
                    break;
                case 'S':
                    degree = 180;
                    break;
                case 'SW':
                    degree = 225;
                    break;
                case 'W':
                    degree = 270;
                    break;
                case 'NW':
                    degree = 315;
                    break;
            }
            
            coordinate = rotateObject(coordinate, origin, degree);
            return coordinate;
        }
        
        this.image;
        this.size;
        this.type;
        
        this.getCursorImage = function(type, size, callback){
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            
            var img = new Image();
            
            canvas.width = Number.parseInt(size) + 32;
            canvas.height = Number.parseInt(size) + 32;
            
            var circle = function(){
                context.lineWidth = 2;
                context.strokeStyle = 'black';
                context.beginPath();
                context.arc((size / 2) + 1, (size / 2) + 1, size / 2, 0, 2 * Math.PI, false);
                context.stroke();            
            }
            
            var text = function(){
                context.lineWidth = 1;
                context.strokeStyle = 'black';

                context.beginPath();
                context.moveTo((size / 4), 0);
                context.lineTo(size - (size / 4), 0);
                context.stroke();

                context.beginPath();
                context.moveTo((size / 2), 0);
                context.lineTo((size / 2), size);
                context.stroke();

                context.beginPath();
                context.moveTo((size / 4), size);
                context.lineTo(size - (size / 4), size);
                context.stroke();
            }
            
            img.onload = function(){
                context.drawImage(img, size, size);
                callback(canvas.toDataURL());
            }
            
            switch (type) {
                case 'brush':
                    circle();
                    img.src = brushImage;
                    break;
                case 'eraser':
                    circle();
                    img.src = eraserImage;
                    break;
                case 'text':
                    text();
                    img.src = alphabetImage();
                    break;
            }
        }

        this.getArrowCursorImage = function(arrowMode, size, canvasRatio, direction, callback){
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            
            var img = new Image();
            
            var boxSize = Number.parseInt(size) + (size / 1.41421356237);
            var origin = {x: (boxSize / 2), y: (boxSize / 2)};
            
            canvas.width = boxSize;
            canvas.height = boxSize;

            var coordinate = getArrow(arrowMode, origin, size, direction);
            
            context.lineWidth = 20 * canvasRatio;
            context.strokeStyle = 'black';
            context.lineJoin = 'round';
            context.lineCap = 'round';
            context.fillStyle = 'black';
            
            for (var i = 0; i < coordinate.length; i++) {
                if (i < (coordinate.length - 1)) {
                    context.beginPath();
                    context.moveTo(coordinate[i].x, coordinate[i].y);
                    context.lineTo(coordinate[i + 1].x, coordinate[i + 1].y);
                    context.stroke();
                }
            }
            
            callback(canvas.toDataURL());
        }
    }
    
    /*----------------------------------*/
    /*------- Internal functions -------*/
    /*----------------------------------*/

    
    /* Clear canvas */
    function clearCanvas(szCanvasObj)
    {
        var self = szCanvasObj;
        var context = self.canvas.getContext('2d');
        
        var cHeight = self.clientHeight;
        var cWidth = self.clientWidth;
        
        context.clearRect(0, 0, cWidth, cHeight);
    }
    
    /* Draw background */    
    function exportImageToBase64(canvas)
    {
        return canvas.toDataURL();
    }
    
    function getCanvas(szCanvasObj)
    {
        return szCanvasObj.canvas;
    }

    var resizeEverything = function(szCanvasObj){
        var self = szCanvasObj;
        
        self.rTime = new Date();
        
        if (self.timeout === false) {
            self.timeout = true;
            setTimeout(function(){resizeCheck(self)}, self.delta);
        }
        resizeLayout(self);
    }
    
    /* Set whether the canvas is responsive to resize event */
    function setResponsive(szCanvasObj, state)
    {
        var self = szCanvasObj;
        
        if (state === true) {
            window.addEventListener('resize', function(){resizeEverything(self)}, false);
        } else {
            window.removeEventListener('resize', function(){resizeEverything(self)}, false);
        }
    }
    
    function rotateObject(coordinates, origin, degree)
    {
        var xNew, yNew;
        var newCoordinates = [];
        
        for (idx in coordinates) {
            xNew = ((Math.cos(degree / 180 * Math.PI) * (coordinates[idx].x - origin.x)) - (Math.sin(degree / 180 * Math.PI) * (coordinates[idx].y - origin.y))) + origin.x;
            yNew = ((Math.sin(degree / 180 * Math.PI) * (coordinates[idx].x - origin.x)) + (Math.cos(degree / 180 * Math.PI) * (coordinates[idx].y - origin.y))) + origin.y;
            
            newCoordinates.push({x: xNew, y: yNew});
        }
        
        return newCoordinates;
    }
    
    function getArrow(type, origin, size, direction)
    {
        var coordinate;
        
        switch (type) {
            case 'full':
                coordinate = [
                    {x: origin.x, y: origin.y + (size / 2)},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x - (size / 2), y: origin.y},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x + (size / 2), y: origin.y},
                ];
                break;
            case 'partial':
                coordinate = [
                    {x: origin.x, y: origin.y + (size / 2)},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x - (size / 2), y: origin.y},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x + (size / 2), y: origin.y},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x - (size / 2), y: origin.y - (size / 2)},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x + (size / 2), y: origin.y - (size / 2)},
                ];
                break;
            case 'none':
                coordinate = [
                    {x: origin.x, y: origin.y + (size / 2)},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x - (size / 2), y: origin.y - (size / 2)},
                    {x: origin.x, y: origin.y - (size / 2)},
                    {x: origin.x + (size / 2), y: origin.y - (size / 2)},
                ];
                break;
        }
        
        var degree = 0;
        
        switch (direction) {
            case 'N':
                degree = 0;
                break;
            case 'NE':
                degree = 45;
                break;
            case 'E':
                degree = 90;
                break;
            case 'SE':
                degree = 135;
                break;
            case 'S':
                degree = 180;
                break;
            case 'SW':
                degree = 225;
                break;
            case 'W':
                degree = 270;
                break;
            case 'NW':
                degree = 315;
                break;
        }
        
        coordinate = rotateObject(coordinate, origin, degree);
        return coordinate;
    }
    
    function paint(szCanvasObj)
    {
        var self = szCanvasObj;
        
        var context = self.canvas.getContext('2d');
        
        context.lineWidth = self.pointSize;
        context.strokeStyle = self.pointColor;
        context.lineJoin = 'round';
        context.lineCap = 'round';
        context.fillStyle = 'blue';
                
        switch (self.mode){
            case 'brush':
                context.globalCompositeOperation = 'source-over';
                break;
            case 'eraser':
                context.globalCompositeOperation = 'destination-out';
                break;
        }
        
        if (self.canDraw === true) {
            if (self.points.length < 2) {
                context.beginPath();
                context.lineWidth = self.pointSize / 2;
                context.arc(self.points[0].x + 1, self.points[0].y + 1, context.lineWidth / 2, 0, 2 * Math.PI, false);
                context.stroke();
            } else if (self.points.length < 3) {
                context.beginPath();
                context.moveTo(self.points[0].x, self.points[0].y);
                context.lineTo(self.points[1].x, self.points[1].y);
                context.stroke();
            } else if (self.points.length >= 3) {
                context.beginPath();
                context.moveTo(self.points[0].x, self.points[0].y);
                
                var i;
                
                for (i = 1; i < self.points.length - 2; i++) {
                    var c = (self.points[i].x + self.points[i + 1].x) / 2;
                    var d = (self.points[i].y + self.points[i + 1].y) / 2;
                    
                    context.quadraticCurveTo(self.points[i].x, self.points[i].y, c, d);
                }
                
                // For the last 2 points
                context.quadraticCurveTo(self.points[i].x, self.points[i].y, self.points[i + 1].x, self.points[i + 1].y);
                
                context.stroke();
            }
            
            if (self.points.length > 9) {
                self.points.splice(0, 1);
            }
        }
    };
    
    function insertText(szCanvasObj, coordinate)
    {
        var self = szCanvasObj;
        var context = self.canvas.getContext('2d');
        
        if (self.canDraw === true) {
            var wrapper = document.createElement('div');
            var textInput = wrapper.appendChild(document.createElement('input'));
            textInput.type = 'text';

            wrapper.style.position = 'absolute';
            wrapper.style.padding = '0px';
            wrapper.style.margin = '0px';
            wrapper.style.zIndex = 4;
            wrapper.style.display = 'inline-block';
            wrapper.style.whiteSpace = 'no-wrap';
            wrapper.style.top = coordinate.y + 'px';
            wrapper.style.left = coordinate.x + 'px';
            
            textInput.style.width = '100%';
            textInput.style.border = '1px dashed black';
            textInput.style.color = self.pointColor;
            textInput.style.backgroundColor = "rgba(255, 255, 255, 0)";
            textInput.size = 1;
            textInput.style.font = self.pointSize + 'px Arial';
            
            self.appendChild(wrapper);
            
            textInput.onkeypress = textInput.onkeydown = function(e){
                if (e.keyCode == 13) {
                    e.preventDefault();
                    mergeDownText(self, this, coordinate);
                } else if (e.keyCode == 27) {
                    this.value = '';
                    mergeDownText(self, this, coordinate);
                }
                
                this.size = (this.value.length > 0) ? this.value.length : 2;
            };
            
            textInput.onblur = function(e){
                mergeDownText(self, this, coordinate);
            }
            
            return textInput;
        }
    }

    function insertArrow(szCanvasObj, coordinate)
    {
        var self = szCanvasObj;
        var context = self.canvas.getContext('2d');
        
        if (self.canDraw === true) {
            var coordinate = getArrow(self.arrowMode, coordinate, self.canvas.width * 0.02752525, self.arrowDirection);
            
            //context.lineWidth = 20 * self.canvas.width / self.svg.clientWidth;
            context.globalCompositeOperation = 'source-over';
            context.lineWidth = 20 * self.canvas.width / 8000;
            context.strokeStyle = self.pointColor;
            context.lineJoin = 'round';
            context.lineCap = 'round';
            context.fillStyle = self.pointColor;
            
            for (var i = 0; i < coordinate.length; i++) {
                if (i < (coordinate.length - 1)) {
                    context.beginPath();
                    context.moveTo(coordinate[i].x, coordinate[i].y);
                    context.lineTo(coordinate[i + 1].x, coordinate[i + 1].y);
                    context.stroke();
                }
            }
        }
    }
    
    function mergeDownText(szCanvasObj, inputText, coordinate)
    {
        var self = szCanvasObj;
        var context = self.canvas.getContext('2d');
        var wrapper = inputText.parentNode;
        
        context.globalCompositeOperation = 'source-over';
        context.font = inputText.style.font;
        context.strokeStyle = self.pointColor;
        context.fillStyle = self.pointColor;
        
        context.fillText(inputText.value, coordinate.x + 1, coordinate.y + Number.parseInt(self.pointSize) - 3);
        inputText.parentNode.parentNode.removeChild(wrapper);
    }
    
    function storeImage(szCanvasObj)
    {
        var self = szCanvasObj;
        
        self.previousImage = exportImageToBase64(self.canvas);
        self.previousDimension.x = self.clientWidth;
        self.previousDimension.y = self.clientHeight;
    }

    function resizeCheck(szCanvasObj)
    {
        var self = szCanvasObj;
        
        if (new Date() - self.rTime < self.delta) {
            setTimeout(function(){resizeCheck(self)}, self.delta);
        } else {
            self.timeout = false;
            resample(self);
        }
    }
    
    function resample(szCanvasObj)
    {
        var self = szCanvasObj;
        var context = self.canvas.getContext('2d');
        
        var img = new Image();
        var imaginaryCanvas = document.createElement('canvas');
        var imaginaryContext = imaginaryCanvas.getContext('2d');
        var imaginaryImage = new Image();
        
        var newHeight = self.clientHeight;
        var newWidth = self.clientWidth;
        
        imaginaryCanvas.setAttribute('height', self.previousDimension.y);
        imaginaryCanvas.setAttribute('width', self.previousDimension.x);
        
        imaginaryImage.crossOrigin = 'Anonymous';
        imaginaryImage.onload = function(){
            imaginaryContext.drawImage(imaginaryImage, 0, 0);
            
            var hermite = new Hermite_class();
            hermite.resample_single(imaginaryCanvas, newWidth, newHeight, true);
            
            var newImage = new Image();
            newImage.onload = function(){
                context.drawImage(newImage, 0, 0);
            };
            newImage.src = imaginaryCanvas.toDataURL();
        }
        
        imaginaryImage.src = self.previousImage;
    }
    
    function resizeLayout(szCanvasObj)
    {
        var self = szCanvasObj;
        
        var nHeight = self.clientWidth / 2;
        var nWidth = self.clientWidth;
        var cHeight = nHeight + self.style.paddingTop;
        
        self.style.height = nHeight + 'px';
        self.container.style.height = cHeight;
        
        self.svg.setAttribute('height', nHeight);
        self.svg.setAttribute('width', nWidth);
        
        self.canvas.setAttribute('height', nHeight);
        self.canvas.setAttribute('width', nWidth);
        
        setCursor(self);
    }
    
    function setPointSize(szCanvasObj, size)
    {
        var self = szCanvasObj;
        self.pointSize = size;
        setCursor(self);
    }
    
    function getSelf(szCanvasObj)
    {
        return szCanvasObj;
    }
    
    function setMode(szCanvasObj, mode)
    {
        var self = szCanvasObj;
        self.mode = mode;
        setCursor(self);
    }

    function setArrowMode(szCanvasObj, arrowMode)
    {
        var self = szCanvasObj;
        self.arrowMode = arrowMode;
        setCursor(self);
    }

    function getArrowMode(szCanvasObj)
    {
        var self = szCanvasObj;
        return self.arrowMode;
    }

    function setArrowDirection(szCanvasObj, arrowDirection)
    {
        var self = szCanvasObj;
        self.arrowDirection = arrowDirection;
        setCursor(self);
    }

    function setColor(szCanvasObj, color)
    {
        var self = szCanvasObj;
        self.pointColor = color;
    }
    
    function setCursor(szCanvasObj)
    {
        var self = szCanvasObj;
        
        var cur = new szCursor();
        if (self.mode == 'arrow') {
            var aSize = self.canvas.width * 0.02752525;
            cur.getArrowCursorImage(self.arrowMode, aSize, self.canvas.width / 8000, self.arrowDirection, function(base64Image){
                self.canvas.style.cursor = 'url(' + base64Image + ') ' + ((aSize + (aSize / 1.41421356237)) / 2).toString() + ' ' + ((aSize + (aSize / 1.41421356237)) / 2).toString() + ', auto';
            });
        } else if (self.mode == 'disabled') {
            self.canvas.style.cursor = 'default';
        } else {
            cur.getCursorImage(self.mode, self.pointSize, function(base64Image){
                if (self.mode == 'text') {
                    self.canvas.style.cursor = 'url(' + base64Image + ') ' + (self.pointSize / 4).toString() + ' 0, auto';
                } else {
                    self.canvas.style.cursor = 'url(' + base64Image + ') ' + (self.pointSize / 2).toString() + ' ' + (self.pointSize / 2).toString() + ', auto';
                }
            });            
        }
    }
    
    function exportData(szCanvasObj)
    {
        var self = szCanvasObj;
        return {
            height: self.clientHeight,
            width: self.clientWidth,
            image: exportImageToBase64(self.canvas),
        }
    }
    
    function importData(szCanvasObj, data)
    {
        var self = szCanvasObj;
        
        self.previousImage = data.image;
        self.previousDimension.x = data.width
        self.previousDimension.y = data.height;
        
        resample(self);
    }
    
    /*-----------------------------------------*/
    /*------- End of Internal functions -------*/
    /*-----------------------------------------*/
    
    /* Public method */
    var methods = {
        init: function(szCanvasObj, options){
            var self = szCanvasObj;
            
            self.innerHTML = backgroundImage;
            self.svg = self.firstChild;
            self.canvas = self.appendChild(document.createElement('canvas'));
            self.container = self.parentElement;
            
            self.points = [];
            self.mouse = {x: 0, y: 0};

            self.childNodes.top = 0;
            self.childNodes.left = 0;
            
            self.svg.style.zIndex = 1;
            self.canvas.style.zIndex = 2;
            
            self.style.position = "relative";
            self.svg.style.position = "absolute";
            self.canvas.style.position = "absolute";
            // self.canvas.style.border = "1px solid black";
            
            self.previousImage = undefined;
            self.previousDimension = {x: 0, y: 0};
            
            self.rTime = undefined;
            self.timeout = false;
            self.delta = 200;

            self.canDraw = true;
            self.isDrawing = false;

            self.pointSize = 2;
            self.mode = 'arrow';
            self.arrowMode = 'full';
            self.arrowDirection = 'N';
            self.pointColor = 'black';
            
            resizeLayout(self);

            if (typeof options === 'undefined') {
                return;
            }
            
            if (typeof options.canDraw === 'boolean') {
                self.canDraw = options.canDraw;
            }

            if (typeof options.mode !== 'undefined') {
                self.mode = options.mode;
            }
            
            setCursor(self);
            
            if (options.responsive === true) {
                setResponsive(self, true);
            } else {
                setResponsive(self, false);
            }
            
            self.canvas.addEventListener('mousemove', function(e){
                if (typeof e.offsetX === 'undefined' && typeof e.offsetY === 'undefined') {
                    self.isDrawing = false;
                    self.points = [];
                    return;
                }
                
                self.mouse.x = typeof e.offsetX !== 'undefined' ? e.offsetX : e.layerX;
                self.mouse.y = typeof e.offsetY !== 'undefined' ? e.offsetY : e.layerY;
                
                if (self.isDrawing) {
                    self.points.push({x: self.mouse.x, y: self.mouse.y});
                    paint(self);
                }
            }, false);
            
            self.canvas.addEventListener('mousedown', function(e){
                self.mouse.x = typeof e.offsetX !== 'undefined' ? e.offsetX : e.layerX;
                self.mouse.y = typeof e.offsetY !== 'undefined' ? e.offsetY : e.layerY;
                
                if (self.mode == 'arrow') {
                    insertArrow(self, {x: self.mouse.x, y: self.mouse.y});
                }
                
                if (self.mode == 'brush' || self.mode == 'eraser') {
                    self.isDrawing = true;
                    if (self.isDrawing) {
                        self.points.push({x: self.mouse.x, y: self.mouse.y});
                        paint(self);
                    }                    
                }
            }, false);
            
            self.canvas.addEventListener('mouseup', function(){
                self.isDrawing = false;
                self.points = [];
                
                if (self.mode == 'text') {
                    var inputText = insertText(self, {x: self.mouse.x, y: self.mouse.y});
                    inputText.focus();
                }
                
                storeImage(self);
            }, false);

            self.canvas.addEventListener('mouseleave', function(){
                self.isDrawing = false;
                self.points = [];
                
                storeImage(self);
            }, false);
            
            if (typeof options.data !== 'undefined') {
                importData(self, options.data);
            }
        },
        canDraw: function(szCanvasObj, state){
            var self = szCanvasObj;
            if (typeof options.state === 'boolean') {
                self.canDraw = state;
            }
        },
        clearCanvas: function(szCanvasObj){
            return clearCanvas(szCanvasObj);
        },
        exportImageToBase64: function(szCanvasObj){
            var self = szCanvasObj;
            return exportImageToBase64(self.canvas);
        },
        setResponsive: function(szCanvasObj, state){
            return setResponsive(szCanvasObj, state);
        },
        getCanvas: function(szCanvasObj){
            return getCanvas(szCanvasObj);
        },
        getSelf: function(szCanvasObj){
            return getSelf(szCanvasObj);
        },
        setMode: function(szCanvasObj, mode){
            return setMode(szCanvasObj, mode);
        },
        setArrowMode: function(szCanvasObj, arrowMode){
            return setArrowMode(szCanvasObj, arrowMode);
        },
        setArrowDirection: function(szCanvasObj, arrowDirection){
            return setArrowDirection(szCanvasObj, arrowDirection);
        },
        getArrowMode: function(szCanvasObj){
            return setArrowDirection(szCanvasObj);
        },
        setColor: function(szCanvasObj, color){
            return setColor(szCanvasObj, color);
        },
        setPointSize: function(szCanvasObj, size){
            return setPointSize(szCanvasObj, size);
        },
        exportData: function(szCanvasObj){
            return exportData(szCanvasObj);
        },
        importData: function(szCanvasObj, data){
            return importData(szCanvasObj, data);
        },
    };
    
    $.fn.szKajOftalmologisCanvas = function(params){
        var self = this.get(0);
        
        if (methods[params]) {
            return methods[params].call(this, self, Array.prototype.slice.call(arguments, 1)[0]);
        } else if (typeof params === 'object'){
            methods.init(self, params);
        } else if (typeof params === 'undefined') {
            methods.init(self);
        } else {
            $.error('Method ' +  params + ' does not exist on jQuery.szKajOftalmologisCanvas');
        }
                
        return this;
    }
})(jQuery);