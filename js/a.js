const url = window.location,
     hash = url.hash,
     href = url.href,
     path = url.pathname,
   search = url.search,
     peso = Intl.NumberFormat('en-US', {style: 'currency', currency: 'PHP'});

const xmbar  = {'exit': '/a/logout.php', 'active': '/a/active_devices.html', 'recent': '/a/recent_devices.html', 'txn': '/a/txn.html', 'pwd': '/a/change_password.html'};

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
  if( !size ) return 0;

  let base = Math.floor(Math.log(size) / Math.log(1024));
  let unit = ['MB','GB','TB','PB','EB','ZB','YB'];

  size = size / Math.pow(1024, base);
  size = Number.isInteger(size) ? size : parseFloat(size).toFixed(2);

  return size + unit[base];
}

function utc_to_local(ts) {
  return new Date(ts).toLocaleString();
}

function empty_table(t_id) {
  let tbody = document.getElementById(t_id).getElementsByTagName('tbody')[0];

  Array.from(tbody.rows).forEach((tr) => tbody.deleteRow(tr));
}
