<?php

ini_set('memory_limit', '1500M');
ini_set('max_execution_time', 0);

$sizex = 6000;
$sizey = 4473;
$mapsizex = 20500;
$mapsizey = 20500;

$mapsize = $mapsizex;
$size = $sizex;
$sizeyoffset = ($sizex - $sizey) / 2;

$polygons = '';

include('roadz.php');

$mod = 110;
foreach ($roads as $road) {
	$p = array(
		($road[0] + $mapsize / 2) / $mapsize * $size + $mod,
		(-$road[1] + $mapsize / 2) / $mapsize * $size - $mod,
		($road[2] + $mapsize / 2) / $mapsize * $size + $mod,
		(-$road[3] + $mapsize / 2) / $mapsize * $size - $mod
	);
	foreach ($p as &$t) {
		$t = round($t, 1);
	}
	$polygons .= <<<d
		[{$p[0]},{$p[1]},{$p[2]},{$p[3]}],
d;
}

include('plhudairports.txt');

$airports = '';
$labels = '';
foreach ($aps as $ap) {
	$p = array(
		($ap[0] + $mapsize / 2) / $mapsize * $size + $mod,
		(-$ap[1] + $mapsize / 2) / $mapsize * $size - $mod,
		($ap[2] + $mapsize / 2) / $mapsize * $size + $mod,
		(-$ap[3] + $mapsize / 2) / $mapsize * $size - $mod
	);
	$a = atan2($p[3]-$p[1],$p[2]-$p[0])-M_PI/2;
	$e = cos($a) * 4;
	$d = sin($a) * 4;
	$mp = array(
		$p[0] - $e,
		$p[1] - $d,
		$p[0] + $e,
		$p[1] + $d,
		$p[2] + $e,
		$p[3] + $d,
		$p[2] - $e,
		$p[3] - $d,
	);
	foreach ($mp as &$t) {
		$t = round($t, 1);
	}
	$tx = round(max($p[0], $p[2]) + 7.0, 1);
	$ty = round(max($p[1], $p[3]) + 7.0, 1);
	$airports .= <<<d
		<polygon class="a" points="{$mp[0]},{$mp[1]},{$mp[2]},{$mp[3]},{$mp[4]},{$mp[5]},{$mp[6]},{$mp[7]}"/>
d;
	$labels .= <<<d
		<text x="{$tx}" y="${ty}">{$ap[4]}</text>
d;
}

$helipads = "";
$waterfields = "";

$stuff = explode("\r\n", file_get_contents('savedpositionsall.txt'));
foreach($stuff as $position) {
	$parts = explode('// ', $position);
	if (count($parts) == 2 && strlen($parts[1]) > 1) {
		$name = substr($parts[1], 2);
		$coords = explode(',', $parts[0]);
		$coords[1] -= 40;
		$coords[2] -= 30;
		$x = round(($coords[1] + $mapsize / 2) / $mapsize * $size + $mod, 1);
		$y = round((-$coords[2] + $mapsize / 2) / $mapsize * $size - $mod, 1);
		$x2 = round($x + 25.0, 1);
		$y2 = round($y - 11.0, 1);
		if (substr($parts[1], 0, 1) == 'W') {
			$name = substr($name, 0, -strlen(' Water Field'));
			$waterfields .= <<<d
				<text x="{$x}" y="${y}">W</text>
				<text x="{$x2}" y="${y2}">{$name}</text>
d;
		}
		if (substr($parts[1], 0, 1) == 'H') {
			$name = substr($name, 0, -strlen(' Helipad'));
			$helipads .= <<<d
				<text x="{$x}" y="${y}">H</text>
				<text x="{$x2}" y="${y2}">{$name}</text>
d;
		}
	}
}

// --------------------------------------------------------
$js = <<<D

var roadsArray = [
	{$polygons}
];

function $(a)
{
	return document.getElementById(a);
}

function toggle(r)
{
	r = $(r);
	r.style.display = (r.style.display=='none'?'block':'none'); 
}
function toggleRoads()
{
	toggle('r');

	if (typeof(roadsArray) === 'undefined')
	{
		return;
	}

	for (c in roadsArray) {
		var n = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
		n.setAttribute('class', 'r');
		n.setAttribute('data-id', c);
		c = roadsArray[c];
		var d = Math.atan2(c[3]-c[1],c[2]-c[0])-Math.PI/2;
		var e = Math.cos(d) * 4;
		d = Math.sin(d) * 4;
		n.setAttribute('points',
			''+
			(c[0]-e)
			+','+
			(c[1]-d)
			+','+
			(c[0]+e)
			+','+
			(c[1]+d)
			+','+
			(c[2]+e)
			+','+
			(c[3]+d)
			+','+
			(c[2]-e)
			+','+
			(c[3]-d)
		);
		n.onclick = rsc;
		r.appendChild(n);
	}
	
	roadsArray = undefined;
}

function rsc()
{
	alert('road segment #' + this.getAttribute('data-id'));
}

var moveData = {};
var svgElement = $('s');

var allGroup = $('all');

var transmatrix = [1, 0, 0, 1, 0, 0];

window.onmousedown = function(e)
{
	e.preventDefault();
	moveData = { 
		x:e.clientX,
		y:e.clientY,
		z:transmatrix[4],
		a:transmatrix[5]
	};
};

window.onmouseup = function(e)
{
	e.preventDefault();
	moveData = {};
};

window.onmousemove = function(e)
{
	e.preventDefault();
	if (!moveData.x)
	{
		return;
	}
	var r = allGroup.getBoundingClientRect();
	transmatrix[4] = parseInt(moveData.z)+(e.clientX-moveData.x)*{$sizex}/r.width*transmatrix[0];
	transmatrix[5] = parseInt(moveData.a)+(e.clientY-moveData.y)*{$sizey}/r.height*transmatrix[3];
	um();
};

document.addEventListener('wheel', window.onmousewheel = document.onmousewheel = function(e)
{
	e.preventDefault();
	if (e.deltaY != 0)
	{
		zoom(e.deltaY<0?1.25:0.8, e.clientX / window.innerWidth, e.clientY / window.innerHeight);
	}
});


function zoom(s,mx,my)
{
	if (transmatrix[0] < 0.5 && s < 1 || transmatrix[0] > 100 && s > 1) {
		return;
	}
	for (var i = 0; i < transmatrix.length; i++)
	{
		transmatrix[i] *= s;
	}
	transmatrix[4] += (1 - s) * {$size} * mx;
	transmatrix[5] += (1 - s) * {$size} * my;
	um();
}

function um()
{
	allGroup.setAttributeNS(null, 'transform', 'matrix(' +  transmatrix.join(' ') + ')');
}

D;
// --------------------------------------------------------

$js = str_replace('roadsArray', 'w', $js);
$js = str_replace('moveData', 'v', $js);
$js = str_replace('svgElement', 'u', $js);
$js = str_replace('allGroup', 't', $js);
$js = str_replace('transmatrix', 'k', $js);

$js = str_replace(' =', '=', $js);
$js = str_replace('= ', '=', $js);
$js = str_replace('+ ', '+', $js);
$js = str_replace(' +', '+', $js);
$js = str_replace('- ', '-', $js);
$js = str_replace(' -', '-', $js);
$js = str_replace('* ', '*', $js);
$js = str_replace(' *', '*', $js);
$js = str_replace('/ ', '/', $js);
$js = str_replace(' /', '/', $js);
$js = str_replace('if (', 'if(', $js);
$js = str_replace(' {', '{', $js);
$js = str_replace('} ', '}', $js);
$js = str_replace(') ', ')', $js);
$js = str_replace('( ', '(', $js);
$js = str_replace(', ', ',', $js);

// --------------------------------------------------------
$d = <<<D

<html>
<head>
<title>PL webmap</title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<style type="text/css">
body
{
	position: relative;
	margin: 0;
	font-family: tahoma,sans-serif;
	background: #000;
	overflow: hidden;
}
p
{
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	margin: 0;
	text-align: center;
}
svg+p
{
	right: inherit;
	display: inline-block;
	bottom: 0;
	top: inherit;
	background: #fff;
	padding: 0.1em 0.4em 0.3em 0.4em;
	cursor: normal;
	font-size: 0.7em;
}
svg
{
	cursor: move;
}
polygon.r
{
	fill: #db730b
}
polygon.a
{
	fill: #f00;
	stroke: #000;
}
polygon:hover
{
	fill: #f00
}
image {
	image-rendering: optimizeSpeed;
	image-rendering: -moz-crisp-edges;
	image-rendering: -o-crisp-edges;
	image-rendering: -webkit-optimize-contrast;
	image-rendering: pixelated;
	image-rendering: optimize-contrast;
	-ms-interpolation-mode: nearest-neighbor
}
text {
	font-size: 1em;
	font-family: tahoma, sans-serif;
	fill: #fff
}
#y text:nth-child(odd) {
	font-size: 2em;
	font-weight: bold;
	stroke: #000
}
#h text:nth-child(odd) {
	fill: #47E305
}
#w text:nth-child(odd) {
	fill: #0547E3
}
</style>
</head>
<body>
<div id="i" class="noselect"></div>
<svg id="s" width="100vw" height="100vh" viewbox="0 0 {$size} {$size}"
	xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
	<g id="all" transform="translate(0,0)">

		<image id="img" x="0" y="{$sizeyoffset}" width="{$sizex}" height="{$sizey}"
			preserveAspectRatio="none" xlink:href="map.jpg"/>

		<g id="r" style="display:none"></g>
		<g id="y">
			<g id="w">
				{$waterfields}
			</g>
			<g id="h">
				{$helipads}
			</g>
		</g>
		<g id="a">
			{$airports}
			<g id="l">
				{$labels}
			</g>
		</g>

	</g>
</svg>
<p>
	<a href="http://thepilotslife.com/forums/index.php?topic=53031.0" target="_blank">http://thepilotslife.com/forums/index.php?topic=53031.0</a> - <a href="http://thepilotslife.com" target="_blank">The Pilot's Life</a> - <a href="about.html">about</a> - &copy; robin_be 2017
</p>
<p>
	<button onclick="zoom(1.25, 0.5, 0.5)">zoom+</button>
	<button onclick="zoom(0.8, 0.5, 0.5)">zoom-</button>
	<button onclick="toggle('h')">toggle helipads</button>
	<button onclick="toggle('w')">toggle waterfields</button>
	<button onclick="toggle('a')">toggle runways</button>
	<button onclick="toggle('l')">toggle runway labels</button>
	<button onclick="toggleRoads()">toggle roads</button>
	<select onchange="$('img').setAttribute('xlink:href', this.value)">
		<option value="map.jpg">default (2.85MB 6000x4473 jpg)</option>
		<option value="map-fs8.png">medium (3.66MB 6000x4473 png)</option>
		<option value="map10k-or8.png">highest (13.3MB 10000x7455 png)</option>
	</select>
</p>
<script>
{$js}
</script>
</body>
</html>

D;
// --------------------------------------------------------

$d = str_replace('toggleRoads', 'x', $d);
$d = str_replace('changequality', 'z', $d);

$d = str_replace("\n", '', $d);
$d = str_replace("\r", '', $d);
$d = str_replace("\t", '', $d);
file_put_contents('index.html', $d);
