const url = window.location,
     hash = url.hash,
     href = url.href,
     path = url.pathname,
   search = url.search,
     peso = Intl.NumberFormat('en-US', {style: 'currency', currency: 'PHP'});

const xmbar  = {'exit': '/a/index.html', 'active': '/a/active_devices.html', 'recent': '/a/recent_devices.html', 'txn': '/a/txn.html', 'pwd': '/a/change_password.html'};

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
  let tags = ['MB','GB','TB','PB','EB','ZB','YB'];

  return parseFloat(size / Math.pow(1024, base)).toFixed(2) + tags[base];
}

function utc_to_local(ts) {
  return new Date(ts).toLocaleString();
}

function empty_table(t_id) {
  let tbody = document.getElementById(t_id).getElementsByTagName('tbody')[0];

  Array.from(tbody.rows).forEach((tr) => tbody.deleteRow(tr));
}
