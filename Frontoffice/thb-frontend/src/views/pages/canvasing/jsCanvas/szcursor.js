/***********************************************
 *                                             *
 * szCursor.js                                 *
 * -----------                                 *
 * Created by Syukron Zahri                    *
 * Copyright (c) Syukron Zahri @ 2016          *
 * Copy is permitted without any modifications *
 *                                             *
 ***********************************************/
// import jQuery from 'jquery'

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