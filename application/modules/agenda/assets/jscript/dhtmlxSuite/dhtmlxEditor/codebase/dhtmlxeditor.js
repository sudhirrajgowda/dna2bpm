//v.3.0 build 110713

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
function dhtmlXEditor(d,g){var b=this;this.skin=g||dhtmlx.skin||"dhx_skyblue";this.iconsPath=dhtmlx.image_path||"../../codebase/imgs/";typeof d=="string"&&(d=document.getElementById(d));for(this.base=d;this.base.childNodes.length>0;)this.base.removeChild(this.base.childNodes[0]);this.tbData=(this._isToolbar=this.initDhtmlxToolbar!=null&&window.dhtmlXToolbarObject!=null?!0:!1)?"":"<div class='dhxeditor_"+this.skin+"_btns'><a href='javascript:void(0);' onclick='return false;' tabindex='-1'><div actv='b' cmd='applyBold' class='dhxeditor_"+
this.skin+"_tbbtn btn_bold'></div></a><a href='javascript:void(0);' onclick='return false;' tabindex='-1'><div actv='i' cmd='applyItalic' class='dhxeditor_"+this.skin+"_tbbtn btn_italic'></div></a><a href='javascript:void(0);' onclick='return false;' tabindex='-1'><div actv='u' cmd='applyUnderscore' class='dhxeditor_"+this.skin+"_tbbtn btn_underline'></div></a><a href='javascript:void(0);' onclick='return false;' tabindex='-1'><div actv='c' cmd='clearFormatting' class='dhxeditor_"+this.skin+"_tbbtn btn_clearformat'></div></a><div class='verline_l'></div><div class='verline_r'></div></div>";
var f=_isIE?this.base.currentStyle.position:window.getComputedStyle(this.base,null).getPropertyValue("position");if(!(f=="relative"||f=="absolute"))this.base.style.position="relative";this.base.innerHTML=this.tbData+"<div style='position:absolute; width: 100%; overflow: hidden;'></div>";var h=new dhtmlXContainerLite(this.base);h.skin=this.skin;h.setContent(this.base.childNodes[this._isToolbar?0:1]);var m=this._isToolbar?0:this.base.childNodes[0].offsetHeight;this.base.adjustContent(this.base,m);this.cBlock=
document.createElement("DIV");this.cBlock.className="dhxcont_content_blocker";this.cBlock.style.display="none";this.base.appendChild(this.cBlock);this.editor=document.createElement("IFRAME");this.editor.className="dhxeditor_mainiframe_"+this.skin;this.editor.frameBorder=0;if(_isOpera)this.editor.scrolling="yes";var j=this.editor;if(_isIE)j.onreadystatechange=function(){if(j.readyState=="complete")try{this.contentWindow.document.body.attachEvent("onfocus",function(a){b._ev("focus",a)}),this.contentWindow.document.body.attachEvent("onblur",
function(a){b._ev("blur",a)}),this.contentWindow.document.body.attachEvent("onkeydown",function(a){b._ev("keydown",a)}),this.contentWindow.document.body.attachEvent("onkeyup",function(a){b._ev("keyup",a)}),this.contentWindow.document.body.attachEvent("onkeypress",function(a){b._ev("keypress",a)}),this.contentWindow.document.body.attachEvent("onmouseup",function(a){b._ev("mouseup",a)}),this.contentWindow.document.body.attachEvent("onmousedown",function(a){b._ev("mousedown",a)}),this.contentWindow.document.body.attachEvent("onclick",
function(a){b._ev("click",a)})}catch(a){}},j.onunload=function(){this.contentWindow.document.body.detachEvent("onfocus",function(){b._ev("focus",event)});this.contentWindow.document.body.detachEvent("onblur",function(){b._ev("blur",event)});this.contentWindow.document.body.detachEvent("onkeydown",function(){b._ev("keydown",event)});this.contentWindow.document.body.detachEvent("onkeyup",function(){b._ev("keyup",event)});this.contentWindow.document.body.detachEvent("onkeypress",function(){b._ev("keypress",
event)});this.contentWindow.document.body.detachEvent("onmouseup",function(){b._ev("mouseup",event)});this.contentWindow.document.body.detachEvent("onmousedown",function(){b._ev("mousedown",event)});this.contentWindow.document.body.detachEvent("onclick",function(){b._ev("click",event)})};else{var l=this.editor;j.onload=function(){this.contentWindow.addEventListener("focus",function(a){b._ev("focus",a)},!1);this.contentWindow.addEventListener("blur",function(a){b._ev("blur",a)},!1);this.contentWindow.addEventListener("keydown",
function(a){b._ev("keydown",a)},!1);this.contentWindow.addEventListener("keyup",function(a){b._ev("keyup",a)},!1);this.contentWindow.addEventListener("keypress",function(a){b._ev("keypress",a)},!1);this.contentWindow.addEventListener("mouseup",function(a){b._ev("mouseup",a)},!1);this.contentWindow.addEventListener("mousedown",function(a){b._ev("mousedown",a)},!1);this.contentWindow.addEventListener("click",function(a){b._ev("click",a)},!1)};j.onunload=function(){this.contentWindow.removeEventListener("focus",
function(a){b._ev("focus",a)},!1);this.contentWindow.removeEventListener("blur",function(a){b._ev("blur",a)},!1);this.contentWindow.removeEventListener("keydown",function(a){b._ev("keydown",a)},!1);this.contentWindow.removeEventListener("keyup",function(a){b._ev("keyup",a)},!1);this.contentWindow.removeEventListener("keypress",function(a){b._ev("keypress",a)},!1);this.contentWindow.removeEventListener("mouseup",function(a){b._ev("mouseup",a)},!1);this.contentWindow.removeEventListener("mousedown",
function(a){b._ev("mousedown",a)},!1);this.contentWindow.removeEventListener("click",function(a){b._ev("click",a)},!1)}}this._ev=function(a,b){this.callEvent("onAccess",[a,b])};this._focus=function(){_isIE?this.editor.contentWindow.document.body.focus():this.editor.contentWindow.focus()};this.base.attachObject(this.editor);this.edWin=this.editor.contentWindow;this.edDoc=this.edWin.document;this._prepareContent=function(a,b){var c="";a===!0&&this.getContent!=null&&(c=this.getContent());var e=this.editor.contentWindow.document;
e.open("text/html","replace");_isOpera?e.write("<html><head><style> html, body { overflow:auto; padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:10pt; background-color:#ffffff;} </style></head><body "+(b!==!0?"contenteditable='true'":"")+" tabindex='0'></body></html>"):window._KHTMLrv?e.write("<html><head><style> html {overflow-x: auto; overflow-y: auto;} body { overflow: auto; overflow-y: scroll;} html,body { padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:10pt; background-color:#ffffff;} </style></head><body "+
(b!==!0?"contenteditable='true'":"")+" tabindex='0'></body></html>"):_isIE?e.write("<html><head><style> html {overflow-y: auto;} body {overflow-y: scroll;} html,body { overflow-x: auto; padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:10pt; background-color:#ffffff;} </style></head><body "+(b!==!0?"contenteditable='true'":"")+" tabindex='0'></body></html>"):e.write("<html><head><style> html,body { overflow-x: auto; overflow-y: scroll; padding:0px; padding-left:1px !important; height:100%; margin:0px; font-family:Tahoma; font-size:10pt; background-color:#ffffff;} </style></head><body "+
(b!==!0?"contenteditable='true'":"")+" tabindex='0'></body></html>");e.close();_isIE?e.contentEditable=b!==!0:e.designMode=b!==!0?"On":"Off";if(_isFF)try{e.execCommand("useCSS",!1,!0)}catch(f){}a===!0&&this.setContent!=null&&this.setContent(c)};this._prepareContent();this.setIconsPath=function(){};this.init=function(){};this.setSizes=function(){var a=this._isToolbar?0:this.base.childNodes[0].offsetHeight;this.base.adjustContent(this.base,a)};this._resizeTM=null;this._resizeTMTime=100;this._doOnResize=
function(){window.clearTimeout(b._resizeTM);b._resizeTM=window.setTimeout(function(){b.setSizes&&b.setSizes()},b._resizeTMTime)};this._doOnUnload=function(){window.detachEvent("onresize",this._doOnResize);window.removeEventListener("resize",this._doOnResize,!1)};dhtmlxEvent(window,"resize",this._doOnResize);this.base.childNodes[0].onselectstart=function(a){a=a||event;a.cancelBubble=!0;a.returnValue=!1;a.preventDefault&&a.preventDefault();return!1};for(var k=0;k<this.base.childNodes[0].childNodes.length-
2;k++)this.base.childNodes[0].childNodes[k].childNodes[0].onmousedown=function(){var a=this.getAttribute("cmd");typeof b[a]=="function"&&(b[a](),b.callEvent("onToolbarClick",[this.getAttribute("actv")]));return!1},this.base.childNodes[0].childNodes[k].childNodes[0].onclick=function(){return!1};this.runCommand=function(a,b){if(this._roMode!==!0){arguments.length<2&&(b=null);_isIE&&this.edWin.focus();try{var c=this.editor.contentWindow.document;c.execCommand(a,!1,b)}catch(e){}if(_isIE){this.edWin.focus();
var f=this;window.setTimeout(function(){f.edWin.focus()},1)}}};this.applyBold=function(){this.runCommand("Bold")};this.applyItalic=function(){this.runCommand("Italic")};this.applyUnderscore=function(){this.runCommand("Underline")};this.clearFormatting=function(){this.runCommand("RemoveFormat")};this._isToolbar&&this.initDhtmlxToolbar();dhtmlxEventable(this);dhtmlxEvent(this.edDoc,"click",function(a){var i=a||window.event,c=i.target||i.srcElement;b.showInfo(c)});_isOpera&&dhtmlxEvent(this.edDoc,"mousedown",
function(a){var i=a||window.event,c=i.target||i.srcElement;b.showInfo(c)});dhtmlxEvent(this.edDoc,"keyup",function(a){var i=a||window.event,c=i.keyCode,e=i.target||i.srcElement;(c==37||c==38||c==39||c==40||c==13)&&b.showInfo(e)});this.attachEvent("onFocusChanged",function(a){b._doOnFocusChanged&&b._doOnFocusChanged(a)});this.showInfo=function(a){if(a=this.getSelectionBounds().end?this.getSelectionBounds().end:a)try{if(this.edWin.getComputedStyle){var b=this.edWin.getComputedStyle(a,null),c=b.getPropertyValue("font-weight")==
401?700:b.getPropertyValue("font-weight");this.style={fontStyle:b.getPropertyValue("font-style"),fontSize:b.getPropertyValue("font-size"),textDecoration:b.getPropertyValue("text-decoration"),fontWeight:c,fontFamily:b.getPropertyValue("font-family"),textAlign:b.getPropertyValue("text-align")};if(window._KHTMLrv)this.style.fontStyle=b.getPropertyValue("font-style"),this.style.vAlign=b.getPropertyValue("vertical-align"),this.style.del=this.isStyleProperty(a,"span","textDecoration","line-through"),this.style.u=
this.isStyleProperty(a,"span","textDecoration","underline")}else b=a.currentStyle,this.style={fontStyle:b.fontStyle,fontSize:b.fontSize,textDecoration:b.textDecoration,fontWeight:b.fontWeight,fontFamily:b.fontFamily,textAlign:b.textAlign};this.setStyleProperty(a,"h1");this.setStyleProperty(a,"h2");this.setStyleProperty(a,"h3");this.setStyleProperty(a,"h4");window._KHTMLrv||(this.setStyleProperty(a,"del"),this.setStyleProperty(a,"sub"),this.setStyleProperty(a,"sup"),this.setStyleProperty(a,"u"));this.callEvent("onFocusChanged",
[this.style,b])}catch(e){return null}};this.getSelectionBounds=function(){var a,b,c,e;if(this.edWin.getSelection){var f=this.edWin.getSelection();a=f.getRangeAt(f.rangeCount-1);c=a.startContainer;e=a.endContainer;b=a.commonAncestorContainer;if(c.nodeName=="#text")b=b.parentNode;if(c.nodeName=="#text")c=c.parentNode;if(c.nodeName.toLowerCase()=="body")c=c.firstChild;if(e.nodeName=="#text")e=e.parentNode;if(e.nodeName.toLowerCase()=="body")e=e.lastChild;c==e&&(b=c);return{root:b,start:c,end:e}}else if(this.edWin.document.selection){a=
this.edDoc.selection.createRange();if(!a.duplicate)return null;b=a.parentElement();var h=a.duplicate(),d=a.duplicate();h.collapse(!0);d.moveToElementText(h.parentElement());d.setEndPoint("EndToStart",h);c=h.parentElement();h=a.duplicate();d=a.duplicate();d.collapse(!1);h.moveToElementText(d.parentElement());h.setEndPoint("StartToEnd",d);e=d.parentElement();if(c.nodeName.toLowerCase()=="body")c=c.firstChild;if(e.nodeName.toLowerCase()=="body")e=e.lastChild;c==e&&(b=c);return{root:b,start:c,end:e}}return null};
this.getContent=function(){return this.edDoc.body?_isFF?this.editor.contentWindow.document.body.innerHTML.replace(/<\/{0,}br\/{0,}>\s{0,}$/gi,""):this.edDoc.body.innerHTML:""};this.setContent=function(a){if(this.edDoc.body){if(navigator.userAgent.indexOf("Firefox")!=-1){if(typeof this._ffTest=="undefined")this.editor.contentWindow.document.body.innerHTML="",this.runCommand("InsertHTML","test"),this._ffTest=this.editor.contentWindow.document.body.innerHTML.length>0;this._ffTest?this.editor.contentWindow.document.body.innerHTML=
a:(this.editor.contentWindow.document.body.innerHTML="",a.length==0&&(a=" "),this.runCommand("InsertHTML",a))}else this.editor.contentWindow.document.body.innerHTML=a;this.callEvent("onContentSet",[])}else dhtmlxEvent(this.edWin,"load",function(){b.setContent(a)})};this.setContentHTML=function(a){(new dtmlXMLLoaderObject(this._ajaxOnLoad,this,!1,!0)).loadXML(a)};this._ajaxOnLoad=function(a,b,c,e,f){f.xmlDoc.responseText&&a.setContent(f.xmlDoc.responseText)}}
function dhtmlXContainerLite(d){var g=this;this.obj=d;this.dhxcont=null;this.setContent=function(b){this.dhxcont=b;this.dhxcont.innerHTML="<div style='position: relative; left: 0px; top: 0px; overflow: hidden;'></div>";this.dhxcont.mainCont=this.dhxcont.childNodes[0];this.obj.dhxcont=this.dhxcont};this.obj._genStr=function(b){for(var f="",h="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",d=0;d<b;d++)f+=h.charAt(Math.round(Math.random()*(h.length-1)));return f};this.obj.adjustContent=
function(b,f,d,g,j){this.dhxcont.style.left=(this._offsetLeft||0)+"px";this.dhxcont.style.top=(this._offsetTop||0)+f+"px";var l=b.clientWidth+(this._offsetWidth||0);if(g!==!0)this.dhxcont.style.width=Math.max(0,l)+"px";if(g!==!0&&this.dhxcont.offsetWidth>l)this.dhxcont.style.width=Math.max(0,l*2-this.dhxcont.offsetWidth)+"px";var k=b.clientHeight+(this._offsetHeight||0);this.dhxcont.style.height=Math.max(0,k-f)+(d!=null?d:0)+"px";if(this.dhxcont.offsetHeight>k-f)this.dhxcont.style.height=Math.max(0,
(k-f)*2-this.dhxcont.offsetHeight)+"px";if(j&&!isNaN(j))this.dhxcont.style.height=Math.max(0,parseInt(this.dhxcont.style.height)-j)+"px";if(this._minDataSizeH!=null&&parseInt(this.dhxcont.style.height)<this._minDataSizeH)this.dhxcont.style.height=this._minDataSizeH+"px";if(this._minDataSizeW!=null&&parseInt(this.dhxcont.style.width)<this._minDataSizeW)this.dhxcont.style.width=this._minDataSizeW+"px";if(g!==!0&&(this.dhxcont.mainCont.style.width=this.dhxcont.clientWidth+"px",this.dhxcont.mainCont.offsetWidth>
this.dhxcont.clientWidth))this.dhxcont.mainCont.style.width=Math.max(0,this.dhxcont.clientWidth*2-this.dhxcont.mainCont.offsetWidth)+"px";var a=this.menu!=null?!this.menuHidden?this.menuHeight:0:0,i=this.toolbar!=null?!this.toolbarHidden?this.toolbarHeight:0:0,c=this.sb!=null?!this.sbHidden?this.sbHeight:0:0;this.dhxcont.mainCont.style.height=this.dhxcont.clientHeight+"px";if(this.dhxcont.mainCont.offsetHeight>this.dhxcont.clientHeight)this.dhxcont.mainCont.style.height=Math.max(0,this.dhxcont.clientHeight*
2-this.dhxcont.mainCont.offsetHeight)+"px";this.dhxcont.mainCont.style.height=Math.max(0,parseInt(this.dhxcont.mainCont.style.height)-a-i-c)+"px"};this.obj.attachToolbar=function(){var b=document.createElement("DIV");b.style.position="relative";b.style.overflow="hidden";b.id="dhxtoolbar_"+this._genStr(12);this.dhxcont.insertBefore(b,this.dhxcont.childNodes[this.menu!=null?1:0]);this.toolbar=new dhtmlXToolbarObject(b.id,this.skin);g.skin=="dhx_web"?(this.toolbarHeight=32,this.dhxcont.className="dhtmlx_editor_extended_"+
g.skin):this.toolbarHeight=b.offsetHeight+(this._isLayout&&this.skin=="dhx_skyblue"?2:0);this.toolbarId=b.id;this._doOnAttachToolbar&&this._doOnAttachToolbar("init");this.adjust();return this.toolbar};this.obj.attachObject=function(b,f){typeof b=="string"&&(b=document.getElementById(b));if(f){b.style.visibility="hidden";b.style.display="";var d=b.offsetWidth,g=b.offsetHeight}this._attachContent("obj",b);if(f&&this._isWindow)b.style.visibility="visible",this._adjustToContent(d,g)};this.obj.adjust=
function(){if(this.skin=="dhx_skyblue"&&this.toolbar){if(this._isWindow||this._isLayout)document.getElementById(this.toolbarId).style.height="29px",this.toolbarHeight=document.getElementById(this.toolbarId).offsetHeight,this._doOnAttachToolbar&&this._doOnAttachToolbar("show");this._isCell&&(document.getElementById(this.toolbarId).className+=" in_layoutcell");this._isAcc&&(document.getElementById(this.toolbarId).className+=" in_acccell")}};this.obj._attachContent=function(b,d,h){for(;g.dhxcont.mainCont.childNodes.length>
0;)g.dhxcont.mainCont.removeChild(g.dhxcont.mainCont.childNodes[0]);if(b=="obj"){if(this._isWindow&&d.cmp==null&&this.skin=="dhx_skyblue")this.dhxcont.mainCont.style.border="#a4bed4 1px solid",this.dhxcont.mainCont.style.backgroundColor="#FFFFFF",this._redraw();g.dhxcont._frame=null;g.dhxcont.mainCont.appendChild(d);g.dhxcont.mainCont.style.overflow=h===!0?"auto":"hidden";d.style.display=""}};this.obj._dhxContDestruct=function(){for(;this.dhxcont.mainCont.childNodes.length>0;)this.dhxcont.mainCont.removeChild(this.dhxcont.mainCont.childNodes[0]);
this.dhxcont.mainCont.innerHTML="";this.dhxcont.mainCont=null;try{delete this.dhxcont.mainCont}catch(b){}for(;this.dhxcont.childNodes.length>0;)this.dhxcont.removeChild(this.dhxcont.childNodes[0]);this.dhxcont.innerHTML="";this.dhxcont=null;try{delete this.dhxcont}catch(d){}this.attachToolbar=this.adjustContent=this.moveContentTo=this.attachObject=this.adjust=this._dhxContDestruct=this._attachContent=this._genStr=null}}
(function(){dhtmlx.extend_api("dhtmlXEditor",{_init:function(d){return[d.parent,d.skin]},content:"setContent"},{})})();
dhtmlXEditor.prototype.unload=function(){if(this._isToolbar)this._unloadExtModule();else for(;this.base.childNodes[0].childNodes.length>0;){if(this.base.childNodes[0].childNodes[0].tagName&&String(this.base.childNodes[0].childNodes[0].tagName).toLowerCase()=="a")this.base.childNodes[0].childNodes[0].childNodes[0].onclick=null,this.base.childNodes[0].childNodes[0].childNodes[0].onmousedown=null,this.base.childNodes[0].childNodes[0].removeChild(this.base.childNodes[0].childNodes[0].childNodes[0]);this.base.childNodes[0].removeChild(this.base.childNodes[0].childNodes[0])}this.tbData=
this.base.childNodes[0].onselectstart=null;this.detachAllEvents();_isIE?this.editor.onreadystatechange=null:this.editor.onload=null;this.editor.parentNode.removeChild(this.editor);this.edWin=this.edDoc=this.editor=this.editor.onunload=null;this.base._dhxContDestruct();this.base._idd=null;for(this.base.name=null;this.base.childNodes.length>0;)this.base.removeChild(this.base.childNodes[0]);this.unload=this.isReadonly=this.setReadonly=this.setContentHTML=this.setContent=this.getContent=this.getSelectionBounds=
this.showInfo=this.detachAllEvents=this.detachEvent=this.eventCatcher=this.checkEvent=this.callEvent=this.attachEvent=this.clearFormatting=this.applyUnderscore=this.applyItalic=this.applyBold=this.runCommand=this.setSizes=this.init=this.setIconsPath=this._doOnUnload=this._doOnResize=this._prepareContent=this._focus=this._ev=this._ajaxOnLoad=this.iconsPath=this.skin=this._resizeTMTime=this._resizeTM=this._isToolbar=this.cBlock=this.base=null};
dhtmlXEditor.prototype.setReadonly=function(d){this._roMode=d===!0;this._prepareContent(!0,this._roMode);this.cBlock.style.display=this._roMode?"":"none"};dhtmlXEditor.prototype.isReadonly=function(){return this._roMode||!1};

//v.3.0 build 110713

/*
Copyright DHTMLX LTD. http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/