<!--t ASCII Table t-->
<!--d (function(){ var table = &#039;&#039;; for (var i=0; i d-->

<div class="ASCII-Table" markdown="1">

|🔠 Hex|🔢 Dec|💱 Char|
|-|-|-|

<div><script>
(function(){

var chars = [
  "NUL", "SOH", "STX", "ETX", "EOT", "ENQ", "ACK", "BEL",
  "BS", "HT", "LF", "VT", "FF", "CR", "SO", "SI",
  "DLE", "DC1", "DC2", "DC3", "DC4", "NAK", "SYN", "ETB",
  "CAN", "EM", "SUB", "ESC", "FS", "GS", "RS", "US",
];

var table = '';
for (var i=0; i<256; i++) {
  var hex = i.toString(16).toUpperCase();
  table += '<tr>' +
    '<td>' + (hex.length == 2 ? hex : '0' + hex) + '</td>' +
    '<td>' + i + '</td>' +
    '<td>' + (i < chars.length ? chars[i] : String.fromCharCode(i)) + '</td>' +
  '</tr>';
}

document.querySelector('.ASCII-Table table tbody').innerHTML += table;
//document.querySelector('.ASCII-Table').appendChild(Object.assign(document.createElement('table'), {innerHTML: table}));

})();
</script></div>
</div>