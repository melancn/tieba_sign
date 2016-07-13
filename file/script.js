$.post('user.php',
    {type:'show'},
    function(data) {
        $('.show_wait').parent().remove();
        for(var i=0;i<data.length;i++){
            var d=data[i];
            addsdata(d[0],d[1],d[2],d[3]);
        }
        addstable();
        addstable();
        getcron();
    },'json')
    .error(function() {
        $('.show_wait').text('获取失败'); 
        }
    );
$('#gourl').text($('#gourl')[0].href);
$('.show table tbody').delegate('.name','change',function(){
	addshow(this);
}).delegate('.cookie','change',function(){
	addshow(this);
}).delegate('.sub','click',function(){
	var u=$(this).parent().parent().data('user')||{type:"new"},th=this;
	if($(this).parent().parent().find('.name').val()&&$(this).parent().parent().find('.cookie').val()){
		u.name=$(this).parent().parent().find('.name').val();
		u.cookie=$(this).parent().parent().find('.cookie').val()=='如果你不想修改BDUSS/cookie请勿动此处'?'':$(this).parent().parent().find('.cookie').val();
		this.disabled=true;
		msgshow(this,'正在提交');
		$.post('user.php',u,function(data) {msgshow(th,data.msg);th.disabled=false;if(data.no==0){$(th).parent().parent().data('user',data.data);th.nextSibling.disabled=false;$(th).next().disabled=false;getcron();}},'json').error(function(){th.disabled=false;msgshow(th,'提交失败');});
   }else{
		msgshow(th,'代号和BDUSS/cookie不能为空');
   }
}).delegate('.del','click',function(){
	var u=$(this).parent().parent().data('user')||{error:'error'},th=this;
	u.type='del';
	$.post('user.php',u,function(data) {msgshow(th,data.msg);if(data.no==0){delshow(th);getcron();}},'json').error(function() {th.disabled=false;msgshow(th,'删除失败'); });
});
function addstable(){
	$('.show table tbody').append('<tr><td><input type="text" class="name" maxlength="45" value="'+getRndStr(6)+'"></td><td><input type="text" class="cookie"></td><td>等待提交</td><td><button class="sub">提交</button><button class="del" disabled>删除</button></td></tr>');
}
function addsdata(a,b,c,d){
	var h=c==2?'cookie失效，请立即修改':'如果你不想修改BDUSS/cookie请勿动此处';
	$('.show table tbody').append('<tr data-user=\'{"type":"up","uid":"'+a+'","cn":"'+d+'"}\'><td><input type="text" class="name" maxlength="45" value="'+b+'"></td><td><input type="text" class="cookie" value="'+h+'"></td><td>等待提交</td><td><button class="sub">提交</button><button class="del">删除</button></td></tr>');
}
function addshow(e){
	if($(e).parent().parent().index()==$('.show table tr').length-2)addstable();
}
function delshow(e){
	if(e)$(e).parent().parent().remove();
	if($('.show table tr').length<3){
		var l=$('.show table tr').length;
		for(var i=1;i<l;i++)addstable();
	}
}
function msgshow(e,t){
	$(e).parent().prev().text(t);
}
function getRndStr(k)
{
    var s=[];
    var a=parseInt(Math.random()*25)+(Math.random()>0.5?65:97);
    for (var i=0;i<k;i++){
        s[i]=Math.random()>0.5 ? parseInt(Math.random()*9) : String.fromCharCode(parseInt(Math.random()*25)+(Math.random()>0.5?65:97));
    }
    return s.join("");
}
function getcron(){
	var a=$('.show tr'),v=0;
	while(a.length){
        v+=Math.ceil((a.data("user")?a.data("user").cn:0)/30);
        a=a.next();
	}
	
	$('.cs').text(getcc(v*2,Math.ceil(v*1.5)));
	$('.cc').text(getcc(v*4,Math.ceil(v*3)));
	$('.cm').text(getcc(v*8,Math.ceil(v*6)));
}
function getcc(v,e){
	var t,l=60/Math.ceil(5/($('.show tr').length-3));
	if(v<=6)t='*/'+Math.ceil(60/v)+' 4';
	else if(v<=114)t='*/10 4-'+Math.ceil(v/6+3);
	else if(v<=132)t='*/10 '+(4-Math.ceil(v-114)/6)+'-22';
	else if(v<228)t='*/'+Math.ceil(60/(v/19))+' 4-22';
	else if(v<264)t='*/'+Math.ceil(60/(v/22))+' 1-22';
	else if(v<19*l)t='*/'+Math.ceil(60/(v/19))+' 4-22';
	else if(v<22*l)t='*/'+Math.ceil(60/(v/22))+' 1-22';
	else{
		if(e)return getcc(e);
		return '次数过多,超过设定范围';
	}
	return t+' * * *';
}