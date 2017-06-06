(function()
{
	// https://github.com/blueimp/JavaScript-Canvas-to-Blob
	// https://developer.mozilla.org/zh-CN/docs/Web/API/HTMLCanvasElement/toBlob
	if (!HTMLCanvasElement.prototype.toBlob) {
	 Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
	  value: function (callback, type, quality) {

	    var binStr = atob( this.toDataURL(type, quality).split(',')[1] ),
	        len = binStr.length,
	        arr = new Uint8Array(len);

	    for (var i=0; i<len; i++ ) {
	     arr[i] = binStr.charCodeAt(i);
	    }

	    callback( new Blob( [arr], {type: type || 'image/png'} ) );
	  }
	 });
	}



	function rotateImg()
	{
		this.image=new Image();
	    this.canvas= document.createElement('canvas');
		// this.canvas.style.display='none';
		document.body.appendChild(this.canvas);
	}

	rotateImg.prototype.rotate=function(p_deg)
	{
		var canvas=this.canvas;
		var image=this.image;
		var canvasContext = canvas.getContext('2d');
		switch(p_deg)
		{
			default :
			case 0 :
				canvas.setAttribute('width', image.width);
				canvas.setAttribute('height', image.height);
				canvasContext.rotate(p_deg * Math.PI / 180);
				canvasContext.drawImage(image, 0, 0);
				break;
			case 90 :
				canvas.setAttribute('width', image.height);
				canvas.setAttribute('height', image.width);
				canvasContext.rotate(p_deg * Math.PI / 180);
				canvasContext.drawImage(image, 0, -image.height);
				break;
			case 180 :
				canvas.setAttribute('width', image.width);
				canvas.setAttribute('height', image.height);
				canvasContext.rotate(p_deg * Math.PI / 180);
				canvasContext.drawImage(image, -image.width, -image.height);
				break;
			case 270 :
			case -90 :
				canvas.setAttribute('width', image.height);
				canvas.setAttribute('height', image.width);
				canvasContext.rotate(p_deg * Math.PI / 180);
				canvasContext.drawImage(image, -image.width, 0);
				break;
		}

	};

	rotateImg.prototype.readFileToCanvas=function(file, callback)
	{
		var reader = new FileReader();
		var image=this.image,$canvas=this.canvas;
		var canvasContext = $canvas.getContext('2d');
		reader.onload = function (fileReaderEvent) {
			image.onload = function () {$canvas.setAttribute('width', this.width); $canvas.setAttribute('height', this.height);  canvasContext.drawImage(this,0,0,this.naturalWidth,this.naturalHeight); callback($canvas,this); };
			image.src = fileReaderEvent.target.result;
		};
		reader.readAsDataURL(file);
	};

	rotateImg.prototype.getDataURL=function(type)
	{
		return this.canvas.toDataURL(type||'image/png');
	};

	rotateImg.prototype.getBlobData=function(callback,type,quality)
	{
		this.canvas.toBlob(callback,type,quality);
	};

	rotateImg.prototype.dataURLtoBlob=function(dataurl)
	{
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
	    while(n--){
	        u8arr[n] = bstr.charCodeAt(n);
	    }
	    return new Blob([u8arr], {type:mime});
    };

    rotateImg.prototype.downloadImg=function(fileName)
    {
    	this.getBlobData(function(blob)
    	{
    		if (window.navigator.msSaveOrOpenBlob)
	        {
	            navigator.msSaveBlob(blob, fileName);
	        }
	        else
	        {
	            var link = document.createElement('a');
	            link.style.display='none';
	            link.href = window.URL.createObjectURL(blob);
	            link.download = fileName;
	            document.body.appendChild(link);
	            link.click();
	            setTimeout(function()
	            {
	                document.body.removeChild(link);
	                window.URL.revokeObjectURL(link.href);
	            },200);
	        }
    	});
    };

    rotateImg.prototype.rotateFileAndGetBlob=function(file,p_deg,callback)
    {
    	var _this=this;
    	this.readFileToCanvas(file,function(canvas)
    	{
    		_this.rotate(p_deg);
    		_this.getBlobData(callback);
    	});
    };

    window.rotateImg=rotateImg;

})();
