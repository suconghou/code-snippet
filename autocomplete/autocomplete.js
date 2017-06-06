(function($,win)
{
	function constructSelectDown(arr,e)
	{
		var html=[];
		html.push('<ul class="autocomplete-selectdown">');
		arr.forEach(function(item)
		{
			html.push('<li class="a-item">',item,'</li>');
		});
		html.push('</ul>');
		html=html.join('');
		return html;
	}

	function getPosition(obj)
	{
		var result = 0;
		if(typeof obj.selectionStart!=='undefined')
		{ //IE以外
			result = obj.selectionStart;
		}
		else
		{
			//IE
			var rng;
			if(obj.tagName == "textarea")
			{ //TEXTAREA
				rng = event.srcElement.createTextRange();
				rng.moveToPoint(event.x,event.y);
			}
			else
			{ //Text
				rng = document.selection.createRange();
			}
			rng.moveStart("character",-event.srcElement.value.length);
			result = rng.text.length;
		}
		return result;
	}

	function autocomplete(options)
	{
		var cfg=options;
		var t=(((1+Math.random())*0x10000000)|0).toString(16);
		var id='autocomplete-'+t;
		var $this=$(this);
		var $body=$('body');

		$body.append('<div class="autocomplete" id="'+id+'"></div>');
		var $auto=$('#'+id);

		var word;
		function keyupEvent(e)
		{
			if((e.keyCode>=65 && e.keyCode<=90)||e.keyCode==8)
			{
				word=getCurrentWord($(this),e);
				if(word.length<=0)
				{
					return hideSelectDown();
				}
				var arr=getMatched(word);
				if(arr.length)
				{
					var html=constructSelectDown(arr);
					showSelectDown(html,e);
				}
				else
				{
					hideSelectDown();
				}
			}
			else
			{
				// console.log(e.keyCode);
			}
		}

		function keydownEvent(e)
		{
			var current;
			if([40,38].indexOf(e.keyCode)>=0)
			{
				var $lis=$auto.find('ul li');
				var len=$lis.length;
				var index=$auto.find('.hover').index();
				index++;
				current=index%len;
				if(e.keyCode==38)
				{
					current=current-2;
					if(current<0)
					{
						current=current+len;
					}
				}
				$lis.eq(current).addClass('hover').siblings().removeClass('hover');
				e.preventDefault();
			}
			else if(e.keyCode==13)
			{
				current=$auto.find('.hover');
				if(current.length)
				{
					current.trigger('click');
				}
				e.preventDefault();
			}
		}

		function getCurrentWord()
		{
			var pos=getPosition($this[0]);
			var val=$this.val();
			var leftText=val.substr(0,pos);
			var offset,reg=/[a-z]/i;
			for(var i=1;i<=leftText.length;i++)
			{
				if(reg.test(leftText.substr(-i,1)))
				{
					offset=i;
				}
				else
				{
					break;
				}
			}
			if(offset)
			{
				return leftText.substr(-offset);
			}
			return '';
		}

		function getMatched(val)
		{
			var ret=[];
			cfg.source.forEach(function(item,index)
			{
				if(item.indexOf(val)>=0)
				{
					ret.push(item);
				}
			});
			return ret;
		}

		function showSelectDown(html,e)
		{
			$auto.html(html).show();
		}

		function hideSelectDown()
		{
			$auto.hide();
		}

		function chooseThis()
		{
			if(word)
			{
				var pos=getPosition($this[0]);
				var origin=$this.val();
				var part1=origin.substr(0,pos-word.length);
				var part2=origin.substr(pos);
				var text=$(this).text();
				$this.val(part1+text+part2+' ').focus();
				hideSelectDown();
			}
			else
			{
				console.warn('no word typed');
			}
		}
		$this.on('keyup',keyupEvent).on('keydown',keydownEvent);
		$auto.on('click','li',chooseThis)
		.on('keydown','li',function(e)
		{
			if(e.keyCode==13)
			{
				chooseThis.call(this,e);
			}
		});
	}

	$.fn.autocomplete=autocomplete;

})(jQuery,window);
