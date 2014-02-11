var len=0;
var cur=0;//当前位置
var mtime;
var data=new Array();

//data[0]=new Array('0','http://tdt.net63.net/plugin/wxwall/headimg/1.jpg','CrapLoveChristy','欢迎来到微信互动墙，积极发言哦！');
var lastid='0';
var vep=true;//查看上墙说明
var vone=false;//查看单条

function viewOneHide(){
	vone=false;
	$("#mone").hide();
}
function viewOne(cid,t)
{
	if(vone==false)
	{
		vone=true;
		var str=t.innerHTML;
		$("#mone").html(str);
		$("#mone").fadeIn(700);
	}else
	{
		vone=false;
		$("#mone").hide();
	}
}
function viewExplan()
{
	if(vep==false)
	{
		vep=true;
		$("#explan").fadeIn(700);
		//clearInterval(mtime);
	}else
	{
		vep=false;
		$("#explan").hide();
		//mtime=setInterval(messageAdd,5000);
	}
}
function messageAdd()
{
	if(cur==len)
	{
		messageData();
		return false;
	}
	var str='<li id=li'+cur+' onclick="viewOne('+cur+',this);"><div class=m1><div class=m2><div class="pic"><img src="'+data[cur][1]+'" width="100" height="100" /></div><div class="c f2"><span>'+data[cur][2]+'：</span>'+data[cur][3]+'</div></div></div></li>';
	$("#list").prepend(str);
	$("#li"+cur).slideDown(800);
	cur++;
	messageData();
}

function messageData()
{
    var wid=$("#wid").text();
    var url='api.php?lastid='+lastid+'&wid='+wid;
	$.getJSON(url,function(d) {
		//alert(d);return;
		if(d['ret']==1)
		{
			$.each(d['data'], function(i,v){
				data.push(new Array(v['num'],v['avatar'],v['nickname'],v['content']));
				lastid=v['num'];
			});
            len = cur+1;
        }
	});
}
window.onload=function()
{
    messageAdd();
	mtime=setInterval(messageAdd,2000);
}