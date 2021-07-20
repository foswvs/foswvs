const url = window.location,
     hash = url.hash,
     href = url.href,
     path = url.pathname,
   search = url.search,
     peso = Intl.NumberFormat('en-US', {style: 'currency', currency: 'PHP'});

const xmbar  = {'exit': '/a/index.html', 'devices':'/a/devices.html', 'txn':'/a/txn.html', 'pwd':'/a/chpwd.html'}

const foswvs = {};

foswvs.txn_o = 0;
foswvs.txn_l = 25;

foswvs.txn = function() {
  fetch(`./x.php?txn=get_all&offset=${this.txn_o}&limit=${this.txn_l}`)
    .then((x) => x.json())
    .then((x) => {
      x.forEach((txn) => {
        displayTxn(txn);
      })
      if (x.length == this.txn_l)
        this.txn_o += this.txn_l;
    }
  );
}

foswvs.bw_add = function(mac, limit) {
  fetch(`./x.php?dev=add_session&mac=${mac}&limit=${limit}`)
    .then((x) => x.json())
    .then((x) => {
      total_mb_used.innerText = x.total_mb_used;
      total_mb_limit.innerText = x.total_mb_limit;
    }
  );
}

foswvs.devinfo = function(mac) {
  let ip_addr  = document.getElementById('ip_addr'),
      mac_addr = document.getElementById('mac_addr'),
      hostname = document.getElementById('hostname'),
      clear_mb = document.getElementById('clear_mb'),
      total_mb_used  = document.getElementById('total_mb_used'),
      total_mb_limit = document.getElementById('total_mb_limit');

  fetch('./x.php?dev=get_session&mac='+mac)
    .then((x) => x.json())
    .then((x) => {
      mac_addr.innerText = x.mac;
      ip_addr.innerText  = x.ip;
      hostname.innerText = x.host;

      total_mb_used.innerText  = x.total_mb_used;
      total_mb_limit.innerText = x.total_mb_limit;

      clear_mb.setAttribute('data-mac', x.mac);

      setTimeout(() => foswvs.devinfo(mac), 2000);
    }
  );
}

if( path == '/a/index.html' ) {
  fetch('./x.php?net=login');
}

if( path == '/a/devices.html' ) {
  fetch('./x.php?dev=all')
    .then((x) => x.json())
    .then((x) => {
      x.forEach((d) => {
        displayDHCP(d);
      })
    }
  );
}

if( path == '/a/dhcp.html' ) {
  fetch('./x.php?net=dhcp_leases')
    .then((x) => x.json())
    .then((x) => {
      x.forEach((d) => {
        displayDHCP(d);
      })
    }
  );
}

if( path == '/a/txn.html' ) {
  foswvs.txn();
}

if( path == '/a/device.html' ) {
  let mac = search.substr(5);

  foswvs.devinfo(mac);
  getDeviceTxn(mac);

  let bw_limit = 1024;

  document.getElementById('bw_input')
    .addEventListener('keydown', (e) => {
      let bw_limit = e.target.value;

      if(e.keyCode != 13 || bw_limit == '') return;


      foswvs.bw_add(mac, bw_limit);
      bw_input.value = '';
    }
  );
}

function displayDHCP(dev) {
  let table = document.getElementById('devices').getElementsByTagName('tbody')[0],
        row = table.insertRow(-1),
    col_mac = row.insertCell(0),
     col_ip = row.insertCell(1),
   col_host = row.insertCell(2),
    col_opt = row.insertCell(3),

    txt_mac = document.createTextNode(dev['mac']),
     txt_ip = document.createTextNode(dev['ip']),
   txt_host = document.createTextNode(dev['host']);

        row.setAttribute('class', 'devinfo');
        row.setAttribute('data-mac',dev['mac']);
        row.setAttribute('title', dev['updated_at']);

    col_mac.appendChild(txt_mac);
     col_ip.appendChild(txt_ip);
   col_host.appendChild(txt_host);
}

function displayTxn(txn) {
  let table = document.getElementById('txns').getElementsByTagName('tbody')[0],
        row = table.insertRow(-1),
    col_mac = row.insertCell(0),
    col_amt = row.insertCell(1),
     col_mb = row.insertCell(2),
     col_ts = row.insertCell(3),

    txt_mac = document.createTextNode(txn.mac),
    txt_amt = document.createTextNode(txn.amt==0 ? 'FREE' : peso.format(txn.amt)),
     txt_mb = document.createTextNode(format_mb(txn.mb)),
     txt_ts = document.createTextNode(new Date(txn.ts).toLocaleString());

    col_mac.appendChild(txt_mac);
    col_amt.appendChild(txt_amt);
     col_mb.appendChild(txt_mb);
     col_ts.appendChild(txt_ts);

    row.setAttribute('class',  'devinfo');
    row.setAttribute('data-mac', txn.mac);
    row.setAttribute('title', `host: ${txn.host} ip: ${txn.ip}`);
}

function txnHistory(txn) {
  let table = document.getElementById('txn_history'),
        row = table.insertRow(-1),
    col_amt = row.insertCell(0),
     col_mb = row.insertCell(1),
     col_ts = row.insertCell(2),
    txt_amt = document.createTextNode(txn.amt==0 ? 'FREE' : peso.format(txn.amt)),
     txt_mb = document.createTextNode(format_mb(txn.mb)),
     txt_ts = document.createTextNode(new Date(txn.ts).toLocaleString());

    col_amt.appendChild(txt_amt);
     col_mb.appendChild(txt_mb);
     col_ts.appendChild(txt_ts);
}

function notxn(){
  let table = document.getElementById('txn_history'),
        row = table.insertRow(-1),
      blank = row.insertCell(0);

      blank.setAttribute('colspan', 3);
      blank.style.textAlign = 'center';
      blank.appendChild(document.createTextNode('No Transaction History'));
}

function format_mb(size) {
  if( !size ) return size + 'MB';

  let base = Math.floor(Math.log(size) / Math.log(1024));
  let tags = ['MB','GB','TB','PB','EB','ZB','YB'];

  return parseFloat(size / Math.pow(1024, base)).toFixed(2) + tags[base];
}

function getDeviceTxn(mac) {
  fetch('./x.php?dev=get_txn&mac='+mac).then((x) => x.json()).then((x) => { x.forEach((tx) => { tx.ts ? txnHistory(tx) : notxn(); }); });
}

document.addEventListener('click', function(e) {
  console.log(e.target);

  if( e.target.id == 'clear_mb' ) {
    fetch(`./x.php?dev=clear_mb&mac=${e.target.dataset.mac}`);
  }

  if( e.target.parentNode.id == 'login' ) {
    let form = e.target.parentNode;

    if( form.password.value ) {
      fetch('./x.php?net=login', {
        method: 'POST',
        body: new FormData(form)
      }).then((x) => {
        if(x.status==200) {
          window.location.href = '/a/devices.html';
        }
      });
    }
  }

  if( e.target.parentNode.id == 'chpwd' ) {
    let form = e.target.parentNode;

    if( form.password.value ) {
      fetch('./x.php?net=chpwd', {
        method: 'POST',
        body: new FormData(form)
      }).then((x) => {
        if(x.status==200) {
          window.location.href = '/a/index.html';
        }
      });
    }
  }

  if( e.target.id == 'moretxns' ) {
    foswvs.txn();
  }

  if( e.target.parentNode.className == 'devinfo' ) {
    window.location.href = '/a/device.html?mac=' + e.target.parentNode.dataset.mac;
  }

  if( e.target.id == 'bw_btn' ) {
    let bw_input = document.getElementById('bw_input');
    let bw_limit = bw_input.value;
    let mac_addr = search.substr(5);

    if( bw_limit < 1 ) return false;

    foswvs.bw_add(mac_addr, bw_limit);

    bw_input.value = '';
  }
});


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
