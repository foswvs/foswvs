const url = window.location,
     hash = url.hash,
     href = url.href,
     path = url.pathname,
   search = url.search,
     peso = Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'});

const xmbar  = {'exit': '/a/logout.php', 'active': '/a/active_devices.html', 'recent': '/a/recent_devices.html', 'txn': '/a/txn.html', 'set': '/a/settings.html'};

if( xpane = document.getElementById('xmenubar') ) {
  let ul = document.createElement('ul');

  for(let x in xmbar) {
    let li = document.createElement('li'),
         a = document.createElement('a');

    a.setAttribute('class', 'button');
    a.setAttribute('href', xmbar[x]);
    a.innerText = x;

    li.appendChild(a);
    ul.appendChild(li);
  }

  xpane.appendChild(ul);
}

function format_mb(size) {
  let s = parseFloat(size);

  if(!s) return '-NA-';

  size = Math.abs(s) * 1024 * 1024;

  let base = Math.floor(Math.log(size) / Math.log(1024));
  let unit = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];

  size = size / Math.pow(1024, base);
  size = Number.isInteger(size) ? size : size.toFixed(2);

  return format_mb_pretty(s, size + unit[base<0 ? 0 : base]);
}

function format_mb_pretty(s,su) {
  return s<0 ? '('+su+')' : su;
}

function utc_to_local(ts) {
  return new Date(parseInt(ts)).toLocaleString("en-US", {hour12:true, timeZone: 'Asia/Manila'});
}

function empty_table(t_id) {
  let tbody = document.getElementById(t_id).getElementsByTagName('tbody')[0];

  Array.from(tbody.rows).forEach((tr) => tbody.deleteRow(tr));
}
