const url = window.location,
     hash = url.hash,
     href = url.href,
     path = url.pathname,
   search = url.search;


const foswvs = {};

foswvs.txn_o = 0;
foswvs.txn_l = 25;

foswvs.txn = function() {
  fetch('./x.php?txn=get_all&offset='+this.txn_o)
    .then((x) => x.json())
    .then((x) => {
      x.forEach((txn) => {
        displayTxn(txn);
      })
      this.txn_o += 25;
    }
  );
}

foswvs.bw_add = function(mac, limit) {
  fetch(`./x.php?device=add_session&mac=${mac}&limit=${limit}`)
    .then((x) => x.json())
    .then((x) => {
      total_mb_used.innerText = x.total_mb_used;
      total_mb_limit.innerText = x.total_mb_limit;
    }
  );
}

foswvs.devinfo = function(mac) {
  let mac_addr = document.getElementById('mac_addr');
  let total_mb_used = document.getElementById('total_mb_used');
  let total_mb_limit = document.getElementById('total_mb_limit');

  fetch('./x.php?device=get_session&mac='+mac)
    .then((x) => x.json())
    .then((x) => {
      mac_addr.innerText = x.mac;
      total_mb_used.innerText = x.total_mb_used;
      total_mb_limit.innerText = x.total_mb_limit;

      setTimeout(() => foswvs.devinfo(mac), 2000);
    }
  );
}

if( path == '/x/' ) {
  fetch('./x.php?network=dhcp_leases')
    .then((x) => x.json())
    .then((x) => {
      x.forEach((d) => {
        displayDHCP(d);
      })
    }
  );
}

if( path == '/x/txn.html' ) {
  foswvs.txn();
}

if( path == '/x/device.html' ) {
  let mac = search.substr(5);

  foswvs.devinfo(mac);

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

    col_mac.appendChild(txt_mac);
     col_ip.appendChild(txt_ip);
   col_host.appendChild(txt_host);
}

function displayTxn(txn) {
  let table = document.getElementById('txns').getElementsByTagName('tbody')[0];
        row = table.insertRow(-1);
    col_mac = row.insertCell(0);
    col_amt = row.insertCell(1);
     col_mb = row.insertCell(2);
     col_ts = row.insertCell(3);

    txt_mac = document.createTextNode(txn.mac);
    txt_amt = document.createTextNode(txn.amt);
     txt_mb = document.createTextNode(format_mb(txn.mb));
     txt_ts = document.createTextNode(txn.ts);

    col_mac.appendChild(txt_mac);
    col_amt.appendChild(txt_amt);
     col_mb.appendChild(txt_mb);
     col_ts.appendChild(txt_ts);

    row.setAttribute('title', 'device id: ' + txn.devId);
}

function format_mb(size) {
  if( !size ) return size + 'MB';

  let base = Math.floor(Math.log(size) / Math.log(1024));
  console.log(base);
  let tags = ['MB','GB','TB','PB','EB','ZB','YB'];

  return parseFloat(size / Math.pow(1024, base)).toFixed(2) + tags[base];
}

function moretxn() {
  foswvs.txn();
}

document.addEventListener('click', function(e) {
  console.log(e);

  if( e.target.id == 'moretxns' ) {
    foswvs.txn();
  }

  if( e.target.parentNode.className == 'devinfo' ) {
    window.location.href = '/x/device.html?mac=' + e.target.parentNode.dataset.mac;
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

